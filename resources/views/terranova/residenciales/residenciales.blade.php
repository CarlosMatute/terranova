@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/cropperjs/cropper.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
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
                                <i data-feather="home" width="28" height="28" class="text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Residenciales</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Registra y administra todas las residenciales, sus bloques y sus lotes</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="home" width="120" height="120" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">
                        <i class="text-white icon-lg pb-3px" data-feather="home"></i> Residenciales Registradas
                    </h5>
                    <button type="button" class="btn btn-blanco btn-xs" id="btn_agregar_residencial" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_residencial">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Residencial
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_residenciales" border="1">
                            <thead class="bg-azul-oscuro">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Imagen</th>
                                    <th scope="col" class="text-white">Nombre</th>
                                    <th scope="col" class="text-white">Descripcion</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($residenciales as $row)
                                    <tr style="font-size: small">
                                        <td scope="row">{{ $row->id }}</td>
                                        <td scope="row">
                                            <div class="me-3">
                                                <img class="wd-30 ht-30 rounded-circle"
                                                    src="{{ asset('storage/residenciales/res_' . $row->id . '/' . $row->imagen) }}"
                                                    alt="user"
                                                    onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                                            </div>
                                        </td>
                                        <td scope="row">{{ $row->nombre }}</td>
                                        <td scope="row">{{ $row->descripcion }}</td>
                                        <td scope="row">
                                            <button type="button" class="btn btn-azul-claro btn-xs btn_editar_residencial"
                                                data-bs-toggle="modal" data-bs-target=".modal_agregar_residencial"
                                                data-id="{{ $row->id }}" data-nombre="{{ $row->nombre }}"
                                                data-descripcion="{{ $row->descripcion }}"
                                                data-imagen="{{ $row->imagen }}">
                                                <i data-feather="check-square" width="16" height="16"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs"
                                                data-bs-toggle="modal" data-bs-target=".modal_eliminar_residencial"
                                                data-id="{{ $row->id }}" data-nombre="{{ $row->nombre }}"
                                                data-descripcion="{{ $row->descripcion }}">
                                                <i data-feather="trash-2" width="16" height="16"></i> Eliminar
                                            </button>
                                            <a href="{{ url('residenciales/' . $row->id . '/bloques') }}" class="btn btn-azul btn-xs" role="button" aria-pressed="true">
                                                <i data-feather="square" width="16" height="16"></i> Bloques
                                            </a>
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

    <div class="modal fade bd-example modal_agregar_residencial" id="modal_agregar_residencial" tabindex="-1"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-azul">
                    <h6 class="modal-title h6 text-white" id="myExtraLargeModalLabel"><i class="icon-lg pb-3px"
                            data-feather="plus"></i> Registrar Nueva Residencial</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-2 d-flex flex-column align-items-center justify-content-center" id="div_res_img_preview">
                                        <div class="position-relative d-inline-block">
                                            <img id="res_img_preview" class="wd-80 ht-80"
                                                src="{{ asset('/assets/images/homes.png') }}"
                                                style="object-fit:cover; border:3px solid var(--ins-azul);">
                                            <button type="button" class="btn btn-azul btn-sm position-absolute bottom-0 end-0 d-flex align-items-center justify-content-center" id="btn_cambiar_foto" style="width:30px;height:30px;padding:0;">
                                                <i data-feather="camera" width="14" height="14"></i>
                                            </button>
                                        </div>
                                        <label class="form-label mt-2" style="font-size:11px;">Imagen <small class="text-muted">(opcional)</small></label>
                                        <input type="file" id="res_img_input" accept="image/*" class="d-none">
                                    </div>
                                    <div class="col-md-6" id="div_modal_agregar_residencial_nombre">
                                        <div class="mb-3">
                                            <label for="modal_agregar_residencial_nombre" class="form-label">Nombre</label>
                                            <input id="modal_agregar_residencial_nombre" class="form-control"
                                                type="text" placeholder="Nombre de la residencial..." />
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="div_modal_agregar_residencial_bloques">
                                        <div class="mb-3">
                                            <label for="modal_agregar_residencial_bloques" class="form-label">Cantidad de
                                                Bloques</label>
                                            <input id="modal_agregar_residencial_bloques" class="form-control"
                                                type="number" placeholder="¿Cuantos bloques?" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label for="modal_agregar_residencial_descripcion"
                                                    class="form-label">Descripción</label>
                                            </div>
                                            <textarea class="form-control" name="tinymce" id="modal_agregar_residencial_descripcion" rows="4"
                                                placeholder="Escriba aquí..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-azul btn-xs" id="btn_guardar_residencial"><i data-feather="save" width="16" height="16"></i> Guardar</button>
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

    <div class="modal fade bd-example modal_eliminar_residencial" id="modal_eliminar_residencial" tabindex="-1"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title h4 text-white" id="myExtraLargeModalLabel"><i class="icon-lg pb-3px"
                            data-feather="x"></i> Eliminar Registro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="row">
                                <center>
                                    <i class="btn-icon-prepend text-warning" data-feather="alert-circle"
                                        style="width: 90px; height: 90px;"></i>
                                    <br><br>
                                    <div class="col-sm-12">
                                        <div class="mb-3">
                                            <h4><label class="form-label"><strong>¿Realmente deseas eliminar este
                                                        registro?</strong></label></h4>
                                            <br>
                                            <h5><label class="form-label"
                                                    id="modal_eliminar_residencial_informacion"></label></h5>
                                            <br>
                                            <p class="fw-normal">Este proceso no se puede revertir</p>
                                        </div>
                                    </div>
                                </center>
                            </div>
                            <!-- Row -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-danger btn-xs" id="btn_eliminar_residencial"><i data-feather="trash-2" width="16" height="16"></i> Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/cropperjs/cropper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('assets/js/alertas_propias.js') }}"></script>
