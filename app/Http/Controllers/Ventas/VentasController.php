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
    private function toCentavos($valor)
    {
        if ($valor === null || $valor === '' || $valor === false) return 0;
        $parts = explode('.', str_replace(',', '', (string)$valor));
        $entero = $parts[0] ?? '0';
        $decimal = isset($parts[1]) ? str_pad(substr($parts[1], 0, 2), 2, '0') : '00';
        return (int) round((float)$entero * 100 + (int)$decimal);
    }

    private function fromCentavos($centavos)
    {
        $abs = abs(intval($centavos));
        $signo = $centavos < 0 ? '-' : '';
        return sprintf('%s%d.%02d', $signo, intdiv($abs, 100), $abs % 100);
    }

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
            TP.NOMBRE AS TIPO_PAGO,
            EV.NOMBRE AS ESTADO,
            V.TOTAL_PAGAR
        FROM 
            VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
        WHERE 
            V.DELETED_AT IS NULL
            AND EV.NOMBRE = 'Activo'
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
            TP.NOMBRE AS TIPO_PAGO,
            EV.NOMBRE AS ESTADO,
            V.TOTAL_PAGAR
        FROM 
            VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
        WHERE 
            V.DELETED_AT IS NULL
            AND EV.NOMBRE = 'Pagado'
        ORDER BY 
            V.CREATED_AT DESC");

        return view('terranova.ventas.ventas');
    }

    public function datos_ventas(Request $request)
    {
        $id_user = Auth::id();
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');
        $estado = $request->input('estado', 'Activo');

        $estadoPendienteId = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Activo'"))->first()->id;
        $estadoPagadoId = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Pagado'"))->first()->id;
        $estadoId = ($estado == 'Pagado') ? $estadoPagadoId : $estadoPendienteId;

        $where = "V.DELETED_AT IS NULL AND V.ESTADO = :estado AND C.ID_USER = :id_user";
        $params = ['estado' => $estadoId, 'id_user' => $id_user];

        if (!empty($search)) {
            $where .= " AND (
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) ILIKE :search
            )";
            $params['search'] = '%' . $search . '%';
        }

        $total = DB::select("SELECT COUNT(*) AS total FROM VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE V.DELETED_AT IS NULL AND V.ESTADO = :estado AND C.ID_USER = :id_user", 
            ['estado' => $estadoId, 'id_user' => $id_user])[0]->total;

        $filtered = DB::select("SELECT COUNT(*) AS total FROM VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            WHERE {$where}", $params)[0]->total;

        $orderColIdx = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderCols = ['V.ID', 'V.FECHA_VENTA', 'CLIENTE', 'TP.NOMBRE', 'V.TOTAL_PAGAR'];
        $orderCol = $orderCols[$orderColIdx] ?? 'V.CREATED_AT';
        $orderDirSql = strtoupper($orderDir) === 'DESC' ? 'DESC NULLS LAST' : 'ASC NULLS LAST';

        $data = DB::select("
            SELECT V.ID, V.FECHA_VENTA,
                TRIM(
                    COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
                ) AS CLIENTE,
                TP.NOMBRE AS TIPO_PAGO, EV.NOMBRE AS ESTADO, V.TOTAL_PAGAR
            FROM VENTAS V
            JOIN CLIENTES C ON V.ID_CLIENTE = C.ID
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
            WHERE {$where}
            ORDER BY {$orderCol} {$orderDirSql}
            LIMIT {$length} OFFSET {$start}
        ", $params);

        $results = array_map(function($r) {
            $badge = ($r->estado == 'Pagado') ? 'bg-success' : 'bg-warning text-dark';
            return [
                'id' => $r->id,
                'fecha_venta' => $r->fecha_venta,
                'cliente' => $r->cliente,
                'tipo_pago' => $r->tipo_pago,
                'total_pagar' => number_format($r->total_pagar, 2),
                'estado' => $r->estado,
                'badge' => $badge,
                'detalle_url' => url('/ventas/detalle/' . $r->id),
            ];
        }, $data);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data' => $results
        ]);
    }

    public function ver_vender()
    {
        return view('terranova.ventas.vender');
    }

    public function guardar_venta(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|integer|exists:clientes,id',
            'tipo_pago' => 'required|in:Contado,Financiado',
            'fecha_venta' => 'required|date',
            'total_contado' => 'required|numeric|min:0',
            'anios_financiamiento' => 'required|integer|min:0|max:50',
            'tasa_interes' => 'required|numeric|min:0|max:100',
            'prima' => 'required|numeric|min:0',
            'cuotas' => 'required|integer|min:0|max:600',
            'total_intereses' => 'required|numeric|min:0',
            'total_pagar' => 'required|numeric|min:0',
            'cuota_mensual' => 'required|numeric|min:0',
            'dia_cobro_mes' => 'required|integer|min:1|max:28',
            'lotes' => 'required|array|min:1',
            'lotes.*' => 'integer|exists:lotes,id',
        ]);

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
            $lotes_seleccionados = $request->lotes;

            if (empty($lotes_seleccionados)) {
                throw new Exception("Debe seleccionar al menos un lote.");
            }

            $cliente = collect(DB::select("SELECT ID FROM CLIENTES WHERE ID = :id AND ID_USER = :id_user AND DELETED_AT IS NULL", [
                'id' => $id_cliente,
                'id_user' => Auth::id()
            ]))->first();
            if (!$cliente) {
                throw new Exception("El cliente seleccionado no existe o no pertenece a su cuenta.");
            }

            $tipo_pago_id = collect(DB::select("SELECT ID FROM CATALOGO_TIPO_PAGO WHERE NOMBRE = :nombre", ['nombre' => $tipo_pago]))->first()->id;
            $estado_codigo = ($tipo_pago == 'Contado') ? 'Pagado' : 'Activo';
            $estado_id = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = :nombre", ['nombre' => $estado_codigo]))->first()->id;

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
                'tipo_pago' => $tipo_pago_id,
                'estado' => $estado_id,
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
                $lote = collect(DB::select("SELECT ID, ID_CLIENTE_RESERVAR FROM LOTES WHERE ID = :id_lote AND DELETED_AT IS NULL", ['id_lote' => $id_lote]))->first();
                if (!$lote) {
                    throw new Exception("Uno de los lotes seleccionados no existe.");
                }
                if ($lote->id_cliente_reservar) {
                    throw new Exception("Uno de los lotes seleccionados está reservado por otro cliente.");
                }
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
                    
                    DB::select("INSERT INTO FECHAS_COBROS (ID_VENTA, FECHA_COBRO) VALUES (:id_venta, :fecha_cobro)", [
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
            V.ID,
            V.ID_CLIENTE,
            TP.NOMBRE AS TIPO_PAGO,
            EV.NOMBRE AS ESTADO,
            V.TOTAL_CONTADO,
            V.ANIOS_FINANCIAMIENTO,
            V.TASA_INTERES,
            V.PRIMA,
            V.CUOTAS,
            V.TOTAL_INTERESES,
            V.TOTAL_PAGAR,
            V.CUOTA_MENSUAL,
            V.DIA_COBRO_MES,
            V.FECHA_VENTA,
            V.CREATED_AT,
            V.UPDATED_AT,
            V.DELETED_AT,
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
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
        WHERE 
            V.ID = :id", ['id' => $id]))->first();

        $cobros = DB::select("SELECT *,
            CASE 
                WHEN FECHA_PAGO IS NOT NULL THEN 'Pagado'
                WHEN CANTIDAD_PAGO IS NOT NULL THEN 'Cola'
                WHEN FECHA_COBRO < CURRENT_DATE THEN 'Atrasado'
                ELSE 'Pendiente'
            END AS ESTADO
        FROM FECHAS_COBROS WHERE ID_VENTA = :id ORDER BY FECHA_COBRO ASC", ['id' => $id]);

        $lotes = DB::select("SELECT 
            L.*,
            B.NOMBRE AS BLOQUE,
            B.ID AS ID_BLOQUE,
            R.NOMBRE AS RESIDENCIAL,
            R.IMAGEN AS RESIDENCIAL_IMAGEN,
            BR.ID_RESIDENCIAL
        FROM 
            LOTES_VENDIDOS LV
            JOIN LOTES L ON LV.ID_LOTE = L.ID
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
        WHERE 
            LV.ID_VENTA = :id", ['id' => $id]);

        $ultima_cuota_pagada = collect(DB::select("SELECT ID FROM FECHAS_COBROS WHERE ID_VENTA = ? AND CANTIDAD_PAGO IS NOT NULL ORDER BY FECHA_COBRO DESC, ID DESC LIMIT 1", [$id]))->first();

        return view('terranova.ventas.detalle')
        ->with('venta', $venta)
        ->with('cobros', $cobros)
        ->with('lotes', $lotes)
        ->with('ultima_cuota_pagada_id', $ultima_cuota_pagada ? $ultima_cuota_pagada->id : null);
    }

    public function pagar_cuota(Request $request)
    {
        try {
            $id = $request->id;
            $cuota = collect(DB::select("SELECT * FROM FECHAS_COBROS WHERE ID = :id", ['id' => $id]))->first();
            if (!$cuota) throw new Exception("Cuota no encontrada.");
            $idVenta = $cuota->id_venta;
            $venta = collect(DB::select("SELECT CUOTA_MENSUAL FROM VENTAS WHERE ID = ?", [$idVenta]))->first();

            $cuotaMensualCentavos = $this->toCentavos($venta->cuota_mensual);
            $yaPagadoCentavos = $this->toCentavos($cuota->cantidad_pago ?: 0);

            if ($cuota->fecha_pago) {
                $nuevaCantidadCentavos = $yaPagadoCentavos;
            } else {
                $restanteCentavos = $cuotaMensualCentavos - $yaPagadoCentavos;
                $nuevaCantidadCentavos = $yaPagadoCentavos + $restanteCentavos;
            }

            DB::select("UPDATE FECHAS_COBROS SET 
                FECHA_PAGO = NOW(),
                CANTIDAD_PAGO = ?,
                UPDATED_AT = NOW()
            WHERE ID = ?", [$this->fromCentavos($nuevaCantidadCentavos), $id]);

            $restantes = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM FECHAS_COBROS WHERE ID_VENTA = ? AND FECHA_PAGO IS NULL", [$idVenta]))->first();
            
            if ($restantes->total == 0) {
                $pagado_id = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Pagado'"))->first()->id;
                DB::select("UPDATE VENTAS SET ESTADO = ?, UPDATED_AT = NOW() WHERE ID = ?", [$pagado_id, $idVenta]);
            }

            return response()->json(['msgSuccess' => 'Cuota pagada correctamente.']);
        } catch (Exception $e) {
            return response()->json(['msgError' => $e->getMessage()]);
        }
    }

    public function revertir_cuota(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = $request->id;
            $cuota = collect(DB::select("SELECT * FROM FECHAS_COBROS WHERE ID = ?", [$id]))->first();
            if (!$cuota) throw new Exception("Cuota no encontrada.");

            $idVenta = $cuota->id_venta;
            $venta = collect(DB::select("SELECT CUOTA_MENSUAL, ESTADO FROM VENTAS WHERE ID = ?", [$idVenta]))->first();

            if ($cuota->fecha_pago) {
                DB::select("UPDATE FECHAS_COBROS SET FECHA_PAGO = NULL, CANTIDAD_PAGO = NULL, UPDATED_AT = NOW() WHERE ID = ?", [$id]);
            } elseif ($cuota->cantidad_pago) {
                DB::select("UPDATE FECHAS_COBROS SET CANTIDAD_PAGO = NULL, UPDATED_AT = NOW() WHERE ID = ?", [$id]);
            } else {
                throw new Exception("La cuota no tiene un estado válido para revertir.");
            }

            $pagado_id = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Pagado'"))->first()->id;
            if ($venta->estado == $pagado_id) {
                $activo_id = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Activo'"))->first()->id;
                DB::select("UPDATE VENTAS SET ESTADO = ?, UPDATED_AT = NOW() WHERE ID = ?", [$activo_id, $idVenta]);
            }

            DB::commit();
            return response()->json(['msgSuccess' => 'Cobro revertido correctamente.']);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['msgError' => $e->getMessage()]);
        }
    }

    public function abonar(Request $request)
    {
        DB::beginTransaction();
        try {
            $idVenta = $request->id_venta;
            $montoCentavos = $this->toCentavos($request->monto);
            if ($montoCentavos <= 0) throw new Exception("El monto debe ser mayor a cero.");

            $venta = collect(DB::select("SELECT CUOTA_MENSUAL FROM VENTAS WHERE ID = ?", [$idVenta]))->first();
            if (!$venta) throw new Exception("Venta no encontrada.");

            $pendientes = DB::select("SELECT ID, CANTIDAD_PAGO FROM FECHAS_COBROS WHERE ID_VENTA = ? AND FECHA_PAGO IS NULL ORDER BY FECHA_COBRO ASC", [$idVenta]);
            if (empty($pendientes)) throw new Exception("No hay cuotas pendientes.");

            $cuotaMensualCentavos = $this->toCentavos($venta->cuota_mensual);
            $restanteCentavos = $montoCentavos;

            foreach ($pendientes as $cuota) {
                if ($restanteCentavos <= 0) break;

                $yaPagadoCentavos = $this->toCentavos($cuota->cantidad_pago);
                $montoOwedCentavos = $cuotaMensualCentavos - $yaPagadoCentavos;

                if ($restanteCentavos >= $montoOwedCentavos) {
                    $nuevoTotalCentavos = $yaPagadoCentavos + $montoOwedCentavos;
                    DB::select("UPDATE FECHAS_COBROS SET FECHA_PAGO = NOW(), CANTIDAD_PAGO = ?, UPDATED_AT = NOW() WHERE ID = ?", [$this->fromCentavos($nuevoTotalCentavos), $cuota->id]);
                    $restanteCentavos -= $montoOwedCentavos;
                } else {
                    $nuevoPagadoCentavos = $yaPagadoCentavos + $restanteCentavos;
                    DB::select("UPDATE FECHAS_COBROS SET CANTIDAD_PAGO = ?, UPDATED_AT = NOW() WHERE ID = ?", [$this->fromCentavos($nuevoPagadoCentavos), $cuota->id]);
                    $restanteCentavos = 0;
                    break;
                }
            }

            $restantes = collect(DB::select("SELECT COUNT(*) AS TOTAL FROM FECHAS_COBROS WHERE ID_VENTA = ? AND FECHA_PAGO IS NULL", [$idVenta]))->first();
            if ($restantes->total == 0) {
                $pagado_id = collect(DB::select("SELECT ID FROM CATALOGO_ESTADO_VENTA WHERE NOMBRE = 'Pagado'"))->first()->id;
                DB::select("UPDATE VENTAS SET ESTADO = ?, UPDATED_AT = NOW() WHERE ID = ?", [$pagado_id, $idVenta]);
            }

            DB::commit();
            return response()->json(['msgSuccess' => "Abono de L. " . $this->fromCentavos($montoCentavos) . " aplicado correctamente."]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['msgError' => $e->getMessage()]);
        }
    }

    public function datos_lotes_disponibles(Request $request)
    {
        $id_user = Auth::id();
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');

        $where = "L.DELETED_AT IS NULL AND L.ID_CLIENTE_RESERVAR IS NULL AND R.ID_USER = :id_user
                  AND NOT EXISTS (SELECT 1 FROM LOTES_VENDIDOS LV WHERE LV.ID_LOTE = L.ID AND LV.DELETED_AT IS NULL)";
        $params = ['id_user' => $id_user];

        if (!empty($search)) {
            $where .= " AND (L.NOMBRE ILIKE :search1 OR B.NOMBRE ILIKE :search2 OR R.NOMBRE ILIKE :search3 OR CAST(L.LOTE AS TEXT) ILIKE :search4)";
            $term = '%' . $search . '%';
            $params['search1'] = $term;
            $params['search2'] = $term;
            $params['search3'] = $term;
            $params['search4'] = $term;
        }

        $total = DB::select("SELECT COUNT(*) AS TOTAL
            FROM LOTES L
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            WHERE {$where}", $params)[0]->total;

        $orderColIdx = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderCols = ['L.ID', 'R.NOMBRE', 'B.NOMBRE', 'L.NOMBRE', 'L.PRECIO', 'L.AREA'];
        $orderCol = $orderCols[$orderColIdx] ?? 'R.NOMBRE';
        $orderDirSql = strtoupper($orderDir) === 'DESC' ? 'DESC NULLS LAST' : 'ASC NULLS LAST';

        $lotes = DB::select("
            SELECT L.ID, L.NOMBRE AS LOTE, B.NOMBRE AS BLOQUE,
                   R.ID AS ID_RESIDENCIAL, R.IMAGEN, R.NOMBRE AS RESIDENCIAL, L.PRECIO, L.AREA
            FROM LOTES L
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            WHERE {$where}
            ORDER BY {$orderCol} {$orderDirSql}
            LIMIT {$length} OFFSET {$start}
        ", $params);

        $results = array_map(function($l) {
            $src = url('storage/residenciales/res_' . $l->id_residencial . '/' . $l->imagen);
            return [
                'id' => $l->id,
                'residencial' => $l->residencial,
                'id_residencial' => $l->id_residencial,
                'imagen' => $l->imagen,
                'img_src' => $src,
                'bloque' => $l->bloque,
                'lote' => $l->lote,
                'precio' => number_format($l->precio, 2),
                'precio_raw' => (float) $l->precio,
                'area' => $l->area,
            ];
        }, $lotes);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) $total,
            'data' => $results,
        ]);
    }

    public function buscar_lotes(Request $request)
    {
        $id_user = Auth::id();
        $search = $request->input('q', '');
        $page = (int) $request->input('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = "L.DELETED_AT IS NULL AND L.ID_CLIENTE_RESERVAR IS NULL AND R.ID_USER = :id_user
                  AND NOT EXISTS (SELECT 1 FROM LOTES_VENDIDOS LV WHERE LV.ID_LOTE = L.ID AND LV.DELETED_AT IS NULL)";
        $params = ['id_user' => $id_user];

        if (!empty($search)) {
            $where .= " AND (L.NOMBRE ILIKE :search1 OR B.NOMBRE ILIKE :search2 OR R.NOMBRE ILIKE :search3 OR CAST(L.LOTE AS TEXT) ILIKE :search4)";
            $term = '%' . $search . '%';
            $params['search1'] = $term;
            $params['search2'] = $term;
            $params['search3'] = $term;
            $params['search4'] = $term;
        }

        $total = DB::select("
            SELECT COUNT(*) AS TOTAL
            FROM LOTES L
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            WHERE {$where}
        ", $params)[0]->total;

        $lotes = DB::select("
            SELECT L.ID, L.NOMBRE AS LOTE, B.NOMBRE AS BLOQUE,
                   R.ID AS ID_RESIDENCIAL, R.IMAGEN, R.NOMBRE AS RESIDENCIAL, L.PRECIO, L.AREA
            FROM LOTES L
            JOIN BLOQUES_RESIDENCIALES BR ON L.ID_BLOQUE_RESIDENCIAL = BR.ID
            JOIN BLOQUES B ON BR.ID_BLOQUE = B.ID
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            WHERE {$where}
            ORDER BY R.NOMBRE, B.NOMBRE, L.ID
            LIMIT {$limit} OFFSET {$offset}
        ", $params);

        $results = array_map(function($l) {
            return [
                'id' => $l->id,
                'text' => $l->residencial . ' - Bloque ' . $l->bloque . ' - Lote ' . $l->lote . ' (' . number_format($l->precio, 2) . ')',
                'nombre' => $l->lote,
                'bloque' => $l->bloque,
                'residencial' => $l->residencial,
                'id_residencial' => $l->id_residencial,
                'imagen' => $l->imagen,
                'precio' => $l->precio,
            ];
        }, $lotes);

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }
}
