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

        // Estadísticas del mes actual
        $stats = collect(DB::select("
            WITH VENTAS_USER AS (
                SELECT ID, CUOTA_MENSUAL FROM VENTAS WHERE DELETED_AT IS NULL AND ID_CLIENTE IN (
                    SELECT ID FROM CLIENTES WHERE ID_USER = :id_user
                )
            ),
            TOTAL AS (
                SELECT COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_COBRAR
                FROM VENTAS_USER V
                JOIN FECHAS_COBROS FC ON V.ID = FC.ID_VENTA
                WHERE TO_CHAR(FC.FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY')
            ),
            PAGADO AS (
                SELECT COALESCE(SUM(FC.CANTIDAD_PAGO), 0) AS TOTAL_PAGADO
                FROM FECHAS_COBROS FC
                JOIN VENTAS_USER V ON FC.ID_VENTA = V.ID
                WHERE TO_CHAR(FC.FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY')
                  AND FC.FECHA_PAGO IS NOT NULL
            )
            SELECT 
                TO_CHAR(NOW(), 'Month') AS MES_ACTUAL,
                TOTAL_COBRAR,
                TOTAL_PAGADO,
                (TOTAL_COBRAR - TOTAL_PAGADO) AS RESTANTE,
                CASE WHEN TOTAL_COBRAR > 0 THEN ROUND((TOTAL_PAGADO * 100 / TOTAL_COBRAR), 1) ELSE 0 END AS PORCENTAJE
            FROM TOTAL, PAGADO
        ", ['id_user' => $id_user]))->first();

        // Conteos generales (filtrados por usuario)
        $lotes_disponibles = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM LOTES WHERE ID_CLIENTE_RESERVAR IS NULL AND DELETED_AT IS NULL"))->first();
        $clientes_totales = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM CLIENTES WHERE DELETED_AT IS NULL AND ID_USER = :id_user", ['id_user' => $id_user]))->first();
        $ventas_activas = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM VENTAS V
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            WHERE EV.NOMBRE = 'Activo' AND V.DELETED_AT IS NULL AND V.ID_CLIENTE IN (
                SELECT ID FROM CLIENTES WHERE ID_USER = :id_user
            )", ['id_user' => $id_user]))->first();

        // Datos para gráfica lineal (últimos 12 meses)
        $chart_data = DB::select("
            SELECT
                TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM') AS MES,
                TO_CHAR(FC.FECHA_COBRO, 'Mon') AS MES_LABEL,
                SUM(FC.CANTIDAD_PAGO) AS TOTAL_COBRAR,
                COALESCE(SUM(CASE WHEN FC.FECHA_PAGO IS NOT NULL THEN FC.CANTIDAD_PAGO ELSE 0 END), 0) AS TOTAL_PAGADO
            FROM FECHAS_COBROS FC
            JOIN VENTAS V ON FC.ID_VENTA = V.ID
            WHERE FC.FECHA_COBRO >= NOW() - INTERVAL '12 months'
              AND V.DELETED_AT IS NULL
              AND V.ID_CLIENTE IN (SELECT ID FROM CLIENTES WHERE ID_USER = :id_user)
            GROUP BY TO_CHAR(FC.FECHA_COBRO, 'YYYY-MM'), TO_CHAR(FC.FECHA_COBRO, 'Mon')
            ORDER BY MES
        ", ['id_user' => $id_user]);

        return view('dashboard')
        ->with('stats', $stats)
        ->with('lotes_disponibles', $lotes_disponibles)
        ->with('clientes_totales', $clientes_totales)
        ->with('ventas_activas', $ventas_activas)
        ->with('chart_data', $chart_data);
    }
}
