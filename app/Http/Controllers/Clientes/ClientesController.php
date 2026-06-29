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
            PRIMER_NOMBRE,
            SEGUNDO_NOMBRE,
            PRIMER_APELLIDO,
            SEGUNDO_APELLIDO,
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
        $accion = $request->input('accion');

        $rules = [
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'identidad' => 'required|string|max:20',
            'contacto_telefonico' => 'required|string|max:20',
            'contacto_telefonico_2' => 'nullable|string|max:20',
            'correo_electronico' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:500',
        ];

        if ($accion == 3) {
            $rules = ['id' => 'required|integer|exists:clientes,id'];
        }

        $request->validate($rules);

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
                    throw new Exception("Ya existe un cliente registrado con el n�mero de identidad " . $identidad);
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
                $ventas = collect(DB::select("SELECT ID FROM VENTAS WHERE ID_CLIENTE = :id AND DELETED_AT IS NULL", ["id" => $id]));
                if (count($ventas) > 0) {
                    throw new Exception("No se puede eliminar el cliente porque tiene ventas asociadas.");
                }

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
                 throw new Exception("Acci�n no v�lida.");
            }

            if($accion != 3){
                $clientes_list = collect(DB::select("SELECT
                    ID,
            PRIMER_NOMBRE,
            SEGUNDO_NOMBRE,
            PRIMER_APELLIDO,
            SEGUNDO_APELLIDO,
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

            if ($accion == 1 || $accion == 2) {
                if ($request->has('referencias_eliminar')) {
                    $refsEliminar = $request->input('referencias_eliminar');
                    if (!empty($refsEliminar)) {
                        $ids = implode(",", array_map('intval', $refsEliminar));
                        DB::statement("UPDATE PUBLIC.REFERENCIAS SET DELETED_AT = NOW() WHERE ID IN ($ids)");
                    }
                }

                if ($request->has('referencias')) {
                    $referencias = $request->input('referencias');
                    if (!empty($referencias)) {
                        foreach ($referencias as $ref) {
                            DB::insert("INSERT INTO PUBLIC.REFERENCIAS (NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION, ID_CLIENTE) VALUES (?, ?, ?, ?)", [
                                $ref['nombre_completo'],
                                $ref['contacto_telefonico'],
                                $ref['direccion'],
                                $id
                            ]);
                        }
                    }
                }

                if ($request->has('beneficiarios_eliminar')) {
                    $bensEliminar = $request->input('beneficiarios_eliminar');
                    if (!empty($bensEliminar)) {
                        $ids = implode(",", array_map('intval', $bensEliminar));
                        DB::statement("UPDATE PUBLIC.BENEFICIARIOS SET DELETED_AT = NOW() WHERE ID IN ($ids)");
                    }
                }

                if ($request->has('beneficiarios')) {
                    $beneficiarios = $request->input('beneficiarios');
                    if (!empty($beneficiarios)) {
                        foreach ($beneficiarios as $ben) {
                            DB::insert("INSERT INTO PUBLIC.BENEFICIARIOS (NOMBRE_COMPLETO, IDENTIDAD, PARENTEZCO, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION, ID_CLIENTE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
                                $ben['nombre_completo'],
                                $ben['identidad'],
                                $ben['parentezco'],
                                $ben['contacto_telefonico'],
                                $ben['contacto_telefonico_2'] ?? null,
                                $ben['correo_electronico'] ?? null,
                                $ben['direccion'],
                                $id
                            ]);
                        }
                    }
                }
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

    public function datos_clientes(Request $request)
    {
        $id_user = Auth::id();
        $draw = (int) $request->input('draw');
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = $request->input('search.value', '');
        $orderColIdx = (int) $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'asc');

        $where = "DELETED_AT IS NULL AND ID_USER = :id_user";
        $params = ['id_user' => $id_user];

        if (!empty($search)) {
            $where .= " AND (
                TRIM(
                    COALESCE(TRIM(PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_APELLIDO) || ' ', '')
                ) ILIKE :search1
                OR IDENTIDAD ILIKE :search2
                OR CONTACTO_TELEFONICO ILIKE :search3
            )";
            $term = '%' . $search . '%';
            $params['search1'] = $term;
            $params['search2'] = $term;
            $params['search3'] = $term;
        }

        $total = DB::select("SELECT COUNT(*) AS total FROM CLIENTES WHERE DELETED_AT IS NULL AND ID_USER = :id_user", ['id_user' => $id_user])[0]->total;

        $filtered = DB::select("SELECT COUNT(*) AS total FROM CLIENTES WHERE {$where}", $params)[0]->total;

        $orderMap = [
            0 => 'ID',
            2 => 'PRIMER_NOMBRE, SEGUNDO_NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO',
            3 => 'IDENTIDAD',
            4 => 'CONTACTO_TELEFONICO',
        ];
        $orderCol = $orderMap[$orderColIdx] ?? 'PRIMER_NOMBRE, SEGUNDO_NOMBRE';
        $orderDirSql = strtoupper($orderDir) === 'DESC' ? 'DESC NULLS LAST' : 'ASC NULLS LAST';

        $data = DB::select("
            SELECT
                ID,
                PRIMER_NOMBRE,
                SEGUNDO_NOMBRE,
                PRIMER_APELLIDO,
                SEGUNDO_APELLIDO,
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
            FROM CLIENTES
            WHERE {$where}
            ORDER BY {$orderCol} {$orderDirSql}
            LIMIT {$length} OFFSET {$start}
        ", $params);

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => (int) $total,
            'recordsFiltered' => (int) $filtered,
            'data' => $data
        ]);
    }

    public function buscar_clientes(Request $request)
    {
        $search = $request->input('q', '');
        $page = (int) $request->input('page', 1);
        $limit = 15;
        $offset = ($page - 1) * $limit;

        $where = "DELETED_AT IS NULL";
        $params = [];

        if (!empty($search)) {
            $where .= " AND (
                TRIM(
                    COALESCE(TRIM(PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_APELLIDO) || ' ', '')
                ) ILIKE :search1
                OR IDENTIDAD ILIKE :search2
            )";
            $term = '%' . $search . '%';
            $params['search1'] = $term;
            $params['search2'] = $term;
        }

        $total = DB::select("SELECT COUNT(*) AS total FROM CLIENTES WHERE {$where}", $params)[0]->total;

        $clientes = DB::select("
            SELECT ID, IDENTIDAD, IMAGEN,
                TRIM(
                    COALESCE(TRIM(PRIMER_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_NOMBRE) || ' ', '') || 
                    COALESCE(TRIM(PRIMER_APELLIDO) || ' ', '') || 
                    COALESCE(TRIM(SEGUNDO_APELLIDO) || ' ', '')
                ) AS NOMBRE
            FROM CLIENTES
            WHERE {$where}
            ORDER BY PRIMER_NOMBRE ASC
            LIMIT {$limit} OFFSET {$offset}
        ", $params);

        $results = array_map(function($c) {
            return [
                'id' => $c->id,
                'text' => $c->nombre . ' - ' . $c->identidad,
                'identidad' => $c->identidad,
                'imagen' => $c->imagen
            ];
        }, $clientes);

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($offset + $limit) < $total
            ]
        ]);
    }

    public function perfil_cliente($id)
    {
        $cliente = collect(DB::select("SELECT
            ID,
            PRIMER_NOMBRE,
            SEGUNDO_NOMBRE,
            PRIMER_APELLIDO,
            SEGUNDO_APELLIDO,
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
            AND ID = :id", ["id" => $id]))->first();

        if (!$cliente) {
            abort(404);
        }

        $ventas = DB::select("SELECT 
            V.ID,
            V.FECHA_VENTA,
            TP.NOMBRE AS TIPO_PAGO,
            EV.NOMBRE AS ESTADO,
            V.TOTAL_CONTADO,
            V.PRIMA,
            V.TOTAL_INTERESES,
            V.TOTAL_PAGAR,
            V.CUOTA_MENSUAL
        FROM 
            VENTAS V
            JOIN CATALOGO_TIPO_PAGO TP ON V.TIPO_PAGO = TP.ID
            JOIN CATALOGO_ESTADO_VENTA EV ON V.ESTADO = EV.ID
        WHERE 
            V.DELETED_AT IS NULL
            AND V.ID_CLIENTE = :id
        ORDER BY 
            V.CREATED_AT DESC", ["id" => $id]);

        $referencias = DB::select("SELECT ID, NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION FROM PUBLIC.REFERENCIAS WHERE ID_CLIENTE = :id AND DELETED_AT IS NULL", ["id" => $id]);
        $beneficiarios = DB::select("SELECT ID, NOMBRE_COMPLETO, IDENTIDAD, PARENTEZCO, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION FROM PUBLIC.BENEFICIARIOS WHERE ID_CLIENTE = :id AND DELETED_AT IS NULL", ["id" => $id]);

        return view('terranova.clientes.perfil')
            ->with('cliente', $cliente)
            ->with('ventas', $ventas)
            ->with('referencias', $referencias)
            ->with('beneficiarios', $beneficiarios);
    }

    public function obtener_referencias(Request $request)
    {
        $id_cliente = $request->input('id_cliente');
        $referencias = DB::select("SELECT ID, NOMBRE_COMPLETO, CONTACTO_TELEFONICO, DIRECCION FROM PUBLIC.REFERENCIAS WHERE ID_CLIENTE = :id_cliente AND DELETED_AT IS NULL", ["id_cliente" => $id_cliente]);
        return response()->json(["referencias" => $referencias]);
    }

    public function obtener_beneficiarios(Request $request)
    {
        $id_cliente = $request->input('id_cliente');
        $beneficiarios = DB::select("SELECT ID, NOMBRE_COMPLETO, IDENTIDAD, PARENTEZCO, CONTACTO_TELEFONICO, CONTACTO_TELEFONICO_2, CORREO_ELECTRONICO, DIRECCION FROM PUBLIC.BENEFICIARIOS WHERE ID_CLIENTE = :id_cliente AND DELETED_AT IS NULL", ["id_cliente" => $id_cliente]);
        return response()->json(["beneficiarios" => $beneficiarios]);
    }
}
