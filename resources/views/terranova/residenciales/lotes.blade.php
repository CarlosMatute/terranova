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
                                    <th scope="col" class="text-white">Area</th>
                                    <th scope="col" class="text-white">Precio</th>
                                    <th scope="col" class="text-white">Estado</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lotes as $row)
                                    <tr style="font-size: small">
                                        <td>{{ $row->id }}</td>
                                        <td><span class="badge bg-success">{{ $row->nombre }}</span></td>
                                        <td>{{ $row->area_formateado }}</td>
                                        <td>{{ $row->precio_formateado }}</td>
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
                                                    <button type="button" class="btn btn-warning btn-xs" data-bs-toggle="modal" data-bs-target="#modal_agregar_lote"
                                                        data-id="{{ $row->id }}" data-precio="{{ $row->precio }}" data-norte="{{ $row->norte }}" 
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
                        <div class="col-md-4" id="div_lote_siguiente">
                            <div class="mb-3">
                                <label class="form-label">Siguiente Lote</label>
                                <button type="button" class="btn btn-success form-control" id="lbl_lote_siguiente"></button>
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

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            table = $('#tbl_lotes').DataTable({
                responsive: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
            $('.select2').select2({ dropdownParent: $('#modal_reservar_lote') });
        });

        $("#btn_agregar_lote").on("click", function() {
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

        $("#btn_guardar_lote").on("click", function() {
            var data = {
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
                    if (res.msgError) Swal.fire('Error', res.msgError, 'error');
                    else Swal.fire('Éxito', res.msgSuccess, 'success').then(() => location.reload());
                }
            });
        });

        $(document).on("click", ".btn_eliminar_lote", function() {
            var id = $(this).data("id");
            var nombre = $(this).data("nombre");
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
                        data: { id: id, accion: 3 },
                        success: function(res) {
                            location.reload();
                        }
                    });
                }
            });
        });

        $(document).on("click", "[data-bs-target='#modal_reservar_lote']", function() {
            id_lote = $(this).data("id");
        });

        $("#btn_confirmar_reserva").on("click", function() {
            var id_cliente = $("#id_cliente_reservar").val();
            var hasta = $("#reservado_hasta").val();
            if (!id_cliente) return Swal.fire('Error', 'Seleccione un cliente', 'error');
            if (!hasta) return Swal.fire('Error', 'Seleccione la fecha de vencimiento', 'error');

            $.ajax({
                type: "POST",
                url: "{{ url('/residenciales/bloques/lotes/guardar') }}",
                data: { id: id_lote, id_cliente_reservar: id_cliente, reservado_hasta: hasta, accion: 4 },
                success: function(res) {
                    if (res.msgError) Swal.fire('Error', res.msgError, 'error');
                    else Swal.fire('Éxito', res.msgSuccess, 'success').then(() => location.reload());
                }
            });
        });

        $(document).on("click", ".btn_quitar_reserva", function() {
            var id = $(this).data("id");
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
