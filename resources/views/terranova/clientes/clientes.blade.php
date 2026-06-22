@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/cropperjs/cropper.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-2">Clientes</h3>
                    <p class="text-muted">Administración integral de clientes y sus referencias.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-secondary">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i class="text-white icon-lg pb-3px" data-feather="users"></i> Clientes Registrados</h5>
                    <button type="button" class="btn btn-light btn-xs" id="btn_agregar_cliente" data-bs-toggle="modal" data-bs-target="#modal_agregar_cliente">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Cliente
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_clientes" border="1">
                            <thead class="bg-secondary">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Imagen</th>
                                    <th scope="col" class="text-white">Nombre Completo</th>
                                    <th scope="col" class="text-white">Identidad</th>
                                    <th scope="col" class="text-white">Teléfono</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientes as $row)
                                    <tr style="font-size: small">
                                        <td>{{ $row->id }}</td>
                                        <td>
                                            <img class="wd-30 ht-30 rounded-circle" src="{{ asset('storage/clientes/cli_' . $row->id . '/' . $row->imagen) }}" 
                                                onerror="this.onerror=null; this.src='{{ asset('/assets/images/user.png') }}';">
                                        </td>
                                        <td>{{ $row->nombre_completo }}</td>
                                        <td>{{ $row->identidad }}</td>
                                        <td>{{ $row->contacto_telefonico }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#modal_agregar_cliente"
                                                    data-id="{{ $row->id }}"
                                                    data-pnombre="{{ $row->primer_nombre }}" data-snombre="{{ $row->segundo_nombre }}"
                                                    data-papellido="{{ $row->primer_apellido }}" data-sapellido="{{ $row->segundo_apellido }}"
                                                    data-identidad="{{ $row->identidad }}" data-tel1="{{ $row->contacto_telefonico }}"
                                                    data-tel2="{{ $row->contacto_telefonico_2 }}" data-email="{{ $row->correo_electronico }}" data-dir="{{ $row->direccion }}">
                                                    <i data-feather="edit-2" width="14" height="14"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-xs btn_eliminar_cliente" data-id="{{ $row->id }}" data-nombre="{{ $row->nombre_completo }}">
                                                    <i data-feather="trash-2" width="14" height="14"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cliente -->
    <div class="modal fade" id="modal_agregar_cliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h6 class="modal-title h6 text-white" id="modal_cliente_titulo">Registrar Cliente</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3"><label class="form-label">Primer Nombre</label><input id="modal_cli_nombre1" class="form-control" type="text" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Segundo Nombre</label><input id="modal_cli_nombre2" class="form-control" type="text" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Primer Apellido</label><input id="modal_cli_apellido1" class="form-control" type="text" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Segundo Apellido</label><input id="modal_cli_apellido2" class="form-control" type="text" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Identidad</label><input id="modal_cli_identidad" class="form-control" type="text" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Teléfono 1</label><input id="modal_cli_tel1" class="form-control" type="text" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Teléfono 2</label><input id="modal_cli_tel2" class="form-control" type="text" /></div>
                                <div class="col-md-12 mb-3"><label class="form-label">Correo</label><input id="modal_cli_email" class="form-control" type="email" /></div>
                                <div class="col-md-12 mb-3"><label class="form-label">Dirección</label><textarea id="modal_cli_dir" class="form-control" rows="2"></textarea></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_guardar_cliente"><i data-feather="save" width="16" height="16"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/alertas_propias.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        var table = null;
        var accion = 1;
        var id_cliente = null;
        var rowIndexEditar = null;
        var btn_activo = true;

        function csrfToken() {
            return $('meta[name="csrf-token"]').attr('content');
        }

        function construirFilaCliente(r) {
            var nombreCompleto = ((r.primer_nombre || '') + ' ' + (r.segundo_nombre || '') + ' ' + (r.primer_apellido || '') + ' ' + (r.segundo_apellido || '')).replace(/\s+/g, ' ').trim();
            var imgSrc = '{{ asset("storage/clientes/cli_") }}' + r.id + '/' + (r.imagen || '');
            var imgOnError = "this.onerror=null; this.src='{{ asset('/assets/images/user.png') }}';";

            var opcionesHtml =
                '<div class="d-flex gap-1">' +
                    '<button type="button" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#modal_agregar_cliente" ' +
                        'data-id="' + r.id + '" ' +
                        'data-pnombre="' + (r.primer_nombre || '') + '" ' +
                        'data-snombre="' + (r.segundo_nombre || '') + '" ' +
                        'data-papellido="' + (r.primer_apellido || '') + '" ' +
                        'data-sapellido="' + (r.segundo_apellido || '') + '" ' +
                        'data-identidad="' + (r.identidad || '') + '" ' +
                        'data-tel1="' + (r.contacto_telefonico || '') + '" ' +
                        'data-tel2="' + (r.contacto_telefonico_2 || '') + '" ' +
                        'data-email="' + (r.correo_electronico || '') + '" ' +
                        'data-dir="' + (r.direccion || '') + '">' +
                        '<i data-feather="edit-2" width="14" height="14"></i>' +
                    '</button> ' +
                    '<button type="button" class="btn btn-danger btn-xs btn_eliminar_cliente" ' +
                        'data-id="' + r.id + '" data-nombre="' + (r.nombre_completo || nombreCompleto) + '">' +
                        '<i data-feather="trash-2" width="14" height="14"></i>' +
                    '</button>' +
                '</div>';

            return [
                r.id,
                '<img class="wd-30 ht-30 rounded-circle" src="' + imgSrc + '" onerror="' + imgOnError + '">',
                r.nombre_completo || nombreCompleto,
                r.identidad || '',
                r.contacto_telefonico || '',
                opcionesHtml
            ];
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken() } });
            table = $('#tbl_clientes').DataTable({
                responsive: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
        });

        $("#btn_agregar_cliente").on("click", function() {
            accion = 1;
            id_cliente = null;
            rowIndexEditar = null;
            $("#modal_cliente_titulo").text("Registrar Cliente");
            $("#modal_agregar_cliente input, #modal_agregar_cliente textarea").val('');
        });

        $(document).on("click", "[data-bs-target='#modal_agregar_cliente']", function() {
            if ($(this).attr('id') == 'btn_agregar_cliente') return;
            accion = 2;
            id_cliente = $(this).data("id");
            $("#modal_cliente_titulo").text("Editar Cliente ID: " + id_cliente);
            $("#modal_cli_nombre1").val($(this).data("pnombre"));
            $("#modal_cli_nombre2").val($(this).data("snombre"));
            $("#modal_cli_apellido1").val($(this).data("papellido"));
            $("#modal_cli_apellido2").val($(this).data("sapellido"));
            $("#modal_cli_identidad").val($(this).data("identidad"));
            $("#modal_cli_tel1").val($(this).data("tel1"));
            $("#modal_cli_tel2").val($(this).data("tel2"));
            $("#modal_cli_email").val($(this).data("email"));
            $("#modal_cli_dir").val($(this).data("dir"));

            var tr = $(this).closest('tr');
            rowIndexEditar = table.row(tr).index();
        });

        $("#btn_guardar_cliente").on("click", function() {
            var primer_nombre = $("#modal_cli_nombre1").val();
            var primer_apellido = $("#modal_cli_apellido1").val();
            var identidad = $("#modal_cli_identidad").val();
            var contacto_telefonico = $("#modal_cli_tel1").val();
            var direccion = $("#modal_cli_dir").val();

            if (primer_nombre == null || primer_nombre == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Primer Nombre.'
                })
                return true;
            }

            if (primer_apellido == null || primer_apellido == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Primer Apellido.'
                })
                return true;
            }

            if (identidad == null || identidad == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Identidad.'
                })
                return true;
            }

            if (contacto_telefonico == null || contacto_telefonico == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Teléfono 1.'
                })
                return true;
            }

            if (direccion == null || direccion == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Dirección.'
                })
                return true;
            }

            if (!btn_activo) {
                return true;
            }

            btn_activo = false;

            var data = {
                _token: csrfToken(),
                id: id_cliente,
                primer_nombre: primer_nombre,
                segundo_nombre: $("#modal_cli_nombre2").val(),
                primer_apellido: primer_apellido,
                segundo_apellido: $("#modal_cli_apellido2").val(),
                identidad: identidad,
                contacto_telefonico: contacto_telefonico,
                contacto_telefonico_2: $("#modal_cli_tel2").val(),
                correo_electronico: $("#modal_cli_email").val(),
                direccion: direccion,
                accion: accion
            };

            $.ajax({
                type: "POST",
                url: "{{ url('/clientes/guardar') }}",
                data: data,
                success: function(res) {
                    if (res.msgError) {
                        btn_activo = true;
                        ToastLG.fire({
                            icon: 'error',
                            title: 'Error al Guardar',
                            html: res.msgError,
                            timer: null
                        });
                        return;
                    }
                    if (res.clientes_list) {
                        var nuevaFila = construirFilaCliente(res.clientes_list);
                        if (accion == 1) {
                            table.row.add(nuevaFila).draw();
                        } else if (accion == 2) {
                            table.row(rowIndexEditar).data(nuevaFila).draw();
                        }
                        feather.replace();
                    } else {
                        location.reload();
                    }
                    $("#modal_agregar_cliente").modal("hide");
                    btn_activo = true;
                    ToastLG.fire({
                        icon: 'success',
                        title: 'Datos Guardados',
                        html: res.msgSuccess,
                        timer: 2000
                    });
                },
                error: function() {
                    btn_activo = true;
                }
            });
        });

        $(document).on("click", ".btn_eliminar_cliente", function() {
            var id = $(this).data("id");
            var nombre = $(this).data("nombre");
            var tr = $(this).closest('tr');
            var rowIndexEliminar = table.row(tr).index();

            Swal.fire({
                title: '¿Eliminar cliente ' + nombre + '?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/clientes/guardar') }}",
                        data: { _token: csrfToken(), id: id, accion: 3 },
                        success: function(res) {
                            if (res.msgError) {
                                ToastLG.fire({
                                    icon: 'error',
                                    title: 'Error al Eliminar',
                                    html: res.msgError,
                                    timer: null
                                });
                                return;
                            }
                            table.row(rowIndexEliminar).remove().draw();
                            ToastLG.fire({
                                icon: 'success',
                                title: 'Cliente Eliminado',
                                html: res.msgSuccess,
                                timer: 2000
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
