@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-2">Carrito de Ventas</h3>
                    <p class="text-muted">Procesa la venta de los lotes seleccionados.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Lotes Seleccionados</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Residencial</th>
                                    <th>Bloque</th>
                                    <th>Lote</th>
                                    <th>Precio</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total_lotes = 0; @endphp
                                @foreach ($lotes as $l)
                                    @php $total_lotes += $l->precio; @endphp
                                    <tr>
                                        <td>{{ $l->residencial }}</td>
                                        <td>{{ $l->bloque }}</td>
                                        <td>{{ $l->nombre }}</td>
                                        <td>{{ number_format($l->precio, 2) }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-xs btn_quitar" data-id="{{ $l->id_apartado }}">Quitar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Lotes:</th>
                                    <th>{{ number_format($total_lotes, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Calculadora de Venta</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select class="form-select select2" id="id_cliente">
                            <option value="">Seleccione un cliente</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Pago</label>
                        <select class="form-select" id="tipo_pago">
                            <option value="Contado">Contado</option>
                            <option value="Financiado">Financiado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Venta</label>
                        <input type="date" class="form-control" id="fecha_venta" value="{{ date('Y-m-d') }}">
                    </div>

                    <div id="seccion_financiamiento" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Años Financiamiento</label>
                            <input type="number" class="form-control" id="anios_financiamiento" value="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tasa Interés (%)</label>
                            <input type="number" class="form-control" id="tasa_interes" value="12">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prima</label>
                            <input type="text" class="form-control currency-input" id="prima" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Día de Cobro (1-28)</label>
                            <input type="number" class="form-control" id="dia_cobro_mes" value="1" min="1" max="28">
                        </div>
                        <hr>
                        <div class="mb-2"><strong>Cuotas:</strong> <span id="lbl_cuotas">0</span></div>
                        <div class="mb-2"><strong>Cuota Mensual:</strong> <span id="lbl_cuota_mensual">0.00</span></div>
                        <div class="mb-2"><strong>Total Intereses:</strong> <span id="lbl_total_intereses">0.00</span></div>
                    </div>

                    <div class="mt-3">
                        <h4>Total a Pagar: <span id="lbl_total_pagar">{{ number_format($total_lotes, 2) }}</span></h4>
                    </div>

                    <button class="btn btn-success w-100 mt-3" id="btn_procesar_venta">Procesar Venta</button>
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
        var total_contado = {{ $total_lotes }};
        var _raw_cuota_mensual = 0;
        var _raw_total_intereses = 0;
        var _raw_total_pagar = 0;
        
        $(document).ready(function() {
            $('.select2').select2();

            $('#tipo_pago').on('change', function() {
                if ($(this).val() == 'Financiado') {
                    $('#seccion_financiamiento').show();
                } else {
                    $('#seccion_financiamiento').hide();
                    recalcular();
                }
            });

            $('#anios_financiamiento, #tasa_interes, #prima').on('input', function() {
                recalcular();
            });

            function recalcular() {
                var tipo = $('#tipo_pago').val();
                if (tipo == 'Contado') {
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

            $('.btn_quitar').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('/ventas/desapartar') }}/" + id,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function() { location.reload(); }
                });
            });

            $('#btn_procesar_venta').on('click', function() {
                var esContado = $('#tipo_pago').val() == 'Contado';
                var data = {
                    id_cliente: $('#id_cliente').val(),
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
                };

                if (!data.id_cliente) {
                    Swal.fire('Error', 'Seleccione un cliente.', 'error');
                    return;
                }

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
