@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/cropperjs/cropper.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/easymde/easymde.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
@endpush

@section('content')

    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('residenciales') }}">Residenciales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bloques</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('storage/residenciales/res_' . $residencial->id . '/' . $residencial->imagen) }}" class="wd-90 ht-90 me-3" alt="..." onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                        <div>
                            <h5 class="mb-2">Residencial: {{ $residencial->nombre }}</h5>
                            <p class="text-muted">{{ $residencial->descripcion }}</p>
                        </div>
                    </div>
                    <hr>
                            <p>En este módulo puede registrar y administrar todos los bloques de la residencial seleccionada.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-secondary">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">
                        <i class="text-white icon-lg pb-3px" data-feather="square"></i> Bloques Registrados
                    </h5>
                    <button type="button" class="btn btn-light btn-xs" id="btn_agregar_residencial" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_bloque">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Bloque
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_bloques" border="1">
                            <thead class="bg-secondary">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Bloque</th>
                                    <th scope="col" class="text-white">Lotes</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bloques as $row)
                                    <tr style="font-size: small">
                                        <td scope="row">{{ $row->id }}</td>
                                        <td scope="row">
                                            
                                            <h4><span class="badge bg-primary">{{ $row->bloque }}</span></h4>
                                        </td>
                                        <td scope="row">{{ $row->lotes }}</td>
                                        <td scope="row">
                                            <button type="button" class="btn btn-danger btn-xs"
                                                data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque"
                                                data-id="{{ $row->id }}" data-bloque="{{ $row->bloque }}">
                                                <i data-feather="trash-2" width="16" height="16"></i> Eliminar
                                            </button>
                                            <a href="{{ url('residenciales/' . $row->id . '/bloques') }}" class="btn btn-success btn-xs" role="button" aria-pressed="true">
                                                <i data-feather="log-in" width="16" height="16"></i> Lotes
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

    <div class="modal fade bd-example modal_agregar_bloque" id="modal_agregar_bloque" tabindex="-1"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h6 class="modal-title h6 text-white" id="myExtraLargeModalLabel"><i class="icon-lg pb-3px"
                            data-feather="plus"></i> Registrar Nuevo Bloque</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_nombre" class="form-label">Siguiente Bloque</label>
                                            <button type="button" class="btn btn-primary form-control">J</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3" id="div_modal_agregar_bloque_cantidad_lotes">
                                            <label for="modal_agregar_bloque_cantidad_lotes" class="form-label">Cantidad de
                                                Lotes</label>
                                            <input id="modal_agregar_bloque_cantidad_lotes" class="form-control"
                                                type="number" placeholder="¿Cuantos lotes?" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3" id="div_modal_agregar_bloque_precio_lotes">
                                            <label for="modal_agregar_bloque_precio_lotes" class="form-label">Precio de
                                                Lotes</label>
                                            <input id="modal_agregar_bloque_precio_lotes" class="form-control"
                                                type="number" placeholder="¿Cual es el precio?" />
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center my-4">
                                    <hr class="flex-grow-1 border-secondary">
                                        <span class="px-3 text-secondary">Colindancias</span>
                                    <hr class="flex-grow-1 border-secondary">
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_norte" class="form-label">Norte <span class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_norte" class="form-control"
                                                type="number" placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_sur" class="form-label">Sur <span class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_sur" class="form-control"
                                                type="number" placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_este" class="form-label">Este <span class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_este" class="form-control"
                                                type="number" placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_oeste" class="form-label">Oeste <span class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_oeste" class="form-control"
                                                type="number" placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_area" class="form-label">Area <span class="text-muted">m²</span></label>
                                            <input id="modal_agregar_bloque_area" class="form-control"
                                                type="number" placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_financiamiento" class="form-label">Años de financiamiento</label>
                                            <input id="modal_agregar_bloque_financiamiento" class="form-control"
                                                type="number" placeholder="¿Cuántos años?" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_guardar_residencial"><i data-feather="save" width="16" height="16"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example modal_eliminar_bloque" id="modal_eliminar_bloque" tabindex="-1"
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
                                                    id="modal_eliminar_bloque_informacion"></label></h5>
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
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_eliminar_residencial"><i data-feather="trash-2" width="16" height="16"></i> Eliminar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/cropperjs/cropper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/easymde/easymde.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/cropper.js') }}"></script>
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce.js') }}"></script>
    <script src="{{ asset('assets/js/easymde.js') }}"></script>
    <script src="{{ asset('assets/js/alertas_propias.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=mzutkZDE"></script>
    <script type="text/javascript">
        var table = null;
        var accion = null;
        var btn_activo = true;
        var id = null;
        var nombre = null;
        var bloques = null;
        var descripcion = null;
        var id_pagina = null;
        var id_permiso_requisito = null;
        var archivoSeleccionado = null;
        var cambiar_imagen = false;
        var url_guardar_residencial = "{{ url('/residenciales/guardar') }}";
        var rowNumber = null;
        var id_seleccionar = localStorage.getItem("tbl_bloques_id_seleccionar");
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('#tbl_bloques').DataTable({
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
            $('#tbl_bloques').each(function() {
                var datatable = $(this);
                // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Buscar');
                search_input.removeClass('form-control-sm');
                // LENGTH - Inline-Form control
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });

            $("#tbl_bloques tbody").on("click", "tr", function() {
                let row = table.row(this);
                let data = row.data();
                if (!data) return;
                rowNumber = parseInt(row.index());
                accion = 2;
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                localStorage.setItem("tbl_bloques_id_seleccionar", data[0]);
            });

        });

        $("#btn_agregar_residencial").on("click", function() {
            $("#div_modal_agregar_bloque_cantidad_lotes").show();
            $("#checkbox_cambiar_imagen").hide();
            $("#fileUpload").show();
            $('#div_modal_agregar_bloque_nombre').removeClass('col-md-12').addClass('col-md-10');
            accion = 1;
        });

        $("#modal_agregar_bloque").on("show.bs.modal", function(e) {
            $('#formSwitch1_cambiar_imagen').prop('checked', false);
            $("#div_modal_agregar_bloque_cantidad_lotes").hide();
            $("#checkbox_cambiar_imagen").show();
            $('#div_modal_agregar_bloque_nombre').removeClass('col-md-10').addClass('col-md-12');
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            nombre = triggerLink.data("nombre");
            descripcion = triggerLink.data("descripcion");
            archivoSeleccionado = null;
            $("#modal_agregar_bloque_nombre").val(nombre);
            $("#modal_agregar_bloque_cantidad_lotes").val('');
            $("#modal_agregar_bloque_precio_lotes").val(descripcion);
        });

        $("#modal_eliminar_bloque").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            nombre = triggerLink.data("nombre");
            descripcion = triggerLink.data("descripcion");
            $("#modal_eliminar_bloque_informacion").html(nombre);
        });

        $(".modal-footer").on("click", "#btn_eliminar_residencial", function() {
            accion = 3;
            if (btn_activo) {
                guardar_residencial();
            }
        });

        $("#btn_guardar_residencial").on("click", function() {
            nombre = $("#modal_agregar_bloque_nombre").val();
            bloques = $("#modal_agregar_bloque_cantidad_lotes").val();
            descripcion = $("#modal_agregar_bloque_precio_lotes").val();
            if ($('#formSwitch1_cambiar_imagen').is(':checked')) {
                cambiar_imagen = true;
            } else {
                cambiar_imagen = false;
            }

            if (nombre == null || nombre == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Nombre.'
                })
                return true;
            }

            if ((bloques == null || bloques == '') && accion == 1) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Cantidad de Bloques.'
                })
                return true;
            }

            if ((bloques <= 0 || bloques > 26) && accion == 1) {
                Toast.fire({
                    icon: 'error',
                    title: 'Debe ingresar una cantidad entre 1 y 26 para Bloques.'
                })
                return true;
            }

            if (descripcion == null || descripcion == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido para Descripción.'
                })
                return true;
            }

            if (btn_activo) {
                guardar_residencial();
                //alert('Función de guardar en construcción');
            }

        });

        function guardar_residencial() {
            //espera('Enviando tu solicitud...');
            const formData = new FormData();
            // Agregar otros campos
            formData.append('accion', accion);
            formData.append('id', id);
            formData.append('nombre', nombre);
            formData.append('bloques', bloques);
            formData.append('descripcion', descripcion);
            formData.append('cambiar_imagen', cambiar_imagen);
            if (archivoSeleccionado) {
                formData.append('archivoSeleccionado', archivoSeleccionado);
            }

            btn_activo = false;
            //console.log([...formData.entries()]);
            $.ajax({
                type: "post",
                url: url_guardar_residencial,
                data: formData,
                processData: false, // IMPORTANTE: evita que jQuery convierta los datos a string
                contentType: false, // IMPORTANTE: permite enviar multipart/form-data
                success: function(data) {
                    if (data.msgError != null) {
                        titleMsg = "Error al Guardar";
                        textMsg = data.msgError;
                        typeMsg = "error";
                        timer = null;
                        btn_activo = true;
                        timeout = data.timeout;
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

                                    // EDITAR
                                    '<button type="button" class="btn btn-warning btn-xs btn_editar_rol" ' +
                                    'data-bs-toggle="modal" data-bs-target=".modal_agregar_bloque" ' +
                                    'data-id="' + row.id + '" ' +
                                    'data-nombre="' + row.nombre + '" ' +
                                    'data-descripcion="' + row.descripcion + '">' +
                                        '<i data-feather="check-square" width="14" height="14"></i> Editar' +
                                    '</button> ' +

                                    // ELIMINAR
                                    '<button type="button" class="btn btn-danger btn-xs" ' +
                                    'data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque" ' +
                                    'data-id="' + row.id + '" ' +
                                    'data-nombre="' + row.nombre + '" ' +
                                    'data-descripcion="' + row.descripcion + '">' +
                                        '<i data-feather="trash-2" width="14" height="14"></i> Eliminar' +
                                    '</button> ' +

                                    // ENTRAR
                                    '<a href="residenciales/' + row.id + '/bloques" ' +
                                    'class="btn btn-dark btn-xs">' +
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
                            $("#modal_eliminar_bloque").modal("hide");
                        }
                        $("#modal_agregar_bloque").modal("hide");
                        btn_activo = true;
                    }
                    //console.log(textMsg);
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
