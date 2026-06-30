<?php

namespace App\Http\Controllers\Estadisticas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class EstadisticaController extends Controller
{
    public function show($id_residencial)
    {
        $id_user = Auth::id();

        $residencial = collect(DB::select("
            SELECT
                R.ID,
                R.NOMBRE,
                R.IMAGEN,
                R.DESCRIPCION,
                COALESCE((SELECT COUNT(*) FROM BLOQUES_RESIDENCIALES WHERE ID_RESIDENCIAL = R.ID AND DELETED_AT IS NULL), 0) AS TOTAL_BLOQUES,
                COALESCE(SUM(CASE WHEN L.ID IS NOT NULL THEN 1 ELSE 0 END), 0) AS TOTAL_LOTES,
                COALESCE(SUM(CASE WHEN LV.ID IS NOT NULL THEN 1 ELSE 0 END), 0) AS LOTES_VENDIDOS,
                COALESCE(SUM(CASE WHEN L.ID_CLIENTE_RESERVAR IS NOT NULL THEN 1 ELSE 0 END), 0) AS LOTES_APARTADOS
            FROM RESIDENCIALES R
            LEFT JOIN BLOQUES_RESIDENCIALES BR ON R.ID = BR.ID_RESIDENCIAL AND BR.DELETED_AT IS NULL
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE AND LV.DELETED_AT IS NULL
            WHERE R.ID = :id_residencial AND R.DELETED_AT IS NULL AND R.ID_USER = :id_user
            GROUP BY R.ID, R.NOMBRE, R.IMAGEN, R.DESCRIPCION
        ", ['id_residencial' => $id_residencial, 'id_user' => $id_user]))->first();

        if (!$residencial) {
            abort(404, 'Residencial no encontrado');
        }

        $stats = $this->getResidencialMonthlyStats($id_residencial);
        $hoy = $this->getResidencialTodayStats($id_residencial);
        $atrasados = $this->getResidencialOverdueStats($id_residencial);
        $chart_data = $this->getResidencialChartData($id_residencial);

        $total_ventas = DB::select("
            SELECT COUNT(DISTINCT V.ID) AS TOTAL
            FROM VENTAS V
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            WHERE BR.ID_RESIDENCIAL = :id_residencial AND V.DELETED_AT IS NULL
        ", ['id_residencial' => $id_residencial])[0]->total;

        return view('terranova.estadisticas.detalle')
            ->with('residencial', $residencial)
            ->with('stats', $stats)
            ->with('hoy', $hoy)
            ->with('atrasados', $atrasados)
            ->with('total_ventas', $total_ventas)
            ->with('chart_data', $chart_data);
    }

    private function getResidencialMonthlyStats($id_residencial)
    {
        return collect(DB::select("
            WITH ventas_residencial AS (
                SELECT DISTINCT V.ID, V.CUOTA_MENSUAL, V.TOTAL_PAGAR
                FROM VENTAS V
                JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
                JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
                JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
                WHERE BR.ID_RESIDENCIAL = :id_residencial
                  AND V.DELETED_AT IS NULL
            ),
            cobros_mes AS (
                SELECT FC.ID_VENTA, FC.CANTIDAD_PAGO, FC.FECHA_PAGO
                FROM FECHAS_COBROS FC
                JOIN ventas_residencial VR ON FC.ID_VENTA = VR.ID
                WHERE TO_CHAR(FC.FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY')
            ),
            esperado AS (
                SELECT COALESCE(SUM(VR.CUOTA_MENSUAL), 0) AS TOTAL_ESPERADO
                FROM cobros_mes CM
                JOIN ventas_residencial VR ON CM.ID_VENTA = VR.ID
            ),
            cobrado AS (
                SELECT COALESCE(SUM(CANTIDAD_PAGO), 0) AS TOTAL_COBRADO
                FROM cobros_mes
                WHERE FECHA_PAGO IS NOT NULL
            )
            SELECT
                TO_CHAR(NOW(), 'Month') AS MES_ACTUAL,
                esperado.TOTAL_ESPERADO,
                cobrado.TOTAL_COBRADO,
                (esperado.TOTAL_ESPERADO - cobrado.TOTAL_COBRADO) AS RESTANTE,
                CASE
                    WHEN esperado.TOTAL_ESPERADO > 0
                    THEN ROUND((cobrado.TOTAL_COBRADO * 100 / esperado.TOTAL_ESPERADO), 1)
                    ELSE 0
                END AS PORCENTAJE
            FROM esperado, cobrado
        ", ['id_residencial' => $id_residencial]))->first();
    }

    private function getResidencialTodayStats($id_residencial)
    {
        return collect(DB::select("
            SELECT
                COALESCE(COUNT(FC.ID), 0) AS TOTAL_COBROS,
                COALESCE(SUM(FC.CANTIDAD_PAGO), 0) AS TOTAL_COBRADO_HOY
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID AND V.DELETED_AT IS NULL
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            WHERE FC.FECHA_PAGO IS NOT NULL
              AND DATE(FC.FECHA_PAGO) = CURRENT_DATE
              AND BR.ID_RESIDENCIAL = :id_residencial
        ", ['id_residencial' => $id_residencial]))->first();
    }

    private function getResidencialOverdueStats($id_residencial)
    {
        return collect(DB::select("
            SELECT
                COALESCE(COUNT(FC.ID), 0) AS TOTAL_ATRASADOS,
                COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_MOROSO
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID AND V.DELETED_AT IS NULL
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            WHERE FC.FECHA_COBRO < CURRENT_DATE
              AND FC.FECHA_PAGO IS NULL
              AND BR.ID_RESIDENCIAL = :id_residencial
        ", ['id_residencial' => $id_residencial]))->first();
    }

    private function getResidencialVentas($id_residencial)
    {
        return DB::select("
            SELECT
                V.ID,
                V.TOTAL_CONTADO,
                V.PRIMA,
                V.CUOTA_MENSUAL,
                V.TOTAL_PAGAR,
                V.CUOTAS,
                V.DIA_COBRO_MES,
                TO_CHAR(V.FECHA_VENTA, 'DD/MM/YYYY') AS FECHA_VENTA_FMT,
                TP.NOMBRE AS TIPO_PAGO,
                EV.NOMBRE AS ESTADO,
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) AS CLIENTE,
                C.IDENTIDAD,
                C.CONTACTO_TELEFONICO,
                CASE
                    WHEN EV.NOMBRE = 'Pagado' THEN V.TOTAL_PAGAR
                    ELSE COALESCE((SELECT SUM(FC2.CANTIDAD_PAGO) FROM FECHAS_COBROS FC2 WHERE FC2.ID_VENTA = V.ID AND FC2.FECHA_PAGO IS NOT NULL), 0)
                END AS TOTAL_COBRADO,
                CASE
                    WHEN EV.NOMBRE = 'Pagado' THEN 100.0
                    WHEN V.TOTAL_PAGAR > 0 THEN ROUND((COALESCE((SELECT SUM(FC2.CANTIDAD_PAGO) FROM FECHAS_COBROS FC2 WHERE FC2.ID_VENTA = V.ID AND FC2.FECHA_PAGO IS NOT NULL), 0) * 100 / V.TOTAL_PAGAR), 1)
                    ELSE 0
                END AS PORCENTAJE,
                (SELECT STRING_AGG(L2.NOMBRE, ', ') FROM LOTES_VENDIDOS LV2 JOIN LOTES L2 ON LV2.ID_LOTE = L2.ID WHERE LV2.ID_VENTA = V.ID AND LV2.DELETED_AT IS NULL) AS LOTES
            FROM VENTAS V
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID AND C.DELETED_AT IS NULL
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            WHERE BR.ID_RESIDENCIAL = :id_residencial
              AND V.DELETED_AT IS NULL
            ORDER BY V.FECHA_VENTA DESC
        ", ['id_residencial' => $id_residencial]);
    }

    private function getResidencialChartData($id_residencial)
    {
        return DB::select("
            SELECT
                TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM') AS MES,
                TO_CHAR(FC.FECHA_COBRO, 'Mon') AS MES_LABEL,
                COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_ESPERADO,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NOT NULL THEN FC.CANTIDAD_PAGO ELSE 0 END), 0) AS TOTAL_COBRADO
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID AND V.DELETED_AT IS NULL
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            WHERE FC.FECHA_COBRO >= NOW() - INTERVAL '12 months'
              AND BR.ID_RESIDENCIAL = :id_residencial
            GROUP BY TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM'), TO_CHAR(FC.FECHA_COBRO, 'Mon')
            ORDER BY MES
        ", ['id_residencial' => $id_residencial]);
    }

    public function datos_ventas_residencial(Request $request, $id_residencial)
    {
        $id_user = Auth::id();

        $pertenece = DB::select("SELECT 1 FROM RESIDENCIALES WHERE ID = :id AND ID_USER = :user AND DELETED_AT IS NULL", [
            'id' => $id_residencial, 'user' => $id_user
        ]);
        if (empty($pertenece)) {
            return response()->json(['error' => 'Residencial no encontrado'], 404);
        }

        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');

        $subwhere = "BR.ID_RESIDENCIAL = :id_residencial AND V.DELETED_AT IS NULL";
        $params = ['id_residencial' => $id_residencial];

        if (!empty($search)) {
            $subwhere .= " AND (
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) ILIKE :search
                OR C.IDENTIDAD ILIKE :search2
                OR TP.NOMBRE ILIKE :search3
                OR EV.NOMBRE ILIKE :search4
            )";
            $params['search'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
            $params['search3'] = '%' . $search . '%';
            $params['search4'] = '%' . $search . '%';
        }

        $total = DB::select("
            SELECT COUNT(DISTINCT V.ID) AS TOTAL
            FROM VENTAS V
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            WHERE BR.ID_RESIDENCIAL = :id_residencial AND V.DELETED_AT IS NULL
        ", ['id_residencial' => $id_residencial])[0]->total;

        $filtered = DB::select("
            SELECT COUNT(DISTINCT V.ID) AS TOTAL
            FROM VENTAS V
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID AND C.DELETED_AT IS NULL
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            WHERE {$subwhere}
        ", $params)[0]->total;

        $orderColIdx = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderCols = [
            'V.ID',
            'TRIM(COALESCE(TRIM(C.PRIMER_NOMBRE) || \' \', \'\') || COALESCE(TRIM(C.SEGUNDO_NOMBRE) || \' \', \'\') || COALESCE(TRIM(C.PRIMER_APELLIDO) || \' \', \'\') || COALESCE(TRIM(C.SEGUNDO_APELLIDO) || \' \', \'\'))',
            'C.IDENTIDAD',
            '',
            'TP.NOMBRE',
            'EV.NOMBRE',
            'V.TOTAL_PAGAR',
        ];
        $orderCol = $orderCols[$orderColIdx] ?? 'V.ID';
        $orderDirSql = strtoupper($orderDir) === 'DESC' ? 'DESC NULLS LAST' : 'ASC NULLS LAST';

        $data = DB::select("
            SELECT DISTINCT V.ID,
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) AS CLIENTE,
                C.IDENTIDAD,
                C.CONTACTO_TELEFONICO,
                TP.NOMBRE AS TIPO_PAGO,
                EV.NOMBRE AS ESTADO,
                V.TOTAL_PAGAR,
                V.TOTAL_CONTADO,
                V.PRIMA,
                V.CUOTA_MENSUAL,
                V.CUOTAS,
                V.DIA_COBRO_MES,
                TO_CHAR(V.FECHA_VENTA, 'DD/MM/YYYY') AS FECHA_VENTA_FMT,
                CASE
                    WHEN EV.NOMBRE = 'Pagado' THEN V.TOTAL_PAGAR
                    ELSE COALESCE((SELECT SUM(FC2.CANTIDAD_PAGO) FROM FECHAS_COBROS FC2 WHERE FC2.ID_VENTA = V.ID AND FC2.FECHA_PAGO IS NOT NULL), 0)
                END AS TOTAL_COBRADO,
                CASE
                    WHEN EV.NOMBRE = 'Pagado' THEN 100.0
                    WHEN V.TOTAL_PAGAR > 0 THEN ROUND((COALESCE((SELECT SUM(FC2.CANTIDAD_PAGO) FROM FECHAS_COBROS FC2 WHERE FC2.ID_VENTA = V.ID AND FC2.FECHA_PAGO IS NOT NULL), 0) * 100 / V.TOTAL_PAGAR), 1)
                    ELSE 0
                END AS PORCENTAJE,
                (SELECT STRING_AGG(L2.NOMBRE, ', ') FROM LOTES_VENDIDOS LV2 JOIN LOTES L2 ON LV2.ID_LOTE = L2.ID WHERE LV2.ID_VENTA = V.ID AND LV2.DELETED_AT IS NULL) AS LOTES
            FROM VENTAS V
            JOIN LOTES_VENDIDOS LV ON V.ID = LV.ID_VENTA AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID AND L.DELETED_AT IS NULL
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID AND BR.DELETED_AT IS NULL
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID AND C.DELETED_AT IS NULL
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            WHERE {$subwhere}
            ORDER BY {$orderCol} {$orderDirSql}
            LIMIT {$length} OFFSET {$start}
        ", $params);

        $results = array_map(function($r) {
            $badgeBg = $r->estado == 'Pagado' ? 'success' : ($r->estado == 'Activo' ? 'primary' : 'secondary');
            $badgeTipo = $r->tipo_pago == 'Contado' ? 'success' : 'info';
            $pct = (float) $r->porcentaje;
            $color = $pct >= 75 ? '#05a34a' : ($pct >= 40 ? '#d4a017' : '#dc3545');
            return [
                'id' => $r->id,
                'cliente' => $r->cliente,
                'identidad' => $r->identidad,
                'lotes' => $r->lotes,
                'tipo_pago' => $r->tipo_pago,
                'estado' => $r->estado,
                'total_pagar' => number_format($r->total_pagar, 2),
                'total_cobrado' => number_format($r->total_cobrado, 2),
                'porcentaje' => $pct,
                'badge_tipo' => $badgeTipo,
                'badge_estado' => $badgeBg,
                'color_pct' => $color,
                'detalle_url' => url('/ventas/detalle/' . $r->id),
            ];
        }, $data);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data' => $results,
        ]);
    }
}
