@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('residenciales') }}">Residenciales</a></li>
            <li class="breadcrumb-item"><a href="{{ url('residenciales/' . $residencial->id . '/bloques') }}">Bloques</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lotes</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('storage/residenciales/res_' . $residencial->id . '/' . $bloque->imagen) }}"
                            class="wd-90 ht-90 me-3" alt="..."
                            onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                        <div>
                            <h3 class="mb-2">Residencial: {{ $residencial->nombre }}</h3>
                            <h4 class="mb-2">Bloque: <span class="badge bg-primary">{{ $bloque->bloque }}</span></h4>
                            <p class="text-muted">Gestión de lotes para este bloque seleccionado.</p>
                        </div>
                    </div>
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
                    <button type="button" class="btn btn-light btn-xs" id="btn_agregar_lote" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_lote">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Lote
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_lotes" border="1">
                            <thead class="bg-secondary text-white">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Lote</th>
                                    <th scope="col" class="text-white">Colindancias</th>
                                    <th scope="col" class="text-white">Area</th>
                                    <th scope="col" class="text-white">Precio</th>
                                    <th scope="col" class="text-white">Financiamiento</th>
                                    <th scope="col" class="text-white">Estado</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lotes as $row)
                                    <tr style="font-size: small">
                                        <td>{{ $row->id }}</td>
                                        <td><span class="badge bg-success">{{ $row->nombre }}</span></td>
                                        <td><small class="text-muted">{{ $row->colindancias }}</small></td>
                                        <td>{{ $row->area_formateado }}</td>
                                        <td>{{ $row->precio_formateado }}</td>
                                        <td>{{ $row->anios_financiamiento_formateado }}</td>
                                        <td>
                                            @if ($row->estado == 'Vendido')
                                                <a href="{{ url('/ventas/detalle/' . $row->id_venta) }}" class="btn btn-danger btn-xs w-100">
                                                    <i data-feather="shopping-bag" width="14" height="14"></i> Vendido
                                                </a>
                                            @elseif ($row->estado == 'Reservado')
                                                <button type="button" class="btn btn-warning btn-xs w-100" onclick="Swal.fire('Reservado por:', '{{ $row->nombre_completo }}<br>Vence: {{ $row->reservado_hasta_formateado }}', 'info')">
                                                    <i data-feather="clock" width="14" height="14"></i> Reservado
                                                </button>
                                            @else
                                                <span class="badge bg-outline-success w-100 text-success">Disponible</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if ($row->estado != 'Vendido')
