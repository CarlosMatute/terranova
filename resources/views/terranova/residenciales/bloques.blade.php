@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <style>
        .table thead.bg-azul-oscuro th { font-weight: 500; font-size: 0.85rem; }
    </style>
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
            <div class="card bg-azul text-white border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body position-relative" style="background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);">
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('storage/residenciales/res_' . $residencial->id . '/' . $residencial->imagen) }}"
                            class="wd-70 ht-70 rounded me-3 border border-white border-2" alt="..."
                            onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Residencial: {{ $residencial->nombre }}</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Gestión de bloques para esta residencial.</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="square" width="120" height="120" class="text-white"></i>
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
                        <i class="text-white icon-lg pb-3px" data-feather="square"></i> Bloques Registrados
                    </h5>
                    <button type="button" class="btn btn-blanco btn-xs" id="btn_agregar_bloque" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_bloque">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Bloque
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_bloques" border="1">
                            <thead class="bg-azul-oscuro">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Bloque</th>
                                    <th scope="col" class="text-white">LOTES</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bloques as $row)
                                    <tr style="font-size: small">
                                        <td>{{ $row->id }}</td>
                                        <td><h4><span class="badge bg-primary">{{ $row->bloque }}</span></h4></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="text-center px-2 py-1" style="background: var(--ins-azul-oscuro); border-radius: 6px; min-width: 55px;">
                                                    <div class="text-white fw-bold" style="font-size: 1rem;">{{ $row->total_lotes }}</div>
                                                    <div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Total</div>
                                                </div>
                                                <div class="text-center px-2 py-1" style="background: var(--ins-azul); border-radius: 6px; min-width: 55px;">
                                                    <div class="text-white fw-bold" style="font-size: 1rem;">{{ $row->vendidos }}</div>
                                                    <div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Vend.</div>
                                                </div>
                                                <div class="text-center px-2 py-1 text-white" style="background: #d4a017; border-radius: 6px; min-width: 55px;">
                                                    <div class="fw-bold" style="font-size: 1rem;">{{ $row->apartados }}</div>
                                                    <div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Apart.</div>
                                                </div>
                                                <div class="text-center px-2 py-1 text-white" style="background: #2e7d32; border-radius: 6px; min-width: 55px;">
                                                    <div class="fw-bold" style="font-size: 1rem;">{{ $row->disponibles }}</div>
                                                    <div style="font-size: 0.55rem; line-height: 1; opacity: 0.7;">Disp.</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if ($row->ultimo)
                                                    <button type="button" class="btn btn-danger btn-xs btn_eliminar_bloque" data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque" data-id="{{ $row->id }}" data-bloque="{{ $row->bloque }}">
                                                        <i data-feather="trash-2" width="14" height="14"></i> Eliminar
                                                    </button>
                                                @endif
                                                <a href="{{ url('residenciales/' . $residencial->id . '/bloques/' . $row->id) }}" class="btn btn-azul btn-xs">
                                                    <i data-feather="grid" width="14" height="14"></i> Lotes
                                                </a>
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

    <!-- Modal Bloque -->
    <div class="modal fade" id="modal_agregar_bloque" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-azul">
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
                                            <label for="modal_agregar_bloque_nombre" class="form-label">Siguiente
                                                Bloque</label>
                                            <button type="button" class="btn btn-azul form-control"
                                                id="modal_agregar_bloque_siguiente"></button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3" id="div_modal_agregar_bloque_cantidad_lotes">
                                            <label for="modal_agregar_bloque_cantidad_lotes" class="form-label">Cantidad
                                                de
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
                                            <label for="modal_agregar_bloque_norte" class="form-label">Norte <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_norte" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_sur" class="form-label">Sur <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_sur" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_este" class="form-label">Este <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_este" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_oeste" class="form-label">Oeste <span
                                                    class="text-muted">m</span></label>
                                            <input id="modal_agregar_bloque_oeste" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_area" class="form-label">Area <span
                                                    class="text-muted">m²</span></label>
                                            <input id="modal_agregar_bloque_area" class="form-control" type="number"
                                                placeholder="Ejm: 100" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="modal_agregar_bloque_financiamiento" class="form-label">Años de
                                                financiamiento</label>
                                            <input id="modal_agregar_bloque_financiamiento" class="form-control"
                                                type="number" placeholder="¿Cuántos años?" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x"
                            width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-azul btn-xs" id="btn_guardar_bloque"><i data-feather="save"
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
                <div class="modal-body" id="modal_eliminar_bloque_body">
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="row">
                                <center id="modal_eliminar_bloque_confirmacion">
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
                                <div id="modal_eliminar_bloque_lotes" style="display:none; width:100%; text-align:left;">
                                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                                        <i data-feather="alert-triangle" class="me-2" width="24" height="24"></i>
                                        <div>
                                            <strong>No se puede eliminar este bloque</strong><br>
                                            Tiene lotes vendidos o apartados.
                                        </div>
                                    </div>
                                    <h6 class="mb-3">Lotes con actividad:</h6>
                                    <div id="modal_eliminar_bloque_lista"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-azul-oscuro">
                    <button type="button" class="btn btn-negro btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-danger btn-xs" id="btn_eliminar_bloque"><i data-feather="trash-2" width="16" height="16"></i> Eliminar</button>
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
        var btn_activo = true;
        var url_guardar_bloque = "{{ url('/residenciales/bloques/guardar') }}";
        var id_residencial = {{ $residencial->id }};
        var id_bloque_siguiente = {{ $bloque_siguiente ? $bloque_siguiente->id : 0 }};
        var cantidad_lotes = 0, precio_lote = 0, norte = 0, sur = 0, este = 0, oeste = 0, area = 0, financiamiento = 0;
        var bloque_siguiente = "{{ $bloque_siguiente ? $bloque_siguiente->nombre : '' }}";
        var rowNumber = null, id = null, bloque = null, accion = 1;
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            table = $('#tbl_bloques').DataTable({
                responsive: true,
                order: [[1, 'asc']],
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

            function calcularArea() {
                var n = parseFloat($("#modal_agregar_bloque_norte").val()) || 0;
                var s = parseFloat($("#modal_agregar_bloque_sur").val()) || 0;
                var e = parseFloat($("#modal_agregar_bloque_este").val()) || 0;
                var w = parseFloat($("#modal_agregar_bloque_oeste").val()) || 0;
                if (n > 0 && s > 0 && e > 0 && w > 0) {
                    var calc = ((n + s) / 2) * ((e + w) / 2);
                    $("#modal_agregar_bloque_area").val(Math.round(calc * 100) / 100);
                }
            }

            $("#modal_agregar_bloque_norte, #modal_agregar_bloque_sur, #modal_agregar_bloque_este, #modal_agregar_bloque_oeste").on("input", calcularArea);

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

        $("#btn_agregar_bloque").on("click", function() {
            $("#modal_agregar_bloque_siguiente").html(bloque_siguiente);
            accion = 1;
        });

        $("#modal_agregar_bloque").on("show.bs.modal", function(e) {
            $("#modal_agregar_bloque_nombre").val('');
            $("#modal_agregar_bloque_cantidad_lotes").val('');
            $("#modal_agregar_bloque_precio_lotes").val('');
            $("#modal_agregar_bloque_norte").val('');
            $("#modal_agregar_bloque_sur").val('');
            $("#modal_agregar_bloque_este").val('');
            $("#modal_agregar_bloque_oeste").val('');
            $("#modal_agregar_bloque_area").val('');
            $("#modal_agregar_bloque_financiamiento").val('');
        });

        $("#modal_eliminar_bloque").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            bloque = triggerLink.data("bloque");
            $("#modal_eliminar_bloque_informacion").html('<h4><span class="badge bg-primary">' + bloque +
                '</span></h4>');
            var urlEstado = "{{ url('/residenciales/bloques') }}/" + id + "/estado-eliminacion";
            $.get(urlEstado, function(data) {
                if (data.puede_eliminar) {
                    $("#modal_eliminar_bloque_confirmacion").show();
                    $("#modal_eliminar_bloque_lotes").hide();
                    $("#btn_eliminar_bloque").prop("disabled", false);
                } else {
                    $("#modal_eliminar_bloque_confirmacion").hide();
                    $("#modal_eliminar_bloque_lotes").show();
                    $("#btn_eliminar_bloque").prop("disabled", true);
                    var html = '<div class="table-responsive"><table class="table table-sm table-bordered">' +
                        '<thead class="bg-azul-oscuro text-white"><tr><th>Lote</th><th>Estado</th></tr></thead><tbody>';
                    $.each(data.lotes, function(i, l) {
                        if (l.estado == 'Vendido' || l.estado == 'Apartado') {
                            var badge = (l.estado == 'Vendido')
                                ? '<span class="badge bg-azul">Vendido</span>'
                                : '<span class="badge bg-azul-claro">Apartado</span>';
                            html += '<tr><td>' + l.nombre + '</td><td>' + badge + '</td></tr>';
                        }
                    });
                    html += '</tbody></table></div>';
                    $("#modal_eliminar_bloque_lista").html(html);
                    feather.replace();
                }
            });
        });

        $(".modal-footer").on("click", "#btn_eliminar_bloque", function() {
            accion = 3;
            if (btn_activo) {
                guardar_bloque();
            }
        });

        $("#btn_guardar_bloque").on("click", function() {
            cantidad_lotes = $("#modal_agregar_bloque_cantidad_lotes").val();
            precio_lote = $("#modal_agregar_bloque_precio_lotes").val();
            norte = $("#modal_agregar_bloque_norte").val();
            sur = $("#modal_agregar_bloque_sur").val();
            este = $("#modal_agregar_bloque_este").val();
            oeste = $("#modal_agregar_bloque_oeste").val();
            area = $("#modal_agregar_bloque_area").val();
            financiamiento = $("#modal_agregar_bloque_financiamiento").val();

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
                url: url_guardar_bloque,
                data: {
                    id: id,
                    id_residencial: id_residencial,
                    id_bloque_siguiente: id_bloque_siguiente,
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
                        titleMsg = (accion == 3) ? "Registro Eliminado" : "Datos Guardados";
                        textMsg = data.msgSuccess;
                        typeMsg = "success";
                        timer = 2000;

                        var bloque_siguiente_list = data.bloque_siguiente;
                        if (bloque_siguiente_list) {
                            bloque_siguiente = bloque_siguiente_list.nombre;
                            $("#modal_agregar_bloque_siguiente").html(bloque_siguiente);
                            id_bloque_siguiente = bloque_siguiente_list.id;
                        }
                        var agregarBotonDT = null;
                        var row = data.bloques_list;
                        var baseUrl = "{{ url('/') }}";
                        if (accion == 1 || accion == 2) {
                            var nuevaFilaDT = [
                                row.id,
                                '<h4><span class="badge bg-primary">' + row.bloque + '</span></h4>',
                                '<div class="d-flex align-items-center gap-1">' +
                                    '<div class="text-center px-2 py-1" style="background: var(--ins-azul-oscuro); border-radius: 6px; min-width: 55px;">' +
                                        '<div class="text-white fw-bold" style="font-size: 1rem;">' + (row.total_lotes || 0) + '</div>' +
                                        '<div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Total</div>' +
                                    '</div>' +
                                    '<div class="text-center px-2 py-1" style="background: var(--ins-azul); border-radius: 6px; min-width: 55px;">' +
                                        '<div class="text-white fw-bold" style="font-size: 1rem;">' + (row.vendidos || 0) + '</div>' +
                                        '<div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Vend.</div>' +
                                    '</div>' +
                                    '<div class="text-center px-2 py-1 text-white" style="background: #d4a017; border-radius: 6px; min-width: 55px;">' +
                                        '<div class="fw-bold" style="font-size: 1rem;">' + (row.apartados || 0) + '</div>' +
                                        '<div class="text-white-50" style="font-size: 0.55rem; line-height: 1;">Apart.</div>' +
                                    '</div>' +
                                    '<div class="text-center px-2 py-1 text-white" style="background: #2e7d32; border-radius: 6px; min-width: 55px;">' +
                                        '<div class="fw-bold" style="font-size: 1rem;">' + (row.disponibles || 0) + '</div>' +
                                        '<div style="font-size: 0.55rem; line-height: 1; opacity: 0.7;">Disp.</div>' +
                                    '</div>' +
                                '</div>',

                                '<div class="d-flex gap-1">' +

                                '<button type="button" class="btn btn-danger btn-xs" data-bs-toggle="modal" data-bs-target=".modal_eliminar_bloque" data-id="' +
                                row.id + '" data-bloque="' + row.bloque + '">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Eliminar' +
                                '</button>' +

                                '<a href="' + baseUrl + '/residenciales/' + id_residencial + '/bloques/' +
                                row.id +
                                '" class="btn btn-azul btn-xs" role="button" aria-pressed="true">' +
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
                                '/bloques" class="btn btn-azul btn-xs" role="button" aria-pressed="true">' +
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
                                '/bloques" class="btn btn-azul btn-xs" role="button" aria-pressed="true">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg> Lotes' +
                                '</a>' +
                                '</div>'
                            ];

                            table.row(rowNumber).remove().draw();
                            table.cell(rowNumber - 1, 3).data(agregarBotonDT).draw();
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
