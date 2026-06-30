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

        return view('dashboard')
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
}
