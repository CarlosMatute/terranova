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
        $nombre = $request->input('nombre');
        $descripcion = $request->input('descripcion');
        $bloque = $request->input('bloque');
        $accion = $request->input('accion');
        $archivoSeleccionado = array();
        $archivoSeleccionado = $request->file('archivoSeleccionado');
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            throw new Exception($nombre);

            $msgSuccess = "Residencial guardado exitosamente.";
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $msgError = $e->getMessage();
        }

        return response()->json([
            "msgSuccess" => $msgSuccess,
            "msgError" => $msgError
        ]);
    }
}