@endpush

@push('custom-scripts')
    <script type="text/javascript">
        var table = null;
        var accion = null;
        var btn_activo = true;
        var id = null;
        var nombre = null;
        var bloques = null;
        var descripcion = null;
        var res_img_blob = null;
        var cropper = null;
        var url_guardar_residencial = "{{ url('/residenciales/guardar') }}";
        var rowNumber = null;
        var id_seleccionar = localStorage.getItem("tbl_residenciales_id_seleccionar");
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('#tbl_residenciales').DataTable({
                "aLengthMenu": [
                    [10, 30, 50, 100, -1],
                    [10, 30, 50, 100, "Todo"]
                ],
                "iDisplayLength": 10,
                responsive: true,
                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    infoPostFix: "",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    },
                    aria: {
                        sortAscending: ": Activar para ordenar la columna de manera ascendente",
                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
            $('#tbl_residenciales').each(function() {
                var datatable = $(this);
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Buscar');
                search_input.removeClass('form-control-sm');
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });

            $("#tbl_residenciales tbody").on("click", "tr", function() {
                let row = table.row(this);
                let data = row.data();
                if (!data) return;
                rowNumber = parseInt(row.index());
                accion = 2;
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                localStorage.setItem("tbl_residenciales_id_seleccionar", data[0]);
            });
        });

        // Image upload and crop
        $("#btn_cambiar_foto").on("click", function() {
            $("#res_img_input").click();
        });

        $("#res_img_input").on("change", function(e) {
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
                res_img_blob = blob;
                $("#res_img_preview").attr("src", canvas.toDataURL());
                $("#modal_cropper").modal("hide");
                if (cropper) { cropper.destroy(); cropper = null; }
            }, "image/jpeg", 0.8);
        });

        $("#modal_cropper").on("hidden.bs.modal", function() {
            if (cropper) { cropper.destroy(); cropper = null; }
        });

        function limpiarModalResidencial() {
            accion = 1;
            id = null;
            res_img_blob = null;
            $("#modal_agregar_residencial_nombre").val('');
            $("#modal_agregar_residencial_bloques").val('');
            $("#modal_agregar_residencial_descripcion").val('');
            $("#res_img_preview").attr("src", "{{ asset('/assets/images/homes.png') }}");
            $("#div_modal_agregar_residencial_bloques").show();
            $("#div_res_img_preview").show();
            $('#div_modal_agregar_residencial_nombre').removeClass('col-md-12').addClass('col-md-6');
            $('#div_modal_agregar_residencial_bloques').removeClass('col-md-12').addClass('col-md-4');
        }

        $(document).on("click", ".btn_editar_residencial", function() {
            accion = 2;
            id = $(this).data("id");
            nombre = $(this).data("nombre");
            descripcion = $(this).data("descripcion");
            var imagen = $(this).data("imagen");

            $("#modal_agregar_residencial_nombre").val(nombre);
            $("#modal_agregar_residencial_descripcion").val(descripcion);
            $("#modal_agregar_residencial_bloques").val('');
            $("#div_modal_agregar_residencial_bloques").hide();
            $("#div_res_img_preview").show();
            $('#div_modal_agregar_residencial_nombre').removeClass('col-md-6').addClass('col-md-10');
            res_img_blob = null;

            if (imagen) {
                $("#res_img_preview").attr("src", "{{ asset('storage/residenciales/res_') }}" + id + "/" + imagen);
            } else {
                $("#res_img_preview").attr("src", "{{ asset('/assets/images/homes.png') }}");
            }
        });

        $("#btn_agregar_residencial").on("click", function() {
            limpiarModalResidencial();
        });

        $("#modal_eliminar_residencial").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            nombre = triggerLink.data("nombre");
            descripcion = triggerLink.data("descripcion");
            $("#modal_eliminar_residencial_informacion").html(nombre);
        });

        $(".modal-footer").on("click", "#btn_eliminar_residencial", function() {
            accion = 3;
            if (btn_activo) {
                guardar_residencial();
            }
        });

        $("#btn_guardar_residencial").on("click", function() {
            nombre = $("#modal_agregar_residencial_nombre").val();
            bloques = $("#modal_agregar_residencial_bloques").val();
            descripcion = $("#modal_agregar_residencial_descripcion").val();

            if (nombre == null || nombre == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Nombre.' })
                return true;
            }

            if ((bloques == null || bloques == '') && accion == 1) {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Cantidad de Bloques.' })
                return true;
            }

            if ((bloques <= 0 || bloques > 26) && accion == 1) {
                Toast.fire({ icon: 'error', title: 'Debe ingresar una cantidad entre 1 y 26 para Bloques.' })
                return true;
            }

            if (descripcion == null || descripcion == '') {
                Toast.fire({ icon: 'error', title: 'Valor requerido para Descripción.' })
                return true;
            }

            if (btn_activo) {
                guardar_residencial();
            }
        });

        function guardar_residencial() {
            const formData = new FormData();
            formData.append('accion', accion);
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('bloques', bloques);
            formData.append('descripcion', descripcion);
            formData.append('cambiar_imagen', res_img_blob ? 'true' : 'false');
            if (res_img_blob) {
                formData.append('archivoSeleccionado', res_img_blob, 'residencial_' + Date.now() + '.jpg');
            }

            btn_activo = false;
            $.ajax({
                type: "post",
                url: url_guardar_residencial,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.msgError != null) {
                        titleMsg = "Error al Guardar";
                        textMsg = data.msgError;
                        typeMsg = "error";
                        timer = null;
                        btn_activo = true;
                    } else {
                        titleMsg = "Datos Guardados";
                        textMsg = data.msgSuccess;
                        typeMsg = "success";
                        timer = 2000;
                        if (accion == 1 || accion == 2) {
                            var row = data.residenciales_list;
                            var basePath = "{{ url('/') }}/storage/residenciales/res_" + row.id + "/";
                            var nuevaFilaDT = [
                                row.id,
                                '<div class="me-3">' +
                                    '<img class="wd-30 ht-30 rounded-circle" ' +
                                    'src="' + basePath + row.imagen + '" ' +
                                    'alt="img" ' +
                                    'onerror="this.onerror=null; this.src=\'' +
                                    "{{ url('/') }}/assets/images/homes.png" + '\';">' +
                                '</div>',
                                row.nombre,
                                '<div class="text-truncate" style="max-width:250px;">' + row.descripcion + '</div>',
                                '<div class="d-flex gap-1">' +
                                    '<button type="button" class="btn btn-azul-claro btn-xs btn_editar_residencial" ' +
                                    'data-bs-toggle="modal" data-bs-target=".modal_agregar_residencial" ' +
                                    'data-id="' + row.id + '" ' +
                                    'data-nombre="' + row.nombre + '" ' +
                                    'data-descripcion="' + row.descripcion + '" ' +
                                    'data-imagen="' + (row.imagen || '') + '">' +
                                        '<i data-feather="check-square" width="14" height="14"></i> Editar' +
                                    '</button> ' +
                                    '<button type="button" class="btn btn-danger btn-xs" ' +
                                    'data-bs-toggle="modal" data-bs-target=".modal_eliminar_residencial" ' +
                                    'data-id="' + row.id + '" ' +
                                    'data-nombre="' + row.nombre + '" ' +
                                    'data-descripcion="' + row.descripcion + '">' +
                                        '<i data-feather="trash-2" width="14" height="14"></i> Eliminar' +
                                    '</button> ' +
                                    '<a href="residenciales/' + row.id + '/bloques" ' +
                                    'class="btn btn-azul btn-xs">' +
                                        '<i data-feather="log-in" width="14" height="14"></i> Entrar' +
                                    '</a>' +
                                '</div>'
                            ];
                        }

                        if (accion == 1) {
                            table.row.add(nuevaFilaDT).draw();
                        } else if (accion == 2) {
                            table.row(rowNumber).data(nuevaFilaDT);
                        } else if (accion == 3) {
                            table.row(rowNumber).remove().draw();
                            $("#modal_eliminar_residencial").modal("hide");
                        }
                        $("#modal_agregar_residencial").modal("hide");
                        btn_activo = true;
                    }
                    ToastLG.fire({
                        icon: typeMsg,
                        title: titleMsg,
                        html: textMsg,
                        timer: timer
                    })
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                },
            });
        }
    </script>
@endpush