<button type="button" class="btn btn-warning btn-xs btn_editar_lote" data-bs-toggle="modal" data-bs-target="#modal_agregar_lote"
    data-id="{{ $row->id }}" data-nombre="{{ $row->nombre }}" data-precio="{{ $row->precio }}" data-norte="{{ $row->norte }}"
    data-sur="{{ $row->sur }}" data-este="{{ $row->este }}" data-oeste="{{ $row->oeste }}"
    data-area="{{ $row->area }}" data-financiamiento="{{ $row->anios_financiamiento }}">
                                                        <i data-feather="edit-2" width="14" height="14"></i> Editar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs btn_eliminar_lote" data-id="{{ $row->id }}" data-nombre="{{ $row->nombre }}">
                                                        <i data-feather="trash-2" width="14" height="14"></i> Eliminar
                                                    </button>

                                                    @if ($row->estado == 'Reservado')
                                                        <button type="button" class="btn btn-secondary btn-xs btn_quitar_reserva" data-id="{{ $row->id }}">
                                                            <i data-feather="user-x" width="14" height="14"></i> Quitar Reserva
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#modal_reservar_lote" data-id="{{ $row->id }}">
                                                            <i data-feather="user-plus" width="14" height="14"></i> Reservar
                                                        </button>
                                                    @endif
                                                @else
                                                    <button class="btn btn-light btn-xs disabled" disabled><i data-feather="lock" width="14" height="14"></i> Bloqueado</button>
                                                @endif
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

    <!-- Modal Agregar/Editar Lote -->
    <div class="modal fade" id="modal_agregar_lote" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title h6 text-white" id="modal_lote_titulo">Registrar Lote</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4" id="div_lote_siguiente" @if($bloque->cantidad_lotes == 0) style="display:none" @endif>
                            <div class="mb-3">
                                <label class="form-label" id="lbl_lote_titulo">Siguiente Lote</label>
                                <button type="button" class="btn btn-success form-control" id="lbl_lote_siguiente">{{ $lote_siguiente->nombre }}</button>
                            </div>
                        </div>
                        <div class="col-md-4" id="div_cantidad_lotes">
                            <div class="mb-3">
                                <label class="form-label">Cantidad a crear</label>
                                <input id="modal_lote_cantidad" class="form-control" type="number" value="1" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Precio del Lote</label>
                                <input id="modal_lote_precio" class="form-control" type="number" />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Colindancias y Area</h6>
                    <div class="row mt-2">
                        <div class="col-md-3 mb-3"><label class="form-label">Norte (m)</label><input id="modal_lote_norte" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Sur (m)</label><input id="modal_lote_sur" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Este (m)</label><input id="modal_lote_este" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label class="form-label">Oeste (m)</label><input id="modal_lote_oeste" class="form-control" type="number" /></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label class="form-label">Area (m²)</label><input id="modal_lote_area" class="form-control" type="number" /></div>
                        <div class="col-md-6 mb-3"><label class="form-label">Años Financiamiento</label><input id="modal_lote_financiamiento" class="form-control" type="number" /></div>   
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_guardar_lote"><i data-feather="save" width="16" height="16"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reservar Lote -->
    <div class="modal fade" id="modal_reservar_lote" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h6 class="modal-title h6 text-white"><i data-feather="user-plus" width="16" height="16"></i> Reservar Lote</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccione Cliente</label>
                        <select class="form-select select2" id="id_cliente_reservar" style="width: 100%">
                            <option value="">Seleccione...</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reservado Hasta</label>
                        <input type="date" class="form-control" id="reservado_hasta" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cancelar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_confirmar_reserva"><i data-feather="check" width="16" height="16"></i> Confirmar Reserva</button>       
                </div>
            </div>
        </div>
    </div>

