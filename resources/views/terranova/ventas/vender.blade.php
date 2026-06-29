@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
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
                    <div class="mb-4">
                        <label class="form-label">Buscar Lote Disponible</label>
                        <select class="form-select select2" id="sel_lote_buscador">
                            <option value="">Buscar por bloque o nombre...</option>
                            @foreach ($lotes_disponibles as $l)
                                <option value="{{ $l->id }}" 
                                    data-nombre="{{ $l->lote }}" 
                                    data-bloque="{{ $l->bloque }}" 
                                    data-residencial="{{ $l->residencial }}" 
                                    data-precio="{{ $l->precio }}">
                                    {{ $l->residencial }} - Bloque {{ $l->bloque }} - Lote {{ $l->lote }} ({{ number_format($l->precio, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-azul mt-2 w-100" id="btn_agregar_lote_lista">
                            <i data-feather="plus" width="16" height="16"></i> Agregar a la venta
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="tbl_lotes_venta">
                            <thead class="bg-azul-oscuro text-white">
                                <tr>
                                    <th class="text-white">Lote Seleccionado</th>
                                    <th class="text-white">Precio</th>
                                    <th class="text-white"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dinámico -->
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th>Total Lotes:</th>
                                    <th id="lbl_total_lotes_lista">0.00</th>
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
            $('#sel_lote_buscador').select2();

            $('#btn_agregar_lote_lista').on('click', function() {
                var sel = $('#sel_lote_buscador').find(':selected');
                var id = sel.val();
                if (!id) return;

                if (lotes_seleccionados.find(l => l.id == id)) {
                    return Swal.fire('Aviso', 'El lote ya está en la lista.', 'warning');
                }

                var lote = {
                    id: id,
                    nombre: sel.data('nombre'),
                    bloque: sel.data('bloque'),
                    residencial: sel.data('residencial'),
                    precio: parseFloat(sel.data('precio'))
                };

                lotes_seleccionados.push(lote);
                renderLista();
                $('#sel_lote_buscador').val('').trigger('change');
            });

            function renderLista() {
                var html = '';
                total_contado = 0;
                lotes_seleccionados.forEach((l, index) => {
                    total_contado += l.precio;
                    html += `<tr>
                        <td>
                            <strong>${l.residencial}</strong><br>
                            <small>Bloque ${l.bloque} - Lote ${l.nombre}</small>
                        </td>
                        <td>${l.precio.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td>
                            <button class="btn btn-link text-danger p-0" onclick="quitarLote(${index})">
                                <i data-feather="x" width="16" height="16"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#tbl_lotes_venta tbody').html(html);
                $('#lbl_total_lotes_lista').text(total_contado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                feather.replace();
                recalcular();
            }

            window.quitarLote = function(index) {
                lotes_seleccionados.splice(index, 1);
                renderLista();
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
                        else Swal.fire('Éxito', res.msgSuccess, 'success').then(() => location.href = "{{ url('/ventas') }}");
                    }
                });
            });
        });
    </script>
@endpush
