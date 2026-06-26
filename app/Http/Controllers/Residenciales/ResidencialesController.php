<?php

namespace App\Http\Controllers\Residenciales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
Use Session;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ResidencialesController extends Controller
{
    public function ver_residenciales()
    {

        $residenciales = DB::select("SELECT
            R.ID,
            R.NOMBRE,
            R.DESCRIPCION,
            R.IMAGEN,
            COALESCE((SELECT COUNT(*) FROM BLOQUES_RESIDENCIALES WHERE ID_RESIDENCIAL = R.ID AND DELETED_AT IS NULL), 0) AS TOTAL_BLOQUES,
            COALESCE((SELECT COUNT(*) FROM BLOQUES_RESIDENCIALES BR2 JOIN LOTES L ON BR2.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL WHERE BR2.ID_RESIDENCIAL = R.ID AND BR2.DELETED_AT IS NULL), 0) AS TOTAL_LOTES
        FROM
            RESIDENCIALES R
        WHERE
            R.DELETED_AT IS NULL
            AND R.ID_USER = :id_user", ["id_user" => Auth::id()]);

        return view('terranova.residenciales.residenciales')
        ->with('residenciales', $residenciales);
    }

    public function guardar_residencial(Request $request)
    {
        $id = $request->id;
        $nombre = $request->input('nombre');
        $descripcion = $request->input('descripcion');
        $bloques = $request->input('bloques');
        $accion = $request->input('accion');
        $cambiar_imagen = ($request->input('cambiar_imagen') == 'true') ? true : false;
        $archivoSeleccionado = null;
        $residenciales_list = null;
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            if($request->hasFile('archivoSeleccionado')) {
                $archivos = $request->file('archivoSeleccionado');
                $archivoSeleccionado = $archivos->getClientOriginalName();
            }

            if($accion == 1){
                $residencial = collect(\DB::select("INSERT INTO
                    PUBLIC.RESIDENCIALES (NOMBRE, DESCRIPCION, ID_USER, IMAGEN)
                VALUES
                    (:nombre, :descripcion, :id_user, :imagen)
                RETURNING
                    ID;", [
                    "nombre" => $nombre,
                    "descripcion" => $descripcion,
                    "id_user" => Auth::id(),
                    "imagen" => $archivoSeleccionado
                ]))->first();

                $id = $residencial->id;

                DB::select("INSERT INTO
                    PUBLIC.BLOQUES_RESIDENCIALES (ID_BLOQUE, ID_RESIDENCIAL)
                SELECT
                    ID,
                    :id_residencial
                FROM
                    BLOQUES
                WHERE
                    DELETED_AT IS NULL
                    AND ID BETWEEN 1 AND :bloques", [
                    "id_residencial" => $id,
                    "bloques" => $bloques
                ]);

                if($request->hasFile('archivoSeleccionado')) {
                    $archivos->storeAs('public/residenciales/res_' . $id, $archivoSeleccionado);
                }

                $msgSuccess = "Residencial " . $nombre . " guardada exitosamente.";
            } elseif($accion == 2){
                $img_old = collect(\DB::select("SELECT IMAGEN FROM PUBLIC.RESIDENCIALES WHERE ID = :id", ["id" => $id]))->first();

                DB::select("UPDATE
                    PUBLIC.RESIDENCIALES
                SET
                    NOMBRE = :nombre,
                    DESCRIPCION = :descripcion,
                    UPDATED_AT = NOW()
                WHERE
                    ID = :id", [
                    "nombre" => $nombre,
                    "descripcion" => $descripcion,
                    "id" => $id
                ]);

                if($cambiar_imagen) {
                    DB::select("UPDATE
                        PUBLIC.RESIDENCIALES
                    SET
                        IMAGEN = :imagen
                    WHERE
                        ID = :id", [
                        "imagen" => $archivoSeleccionado,
                        "id" => $id
                    ]);

                    Storage::delete('public/residenciales/res_' . $id . '/' . $img_old->imagen);
                    if($request->hasFile('archivoSeleccionado')) {
                        $archivos->storeAs('public/residenciales/res_' . $id, $archivoSeleccionado);
                    }
                }
                $msgSuccess = "Residencial " . $nombre . " actualizada exitosamente.";
            }elseif($accion == 3){
                DB::select("UPDATE
                    PUBLIC.RESIDENCIALES
                SET
                    DELETED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id" => $id
                ]);

                $msgSuccess = "Residencial " . $nombre . " eliminada exitosamente.";
            }else{
                 throw new Exception("Acción no válida.");
            }

            $residenciales_list = collect(\DB::select("SELECT
                R.ID,
                R.NOMBRE,
                R.DESCRIPCION,
                R.IMAGEN,
                COALESCE((SELECT COUNT(*) FROM BLOQUES_RESIDENCIALES WHERE ID_RESIDENCIAL = R.ID AND DELETED_AT IS NULL), 0) AS TOTAL_BLOQUES,
                COALESCE((SELECT COUNT(*) FROM BLOQUES_RESIDENCIALES BR2 JOIN LOTES L ON BR2.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL WHERE BR2.ID_RESIDENCIAL = R.ID AND BR2.DELETED_AT IS NULL), 0) AS TOTAL_LOTES
            FROM
                RESIDENCIALES R
            WHERE
                R.DELETED_AT IS NULL
                AND R.ID = :id", ["id" => $id]))->first();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $msgError = $e->getMessage();
        }

        return response()->json([
            "msgSuccess" => $msgSuccess,
            "msgError" => $msgError,
            "residenciales_list" => $residenciales_list
        ]);
    }

    public function estado_eliminacion($id_residencial)
    {
        $bloques = DB::select("SELECT
            B.NOMBRE AS BLOQUE,
            COUNT(L.ID) AS TOTAL_LOTES,
            COUNT(LV.ID_VENTA) AS VENDIDOS,
            COUNT(L.ID_CLIENTE_RESERVAR) AS APARTADOS
        FROM
            BLOQUES_RESIDENCIALES BR
            JOIN BLOQUES B ON B.ID = BR.ID_BLOQUE
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
        WHERE
            BR.ID_RESIDENCIAL = :id
            AND BR.DELETED_AT IS NULL
        GROUP BY
            B.NOMBRE
        ORDER BY
            B.NOMBRE", ["id" => $id_residencial]);

        $total_vendidos = array_sum(array_column($bloques, 'vendidos'));
        $total_apartados = array_sum(array_column($bloques, 'apartados'));
        $puede_eliminar = ($total_vendidos == 0 && $total_apartados == 0);

        return response()->json([
            "puede_eliminar" => $puede_eliminar,
            "total_vendidos" => $total_vendidos,
            "total_apartados" => $total_apartados,
            "bloques" => $bloques
        ]);
    }

    public function estado_eliminacion_bloque($id_bloque)
    {
        $lotes = DB::select("SELECT
            L.ID,
            'L-' || L.LOTE AS NOMBRE,
            CASE
                WHEN LV.ID_VENTA IS NOT NULL THEN 'Vendido'
                WHEN L.ID_CLIENTE_RESERVAR IS NOT NULL THEN 'Apartado'
                ELSE 'Disponible'
            END AS ESTADO
        FROM
            LOTES L
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
        WHERE
            L.ID_BLOQUE_RESIDENCIAL = :id
            AND L.DELETED_AT IS NULL
        ORDER BY
            L.LOTE", ["id" => $id_bloque]);

        $vendidos = 0;
        $apartados = 0;
        foreach ($lotes as $l) {
            if ($l->estado == 'Vendido') $vendidos++;
            if ($l->estado == 'Apartado') $apartados++;
        }

        return response()->json([
            "puede_eliminar" => ($vendidos == 0 && $apartados == 0),
            "vendidos" => $vendidos,
            "apartados" => $apartados,
            "lotes" => $lotes
        ]);
    }

    public function ver_bloques($id_residencial)
    {

        $residencial = collect(\DB::select("SELECT
            ID,
            NOMBRE,
            DESCRIPCION,
            IMAGEN
        FROM
            RESIDENCIALES
        WHERE
            DELETED_AT IS NULL
            AND ID = :id", ["id" => $id_residencial]))->first();

        $bloques = DB::select("SELECT
            BR.ID,
            B.NOMBRE AS BLOQUE,
            COUNT(L.ID) AS TOTAL_LOTES,
            COUNT(LV.ID_VENTA) AS VENDIDOS,
            COUNT(L.ID_CLIENTE_RESERVAR) AS APARTADOS,
            COUNT(L.ID) - COUNT(LV.ID_VENTA) - COUNT(L.ID_CLIENTE_RESERVAR) AS DISPONIBLES,
            (
                ROW_NUMBER() OVER (
                    ORDER BY
                        B.NOMBRE DESC
                ) = 1
            ) AS ULTIMO
        FROM
            BLOQUES B
            JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
        WHERE
            BR.ID_RESIDENCIAL = :id
            AND BR.DELETED_AT IS NULL
        GROUP BY
            BR.ID,
            B.NOMBRE
        ORDER BY
            B.NOMBRE;", ["id" => $id_residencial]);

        $bloque_siguiente = collect(\DB::select("SELECT
            ID,
            NOMBRE
        FROM
            BLOQUES
        WHERE
            ID = (
                SELECT
                    B.ID
                FROM
                    BLOQUES B
                    JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                WHERE
                    BR.ID_RESIDENCIAL = :id
                    AND BR.DELETED_AT IS NULL
                ORDER BY
                    B.ID DESC
                LIMIT
                    1
            ) + 1", ["id" => $id_residencial]))->first();

        return view('terranova.residenciales.bloques')
        ->with('residencial', $residencial)
        ->with('bloques', $bloques)
        ->with('bloque_siguiente', $bloque_siguiente);
    }

    public function guardar_bloque(Request $request)
    {
        $id = $request->id;
        $id_residencial = $request->id_residencial;
        $id_bloque_siguiente = $request->id_bloque_siguiente;
        $cantidad_lotes = $request->cantidad_lotes;
        $precio_lote = $request->precio_lote;
        $norte = $request->norte;
        $sur = $request->sur;
        $este = $request->este;
        $oeste = $request->oeste;
        $area = $request->area;
        $financiamiento = $request->financiamiento;
        $accion = $request->accion;
        $bloques_list = null;
        $bloque_anterior = null;
        $bloque_siguiente = null;
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            if($accion == 1){
                $bloque = collect(\DB::select("INSERT INTO
                    BLOQUES_RESIDENCIALES (ID_BLOQUE, ID_RESIDENCIAL)
                VALUES
                    (:id_bloque_siguiente, :id_residencial)
                RETURNING
                    ID;", [
                    "id_bloque_siguiente" => $id_bloque_siguiente,
                    "id_residencial" => $id_residencial
                ]))->first();

                $id = $bloque->id;

                DB::select("INSERT INTO
                    PUBLIC.LOTES (
                        NOMBRE,
                        LOTE,
                        AREA,
                        NORTE,
                        SUR,
                        ESTE,
                        OESTE,
                        PRECIO,
                        ANIOS_FINANCIAMIENTO,
                        ID_BLOQUE_RESIDENCIAL
                    )
                SELECT
                    'L-' || GS,
                    GS,
                    :area,
                    :norte,
                    :sur,
                    :este,
                    :oeste,
                    :precio,
                    :financiamiento,
                    :id_bloque_residencial
                FROM
                    GENERATE_SERIES(1, :cantidad_lotes) AS GS", [
                    "id_bloque_residencial" => $id,
                    "area" => $area,
                    "norte" => $norte,
                    "sur" => $sur,
                    "este" => $este,
                    "oeste" => $oeste,
                    "precio" => $precio_lote,
                    "financiamiento" => $financiamiento,
                    "cantidad_lotes" => $cantidad_lotes
                ]);

                $msgSuccess = "Bloque guardado exitosamente.";
            } elseif($accion == 2){

                $msgSuccess = "Bloque actualizado exitosamente.";
            }elseif($accion == 3){
                DB::select("UPDATE
                    PUBLIC.BLOQUES_RESIDENCIALES
                SET
                    DELETED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id" => $id
                ]);

                $bloque_anterior = collect(\DB::select("WITH
                    ACTUAL AS (
                        SELECT
                            B.NOMBRE
                        FROM
                            BLOQUES B
                            JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                        WHERE
                            BR.ID = :id
                    )
                SELECT
                    BR.ID,
                    B.NOMBRE AS BLOQUE,
                    COUNT(L.ID) AS LOTES
                FROM
                    BLOQUES B
                    JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                    LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
                    AND L.DELETED_AT IS NULL
                WHERE
                    BR.ID_RESIDENCIAL = :id_residencial
                    AND BR.DELETED_AT IS NULL
                    AND B.NOMBRE < (
                        SELECT
                            NOMBRE
                        FROM
                            ACTUAL
                    )
                GROUP BY
                    BR.ID,
                    B.NOMBRE
                ORDER BY
                    B.NOMBRE DESC
                LIMIT
                    1;", [
                    "id" => $id,
                    "id_residencial" => $id_residencial
                ]))->first();

                $msgSuccess = "Bloque eliminado exitosamente.";
            }else{
                 throw new Exception("Acción no válida.");
            }

            $bloques_list = collect(\DB::select("SELECT
                BR.ID,
                B.NOMBRE AS BLOQUE,
                COUNT(L.ID) AS TOTAL_LOTES,
                COUNT(LV.ID_VENTA) AS VENDIDOS,
                COUNT(L.ID_CLIENTE_RESERVAR) AS APARTADOS,
                COUNT(L.ID) - COUNT(LV.ID_VENTA) - COUNT(L.ID_CLIENTE_RESERVAR) AS DISPONIBLES
            FROM
                BLOQUES B
                JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL AND L.DELETED_AT IS NULL
                LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
            WHERE
                BR.id = :id
                AND BR.DELETED_AT IS NULL
            GROUP BY
                BR.ID,
                B.NOMBRE
            ORDER BY
                B.NOMBRE", ["id" => $id]))->first();

            $bloque_siguiente = collect(\DB::select("SELECT
                ID,
                NOMBRE
            FROM
                BLOQUES
            WHERE
                ID = (
                    SELECT
                        B.ID
                    FROM
                        BLOQUES B
                        JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                    WHERE
                        BR.ID_RESIDENCIAL = :id
                        AND BR.DELETED_AT IS NULL
                    ORDER BY
                        B.ID DESC
                    LIMIT
                        1
            ) + 1", ["id" => $id_residencial]))->first();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $msgError = $e->getMessage();
        }

        return response()->json([
            "msgSuccess" => $msgSuccess,
            "msgError" => $msgError,
            "bloques_list" => $bloques_list,
            "bloque_anterior" => $bloque_anterior,
            "bloque_siguiente" => $bloque_siguiente
        ]);

    }

    public function ver_lotes($id_residencial, $id_bloque)
    {

        $bloque = collect(\DB::select("SELECT
            R.ID ID_RESIDENCIAL,
            R.NOMBRE RESIDENCIAL,
            R.IMAGEN,
            BR.ID ID_BLOQUE_RESIDENCIAL,
            B.NOMBRE BLOQUE,
            COUNT(L.ID) CANTIDAD_LOTES
        FROM
            BLOQUES B
            JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            JOIN RESIDENCIALES R ON BR.ID_RESIDENCIAL = R.ID
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
        WHERE
            BR.DELETED_AT IS NULL
            AND R.DELETED_AT IS NULL
            AND B.DELETED_AT IS NULL
            AND L.DELETED_AT IS NULL
            AND BR.ID = :id
        GROUP BY
            R.ID,
            R.NOMBRE,
            R.IMAGEN,
            BR.ID,
            B.NOMBRE", ["id" => $id_bloque]))->first();

        $lotes = DB::select("SELECT
            L.ID,
            L.NOMBRE,
            L.AREA,
            L.AREA || ' m²' AREA_FORMATEADO,
            L.NORTE,
            L.SUR,
            L.ESTE,
            L.OESTE,
            'Norte: ' || L.NORTE || ' m, Sur: ' || L.SUR || ' m, Este: ' || L.ESTE || ' m, Oeste: ' || L.OESTE || ' m.' COLINDANCIAS,
            L.PRECIO,
            TO_CHAR(L.PRECIO, 'L999,999,999.99') AS PRECIO_FORMATEADO,
            L.ANIOS_FINANCIAMIENTO,
            CASE
                WHEN L.ANIOS_FINANCIAMIENTO = 1 THEN L.ANIOS_FINANCIAMIENTO || ' año'
                ELSE L.ANIOS_FINANCIAMIENTO || ' años'
            END ANIOS_FINANCIAMIENTO_FORMATEADO,
            L.ID_CLIENTE_RESERVAR,
            LV.ID_VENTA,
            CASE
                WHEN LV.ID_VENTA IS NOT NULL THEN 'Vendido'
                WHEN L.ID_CLIENTE_RESERVAR IS NOT NULL THEN 'Reservado'
                ELSE 'Disponible'
            END ESTADO,
            TRIM(
                COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || 
                COALESCE(TRIM(C.SEGUNDO_APELLIDO) || ' ', '')
            ) NOMBRE_COMPLETO,
            C.IMAGEN AS CLIENTE_IMAGEN,
            L.RESERVADO_HASTA,
            TO_CHAR(L.RESERVADO_HASTA, 'DD/MM/YYYY') AS RESERVADO_HASTA_FORMATEADO
        FROM
            LOTES L
            LEFT JOIN CLIENTES C ON L.ID_CLIENTE_RESERVAR = C.ID
            LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
        WHERE
            L.DELETED_AT IS NULL
            AND L.ID_BLOQUE_RESIDENCIAL = :id
        ORDER BY
            L.ID", ["id" => $id_bloque]);

        $lote_siguiente = collect(\DB::select("SELECT
            'L-'||COALESCE(MAX(L.LOTE), 0) + 1 AS NOMBRE
        FROM
            LOTES L
        WHERE
            L.DELETED_AT IS NULL
            AND L.ID_BLOQUE_RESIDENCIAL = :id;", ["id" => $id_bloque]))->first();

        $clientes = DB::select("SELECT ID, PRIMER_NOMBRE || ' ' || PRIMER_APELLIDO AS NOMBRE FROM CLIENTES WHERE DELETED_AT IS NULL ORDER BY PRIMER_NOMBRE ASC");

        $residencial = collect(\DB::select("SELECT ID, NOMBRE FROM RESIDENCIALES WHERE ID = :id", ["id" => $id_residencial]))->first();

        return view('terranova.residenciales.lotes')
        ->with('bloque', $bloque)
        ->with('lotes', $lotes)
        ->with('lote_siguiente', $lote_siguiente)
        ->with('clientes', $clientes)
        ->with('residencial', $residencial);
    }

    public function guardar_lote(Request $request)
    {
        $id = $request->id;
        $id_bloque_residencial = $request->id_bloque_residencial;
        $cantidad_lotes = $request->cantidad_lotes;
        $precio_lote = $request->precio_lote;
        $norte = $request->norte;
        $sur = $request->sur;
        $este = $request->este;
        $oeste = $request->oeste;
        $area = $request->area;
        $financiamiento = $request->financiamiento;
        $accion = $request->accion;

        // Campos para reservación
        $id_cliente_reservar = $request->id_cliente_reservar;
        $reservado_hasta = $request->reservado_hasta;

        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            if($accion == 1){
                if($cantidad_lotes > 1) {
                    DB::select("WITH max_lote AS (
                        SELECT COALESCE(MAX(L.LOTE), 0) AS ultimo
                        FROM LOTES L
                        WHERE L.DELETED_AT IS NULL
                            AND L.ID_BLOQUE_RESIDENCIAL = :id_bloque_residencial
                    )
                    INSERT INTO
                        PUBLIC.LOTES (
                            NOMBRE,
                            LOTE,
                            AREA,
                            NORTE,
                            SUR,
                            ESTE,
                            OESTE,
                            PRECIO,
                            ANIOS_FINANCIAMIENTO,
                            ID_BLOQUE_RESIDENCIAL
                        )
                    SELECT
                        'L-' || (ultimo + GS),
                        ultimo + GS,
                        :area,
                        :norte,
                        :sur,
                        :este,
                        :oeste,
                        :precio,
                        :financiamiento,
                        :id_bloque_residencial
                    FROM
                        GENERATE_SERIES(1, :cantidad_lotes) AS GS,
                        max_lote", [
                        "id_bloque_residencial" => $id_bloque_residencial,
                        "area" => $area,
                        "norte" => $norte,
                        "sur" => $sur,
                        "este" => $este,
                        "oeste" => $oeste,
                        "precio" => $precio_lote,
                        "financiamiento" => $financiamiento,
                        "cantidad_lotes" => $cantidad_lotes
                    ]);
                }else {
                    $loteInsertado = collect(DB::select("INSERT INTO
                        PUBLIC.LOTES (
                            NOMBRE,
                            LOTE,
                            AREA,
                            NORTE,
                            SUR,
                            ESTE,
                            OESTE,
                            PRECIO,
                            ANIOS_FINANCIAMIENTO,
                            ID_BLOQUE_RESIDENCIAL
                        )
                    SELECT
                        'L-' || COALESCE(MAX(L.LOTE), 0) + 1,
                        COALESCE(MAX(L.LOTE), 0) + 1,
                        :area,
                        :norte,
                        :sur,
                        :este,
                        :oeste,
                        :precio,
                        :financiamiento,
                        :id_bloque_residencial
                    FROM
                        LOTES L
                    WHERE
                        L.DELETED_AT IS NULL
                        AND L.ID_BLOQUE_RESIDENCIAL = :id_bloque_residencial
                    RETURNING ID;", [
                        "area" => $area,
                        "norte" => $norte,
                        "sur" => $sur,
                        "este" => $este,
                        "oeste" => $oeste,
                        "precio" => $precio_lote,
                        "financiamiento" => $financiamiento,
                        "id_bloque_residencial" => $id_bloque_residencial
                    ]))->first();
                }

                $msgSuccess = "Lote guardado exitosamente.";
            } elseif($accion == 2){
                DB::select("UPDATE
                    PUBLIC.LOTES
                SET
                    PRECIO = :precio,
                    AREA = :area,
                    NORTE = :norte,
                    SUR = :sur,
                    ESTE = :este,
                    OESTE = :oeste,
                    ANIOS_FINANCIAMIENTO = :financiamiento,
                    UPDATED_AT = NOW()
                WHERE
                    ID = :id", [
                    "precio" => $precio_lote,
                    "area" => $area,
                    "norte" => $norte,
                    "sur" => $sur,
                    "este" => $este,
                    "oeste" => $oeste,
                    "financiamiento" => $financiamiento,
                    "id" => $id
                ]);
                $msgSuccess = "Lote actualizado exitosamente.";
            }elseif($accion == 3){
                $lote_estado = collect(DB::select("SELECT
                    CASE
                        WHEN LV.ID_VENTA IS NOT NULL THEN 'Vendido'
                        WHEN L.ID_CLIENTE_RESERVAR IS NOT NULL THEN 'Reservado'
                        ELSE 'Disponible'
                    END AS ESTADO
                FROM LOTES L
                LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
                WHERE L.ID = :id", ["id" => $id]))->first();

                if ($lote_estado && $lote_estado->estado != 'Disponible') {
                    throw new Exception("No se puede eliminar un lote que está " . strtolower($lote_estado->estado) . ".");
                }

                DB::select("UPDATE
                    PUBLIC.LOTES
                SET
                    DELETED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id" => $id
                ]);

                $msgSuccess = "Lote eliminado exitosamente.";
            }elseif($accion == 4){ // Reservar
                DB::select("UPDATE
                    PUBLIC.LOTES
                SET
                    ID_CLIENTE_RESERVAR = :id_cliente,
                    RESERVADO_HASTA = :reservado_hasta,
                    UPDATED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id_cliente" => $id_cliente_reservar,
                    "reservado_hasta" => $reservado_hasta,
                    "id" => $id
                ]);
                $msgSuccess = "Lote reservado exitosamente.";
            }elseif($accion == 5){ // Quitar Reserva
                DB::select("UPDATE
                    PUBLIC.LOTES
                SET
                    ID_CLIENTE_RESERVAR = NULL,
                    RESERVADO_HASTA = NULL,
                    UPDATED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id" => $id
                ]);
                $msgSuccess = "Reserva quitada exitosamente.";
            }else{
                 throw new Exception("Acción no válida.");
            }

            DB::commit();

            $loteData = null;
            $loteId = null;

            if ($accion == 1 && $cantidad_lotes <= 1) {
                $loteId = $loteInsertado->id ?? null;
            } elseif (in_array($accion, [2, 4, 5])) {
                $loteId = $id;
            }

            if ($loteId) {
                $lote = DB::select("
                    SELECT
                        L.ID, L.NOMBRE, L.AREA, L.AREA || ' m²' AS AREA_FORMATEADO,
                        L.NORTE, L.SUR, L.ESTE, L.OESTE, L.PRECIO,
                        TO_CHAR(L.PRECIO, 'L999,999,999.99') AS PRECIO_FORMATEADO,
                        L.ANIOS_FINANCIAMIENTO, L.ID_CLIENTE_RESERVAR, LV.ID_VENTA, C.IMAGEN AS CLIENTE_IMAGEN,
                        'Norte: ' || L.NORTE || ' m, Sur: ' || L.SUR || ' m, Este: ' || L.ESTE || ' m, Oeste: ' || L.OESTE || ' m.' AS COLINDANCIAS,
                        CASE WHEN L.ANIOS_FINANCIAMIENTO = 1 THEN L.ANIOS_FINANCIAMIENTO || ' año'
                             ELSE L.ANIOS_FINANCIAMIENTO || ' años' END AS ANIOS_FINANCIAMIENTO_FORMATEADO,
                        CASE WHEN LV.ID_VENTA IS NOT NULL THEN 'Vendido'
                             WHEN L.ID_CLIENTE_RESERVAR IS NOT NULL THEN 'Reservado'
                             ELSE 'Disponible' END AS ESTADO,
                        TRIM(COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') ||
                             COALESCE(C.SEGUNDO_NOMBRE || ' ', '') ||
                             COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') ||
                             COALESCE(C.SEGUNDO_APELLIDO, '')) AS NOMBRE_COMPLETO,
                        L.RESERVADO_HASTA,
                        TO_CHAR(L.RESERVADO_HASTA, 'DD/MM/YYYY') AS RESERVADO_HASTA_FORMATEADO
                    FROM LOTES L
                    LEFT JOIN CLIENTES C ON L.ID_CLIENTE_RESERVAR = C.ID
                    LEFT JOIN LOTES_VENDIDOS LV ON L.ID = LV.ID_LOTE
                    WHERE L.DELETED_AT IS NULL AND L.ID = :id
                ", ["id" => $loteId]);

                $loteData = $lote[0] ?? null;
            }
        } catch (Exception $e) {
            DB::rollback();
            $msgError = $e->getMessage();
        }

        return response()->json([
            "msgSuccess" => $msgSuccess,
            "msgError" => $msgError,
            "lote" => $loteData
        ]);

    }
}
