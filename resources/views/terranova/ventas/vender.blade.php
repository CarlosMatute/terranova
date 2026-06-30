@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card bg-azul text-white border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body position-relative" style="background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background: rgba(255,255,255,0.15);">
                                <i data-feather="shopping-cart" width="28" height="28" class="text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Vender</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Seleccione los lotes y procese el financiamiento o pago de contado.</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="shopping-cart" width="120" height="120" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Panel de Selección -->
        <div class="col-md-7">
            <div class="card border-azul mb-4">
                <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i data-feather="search" width="16" height="16"></i> Selección de Lotes</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Residencial</label>
                            <select class="form-select" id="sel_residencial">
                                <option value="">Seleccione un residencial...</option>
                                @foreach($residenciales as $r)
                                <option value="{{ $r->id }}" data-imagen="{{ $r->imagen }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bloque</label>
                            <select class="form-select" id="sel_bloque" disabled>
                                <option value="">Primero seleccione un residencial...</option>
                            </select>
                        </div>
                    </div>

                    <div id="lotes_container" style="display:none;">
                        <hr>
                        <h6 class="fw-semibold mb-2"><i data-feather="grid" width="16" height="16"></i> Lotes Disponibles</h6>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tbl_lotes_disponibles">
                                <thead class="bg-azul-oscuro text-white">
                                    <tr>
                                        <th class="text-white">Lote</th>
                                        <th class="text-white text-end">Precio</th>
                                        <th class="text-white text-end">Área</th>
                                        <th class="text-white text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <h6 class="fw-semibold mb-2"><i data-feather="shopping-cart" width="16" height="16"></i> Lotes Seleccionados</h6>
                    <div class="table-responsive">
                        <table class="table table-hover" id="tbl_lotes_venta">
                            <thead class="bg-azul-oscuro text-white">
                                <tr>
                                    <th class="text-white">Lote Seleccionado</th>
                                    <th class="text-white text-end">Precio</th>
                                    <th class="text-white text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th>Total Lotes:</th>
                                    <th id="lbl_total_lotes_lista" class="text-end">0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel de Calculadora -->
        <div class="col-md-5">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="pocket" width="16" height="16"></i> Plan de Venta</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select select2" id="id_cliente">
                            <option value="">Seleccione un cliente...</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Pago</label>
                            <select class="form-select" id="tipo_pago">
                                <option value="Contado">Contado</option>
                                <option value="Financiado">Financiado</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Venta</label>
                            <input type="date" class="form-control" id="fecha_venta" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div id="seccion_financiamiento" style="display:none;" class="p-3 bg-blanco-humo rounded border">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Años</label>
                                <input type="number" class="form-control" id="anios_financiamiento" value="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Interés (%)</label>
                                <input type="number" class="form-control" id="tasa_interes" value="12">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prima</label>
                                <input type="text" class="form-control currency-input" id="prima" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Día Cobro</label>
                                <input type="number" class="form-control" id="dia_cobro_mes" value="1" min="1" max="28">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-1"><span>Cuotas:</span> <strong id="lbl_cuotas">0</strong></div>
                        <div class="d-flex justify-content-between mb-1"><span>Cuota Mensual:</span> <strong id="lbl_cuota_mensual">0.00</strong></div>
                        <div class="d-flex justify-content-between mb-1"><span>Total Intereses:</span> <strong id="lbl_total_intereses">0.00</strong></div>
                    </div>

                    <div class="mt-4 p-3 bg-azul-oscuro text-white rounded">
                        <h4 class="mb-0 text-center">TOTAL A PAGAR: <br><span id="lbl_total_pagar">0.00</span></h4>
                    </div>

                    <button class="btn btn-azul btn-lg w-100 mt-4" id="btn_procesar_venta">
                        <i data-feather="check-circle" width="18" height="18"></i> PROCESAR VENTA
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        var lotes_seleccionados = [];
        var total_contado = 0;
        var _raw_cuota_mensual = 0;
        var _raw_total_intereses = 0;
        var _raw_total_pagar = 0;

        $(document).ready(function() {
            function formatCliente(cliente) {
                if (!cliente.id) return cliente.text;
                var imgSrc = '{{ asset("storage/clientes/cli_") }}' + cliente.id + '/' + (cliente.imagen || '');
                return '<img src="' + imgSrc + '" onerror="this.src=\'{{ asset("/assets/images/placeholder_user.png") }}\'" class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;"> <span>' + cliente.text + '</span>';
            }

            $('#id_cliente').select2({
                templateResult: formatCliente,
                templateSelection: formatCliente,
                escapeMarkup: function(m) { return m; },
                ajax: {
                    url: "{{ url('/clientes/buscar') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, page: params.page || 1 };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });

            function imgResidencial(id_residencial, imagen) {
                return '{{ asset("storage/residenciales/res_") }}' + (id_residencial || '') + '/' + (imagen || '');
            }

            $('#sel_residencial').select2({
                templateResult: function(opt) {
                    if (!opt.id) return opt.text;
                    var img = $(opt.element).data('imagen');
                    var src = imgResidencial(opt.id, img);
                    return $('<div class="d-flex align-items-center gap-2">' +
                        '<img src="' + src + '" onerror="this.src=\'{{ asset("/assets/images/homes.png") }}\'" ' +
                        'style="width:24px;height:24px;object-fit:cover;border-radius:3px;"> ' +
                        '<span>' + opt.text + '</span></div>');
                },
                templateSelection: function(opt) {
                    if (!opt.id) return opt.text;
                    var img = $(opt.element).data('imagen');
                    var src = imgResidencial(opt.id, img);
                    return $('<div class="d-flex align-items-center gap-1">' +
                        '<img src="' + src + '" onerror="this.src=\'{{ asset("/assets/images/homes.png") }}\'" ' +
                        'style="width:20px;height:20px;object-fit:cover;border-radius:3px;"> ' +
                        '<span>' + opt.text + '</span></div>');
                },
                escapeMarkup: function(m) { return m; }
            });

            $('#sel_residencial').on('change', function() {
                var id = $(this).val();
                $('#sel_bloque').val('').prop('disabled', true).html('<option value="">Cargando bloques...</option>');
                $('#lotes_container').hide().find('tbody').empty();
                if (!id) {
                    $('#sel_bloque').html('<option value="">Primero seleccione un residencial...</option>');
                    return;
                }
                $.getJSON('{{ url("/ventas/bloques-por-residencial") }}/' + id, function(data) {
                    var html = '<option value="">Seleccione un bloque...</option>';
                    $.each(data, function(i, b) {
                        html += '<option value="' + b.id + '">' + b.nombre + '</option>';
                    });
                    $('#sel_bloque').html(html).prop('disabled', false);
                });
            });

            $('#sel_bloque').on('change', function() {
                var id = $(this).val();
                $('#lotes_container').hide();
                if (window.dt_lotes) { window.dt_lotes.destroy(); window.dt_lotes = null; }
                $('#tbl_lotes_disponibles tbody').empty();
                if (!id) return;
                $.getJSON('{{ url("/ventas/lotes-por-br") }}/' + id, function(data) {
                    if (!data.length) {
                        $('#lotes_container').show();
                        $('#tbl_lotes_disponibles tbody').html('<tr><td colspan="4" class="text-center text-muted">No hay lotes disponibles en este bloque.</td></tr>');
                        return;
                    }
                    var html = '';
                    $.each(data, function(i, l) {
                        if (lotes_seleccionados.find(function(s) { return s.id == l.id; })) return;
                        html += '<tr>' +
                            '<td><span class="badge bg-primary">' + l.nombre + '</span></td>' +
                            '<td class="text-end">L ' + parseFloat(l.precio).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</td>' +
                            '<td class="text-end">' + l.area + '</td>' +
                            '<td class="text-center"><button class="btn btn-success btn-xs btn-agregar-lote" data-id="' + l.id + '" data-nombre="' + l.nombre + '" data-precio="' + l.precio + '"><i data-feather="plus" width="14" height="14"></i></button></td>' +
                            '</tr>';
                    });
                    $('#tbl_lotes_disponibles tbody').html(html);
                    window.dt_lotes = $('#tbl_lotes_disponibles').DataTable({
                        language: { url: "{{ asset('assets/plugins/datatables-net/i18n/Spanish.json') }}" },
                        pageLength: 10,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todo"]],
                        columns: [
                            null, null, null,
                            { orderable: false, searchable: false }
                        ]
                    });
                    $('#lotes_container').show();
                    feather.replace();
                });
            });

            $('#tbl_lotes_disponibles').on('click', '.btn-agregar-lote', function() {
                var btn = $(this);
                var id = btn.data('id');
                if (lotes_seleccionados.find(l => l.id == id)) return;
                var id_br = $('#sel_bloque').val();
                var id_res = $('#sel_residencial').val();
                var resNombre = $('#sel_residencial option:selected').text();
                var resImagen = $('#sel_residencial option:selected').data('imagen');
                var bloqueNombre = $('#sel_bloque option:selected').text();
                lotes_seleccionados.push({
                    id: id,
                    nombre: btn.data('nombre'),
                    bloque: bloqueNombre,
                    residencial: resNombre,
                    id_residencial: id_res,
                    imagen: resImagen,
                    precio: parseFloat(btn.data('precio'))
                });
                renderLista();
                if (window.dt_lotes) window.dt_lotes.row(btn.closest('tr')).remove().draw();
            });

            function renderLista() {
                var html = '';
                total_contado = 0;
                lotes_seleccionados.forEach(function(l, index) {
                    total_contado += l.precio;
                    var src = imgResidencial(l.id_residencial, l.imagen);
                    html += '<tr>' +
                        '<td><div class="d-flex align-items-center gap-2">' +
                        '<img src="' + src + '" onerror="this.src=\'{{ asset("/assets/images/homes.png") }}\'" style="width:28px;height:28px;object-fit:cover;border-radius:4px;">' +
                        '<div><strong>' + l.residencial + '</strong><br><small>Bloque <span class="badge bg-primary">' + l.bloque + '</span> - Lote <span class="badge bg-primary">' + l.nombre + '</span></small></div></div></td>' +
                        '<td class="text-end">' + l.precio.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</td>' +
                        '<td class="text-center"><button class="btn btn-link text-danger p-0" onclick="quitarLote(' + index + ')"><i data-feather="x" width="16" height="16"></i></button></td>' +
                        '</tr>';
                });
                $('#tbl_lotes_venta tbody').html(html);
                $('#lbl_total_lotes_lista').text(total_contado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                feather.replace();
                recalcular();
            }

            window.quitarLote = function(index) {
                var lote = lotes_seleccionados[index];
                lotes_seleccionados.splice(index, 1);
                renderLista();
                if ($('#sel_bloque').val()) {
                    var id_br = $('#sel_bloque').val();
                    $.getJSON('{{ url("/ventas/lotes-por-br") }}/' + id_br, function(data) {
                        var match = data.find(function(l) { return l.id == lote.id; });
                        if (match) {
                            var row = '<tr><td><span class="badge bg-primary">' + match.nombre + '</span></td>' +
                                '<td class="text-end">L ' + parseFloat(match.precio).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</td>' +
                                '<td class="text-end">' + match.area + '</td>' +
                                '<td class="text-center"><button class="btn btn-success btn-xs btn-agregar-lote" data-id="' + match.id + '" data-nombre="' + match.nombre + '" data-precio="' + match.precio + '"><i data-feather="plus" width="14" height="14"></i></button></td></tr>';
                            if (window.dt_lotes) {
                                window.dt_lotes.row.add($(row)[0]).draw();
                            } else {
                                $('#tbl_lotes_disponibles tbody').append(row);
                            }
                            feather.replace();
                        }
                    });
                }
            };

            $('#tipo_pago').on('change', function() {
                if ($(this).val() == 'Financiado') $('#seccion_financiamiento').show();
                else $('#seccion_financiamiento').hide();
                recalcular();
            });

            $('#anios_financiamiento, #tasa_interes, #prima').on('input', function() {
                recalcular();
            });

            function recalcular() {
                var tipo = $('#tipo_pago').val();
                if (tipo == 'Contado' || total_contado == 0) {
                    _raw_cuota_mensual = 0;
                    _raw_total_intereses = 0;
                    _raw_total_pagar = total_contado;
                    $('#lbl_cuotas').text(0);
                    $('#lbl_cuota_mensual').text('0.00');
                    $('#lbl_total_intereses').text('0.00');
                    $('#lbl_total_pagar').text(total_contado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                    return;
                }

                var anios = parseFloat($('#anios_financiamiento').val()) || 0;
                var tasa = (parseFloat($('#tasa_interes').val()) || 0) / 100;
                var prima = parseCurrency($('#prima').val());
                
                var capital = total_contado - prima;
                var cuotas = anios * 12;
                _raw_total_intereses = capital * tasa * anios;
                _raw_total_pagar = capital + _raw_total_intereses;

                var cuota_mensual_raw = (cuotas > 0) ? (_raw_total_pagar / cuotas) : 0;
                var cuota_mensual = Math.round(cuota_mensual_raw * 100) / 100;
                var suma_cuotas = cuota_mensual * cuotas;
                if (cuotas > 0 && suma_cuotas !== _raw_total_pagar) {
                    var residual = Math.round((_raw_total_pagar - suma_cuotas) * 100) / 100;
                    cuota_mensual = Math.round((cuota_mensual + residual) * 100) / 100;
                }
                _raw_cuota_mensual = cuota_mensual;

                $('#lbl_cuotas').text(cuotas);
                $('#lbl_cuota_mensual').text(cuota_mensual.toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#lbl_total_intereses').text(_raw_total_intereses.toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#lbl_total_pagar').text((_raw_total_pagar + prima).toLocaleString('en-US', {minimumFractionDigits: 2}));
            }

            $('#btn_procesar_venta').on('click', function() {
                if (lotes_seleccionados.length == 0) return Swal.fire('Error', 'Seleccione al menos un lote.', 'error');
                var id_cliente = $('#id_cliente').val();
                if (!id_cliente) return Swal.fire('Error', 'Seleccione un cliente.', 'error');

                var esContado = $('#tipo_pago').val() == 'Contado';
                var data = {
                    id_cliente: id_cliente,
                    tipo_pago: $('#tipo_pago').val(),
                    fecha_venta: $('#fecha_venta').val(),
                    total_contado: total_contado,
                    anios_financiamiento: esContado ? 0 : $('#anios_financiamiento').val(),
                    tasa_interes: esContado ? 0 : $('#tasa_interes').val(),
                    prima: esContado ? 0 : parseCurrency($('#prima').val()),
                    cuotas: esContado ? 0 : $('#lbl_cuotas').text(),
                    total_intereses: esContado ? 0 : _raw_total_intereses,
                    total_pagar: esContado ? total_contado : (_raw_total_pagar + parseCurrency($('#prima').val())),
                    cuota_mensual: esContado ? 0 : _raw_cuota_mensual,
                    dia_cobro_mes: esContado ? 1 : $('#dia_cobro_mes').val(),
                    lotes: lotes_seleccionados.map(l => l.id)
                };

                $.ajax({
                    type: "POST",
                    url: "{{ url('/ventas/guardar') }}",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: data,
                    success: function(res) {
                        if (res.msgError) Swal.fire('Error', res.msgError, 'error');
                        else Swal.fire('Éxito', res.msgSuccess, 'success').then(function() { location.href = "{{ url('/ventas') }}"; });
                    }
                });
            });
        });
    </script>
@endpush
