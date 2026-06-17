<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
Use Session;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientesController extends Controller
{
    public function ver_clientes()
    {
        $clientes = DB::select("SELECT
            ID,
            TRIM(
                COALESCE(TRIM(PRIMER_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(SEGUNDO_NOMBRE) || ' ', '') || 
                COALESCE(TRIM(PRIMER_APELLIDO) || ' ', '') || 
                COALESCE(TRIM(SEGUNDO_APELLIDO) || ' ', '')
            ) AS NOMBRE_COMPLETO,
            IDENTIDAD,
            CONTACTO_TELEFONICO,
            CONTACTO_TELEFONICO_2,
            CORREO_ELECTRONICO,
            DIRECCION,
            IMAGEN
        FROM
            CLIENTES
        WHERE
            DELETED_AT IS NULL
            AND ID_USER = :id_user
        ORDER BY
            PRIMER_NOMBRE, SEGUNDO_NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO", ["id_user" => Auth::id()]);

        return view('terranova.clientes.clientes')
        ->with('clientes', $clientes);
    }

    public function guardar_cliente(Request $request)
    {
        $id = $request->id;
        $primer_nombre = $request->input('primer_nombre');
        $segundo_nombre = $request->input('segundo_nombre');
        $primer_apellido = $request->input('primer_apellido');
        $segundo_apellido = $request->input('segundo_apellido');
        $identidad = $request->input('identidad');
        $contacto_telefonico = $request->input('contacto_telefonico');
        $contacto_telefonico_2 = $request->input('contacto_telefonico_2');
        $correo_electronico = $request->input('correo_electronico');
        $direccion = $request->input('direccion');
        
        $accion = $request->input('accion');
        $cambiar_imagen = ($request->input('cambiar_imagen') == 'true') ? true : false;
        $archivoSeleccionado = null;
        $clientes_list = null;
        $msgSuccess = null;
        $msgError = null;

        DB::beginTransaction();
        try {
            if($request->hasFile('archivoSeleccionado')) {
                $archivos = $request->file('archivoSeleccionado');
                $archivoSeleccionado = $archivos->getClientOriginalName();
            }

            if($accion == 1){
                $existe = collect(DB::select("SELECT ID FROM CLIENTES WHERE IDENTIDAD = :identidad AND ID_USER = :id_user AND DELETED_AT IS NULL", [
                    "identidad" => $identidad,
                    "id_user" => Auth::id()
                ]))->first();

                if($existe){
                    throw new Exception("Ya existe un cliente registrado con el número de identidad " . $identidad);
                }

                $cliente = collect(DB::select("INSERT INTO
                    PUBLIC.CLIENTES (
                        PRIMER_NOMBRE, SEGUNDO_NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO,
                        IDENTIDAD, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2,
                        CORREO_ELECTRONICO, DIRECCION, ID_USER, IMAGEN
                    )
                VALUES
                    (:primer_nombre, :segundo_nombre, :primer_apellido, :segundo_apellido,
                    :identidad, :contacto_telefonico, :contacto_telefonico_2,
                    :correo_electronico, :direccion, :id_user, :imagen)
                RETURNING
                    ID;", [
                    "primer_nombre" => $primer_nombre,
                    "segundo_nombre" => $segundo_nombre,
                    "primer_apellido" => $primer_apellido,
                    "segundo_apellido" => $segundo_apellido,
                    "identidad" => $identidad,
                    "contacto_telefonico" => $contacto_telefonico,
                    "contacto_telefonico_2" => $contacto_telefonico_2,
                    "correo_electronico" => $correo_electronico,
                    "direccion" => $direccion,
                    "id_user" => Auth::id(),
                    "imagen" => $archivoSeleccionado
                ]))->first();

                $id = $cliente->id;

                if($request->hasFile('archivoSeleccionado')) {
                    $archivos->storeAs('public/clientes/cli_' . $id, $archivoSeleccionado);
                }

                $msgSuccess = "Cliente " . $primer_nombre . " " . $primer_apellido . " guardado exitosamente.";
            } elseif($accion == 2){
                $img_old = collect(DB::select("SELECT IMAGEN FROM PUBLIC.CLIENTES WHERE ID = :id", ["id" => $id]))->first();

                DB::select("UPDATE
                    PUBLIC.CLIENTES
                SET
                    PRIMER_NOMBRE = :primer_nombre,
                    SEGUNDO_NOMBRE = :segundo_nombre,
                    PRIMER_APELLIDO = :primer_apellido,
                    SEGUNDO_APELLIDO = :segundo_apellido,
                    IDENTIDAD = :identidad,
                    CONTACTO_TELEFONICO = :contacto_telefonico,
                    CONTACTO_TELEFONICO_2 = :contacto_telefonico_2,
                    CORREO_ELECTRONICO = :correo_electronico,
                    DIRECCION = :direccion,
                    UPDATED_AT = NOW()
                WHERE
                    ID = :id", [
                    "primer_nombre" => $primer_nombre,
                    "segundo_nombre" => $segundo_nombre,
                    "primer_apellido" => $primer_apellido,
                    "segundo_apellido" => $segundo_apellido,
                    "identidad" => $identidad,
                    "contacto_telefonico" => $contacto_telefonico,
                    "contacto_telefonico_2" => $contacto_telefonico_2,
                    "correo_electronico" => $correo_electronico,
                    "direccion" => $direccion,
                    "id" => $id
                ]);

                if($cambiar_imagen) {
                    DB::select("UPDATE
                        PUBLIC.CLIENTES
                    SET
                        IMAGEN = :imagen
                    WHERE
                        ID = :id", [
                        "imagen" => $archivoSeleccionado,
                        "id" => $id
                    ]);

                    if($img_old && $img_old->imagen){
                        Storage::delete('public/clientes/cli_' . $id . '/' . $img_old->imagen);
                    }
                    if($request->hasFile('archivoSeleccionado')) {
                        $archivos->storeAs('public/clientes/cli_' . $id, $archivoSeleccionado);
                    }
                }
                $msgSuccess = "Cliente " . $primer_nombre . " actualizado exitosamente.";
            }elseif($accion == 3){
                DB::select("UPDATE
                    PUBLIC.CLIENTES
                SET
                    DELETED_AT = NOW()
                WHERE
                    ID = :id", [
                    "id" => $id
                ]);

                $msgSuccess = "Cliente eliminado exitosamente.";
            }else{
                 throw new Exception("Acción no válida.");
            }

            if($accion != 3){
                $clientes_list = collect(DB::select("SELECT
                    ID,
                    TRIM(
                        COALESCE(TRIM(PRIMER_NOMBRE) || ' ', '') || 
                        COALESCE(TRIM(SEGUNDO_NOMBRE) || ' ', '') || 
                        COALESCE(TRIM(PRIMER_APELLIDO) || ' ', '') || 
                        COALESCE(TRIM(SEGUNDO_APELLIDO) || ' ', '')
                    ) AS NOMBRE_COMPLETO,
                    IDENTIDAD,
                    CONTACTO_TELEFONICO,
                    CORREO_ELECTRONICO,
                    DIRECCION,
                    IMAGEN
                FROM
                    CLIENTES
                WHERE
                    DELETED_AT IS NULL
                    AND ID = :id", ["id" => $id]))->first();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $msgError = $e->getMessage();
        }

        return response()->json([
            "msgSuccess" => $msgSuccess,
            "msgError" => $msgError,
            "clientes_list" => $clientes_list
        ]);
    }
}
