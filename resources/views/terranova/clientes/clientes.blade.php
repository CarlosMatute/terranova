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
                                                    data-id="{{ $row->id }}" data-identidad="{{ $row->identidad }}" data-tel1="{{ $row->contacto_telefonico }}"
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
@endpush

@push('custom-scripts')
    <script>
        var table = null;
        var accion = 1;
        var id_cliente = null;

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            table = $('#tbl_clientes').DataTable({
                responsive: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
        });

        $("#btn_agregar_cliente").on("click", function() {
            accion = 1;
            id_cliente = null;
            $("#modal_cliente_titulo").text("Registrar Cliente");
            $("#modal_agregar_cliente input, #modal_agregar_cliente textarea").val('');
        });

        $(document).on("click", "[data-bs-target='#modal_agregar_cliente']", function() {
            if ($(this).attr('id') == 'btn_agregar_cliente') return;
            accion = 2;
            id_cliente = $(this).data("id");
            $("#modal_cliente_titulo").text("Editar Cliente ID: " + id_cliente);
            $("#modal_cli_identidad").val($(this).data("identidad"));
            $("#modal_cli_tel1").val($(this).data("tel1"));
            $("#modal_cli_tel2").val($(this).data("tel2"));
            $("#modal_cli_email").val($(this).data("email"));
            $("#modal_cli_dir").val($(this).data("dir"));
        });

        $("#btn_guardar_cliente").on("click", function() {
            var data = {
                id: id_cliente,
                primer_nombre: $("#modal_cli_nombre1").val(),
                segundo_nombre: $("#modal_cli_nombre2").val(),
                primer_apellido: $("#modal_cli_apellido1").val(),
                segundo_apellido: $("#modal_cli_apellido2").val(),
                identidad: $("#modal_cli_identidad").val(),
                contacto_telefonico: $("#modal_cli_tel1").val(),
                contacto_telefonico_2: $("#modal_cli_tel2").val(),
                correo_electronico: $("#modal_cli_email").val(),
                direccion: $("#modal_cli_dir").val(),
                accion: accion
            };

            $.ajax({
                type: "POST",
                url: "{{ url('/clientes/guardar') }}",
                data: data,
                success: function(res) {
                    if (res.msgError) Swal.fire('Error', res.msgError, 'error');
                    else Swal.fire('Éxito', res.msgSuccess, 'success').then(() => location.reload());
                }
            });
        });

        $(document).on("click", ".btn_eliminar_cliente", function() {
            var id = $(this).data("id");
            Swal.fire({
                title: '¿Eliminar cliente?',
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
                        data: { id: id, accion: 3 },
                        success: function(res) { location.reload(); }
                    });
                }
            });
        });
    </script>
@endpush