@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        var table = null;
        var accion = 1;
        var id_lote = null;
        var id_bloque_residencial = {{ $bloque->id_bloque_residencial }};
        var cantidad_lotes_existentes = {{ $bloque->cantidad_lotes }};
        var rowIndexEditar = null;
        var rowIndexReservar = null;

        function construirFilaLote(r) {
            var estadoHtml = '';
            if (r.estado == 'Vendido') {
                estadoHtml = '<a href="{{ url('/ventas/detalle') }}/' + r.id_venta + '" class="btn btn-danger btn-xs w-100"><i data-feather="shopping-bag" width="14" height="14"></i> Vendido</a>';
            } else if (r.estado == 'Reservado') {
                var nombreComp = (r.nombre_completo || '').replace(/'/g, '&#39;');
                estadoHtml = '<button type="button" class="btn btn-warning btn-xs w-100" onclick="Swal.fire(\'Reservado por:\', \'' + nombreComp + '<br>Vence: ' + r.reservado_hasta_formateado + '\', \'info\')"><i data-feather="clock" width="14" height="14"></i> Reservado</button>';
            } else {
                estadoHtml = '<span class="badge bg-outline-success w-100 text-success">Disponible</span>';
            }

            var opcionesHtml = '';
            if (r.estado != 'Vendido') {
                opcionesHtml +=
                    '<div class="d-flex gap-1">' +
                    '<button type="button" class="btn btn-warning btn-xs btn_editar_lote" data-bs-toggle="modal" data-bs-target="#modal_agregar_lote" data-id="' + r.id + '" data-nombre="' + r.nombre + '" data-precio="' + r.precio + '" data-norte="' + r.norte + '" data-sur="' + r.sur + '" data-este="' + r.este + '" data-oeste="' + r.oeste + '" data-area="' + r.area + '" data-financiamiento="' + r.anios_financiamiento + '"><i data-feather="edit-2" width="14" height="14"></i> Editar</button>' +
                    '<button type="button" class="btn btn-danger btn-xs btn_eliminar_lote" data-id="' + r.id + '" data-nombre="' + r.nombre + '"><i data-feather="trash-2" width="14" height="14"></i> Eliminar</button>';

                if (r.estado == 'Reservado') {
                    opcionesHtml += '<button type="button" class="btn btn-secondary btn-xs btn_quitar_reserva" data-id="' + r.id + '"><i data-feather="user-x" width="14" height="14"></i> Quitar Reserva</button>';
                } else {
                    opcionesHtml += '<button type="button" class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#modal_reservar_lote" data-id="' + r.id + '"><i data-feather="user-plus" width="14" height="14"></i> Reservar</button>';
                }

                opcionesHtml += '</div>';
            } else {
                opcionesHtml = '<button class="btn btn-light btn-xs disabled" disabled><i data-feather="lock" width="14" height="14"></i> Bloqueado</button>';
            }

            return [
                r.id,
                '<span class="badge bg-success">' + r.nombre + '</span>',
                r.colindancias,
                r.area_formateado,
                r.precio_formateado,
                r.anios_financiamiento_formateado,
                estadoHtml,
                opcionesHtml
            ];
        }

        function csrfToken() {
            return $('meta[name="csrf-token"]').attr('content');
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken() } });
            table = $('#tbl_lotes').DataTable({
                responsive: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
            $('.select2').select2({ dropdownParent: $('#modal_reservar_lote') });
        });

        $("#btn_agregar_lote").on("click", function() {
            accion = 1;
        });

        $(document).on("click", ".btn_editar_lote", function() {
            var tr = $(this).closest('tr');
            rowIndexEditar = table.row(tr).index();
        });

        $("#modal_agregar_lote").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id_lote = triggerLink.data("id");

            if (id_lote) {
                accion = 2;
                $("#modal_lote_titulo").text("Editar Lote");
                $("#div_lote_siguiente").show();
                $("#div_cantidad_lotes").hide();
                $("#lbl_lote_titulo").text("Lote Actual");
                $("#lbl_lote_siguiente").text(triggerLink.data("nombre"));
                $("#lbl_lote_siguiente").removeClass("btn-success").addClass("btn-info");
                
                $("#modal_lote_precio").val(triggerLink.data("precio"));
                $("#modal_lote_norte").val(triggerLink.data("norte"));
                $("#modal_lote_sur").val(triggerLink.data("sur"));
                $("#modal_lote_este").val(triggerLink.data("este"));
                $("#modal_lote_oeste").val(triggerLink.data("oeste"));
                $("#modal_lote_area").val(triggerLink.data("area"));
                $("#modal_lote_financiamiento").val(triggerLink.data("financiamiento"));
            } else {
                accion = 1;
                $("#modal_lote_titulo").text("Registrar Lote");
                if (cantidad_lotes_existentes > 0) {
                    $("#div_lote_siguiente").show();
                }
                $("#div_cantidad_lotes").show();
                $("#lbl_lote_titulo").text("Siguiente Lote");
                $("#lbl_lote_siguiente").removeClass("btn-info").addClass("btn-success");
                $("#lbl_lote_siguiente").text('{{ $lote_siguiente->nombre }}');

                $("#modal_lote_cantidad").val('1');
                $("#modal_lote_precio").val('');
                $("#modal_lote_norte").val('');
                $("#modal_lote_sur").val('');
                $("#modal_lote_este").val('');
                $("#modal_lote_oeste").val('');
                $("#modal_lote_area").val('');
                $("#modal_lote_financiamiento").val('');
            }
        });

        $(document).on("input", "#modal_lote_norte, #modal_lote_sur, #modal_lote_este, #modal_lote_oeste", function() {
            var norte = parseFloat($("#modal_lote_norte").val()) || 0;
            var sur = parseFloat($("#modal_lote_sur").val()) || 0;
            var este = parseFloat($("#modal_lote_este").val()) || 0;
            var oeste = parseFloat($("#modal_lote_oeste").val()) || 0;

            var area = ((norte + sur) / 2) * ((este + oeste) / 2);
            $("#modal_lote_area").val(area.toFixed(2));
        });

        $("#modal_eliminar_bloque").on("show.bs.modal", function(e) {
            var triggerLink = $(e.relatedTarget);
            id = triggerLink.data("id");
            bloque = triggerLink.data("bloque");
            $("#modal_eliminar_bloque_informacion").html('<h4><span class="badge bg-primary">' + bloque +
                '</span></h4>');
        });

        $("#btn_guardar_lote").on("click", function() {
            var data = {
                _token: csrfToken(),
                id: id_lote,
                id_bloque_residencial: id_bloque_residencial,
                cantidad_lotes: $("#modal_lote_cantidad").val(),
                precio_lote: $("#modal_lote_precio").val(),
                norte: $("#modal_lote_norte").val(),
                sur: $("#modal_lote_sur").val(),
                este: $("#modal_lote_este").val(),
                oeste: $("#modal_lote_oeste").val(),
                area: $("#modal_lote_area").val(),
                financiamiento: $("#modal_lote_financiamiento").val(),
                accion: accion
            };

            $.ajax({
                type: "POST",
                url: "{{ url('/residenciales/bloques/lotes/guardar') }}",
                data: data,
                success: function(res) {
                    if (res.msgError) {
                        Swal.fire('Error', res.msgError, 'error');
                        return;
                    }

                    if (res.lote) {
                        var nuevaFila = construirFilaLote(res.lote);
                        if (accion == 1) {
                            table.row.add(nuevaFila).draw();
                            cantidad_lotes_existentes++;
                        } else if (accion == 2) {
                            table.row(rowIndexEditar).data(nuevaFila).draw();
                        }
                        feather.replace();
                    } else {
                        location.reload();
                    }

                    $("#modal_agregar_lote").modal("hide");
                    Swal.fire('Éxito', res.msgSuccess, 'success');
                }
            });
        });

        $(document).on("click", ".btn_eliminar_lote", function() {
            var id = $(this).data("id");
            var nombre = $(this).data("nombre");
            var tr = $(this).closest('tr');
            var rowIndexEliminar = table.row(tr).index();

            Swal.fire({
                title: '¿Eliminar lote ' + nombre + '?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/residenciales/bloques/lotes/guardar') }}",
                        data: { _token: csrfToken(), id: id, accion: 3 },
                        success: function(res) {
                            if (res.msgError) {
                                Swal.fire('Error', res.msgError, 'error');
                                return;
                            }
                            table.row(rowIndexEliminar).remove().draw();
                            Swal.fire('Éxito', res.msgSuccess, 'success');
                        }
                    });
                }
            });
        });

        $(document).on("click", "[data-bs-target='#modal_reservar_lote']", function() {
            id_lote = $(this).data("id");
            var tr = $(this).closest('tr');
            rowIndexReservar = table.row(tr).index();
        });

        $("#btn_confirmar_reserva").on("click", function() {
            var id_cliente = $("#id_cliente_reservar").val();
            var hasta = $("#reservado_hasta").val();
            if (!id_cliente) return Swal.fire('Error', 'Seleccione un cliente', 'error');
            if (!hasta) return Swal.fire('Error', 'Seleccione la fecha de vencimiento', 'error');

            $.ajax({
                type: "POST",
                url: "{{ url('/residenciales/bloques/lotes/guardar') }}",
                data: { _token: csrfToken(), id: id_lote, id_cliente_reservar: id_cliente, reservado_hasta: hasta, accion: 4 },
                success: function(res) {
                    if (res.msgError) {
                        Swal.fire('Error', res.msgError, 'error');
                        return;
                    }
                    if (res.lote) {
                        table.row(rowIndexReservar).data(construirFilaLote(res.lote)).draw();
                        feather.replace();
                    } else {
                        location.reload();
                    }
                    $("#modal_reservar_lote").modal("hide");
                    Swal.fire('Éxito', res.msgSuccess, 'success');
                }
            });
        });

        $(document).on("click", ".btn_quitar_reserva", function() {
            var id = $(this).data("id");
            var tr = $(this).closest('tr');
            var rowIndex = table.row(tr).index();

            $.ajax({
                type: "post",
                url: "{{ url('/residenciales/bloques/lotes/guardar') }}",
                data: { _token: csrfToken(), id: id, accion: 5 },
                success: function(res) {
                    if (res.msgError) {
                        Swal.fire('Error', res.msgError, 'error');
                        return;
                    }
                    if (res.lote) {
                        table.row(rowIndex).data(construirFilaLote(res.lote)).draw();
                        feather.replace();
                    } else {
                        location.reload();
                    }
                    Swal.fire('Éxito', res.msgSuccess, 'success');
                }
            });
        });
    </script>
@endpush
