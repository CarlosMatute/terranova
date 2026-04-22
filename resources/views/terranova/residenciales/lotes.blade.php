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
            <li class="breadcrumb-item"><a
                    href="{{ url('residenciales') }}/{{ $bloque->id_residencial }}/bloques">Bloques</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lotes</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('storage/residenciales/res_' . $bloque->id_residencial . '/' . $bloque->imagen) }}"
                            class="wd-90 ht-90 me-3" alt="..."
                            onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                        <div>
                            <h3 class="mb-2">Residencial: {{ $bloque->residencial }}</h3>
                            <h4 class="mb-2">Bloque: <span class="badge bg-primary">{{ $bloque->bloque }}</span></h4>
                            {{-- <p class="text-muted">{{ $bloque->descripcion }}</p> --}}
                        </div>
                    </div>
                    <hr>
                    <p>En este módulo puede registrar y administrar todos los lotes del bloque seleccionado.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-secondary">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">
                        <i class="text-white icon-lg pb-3px" data-feather="grid"></i> Lotes Registrados
                    </h5>
                    <button type="button" class="btn btn-light btn-xs" id="btn_agregar_bloque" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_lote">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Lote
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_lotes" border="1">
                            <thead class="bg-secondary">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Lote</th>
                                    <th scope="col" class="text-white">Area</th>
                                    <th scope="col" class="text-white">Colindancias</th>
                                    <th scope="col" class="text-white">Precio</th>
                                    <th scope="col" class="text-white">Financiamiento</th>
                                    <th scope="col" class="text-white">Estado</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lotes as $row)
                                    <tr style="font-size: small">
                                        <td scope="row">{{ $row->id }}</td>
                                        <td scope="row">
                                            <h4><span class="badge bg-success">{{ $row->nombre }}</span></h4>
                                        </td>
                                        <td scope="row">{{ $row->area_formateado }}</td>
                                        <td scope="row">{{ $row->colindancias }}</td>
                                        <td scope="row">{{ $row->precio_formateado }}</td>
                                        <td scope="row">{{ $row->anios_financiamiento_formateado }}</td>
                                        <td scope="row">
                                            <h6>
                                                @if ($row->id_cliente_reservar == null)
                                                    <span class="badge bg-success"><i data-feather="check" width="14"
                                                            height="14"></i> {{ $row->estado }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark"><i data-feather="clock"
                                                            width="14" height="14"></i> {{ $row->estado }}</span>
                                                @endif
                                            </h6>
                                        </td>
                                        <td scope="row">

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

    <div class="modal fade bd-example modal_agregar_lote" id="modal_agregar_lote" tabindex="-1"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h6 class="modal-title h6 text-white" id="myExtraLargeModalLabel"><i class="icon-lg pb-3px"
                            data-feather="plus"></i> @if($bloque->cantidad_lotes == 0) Registrar Lotes @else Registrar Nuevo Lote @endif</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    @if ($bloque->cantidad_lotes == 0)
                                    <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="modal_agregar_lote_nombre" class="form-label">Registro de Lotes</label>
                                                <button type="button" class="btn btn-success form-control"
                                                    >Masivo</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3" id="div_modal_agregar_lote_cantidad_lotes">
                                                <label for="modal_agregar_lote_cantidad_lotes" class="form-label">Cantidad
                                                    de
                                                    Lotes</label>
                                                <input id="modal_agregar_lote_cantidad_lotes" class="form-control"
                                                    type="number" placeholder="¿Cuantos lotes?" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3" id="div_modal_agregar_lote_precio_lotes">
                                                <label for="modal_agregar_lote_precio_lotes" class="form-label">Precio de los
                                                    Lotes</label>
                                                <input id="modal_agregar_lote_precio_lotes" class="form-control"
                                                    type="number" placeholder="¿Cual es el precio?" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="modal_agregar_lote_nombre" class="form-label">Siguiente
                                                    Lote</label>
                                                <button type="button" class="btn btn-success form-control"
                                                    id="modal_agregar_lote_siguiente"></button>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3" id="div_modal_agregar_lote_precio_lotes">
                                                <label for="modal_agregar_lote_precio_lotes" class="form-label">Precio del
                                                    Lote</label>
                                                <input id="modal_agregar_lote_precio_lotes" class="form-control"
                                                    type="number" placeholder="¿Cual es el precio?" />
                                            </div>
                                        </div>
                                    @endif


                                </div>
                                <div class="d-flex align-items-center my-4">
                                    <hr class="flex-grow-1 border-secondary">
                                    <span class="px-3 text-secondary">Colindancias</span>
                                    <hr class="flex-grow-1 border-secondary">
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_norte" class="form-label">Norte <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_lote_norte" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_sur" class="form-label">Sur <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_lote_sur" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_este" class="form-label">Este <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_lote_este" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_oeste" class="form-label">Oeste <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_lote_oeste" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_area" class="form-label">Area <span
                                                    class="text-muted">m²</span></label>
                                            <input id="modal_agregar_lote_area" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_lote_financiamiento" class="form-label">Años de
                                                financiamiento</label>
                                            <input id="modal_agregar_lote_financiamiento" class="form-control"
                                                type="number" placeholder="¿Cuántos años?" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x"
                            width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_guardar_bloque"><i data-feather="save"
                            width="16" height="16"></i> Guardar</button>
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
                                                        bloque?</strong></label></h4>
                                            <br>
                                            <h5><label class="form-label" id="modal_eliminar_bloque_informacion"></label>
                                            </h5>
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
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x"
                            width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_eliminar_bloque"><i
                            data-feather="trash-2" width="16" height="16"></i> Eliminar</button>
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
        var id_bloque_residencial = {{$bloque->id_bloque_residencial}};
        var bloque = null;
        var id_residencial = null;
        var cantidad_lotes = null;
        var precio_lote = null;
        var norte = null;
        var sur = null;
        var este = null;
        var oeste = null;
        var area = null;
        var financiamiento = null;
        var lote_siguiente = '{{ $lote_siguiente->nombre }}';
        var url_guardar_lote = "{{ url('/residenciales/bloques/lotes/guardar') }}";
        var rowNumber = null;
        var id_seleccionar = localStorage.getItem("tbl_lotes_id_seleccionar");
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('#tbl_lotes').DataTable({
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
            $('#tbl_lotes').each(function() {
                var datatable = $(this);
                // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Buscar');
                search_input.removeClass('form-control-sm');
                // LENGTH - Inline-Form control
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
            });

            $("#tbl_lotes tbody").on("click", "tr", function() {
                let row = table.row(this);
                let data = row.data();
                if (!data) return;
                rowNumber = parseInt(row.index());
                accion = 2;
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                localStorage.setItem("tbl_lotes_id_seleccionar", data[0]);
            });

        });

        $("#btn_agregar_bloque").on("click", function() {
            $("#modal_agregar_lote_siguiente").html(lote_siguiente);
            accion = 1;
        });

        $("#modal_agregar_lote").on("show.bs.modal", function(e) {
            $("#modal_agregar_lote_nombre").val('');
            $("#modal_agregar_lote_cantidad_lotes").val('');
            $("#modal_agregar_lote_precio_lotes").val('');
            $("#modal_agregar_lote_norte").val('');
            $("#modal_agregar_lote_sur").val('');
            $("#modal_agregar_lote_este").val('');
            $("#modal_agregar_lote_oeste").val('');
            $("#modal_agregar_lote_area").val('');
            $("#modal_agregar_lote_financiamiento").val('');
        });

        $("#modal_eliminar_bloque").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            bloque = triggerLink.data("bloque");
            $("#modal_eliminar_bloque_informacion").html('<h4><span class="badge bg-primary">' + bloque +
                '</span></h4>');
        });

        $(".modal-footer").on("click", "#btn_eliminar_bloque", function() {
            accion = 3;
            if (btn_activo) {
                guardar_bloque();
            }
        });

        $("#btn_guardar_bloque").on("click", function() {
            @if($bloque->cantidad_lotes == 0)
            cantidad_lotes = $("#modal_agregar_lote_cantidad_lotes").val();
            @else
            cantidad_lotes = 1;
            @endif
            precio_lote = $("#modal_agregar_lote_precio_lotes").val();
            norte = $("#modal_agregar_lote_norte").val();
            sur = $("#modal_agregar_lote_sur").val();
            este = $("#modal_agregar_lote_este").val();
            oeste = $("#modal_agregar_lote_oeste").val();
            area = $("#modal_agregar_lote_area").val();
            financiamiento = $("#modal_agregar_lote_financiamiento").val();

            if (cantidad_lotes == null || cantidad_lotes == '' || cantidad_lotes <= 0) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Cantidad de Lotes.'
                })
                return true;
            }

            if ((precio_lote == null || precio_lote == '' || precio_lote <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Precio del Lote.'
                })
                return true;
            }

            if ((norte == null || norte == '' || norte <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Colindancia Norte.'
                })
                return true;
            }

            if ((sur == null || sur == '' || sur <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Colindancia Sur.'
                })
                return true;
            }
            if ((este == null || este == '' || este <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Colindancia Este.'
                })
                return true;
            }
            if ((oeste == null || oeste == '' || oeste <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Colindancia Oeste.'
                })
                return true;
            }

            if ((area == null || area == '' || area <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Area del Bloque.'
                })
                return true;
            }

            if ((financiamiento == null || financiamiento == '' || financiamiento <= 0)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Valor requerido y mayor a cero para Años de Financiamiento.'
                })
                return true;
            }

            if (btn_activo) {
                guardar_bloque();
                //alert('Función de guardar en construcción');
            }

        });

        function guardar_bloque() {
            //espera('Enviando tu solicitud...');
            btn_activo = false;
            //console.log([...formData.entries()]);
            $.ajax({
                type: "post",
                url: url_guardar_lote,
                data: {
                    id: id,
                    id_bloque_residencial: id_bloque_residencial,
                    id_residencial: id_residencial,
                    cantidad_lotes: cantidad_lotes,
                    precio_lote: precio_lote,
                    norte: norte,
                    sur: sur,
                    este: este,
                    oeste: oeste,
                    area: area,
                    financiamiento: financiamiento,
                    accion: accion
                },
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

                        var bloque_siguiente_list = data.bloque_siguiente;
                        bloque_siguiente = bloque_siguiente_list.nombre;
                        $("#modal_agregar_lote_siguiente").html(bloque_siguiente);
                        id_bloque_siguiente = bloque_siguiente_list.id;
                        var agregarBotonDT = null;
                        var row = data.bloques_list;
                        var baseUrl = "{{ url('/') }}";
                        if (accion == 1 || accion == 2) {
                            var nuevaFilaDT = [
                                row.id,
                                '<h4><span class="badge bg-primary">' + row.bloque + '</span></h4>',
                                row.lotes,

                                '<div class="d-flex gap-1">' +

                                '<button type="button" class="btn btn-danger btn-xs" data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque" data-id="' +
                                row.id + '" data-bloque="' + row.bloque + '">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Eliminar' +
                                '</button>' +

                                '<a href="' + baseUrl + '/residenciales/' + id_residencial + '/bloques/' +
                                row.id +
                                '" class="btn btn-success btn-xs" role="button" aria-pressed="true">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> Lotes' +
                                '</a>' +
                                '</div>'
                            ];
                        }
                        if (accion == 1) {
                            table.row.add(nuevaFilaDT).draw();
                            var filas = table.rows().count();

                            var quitarBotonDT = '<div class="d-flex gap-1">' +

                                '<a href="' + baseUrl + '/residenciales/' + id_residencial +
                                '/bloques" class="btn btn-success btn-xs" role="button" aria-pressed="true">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg> Lotes' +
                                '</a>' +
                                '</div>';

                            table.cell(filas - 2, 3).data(quitarBotonDT).draw();
                        } else if (accion == 2) {
                            table.row(rowNumber).data(nuevaFilaDT);
                        } else if (accion == 3) {

                            var bloque_anterior_list = data.bloque_anterior;
                            console.log(bloque_anterior_list.bloque);
                            agregarBotonDT = [
                                '<div class="d-flex gap-1">' +

                                '<button type="button" class="btn btn-danger btn-xs" data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque" data-id="' +
                                bloque_anterior_list.id + '" data-bloque="' + bloque_anterior_list.bloque +
                                '">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Eliminar' +
                                '</button>' +

                                '<a href="' + baseUrl + '/residenciales/' + id_residencial +
                                '/bloques" class="btn btn-success btn-xs" role="button" aria-pressed="true">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg> Lotes' +
                                '</a>' +
                                '</div>'
                            ];

                            table.row(rowNumber).remove().draw();
                            table.cell(rowNumber - 1, 3).data(agregarBotonDT).draw();
                            $("#modal_eliminar_bloque").modal("hide");
                        }
                        $("#modal_agregar_lote").modal("hide");
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
