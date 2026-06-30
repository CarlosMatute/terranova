<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $id_user = Auth::id();

        $stats = $this->getMonthlyStats($id_user);
        $hoy = $this->getTodayStats($id_user);
        $atrasados = $this->getOverdueStats($id_user);
        $proximos = $this->getUpcomingPayments($id_user);
        $top_morosos = $this->getTopMorosos($id_user, 5);
        $chart_data = $this->getChartData($id_user);
        $conteos = $this->getConteos($id_user);

        $calendario = $this->getCalendario($id_user, date('n'), date('Y'));

        return view('dashboard')->with('calendario', $calendario)
            ->with('stats', $stats)
            ->with('hoy', $hoy)
            ->with('atrasados', $atrasados)
            ->with('proximos', $proximos)
            ->with('top_morosos', $top_morosos)
            ->with('chart_data', $chart_data)
            ->with('conteos', $conteos);
    }

    private function getMonthlyStats($id_user)
    {
        return collect(DB::select("
            WITH ventas_user AS (
                SELECT V.ID, V.CUOTA_MENSUAL 
                FROM VENTAS V
                JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
                WHERE V.DELETED_AT IS NULL AND C.ID_USER = :id_user
            ),
            cobros_mes AS (
                SELECT FC.ID_VENTA, FC.CANTIDAD_PAGO, FC.FECHA_PAGO
                FROM FECHAS_COBROS FC
                JOIN ventas_user VU ON FC.ID_VENTA = VU.ID
                WHERE TO_CHAR(FC.FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY')
            ),
            esperado AS (
                SELECT COALESCE(SUM(VU.CUOTA_MENSUAL), 0) AS TOTAL_ESPERADO
                FROM cobros_mes CM
                JOIN ventas_user VU ON CM.ID_VENTA = VU.ID
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
        ", ['id_user' => $id_user]))->first();
    }

    private function getTodayStats($id_user)
    {
        return collect(DB::select("
            SELECT 
                COALESCE(COUNT(FC.ID), 0) AS TOTAL_COBROS,
                COALESCE(SUM(FC.CANTIDAD_PAGO), 0) AS TOTAL_COBRADO_HOY
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_PAGO IS NOT NULL 
              AND DATE(FC.FECHA_PAGO) = CURRENT_DATE
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
        ", ['id_user' => $id_user]))->first();
    }

    private function getOverdueStats($id_user)
    {
        return collect(DB::select("
            SELECT 
                COALESCE(COUNT(FC.ID), 0) AS TOTAL_ATRASADOS,
                COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_MOROSO
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_COBRO < CURRENT_DATE
              AND FC.FECHA_PAGO IS NULL
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
        ", ['id_user' => $id_user]))->first();
    }

    private function getUpcomingPayments($id_user)
    {
        return DB::select("
            SELECT 
                FC.ID,
                FC.FECHA_COBRO,
                TO_CHAR(FC.FECHA_COBRO, 'DD/MM/YYYY') AS FECHA_COBRO_FMT,
                V.CUOTA_MENSUAL,
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) AS CLIENTE
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_COBRO BETWEEN CURRENT_DATE AND (CURRENT_DATE + INTERVAL '7 days')
              AND FC.FECHA_PAGO IS NULL
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
            ORDER BY FC.FECHA_COBRO ASC
            LIMIT 10
        ", ['id_user' => $id_user]);
    }

    private function getTopMorosos($id_user, $limit = 5)
    {
        return DB::select("
            SELECT 
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) AS CLIENTE,
                COUNT(FC.ID) AS CUOTAS_ATRASADAS,
                COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_ADEUDADO,
                MIN(FC.FECHA_COBRO) AS FECHA_MAS_ANTIGUA
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_COBRO < CURRENT_DATE
              AND FC.FECHA_PAGO IS NULL
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
            GROUP BY C.ID, C.PRIMER_NOMBRE, C.SEGUNDO_NOMBRE, C.PRIMER_APELLIDO, C.SEGUNDO_APELLIDO
            ORDER BY TOTAL_ADEUDADO DESC, CUOTAS_ATRASADAS DESC
            LIMIT :limite
        ", ['id_user' => $id_user, 'limite' => $limit]);
    }

    private function getChartData($id_user)
    {
        return DB::select("
            SELECT
                TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM') AS MES,
                TO_CHAR(FC.FECHA_COBRO, 'Mon') AS MES_LABEL,
                COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_ESPERADO,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NOT NULL THEN FC.CANTIDAD_PAGO ELSE 0 END), 0) AS TOTAL_COBRADO
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_COBRO >= NOW() - INTERVAL '12 months'
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
            GROUP BY TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM'), TO_CHAR(FC.FECHA_COBRO, 'Mon')
            ORDER BY MES
        ", ['id_user' => $id_user]);
    }

    private function getConteos($id_user)
    {
        $lotes_disponibles = collect(DB::select("
            SELECT COUNT(*) AS TOTAL FROM LOTES 
            WHERE ID_CLIENTE_RESERVAR IS NULL AND DELETED_AT IS NULL
        "))->first();

        $clientes_totales = collect(DB::select("
            SELECT COUNT(*) AS TOTAL FROM CLIENTES 
            WHERE DELETED_AT IS NULL AND ID_USER = :id_user
        ", ['id_user' => $id_user]))->first();

        $ventas_activas = collect(DB::select("
            SELECT COUNT(*) AS TOTAL FROM VENTAS V
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE EV.NOMBRE = 'Activo' 
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
        ", ['id_user' => $id_user]))->first();

        $ventas_completadas = collect(DB::select("
            SELECT COUNT(*) AS TOTAL FROM VENTAS V
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE EV.NOMBRE = 'Pagado' 
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
        ", ['id_user' => $id_user]))->first();

        return (object)[
            'lotes_disponibles' => $lotes_disponibles->total,
            'clientes_totales' => $clientes_totales->total,
            'ventas_activas' => $ventas_activas->total,
            'ventas_completadas' => $ventas_completadas->total,
        ];
    }

    private function getCalendario($id_user, $month, $year)
    {
        $start = "{$year}-{$month}-01";
        $end = date('Y-m-t', strtotime($start));

        $rows = DB::select("
            SELECT
                FC.FECHA_COBRO,
                COUNT(*) AS TOTAL_CUOTAS,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NOT NULL THEN 1 ELSE 0 END), 0) AS COBRADAS,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NULL AND FC.FECHA_COBRO < CURRENT_DATE THEN 1 ELSE 0 END), 0) AS ATRASADAS,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NULL AND FC.FECHA_COBRO >= CURRENT_DATE THEN 1 ELSE 0 END), 0) AS PENDIENTES,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NOT NULL THEN FC.CANTIDAD_PAGO ELSE 0 END), 0) AS TOTAL_COBRADO,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NULL THEN V.CUOTA_MENSUAL ELSE 0 END), 0) AS TOTAL_PENDIENTE
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE FC.FECHA_COBRO BETWEEN :start AND :end
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
            GROUP BY FC.FECHA_COBRO
            ORDER BY FC.FECHA_COBRO
        ", ['start' => $start, 'end' => $end, 'id_user' => $id_user]);

        $dias = [];
        $numDays = date('t', strtotime($start));
        for ($d = 1; $d <= $numDays; $d++) {
            $date = "{$year}-{$month}-" . str_pad($d, 2, '0', STR_PAD_LEFT);
            $dias[$date] = [
                'total' => 0, 'cobradas' => 0, 'atrasadas' => 0,
                'pendientes' => 0, 'total_cobrado' => 0, 'total_pendiente' => 0
            ];
        }
        foreach ($rows as $r) {
            $dias[$r->fecha_cobro] = [
                'total' => (int)$r->total_cuotas,
                'cobradas' => (int)$r->cobradas,
                'atrasadas' => (int)$r->atrasadas,
                'pendientes' => (int)$r->pendientes,
                'total_cobrado' => (float)$r->total_cobrado,
                'total_pendiente' => (float)$r->total_pendiente,
            ];
        }

        return (object)[
            'year' => (int)$year,
            'month' => (int)$month,
            'month_name' => date('F', strtotime($start)),
            'num_days' => $numDays,
            'start_day' => (int)date('N', strtotime($start)),
            'dias' => $dias,
        ];
    }

    public function calendario_ajax(Request $request)
    {
        $id_user = Auth::id();
        $month = (int)$request->input('month', date('n'));
        $year = (int)$request->input('year', date('Y'));
        $data = $this->getCalendario($id_user, $month, $year);
        return response()->json($data);
    }

    public function detalle_dia_ajax(Request $request)
    {
        $id_user = Auth::id();
        $date = $request->input('date');

        $rows = DB::select("
            SELECT
                V.ID AS ID_VENTA,
                V.CUOTA_MENSUAL,
                V.TOTAL_PAGAR,
                FC.ID AS ID_CUOTA,
                FC.FECHA_COBRO,
                FC.FECHA_PAGO,
                FC.CANTIDAD_PAGO,
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') ||
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') ||
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO), '')
                ) AS CLIENTE,
                C.CONTACTO_TELEFONICO,
                C.CONTACTO_TELEFONICO_2,
                R.NOMBRE AS RESIDENCIAL,
                B.NOMBRE AS BLOQUE,
                L.NOMBRE AS LOTE
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            JOIN LOTES_VENDIDOS LV ON LV.ID_VENTA = V.ID AND LV.DELETED_AT IS NULL
            JOIN LOTES L ON LV.ID_LOTE = L.ID
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            WHERE FC.FECHA_COBRO = :fecha
              AND V.DELETED_AT IS NULL
              AND C.ID_USER = :id_user
            ORDER BY C.PRIMER_NOMBRE
        ", ['fecha' => $date, 'id_user' => $id_user]);

        $ventas = [];
        foreach ($rows as $r) {
            $estado = 'pendiente';
            $badge = 'bg-warning text-dark';
            $label = 'Pendiente';
            if ($r->fecha_pago) {
                $estado = 'cobrada';
                $badge = 'bg-success';
                $label = 'Cobrada';
            } elseif (strtotime($r->fecha_cobro) < strtotime(date('Y-m-d'))) {
                $estado = 'atrasada';
                $badge = 'bg-danger';
                $label = 'Atrasada';
            }

            $cuota = [
                'id' => $r->id_cuota,
                'monto' => (float)$r->cuota_mensual,
                'fecha_cobro' => $r->fecha_cobro,
                'fecha_pago' => $r->fecha_pago,
                'cantidad_pago' => $r->cantidad_pago ? (float)$r->cantidad_pago : null,
                'estado' => $estado,
                'badge' => $badge,
                'estado_label' => $label,
            ];

            $vid = $r->id_venta;
            if (!isset($ventas[$vid])) {
                $ventas[$vid] = [
                    'id_venta' => $vid,
                    'cliente' => $r->cliente,
                    'telefono' => $r->contacto_telefonico,
                    'telefono_2' => $r->contacto_telefonico_2,
                    'cuota_mensual' => (float)$r->cuota_mensual,
                    'total_pagar' => (float)$r->total_pagar,
                    'residencial' => $r->residencial,
                    'bloque' => $r->bloque,
                    'lote' => $r->lote,
                    'cuotas' => [],
                ];
            }
            $ventas[$vid]['cuotas'][] = $cuota;
        }

        return response()->json([
            'date' => $date,
            'total_ventas' => count($ventas),
            'total_cuotas' => count($rows),
            'ventas' => array_values($ventas),
        ]);
    }
}
