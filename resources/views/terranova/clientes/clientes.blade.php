@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/cropperjs/cropper.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .nav-tabs .nav-link.active { border-bottom-color: var(--ins-azul) !important; color: var(--ins-azul) !important; }
        .nav-tabs .nav-link:hover { border-bottom-color: var(--ins-azul-claro) !important; }
        .table thead.bg-azul-oscuro th { font-weight: 500; font-size: 0.85rem; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card bg-azul text-white border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body position-relative" style="background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background: rgba(255,255,255,0.15);">
                                <i data-feather="users" width="28" height="28" class="text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Clientes</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Administración integral de clientes, referencias y beneficiarios</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="users" width="120" height="120" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i class="text-white icon-lg pb-3px" data-feather="users"></i> Clientes Registrados</h5>
                    <button type="button" class="btn btn-blanco btn-xs" id="btn_agregar_cliente" data-bs-toggle="modal" data-bs-target="#modal_agregar_cliente">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Cliente
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_clientes" border="1">
                            <thead class="bg-azul-oscuro">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Imagen</th>
                                    <th scope="col" class="text-white">Nombre Completo</th>
                                    <th scope="col" class="text-white">Identidad</th>
                                    <th scope="col" class="text-white">Teléfono</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
                <div class="modal-header bg-azul">
                    <h6 class="modal-title h6 text-white" id="modal_cliente_titulo">Registrar Cliente</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-line" id="clienteTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab" aria-controls="datos" aria-selected="true">Datos del Cliente</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="referencias-tab" data-bs-toggle="tab" data-bs-target="#referencias" type="button" role="tab" aria-controls="referencias" aria-selected="false">Referencias</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="beneficiarios-tab" data-bs-toggle="tab" data-bs-target="#beneficiarios" type="button" role="tab" aria-controls="beneficiarios" aria-selected="false">Beneficiarios</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="clienteTabContent">
                        <div class="tab-pane fade show active p-3" id="datos" role="tabpanel" aria-labelledby="datos-tab">
                            <div class="row mb-3">
                                <div class="col-12 text-center">
                                    <div class="position-relative d-inline-block">
                                        <img id="cli_img_preview" class="wd-100 ht-100 rounded-circle" src="{{ asset('/assets/images/placeholder_user.png') }}" style="object-fit:cover; border:3px solid var(--ins-azul);">
                                        <button type="button" class="btn btn-azul btn-sm position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" id="btn_cambiar_foto" style="width:30px;height:30px;padding:0;">
                                            <i data-feather="camera" width="14" height="14"></i>
                                        </button>
                                    </div>
                                    <input type="file" id="cli_img_input" accept="image/*" class="d-none">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3"><label class="form-label">Primer Nombre <span class="text-danger">*</span></label><input id="modal_cli_nombre1" class="form-control" type="text" placeholder="ej. Juan" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Segundo Nombre</label><input id="modal_cli_nombre2" class="form-control" type="text" placeholder="ej. Alberto" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Primer Apellido <span class="text-danger">*</span></label><input id="modal_cli_apellido1" class="form-control" type="text" placeholder="ej. Pérez" /></div>
                                <div class="col-md-3 mb-3"><label class="form-label">Segundo Apellido</label><input id="modal_cli_apellido2" class="form-control" type="text" placeholder="ej. López" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Identidad <span class="text-danger">*</span> <small class="text-muted">(sin guiones)</small></label><input id="modal_cli_identidad" class="form-control" type="text" placeholder="ej. 0801199901123" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Teléfono 1 <span class="text-danger">*</span></label><input id="modal_cli_tel1" class="form-control" type="text" placeholder="tel/cel" /></div>
                                <div class="col-md-4 mb-3"><label class="form-label">Teléfono 2</label><input id="modal_cli_tel2" class="form-control" type="text" placeholder="tel/cel" /></div>
                                <div class="col-md-12 mb-3"><label class="form-label">Correo</label><input id="modal_cli_email" class="form-control" type="email" placeholder="correo electrónico" /></div>
                                <div class="col-md-12 mb-3"><label class="form-label">Dirección <span class="text-danger">*</span></label><textarea id="modal_cli_dir" class="form-control" rows="2" placeholder="ej. Col. Kennedy, Bloque 5"></textarea></div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-3" id="referencias" role="tabpanel" aria-labelledby="referencias-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Referencias Personales</h6>
                                <button type="button" class="btn btn-azul btn-xs" id="btn_agregar_referencia"><i data-feather="plus" width="14" height="14"></i> Agregar Referencia</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" id="tbl_referencias">
                                    <thead class="bg-azul-oscuro">
                                        <tr>
                                            <th class="text-white">Nombre Completo</th>
                                            <th class="text-white">Teléfono</th>
                                            <th class="text-white">Dirección</th>
                                            <th class="text-white" style="width:60px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_referencias"></tbody>
                                </table>
                            </div>
                            <div class="card card-body bg-blanco-humo mt-2 d-none" id="form_referencia_container">
                                <div class="row">
                                    <div class="col-md-4 mb-2"><label class="form-label small">Nombre Completo <span class="text-danger">*</span></label><input id="ref_nombre" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Teléfono <span class="text-danger">*</span></label><input id="ref_telefono" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Dirección <span class="text-danger">*</span></label><input id="ref_direccion" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-12 mt-2">
                                        <button type="button" class="btn btn-azul btn-xs" id="btn_guardar_referencia"><i data-feather="check" width="14" height="14"></i> Agregar</button>
                                        <button type="button" class="btn btn-negro btn-xs" id="btn_cancelar_referencia"><i data-feather="x" width="14" height="14"></i> Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-3" id="beneficiarios" role="tabpanel" aria-labelledby="beneficiarios-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Beneficiarios</h6>
                                <button type="button" class="btn btn-azul btn-xs" id="btn_agregar_beneficiario"><i data-feather="plus" width="14" height="14"></i> Agregar Beneficiario</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm" id="tbl_beneficiarios">
                                    <thead class="bg-azul-oscuro">
                                        <tr>
                                            <th class="text-white">Nombre Completo</th>
                                            <th class="text-white">Identidad</th>
                                            <th class="text-white">Parentezco</th>
                                            <th class="text-white">Teléfono 1</th>
                                            <th class="text-white">Teléfono 2</th>
                                            <th class="text-white">Correo</th>
                                            <th class="text-white">Dirección</th>
                                            <th class="text-white" style="width:60px">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_beneficiarios"></tbody>
                                </table>
                            </div>
                            <div class="card card-body bg-blanco-humo mt-2 d-none" id="form_beneficiario_container">
                                <div class="row">
                                    <div class="col-md-4 mb-2"><label class="form-label small">Nombre Completo <span class="text-danger">*</span></label><input id="ben_nombre" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Identidad <span class="text-danger">*</span> <small class="text-muted">(sin guiones)</small></label><input id="ben_identidad" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Parentezco <span class="text-danger">*</span></label><input id="ben_parentezco" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Teléfono 1 <span class="text-danger">*</span></label><input id="ben_tel1" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Teléfono 2</label><input id="ben_tel2" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-md-4 mb-2"><label class="form-label small">Correo</label><input id="ben_email" class="form-control form-control-sm" type="email" /></div>
                                    <div class="col-md-12 mb-2"><label class="form-label small">Dirección <span class="text-danger">*</span></label><input id="ben_direccion" class="form-control form-control-sm" type="text" /></div>
                                    <div class="col-12 mt-2">
                                        <button type="button" class="btn btn-azul btn-xs" id="btn_guardar_beneficiario"><i data-feather="check" width="14" height="14"></i> Agregar</button>
                                        <button type="button" class="btn btn-negro btn-xs" id="btn_cancelar_beneficiario"><i data-feather="x" width="14" height="14"></i> Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-azul btn-xs" id="btn_guardar_cliente"><i data-feather="save" width="16" height="16"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cropper -->
    <div class="modal fade" id="modal_cropper" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header bg-azul">
                    <h6 class="modal-title h6 text-white">Recortar Foto</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body p-2">
                    <img id="cropping_image" src="" class="w-100">
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="14" height="14"></i> Cancelar</button>
                    <button type="button" class="btn btn-azul btn-xs" id="btn_confirmar_crop"><i data-feather="check" width="14" height="14"></i> Aplicar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/cropperjs/cropper.min.js') }}"></script>
    <script src="{{ asset('assets/js/alertas_propias.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        var table = null;
        var accion = 1;
        var id_cliente = null;
        var btn_activo = true;

        var referencias = [];
        var beneficiarios = [];
        var refTempId = 0;
        var benTempId = 0;
        var cropper = null;
        var cli_img_blob = null;

        function csrfToken() {
            return $('meta[name="csrf-token"]').attr('content');
        }

        function escHtml(str) {
            return $('<div>').text(str).html();
        }

        function renderReferencias() {
            var tbody = $('#tbody_referencias').empty();
            $.each(referencias, function(i, r) {
                if (r._eliminar) return;
                tbody.append(
                    '<tr>' +
                        '<td>' + (r.nombre_completo || '') + '</td>' +
                        '<td>' + (r.contacto_telefonico || '') + '</td>' +
                        '<td>' + (r.direccion || '') + '</td>' +
                        '<td class="text-center"><button type="button" class="btn btn-danger btn-xs btn_quitar_referencia" data-temp="' + r._temp + '"><i data-feather="trash-2" width="12" height="12"></i></button></td>' +
                    '</tr>'
                );
            });
            feather.replace();
        }

        function renderBeneficiarios() {
            var tbody = $('#tbody_beneficiarios').empty();
            $.each(beneficiarios, function(i, b) {
                if (b._eliminar) return;
                tbody.append(
                    '<tr>' +
                        '<td>' + (b.nombre_completo || '') + '</td>' +
                        '<td>' + (b.identidad || '') + '</td>' +
                        '<td>' + (b.parentezco || '') + '</td>' +
                        '<td>' + (b.contacto_telefonico || '') + '</td>' +
                        '<td>' + (b.contacto_telefonico_2 || '') + '</td>' +
                        '<td>' + (b.correo_electronico || '') + '</td>' +
                        '<td>' + (b.direccion || '') + '</td>' +
                        '<td class="text-center"><button type="button" class="btn btn-danger btn-xs btn_quitar_beneficiario" data-temp="' + b._temp + '"><i data-feather="trash-2" width="12" height="12"></i></button></td>' +
                    '</tr>'
                );
            });
            feather.replace();
        }

        function limpiarModal() {
            accion = 1;
            id_cliente = null;
            referencias = [];
            beneficiarios = [];
            refTempId = 0;
            benTempId = 0;
            cli_img_blob = null;
            $("#modal_cliente_titulo").text("Registrar Cliente");
            $("#modal_agregar_cliente input, #modal_agregar_cliente textarea").val('');
            $("#cli_img_preview").attr("src", "{{ asset('/assets/images/placeholder_user.png') }}");
            renderReferencias();
            renderBeneficiarios();
            $('#form_referencia_container').addClass('d-none');
            $('#form_beneficiario_container').addClass('d-none');
            $('.nav-tabs a:first').tab('show');
        }

        function cargarReferencias(clienteId) {
            $.ajax({
                type: "POST",
                url: "{{ url('/clientes/obtener-referencias') }}",
                data: { _token: csrfToken(), id_cliente: clienteId },
                success: function(res) {
                    referencias = (res.referencias || []).map(function(r) {
                        r._temp = --refTempId;
                        return r;
                    });
                    renderReferencias();
                }
            });
        }

        function cargarBeneficiarios(clienteId) {
            $.ajax({
                type: "POST",
                url: "{{ url('/clientes/obtener-beneficiarios') }}",
                data: { _token: csrfToken(), id_cliente: clienteId },
                success: function(res) {
                    beneficiarios = (res.beneficiarios || []).map(function(b) {
                        b._temp = --benTempId;
                        return b;
                    });
                    renderBeneficiarios();
                }
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken() } });
            table = $('#tbl_clientes').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ url('/clientes/datos') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'id', name: 'id' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var imgSrc = '{{ asset("storage/clientes/cli_") }}' + row.id + '/' + (row.imagen || '');
                            return '<img class="wd-30 ht-30 rounded-circle" src="' + imgSrc + '" onerror="this.onerror=null; this.src=\'{{ asset('/assets/images/placeholder_user.png') }}\';">';
                        }
                    },
                    { data: 'nombre_completo', name: 'nombre_completo' },
                    { data: 'identidad', name: 'identidad' },
                    { data: 'contacto_telefonico', name: 'contacto_telefonico' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var nombreCompleto = ((row.primer_nombre || '') + ' ' + (row.segundo_nombre || '') + ' ' + (row.primer_apellido || '') + ' ' + (row.segundo_apellido || '')).replace(/\s+/g, ' ').trim();
                            return '<div class="d-flex gap-1">' +
                                '<a href="{{ url('clientes/perfil') }}/' + row.id + '" class="btn btn-azul btn-xs">' +
                                    '<i data-feather="user" width="14" height="14"></i> Perfil' +
                                '</a> ' +
                                '<button type="button" class="btn btn-azul-claro btn-xs btn_editar_cliente" ' +
                                    'data-id="' + row.id + '" ' +
                                    'data-pnombre="' + escHtml(row.primer_nombre || '') + '" ' +
                                    'data-snombre="' + escHtml(row.segundo_nombre || '') + '" ' +
                                    'data-papellido="' + escHtml(row.primer_apellido || '') + '" ' +
                                    'data-sapellido="' + escHtml(row.segundo_apellido || '') + '" ' +
                                    'data-identidad="' + escHtml(row.identidad || '') + '" ' +
                                    'data-tel1="' + escHtml(row.contacto_telefonico || '') + '" ' +
                                    'data-tel2="' + escHtml(row.contacto_telefonico_2 || '') + '" ' +
                                    'data-email="' + escHtml(row.correo_electronico || '') + '" ' +
                                    'data-dir="' + escHtml(row.direccion || '') + '" ' +
                                    'data-imagen="' + (row.imagen || '') + '" ' +
                                    'data-bs-toggle="modal" data-bs-target="#modal_agregar_cliente">' +
                                    '<i data-feather="edit-2" width="14" height="14"></i> Editar' +
                                '</button> ' +
                                '<button type="button" class="btn btn-danger btn-xs btn_eliminar_cliente" ' +
                                    'data-id="' + row.id + '" data-nombre="' + nombreCompleto + '">' +
                                    '<i data-feather="trash-2" width="14" height="14"></i> Eliminar' +
                                '</button>' +
                            '</div>';
                        }
                    }
                ],
                order: [[2, 'asc']],
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
                drawCallback: function() {
                    feather.replace();
                }
            });
        });

        // Image upload and crop
        $("#btn_cambiar_foto").on("click", function() {
            $("#cli_img_input").click();
        });

        $("#cli_img_input").on("change", function(e) {
            var file = e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                $("#cropping_image").attr("src", e.target.result);
                $("#modal_cropper").modal("show");
                setTimeout(function() {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(document.getElementById("cropping_image"), {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1
                    });
                }, 500);
            };
            reader.readAsDataURL(file);
        });

        $("#btn_confirmar_crop").on("click", function() {
            if (!cropper) return;
            var canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
            canvas.toBlob(function(blob) {
                cli_img_blob = blob;
                $("#cli_img_preview").attr("src", canvas.toDataURL());
                $("#modal_cropper").modal("hide");
                if (cropper) { cropper.destroy(); cropper = null; }
            }, "image/jpeg", 0.8);
        });

        $("#modal_cropper").on("hidden.bs.modal", function() {
            if (cropper) { cropper.destroy(); cropper = null; }
        });

        $("#btn_agregar_cliente").on("click", function() {
            limpiarModal();
        });

        $(document).on("click", ".btn_editar_cliente", function() {
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

            referencias = [];
            beneficiarios = [];
            refTempId = 0;
            benTempId = 0;
            cli_img_blob = null;
            renderReferencias();
            renderBeneficiarios();
            cargarReferencias(id_cliente);
            cargarBeneficiarios(id_cliente);

            var imagen = $(this).data("imagen");
            if (imagen) {
                $("#cli_img_preview").attr("src", "{{ asset('storage/clientes/cli_') }}" + id_cliente + "/" + imagen);
            } else {
                $("#cli_img_preview").attr("src", "{{ asset('/assets/images/placeholder_user.png') }}");
            }
        });

        // Referencias
        $("#btn_agregar_referencia").on("click", function() {
            $('#form_referencia_container').removeClass('d-none');
            $('#ref_nombre, #ref_telefono, #ref_direccion').val('');
            $('#ref_nombre').focus();
        });

        $("#btn_cancelar_referencia").on("click", function() {
            $('#form_referencia_container').addClass('d-none');
        });

        $("#btn_guardar_referencia").on("click", function() {
            var nombre = $('#ref_nombre').val().trim();
            var telefono = $('#ref_telefono').val().trim();
            var direccion = $('#ref_direccion').val().trim();

            if (!nombre || !telefono || !direccion) {
                Toast.fire({ icon: 'error', title: 'Todos los campos de referencia son requeridos.' });
                return;
            }

            referencias.push({
                _temp: --refTempId,
                nombre_completo: nombre,
                contacto_telefonico: telefono,
                direccion: direccion
            });

            renderReferencias();
            $('#form_referencia_container').addClass('d-none');
        });

        $(document).on("click", ".btn_quitar_referencia", function() {
            var temp = parseInt($(this).data('temp'));
            $.each(referencias, function(i, r) {
                if (r._temp === temp) {
                    if (r.id) {
                        r._eliminar = true;
                    } else {
                        referencias.splice(i, 1);
                    }
                    return false;
                }
            });
            renderReferencias();
        });

        // Beneficiarios
        $("#btn_agregar_beneficiario").on("click", function() {
            $('#form_beneficiario_container').removeClass('d-none');
            $('#ben_nombre, #ben_identidad, #ben_parentezco, #ben_tel1, #ben_tel2, #ben_email, #ben_direccion').val('');
            $('#ben_nombre').focus();
        });

        $("#btn_cancelar_beneficiario").on("click", function() {
            $('#form_beneficiario_container').addClass('d-none');
        });

        $("#btn_guardar_beneficiario").on("click", function() {
            var nombre = $('#ben_nombre').val().trim();
            var identidad = $('#ben_identidad').val().trim();
            var parentezco = $('#ben_parentezco').val().trim();
            var tel1 = $('#ben_tel1').val().trim();
            var direccion = $('#ben_direccion').val().trim();

            if (!nombre || !identidad || !parentezco || !tel1 || !direccion) {
                Toast.fire({ icon: 'error', title: 'Los campos marcados con * son requeridos.' });
                return;
            }

            beneficiarios.push({
                _temp: --benTempId,
                nombre_completo: nombre,
                identidad: identidad,
                parentezco: parentezco,
                contacto_telefonico: tel1,
                contacto_telefonico_2: $('#ben_tel2').val().trim(),
                correo_electronico: $('#ben_email').val().trim(),
                direccion: direccion
            });

            renderBeneficiarios();
            $('#form_beneficiario_container').addClass('d-none');
        });

        $(document).on("click", ".btn_quitar_beneficiario", function() {
            var temp = parseInt($(this).data('temp'));
            $.each(beneficiarios, function(i, b) {
                if (b._temp === temp) {
                    if (b.id) {
                        b._eliminar = true;
                    } else {
                        beneficiarios.splice(i, 1);
                    }
                    return false;
                }
            });
            renderBeneficiarios();
        });

        // Guardar cliente con referencias y beneficiarios
        $("#btn_guardar_cliente").on("click", function() {
            var primer_nombre = $("#modal_cli_nombre1").val();
            var primer_apellido = $("#modal_cli_apellido1").val();
            var identidad = $("#modal_cli_identidad").val();
            var contacto_telefonico = $("#modal_cli_tel1").val();
            var direccion = $("#modal_cli_dir").val();

            if (primer_nombre == null || primer_nombre == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Primer Nombre.' });
                return true;
            }
            if (primer_apellido == null || primer_apellido == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Primer Apellido.' });
                return true;
            }
            if (identidad == null || identidad == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Identidad.' });
                return true;
            }
            if (contacto_telefonico == null || contacto_telefonico == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Teléfono 1.' });
                return true;
            }
            if (direccion == null || direccion == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Dirección.' });
                return true;
            }
            if (!btn_activo) {
                return true;
            }

            btn_activo = false;

            var refsGuardar = referencias.filter(function(r) { return !r.id && !r._eliminar; });
            var refsEliminar = referencias.filter(function(r) { return r.id && r._eliminar; }).map(function(r) { return r.id; });
            var bensGuardar = beneficiarios.filter(function(b) { return !b.id && !b._eliminar; });
            var bensEliminar = beneficiarios.filter(function(b) { return b.id && b._eliminar; }).map(function(b) { return b.id; });

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
                accion: accion,
                referencias: refsGuardar,
                referencias_eliminar: refsEliminar,
                beneficiarios: bensGuardar,
                beneficiarios_eliminar: bensEliminar,
                cambiar_imagen: cli_img_blob ? 'true' : 'false'
            };

            var useFormData = (cli_img_blob !== null);
            var ajaxData = useFormData ? new FormData() : data;

            if (useFormData) {
                $.each(data, function(key, value) {
                    if (Array.isArray(value)) {
                        $.each(value, function(i, item) {
                            if (typeof item === 'object') {
                                $.each(item, function(k, v) {
                                    ajaxData.append(key + '[' + i + '][' + k + ']', v);
                                });
                            } else {
                                ajaxData.append(key + '[]', item);
                            }
                        });
                    } else {
                        ajaxData.append(key, value);
                    }
                });
                ajaxData.append('archivoSeleccionado', cli_img_blob, 'cliente_' + Date.now() + '.jpg');
            }

            $.ajax({
                type: "POST",
                url: "{{ url('/clientes/guardar') }}",
                data: ajaxData,
                processData: !useFormData,
                contentType: useFormData ? false : undefined,
                success: function(res) {
                    if (res.msgError) {
                        btn_activo = true;
                        ToastLG.fire({ icon: 'error', title: 'Error al Guardar', html: res.msgError, timer: null });
                        return;
                    }
                    $("#modal_agregar_cliente").modal("hide");
                    btn_activo = true;
                    table.ajax.reload();
                    ToastLG.fire({ icon: 'success', title: 'Datos Guardados', html: res.msgSuccess, timer: 2000 });
                },
                error: function() {
                    btn_activo = true;
                }
            });
        });

        $(document).on("click", ".btn_eliminar_cliente", function() {
            var id = $(this).data("id");
            var nombre = $(this).data("nombre");

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
                                ToastLG.fire({ icon: 'error', title: 'Error al Eliminar', html: res.msgError, timer: null });
                                return;
                            }
                            table.ajax.reload();
                            ToastLG.fire({ icon: 'success', title: 'Cliente Eliminado', html: res.msgSuccess, timer: 2000 });
                        }
                    });
                }
            });
        });
    </script>
@endpush
