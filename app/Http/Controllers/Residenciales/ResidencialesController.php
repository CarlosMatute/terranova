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
        $bloque = $request->input('bloque');
        $accion = $request->input('accion');
        $archivoSeleccionado = null;
        $residenciales_list = null;
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {

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

                if($request->hasFile('archivoSeleccionado')) {
                    $archivos->storeAs('public/residenciales/res_' . $id, $archivoSeleccionado);  
                }

                $msgSuccess = "Residencial " . $nombre . " guardada exitosamente.";
            };

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
}
