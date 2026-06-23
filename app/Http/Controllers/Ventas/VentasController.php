<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
Use Session;
use Exception;
use Illuminate\Support\Facades\Auth;

class VentasController extends Controller
{
    public function ver_ventas()
    {
        $ventas_pendientes = DB::select("SELECT 
            V.ID,
            V.FECHA_VENTA,
            TRIM(
                COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
            ) AS CLIENTE,
            V.TIPO_PAGO,
            V.ESTADO,
            V.TOTAL_PAGAR
        FROM 
            VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
        WHERE 
            V.DELETED_AT IS NULL
            AND V.ESTADO = 'Pendiente'
        ORDER BY 
            V.CREATED_AT DESC");

        $ventas_pagadas = DB::select("SELECT 
            V.ID,
            V.FECHA_VENTA,
            TRIM(
                COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
            ) AS CLIENTE,
            V.TIPO_PAGO,
            V.ESTADO,
            V.TOTAL_PAGAR
        FROM 
            VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
        WHERE 
            V.DELETED_AT IS NULL
            AND V.ESTADO = 'Pagado'
        ORDER BY 
            V.CREATED_AT DESC");

        return view('terranova.ventas.ventas')
        ->with('ventas_pendientes', $ventas_pendientes)
        ->with('ventas_pagadas', $ventas_pagadas);
    }

    public function ver_vender()
    {
        $clientes = DB::select("SELECT ID, IDENTIDAD, IMAGEN, TRIM(PRIMER_NOMBRE || ' ' || COALESCE(SEGUNDO_NOMBRE, '') || ' ' || PRIMER_APELLIDO || ' ' || COALESCE(SEGUNDO_APELLIDO, '')) AS NOMBRE FROM CLIENTES WHERE DELETED_AT IS NULL ORDER BY PRIMER_NOMBRE ASC");

        $lotes_disponibles = DB::select("SELECT 
            L.ID,
            L.NOMBRE AS LOTE,
            B.NOMBRE AS BLOQUE,
            R.NOMBRE AS RESIDENCIAL,
            L.PRECIO,
            L.ANIOS_FINANCIAMIENTO,
            L.AREA
        FROM 
            LOTES L
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
        WHERE 
            L.DELETED_AT IS NULL
            AND L.ID_CLIENTE_RESERVAR IS NULL
            AND LV.ID_LOTE IS NULL
        ORDER BY 
            R.NOMBRE, B.NOMBRE, L.ID");

        return view('terranova.ventas.vender')
        ->with('clientes', $clientes)
        ->with('lotes_disponibles', $lotes_disponibles);
    }

    public function guardar_venta(Request $request)
    {
        DB::beginTransaction();
        try {
            $id_cliente = $request->id_cliente;
            $tipo_pago = $request->tipo_pago;
            $fecha_venta = $request->fecha_venta;
            $total_contado = $request->total_contado;
            $anios_financiamiento = $request->anios_financiamiento;
            $tasa_interes = $request->tasa_interes;
            $prima = $request->prima;
            $cuotas = $request->cuotas;
            $total_intereses = $request->total_intereses;
            $total_pagar = $request->total_pagar;
            $cuota_mensual = $request->cuota_mensual;
            $dia_cobro_mes = $request->dia_cobro_mes;
            $lotes_seleccionados = $request->lotes; // Array de IDs

            if (empty($lotes_seleccionados)) {
                throw new Exception("Debe seleccionar al menos un lote.");
            }

            $estado = ($tipo_pago == 'Contado') ? 'Pagado' : 'Pendiente';

            $venta = collect(DB::select("INSERT INTO PUBLIC.VENTAS (
                ID_CLIENTE, TIPO_PAGO, ESTADO, TOTAL_CONTADO, ANIOS_FINANCIAMIENTO,
                TASA_INTERES, PRIMA, CUOTAS, TOTAL_INTERESES, TOTAL_PAGAR,
                CUOTA_MENSUAL, DIA_COBRO_MES, FECHA_VENTA
            ) VALUES (
                :id_cliente, :tipo_pago, :estado, :total_contado, :anios_financiamiento,
                :tasa_interes, :prima, :cuotas, :total_intereses, :total_pagar,
                :cuota_mensual, :dia_cobro_mes, :fecha_venta
            ) RETURNING ID", [
                'id_cliente' => $id_cliente,
                'tipo_pago' => $tipo_pago,
                'estado' => $estado,
                'total_contado' => $total_contado,
                'anios_financiamiento' => $anios_financiamiento,
                'tasa_interes' => $tasa_interes,
                'prima' => $prima,
                'cuotas' => $cuotas,
                'total_intereses' => $total_intereses,
                'total_pagar' => $total_pagar,
                'cuota_mensual' => $cuota_mensual,
                'dia_cobro_mes' => $dia_cobro_mes,
                'fecha_venta' => $fecha_venta
            ]))->first();

            $id_venta = $venta->id;

            foreach ($lotes_seleccionados as $id_lote) {
                // Verificar disponibilidad de nuevo
                $vendido = collect(DB::select("SELECT ID FROM LOTES_VENDIDOS WHERE ID_LOTE = :id_lote", ['id_lote' => $id_lote]))->first();
                if ($vendido) {
                    throw new Exception("Uno de los lotes seleccionados ya ha sido vendido.");
                }

                DB::select("INSERT INTO LOTES_VENDIDOS (ID_LOTE, ID_VENTA) VALUES (:id_lote, :id_venta)", [
                    'id_lote' => $id_lote,
                    'id_venta' => $id_venta
                ]);
            }

            if ($tipo_pago == 'Financiado') {
                $fecha_base = new \DateTime($fecha_venta);
                $fecha_base->setDate($fecha_base->format('Y'), $fecha_base->format('m'), $dia_cobro_mes);
                
                for ($i = 1; $i <= $cuotas; $i++) {
                    $fecha_pago = clone $fecha_base;
                    $fecha_pago->modify("+$i month");
                    
                    DB::select("INSERT INTO FECHAS_COBROS (ID_VENTA, FECHA_COBRO, ESTADO) VALUES (:id_venta, :fecha_cobro, 'Pendiente')", [
                        'id_venta' => $id_venta,
                        'fecha_cobro' => $fecha_pago->format('Y-m-d')
                    ]);
                }
            }

            DB::commit();
            return response()->json(['msgSuccess' => 'Venta registrada con éxito.']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['msgError' => $e->getMessage()]);
        }
    }

    public function ver_detalle_venta($id)
    {
        $venta = collect(DB::select("SELECT 
            V.*,
            TRIM(
                COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
            ) AS CLIENTE_NOMBRE,
            C.IMAGEN AS CLIENTE_IMAGEN
        FROM 
            VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
        WHERE 
            V.ID = :id", ['id' => $id]))->first();

        $cobros = DB::select("SELECT * FROM FECHAS_COBROS WHERE ID_VENTA = :id ORDER BY FECHA_COBRO ASC", ['id' => $id]);

        $lotes = DB::select("SELECT 
            L.*,
            B.NOMBRE AS BLOQUE,
            R.NOMBRE AS RESIDENCIAL
        FROM 
            LOTES_VENDIDOS LV
            JOIN LOTES L ON LV.ID_LOTE = L.ID
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
        WHERE 
            LV.ID_VENTA = :id", ['id' => $id]);

        return view('terranova.ventas.detalle')
        ->with('venta', $venta)
        ->with('cobros', $cobros)
        ->with('lotes', $lotes);
    }

    public function pagar_cuota(Request $request)
    {
        try {
            $id = $request->id;
            $cuota = collect(DB::select("SELECT * FROM FECHAS_COBROS WHERE ID = :id", ['id' => $id]))->first();
            $venta = collect(DB::select("SELECT CUOTA_MENSUAL FROM VENTAS WHERE ID = :id_venta", ['id' => $cuota->id_venta]))->first();

            DB::select("UPDATE FECHAS_COBROS SET 
                ESTADO = 'Pagado',
                FECHA_PAGO = NOW(),
                CANTIDAD_PAGO = :monto,
                UPDATED_AT = NOW()
            WHERE ID = :id", [
                'id' => $id,
                'monto' => $venta->cuota_mensual
            ]);

            $restantes = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM FECHAS_COBROS WHERE ID_VENTA = :id_venta AND ESTADO != 'Pagado'", ['id_venta' => $cuota->id_venta]))->first();
            
            if ($restantes->total == 0) {
                DB::select("UPDATE VENTAS SET ESTADO = 'Pagado', UPDATED_AT = NOW() WHERE ID = :id_venta", ['id_venta' => $cuota->id_venta]);
            }

            return response()->json(['msgSuccess' => 'Cuota pagada correctamente.']);
        } catch (Exception $e) {
            return response()->json(['msgError' => $e->getMessage()]);
        }
    }
}
