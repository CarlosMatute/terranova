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
            B.NOMBRE BLOQUE,
            COUNT(L.ID) LOTES
        FROM
            BLOQUES B
            JOIN BLOQUES_RESIDENCIALES BR ON B.ID = BR.ID_BLOQUE
            LEFT JOIN LOTES L ON BR.ID = L.ID_BLOQUE_RESIDENCIAL
        WHERE
            BR.ID_RESIDENCIAL = :id
            AND BR.DELETED_AT IS NULL
            AND L.DELETED_AT IS NULL
        GROUP BY
	        BR.ID,
            B.NOMBRE
        ORDER BY
            B.NOMBRE", ["id" => $id_residencial]);

        return view('terranova.residenciales.bloques')
        ->with('residencial', $residencial)
        ->with('bloques', $bloques);
    }
}
