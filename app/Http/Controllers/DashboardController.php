<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Actualizar estados de cobros atrasados
        DB::select("UPDATE FECHAS_COBROS SET ESTADO = 'Atrasado' 
            WHERE ESTADO != 'Pagado' AND FECHA_COBRO < CURRENT_DATE");
        
        DB::select("UPDATE FECHAS_COBROS SET ESTADO = 'Dia de cobro' 
            WHERE ESTADO != 'Pagado' AND FECHA_COBRO = CURRENT_DATE");

        // Estadísticas del mes actual
        $stats = collect(DB::select("
            WITH TOTAL AS (
                SELECT COALESCE(SUM(V.CUOTA_MENSUAL), 0) AS TOTAL_COBRAR
                FROM VENTAS V
                JOIN FECHAS_COBROS FC ON V.ID = FC.ID_VENTA
                WHERE TO_CHAR(FC.FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY')
            ), PAGADO AS (
                SELECT COALESCE(SUM(CANTIDAD_PAGO), 0) AS TOTAL_PAGADO
                FROM FECHAS_COBROS
                WHERE TO_CHAR(FECHA_COBRO, 'MM-YYYY') = TO_CHAR(NOW(), 'MM-YYYY') AND ESTADO = 'Pagado'
            )
            SELECT 
                TO_CHAR(NOW(), 'Month') AS MES_ACTUAL,
                TOTAL_COBRAR,
                TOTAL_PAGADO,
                (TOTAL_COBRAR - TOTAL_PAGADO) AS RESTANTE,
                CASE WHEN TOTAL_COBRAR > 0 THEN ROUND((TOTAL_PAGADO * 100 / TOTAL_COBRAR), 1) ELSE 0 END AS PORCENTAJE
            FROM TOTAL, PAGADO
        "))->first();

        // Conteos generales
        $lotes_disponibles = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM LOTES WHERE ID_CLIENTE_RESERVAR IS NULL AND DELETED_AT IS NULL"))->first();
        $clientes_totales = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM CLIENTES WHERE DELETED_AT IS NULL"))->first();
        $ventas_activas = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM VENTAS WHERE ESTADO = 'Pendiente' AND DELETED_AT IS NULL"))->first();

        return view('dashboard')
        ->with('stats', $stats)
        ->with('lotes_disponibles', $lotes_disponibles)
        ->with('clientes_totales', $clientes_totales)
        ->with('ventas_activas', $ventas_activas);
    }
}
