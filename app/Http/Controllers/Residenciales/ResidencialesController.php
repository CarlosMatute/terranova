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
            ID,
            NOMBRE,
            DESCRIPCION,
            IMAGEN
        FROM
            RESIDENCIALES
        WHERE
            DELETED_AT IS NULL
            AND ID_USER = :id_user", ["id_user" => Auth::id()]);

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
            //throw new Exception($cambiar_imagen);
            if($request->hasFile('archivoSeleccionado')) {
                $archivos = $request->file('archivoSeleccionado'); 
                $archivoSeleccionado = $archivos->getClientOriginalName();
                //$archivos->storeAs('public/residenciales', $archivoSeleccionado);  
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
                ID,
                NOMBRE,
                DESCRIPCION,
                IMAGEN
            FROM
                RESIDENCIALES
            WHERE
                DELETED_AT IS NULL
                AND ID = :id", ["id" => $id]))->first();

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
            COUNT(L.ID) AS LOTES,
            (
                ROW_NUMBER() OVER (
                    ORDER BY
                        B.NOMBRE DESC
                ) = 1
            ) AS ULTIMO
        FROM
            BLOQUES B
            JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
            AND L.DELETED_AT IS NULL
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
            ///throw new Exception($id_bloque_siguiente);
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
                
                $msgSuccess = "Residencial " . $nombre . " actualizada exitosamente.";
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
                    BR.ID_RESIDENCIAL = 1
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
                    "id" => $id
                ]))->first();

                $msgSuccess = "Bloque eliminado exitosamente.";
            }else{  
                 throw new Exception("Acción no válida.");
            }

            $bloques_list = collect(\DB::select("SELECT
                BR.ID,
                B.NOMBRE BLOQUE,
                COUNT(L.ID) LOTES
            FROM
                BLOQUES B
                JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
                LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
            WHERE
                BR.id = :id
                AND BR.DELETED_AT IS NULL
                AND L.DELETED_AT IS NULL
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
            CASE
                WHEN L.ID_CLIENTE_RESERVAR IS NULL THEN 'Disponible'
                ELSE 'Reservado'
            END ESTADO,
            TRIM(
                COALESCE(TRIM(C.PRIMER_NOMBRE) || ' ', '') || COALESCE(TRIM(C.SEGUNDO_NOMBRE) || ' ', '') || COALESCE(TRIM(C.PRIMER_APELLIDO) || ' ', '') || COALESCE(TRIM(C.SEGUNDO_APELLIDO || ' '), '')
            ) NOMBRE_COMPLETO,
            L.RESERVADO_HASTA
        FROM
            LOTES L
            LEFT JOIN CLIENTES C ON L.ID_CLIENTE_RESERVAR = C.ID
        WHERE
            L.DELETED_AT IS NULL
            AND L.ID_BLOQUE_RESIDENCIAL = :id
        ORDER BY
	        ID", ["id" => $id_bloque]);

        $lote_siguiente = collect(\DB::select("SELECT
            'L-'||COALESCE(MAX(L.LOTE), 0) + 1 AS NOMBRE
        FROM
            LOTES L
        WHERE
            L.DELETED_AT IS NULL
            AND L.ID_BLOQUE_RESIDENCIAL = :id;", ["id" => $id_bloque]))->first();

        return view('terranova.residenciales.lotes')
        ->with('bloque', $bloque)
        ->with('lotes', $lotes)
        ->with('lote_siguiente', $lote_siguiente);
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
        $bloques_list = null;
        $bloque_anterior = null;
        $bloque_siguiente = null;
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            //throw new Exception($accion);
            if($accion == 1){
                if($cantidad_lotes > 1) {
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
                        AND L.ID_BLOQUE_RESIDENCIAL = :id_bloque_residencial;", [
                        "area" => $area,
                        "norte" => $norte,
                        "sur" => $sur,
                        "este" => $este,
                        "oeste" => $oeste,
                        "precio" => $precio_lote,
                        "financiamiento" => $financiamiento,
                        "id_bloque_residencial" => $id_bloque_residencial
                    ]);
                }

                $msgSuccess = "Lote guardado exitosamente.";
            } elseif($accion == 2){
                
                $msgSuccess = "Residencial " . $nombre . " actualizada exitosamente.";
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
                    BR.ID_RESIDENCIAL = 1
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
                    "id" => $id
                ]))->first();

                $msgSuccess = "Bloque eliminado exitosamente.";
            }else{  
                 throw new Exception("Acción no válida.");
            }

            // $bloques_list = collect(\DB::select("SELECT
            //     BR.ID,
            //     B.NOMBRE BLOQUE,
            //     COUNT(L.ID) LOTES
            // FROM
            //     BLOQUES B
            //     JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            //     LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
            // WHERE
            //     BR.id = :id
            //     AND BR.DELETED_AT IS NULL
            //     AND L.DELETED_AT IS NULL
            // GROUP BY
            //     BR.ID,
            //     B.NOMBRE
            // ORDER BY
            //     B.NOMBRE", ["id" => $id]))->first();

            // $bloque_siguiente = collect(\DB::select("SELECT
            //     ID,
            //     NOMBRE
            // FROM
            //     BLOQUES
            // WHERE
            //     ID = (
            //         SELECT
            //             B.ID
            //         FROM
            //             BLOQUES B
            //             JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            //         WHERE
            //             BR.ID_RESIDENCIAL = :id
            //             AND BR.DELETED_AT IS NULL
            //         ORDER BY
            //             B.ID DESC
            //         LIMIT
            //             1
            // ) + 1", ["id" => $id_residencial]))->first();

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
}
