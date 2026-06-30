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
            <li class="breadcrumb-item"><a href="{{ url('ventas') }}">Ventas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalle de Venta #{{ $venta->id }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card bg-azul text-white border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body position-relative" style="background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background: rgba(255,255,255,0.15);">
                                <i data-feather="file-text" width="28" height="28" class="text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Detalle de Venta #{{ $venta->id }}</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Información detallada de la venta, lotes adquiridos y plan de pagos.</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="file-text" width="120" height="120" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-azul mb-4">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="info" width="16" height="16"></i> Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                        <img src="{{ asset('storage/clientes/cli_' . $venta->id_cliente . '/' . $venta->cliente_imagen) }}" 
                            class="rounded-circle me-3 border" style="width: 48px; height: 48px; object-fit: cover;"
                            onerror="this.onerror=null; this.src='{{ asset('/assets/images/placeholder_user.png') }}';">
                        <div>
                            <strong class="d-block">{{ $venta->cliente_nombre }}</strong>
                            <small class="text-muted">Cliente</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2"><span>Fecha Venta:</span> <strong>{{ $venta->fecha_venta }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Tipo de Pago:</span> <strong>{{ $venta->tipo_pago }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Estado:</span> <span class="badge {{ $venta->estado == 'Pagado' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $venta->estado }}</span></div>
                    @if ($venta->tipo_pago == 'Financiado')
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-2"><span>Años:</span> <strong>{{ $venta->anios_financiamiento }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Tasa Interés:</span> <strong>{{ $venta->tasa_interes }}%</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Cuotas:</span> <strong>{{ $venta->cuotas }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Cuota Mensual:</span> <strong>L. {{ number_format($venta->cuota_mensual, 2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Día Cobro:</span> <strong>{{ $venta->dia_cobro_mes }}</strong></div>
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-2"><span>Total Contado:</span> <strong>L. {{ number_format($venta->total_contado, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Prima:</span> <strong>L. {{ number_format($venta->prima, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Intereses:</span> <strong>L. {{ number_format($venta->total_intereses, 2) }}</strong></div>
                    <hr>
                    <h4 class="text-center text-azul">Total: L. {{ number_format($venta->total_pagar, 2) }}</h4>
                </div>
            </div>

            <div class="card border-azul">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="grid" width="16" height="16"></i> Lotes</h5>
                </div>
                <div class="card-body p-2">
                    @foreach ($lotes as $l)
                        <div class="d-flex align-items-center p-2 border-bottom">
                            <img src="{{ asset('storage/residenciales/res_' . $l->id_residencial . '/' . $l->residencial_imagen) }}" 
                                class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;"
                                onerror="this.onerror=null; this.src='{{ asset('/assets/images/homes.png') }}';">
                            <div class="flex-grow-1">
                                <a href="{{ url('/residenciales/' . $l->id_residencial . '/bloques/' . $l->id_bloque) }}" class="text-decoration-none text-dark fw-bold">{{ $l->residencial }}</a>
                                <br>
                                <small>Bloque <span class="badge bg-primary">{{ $l->bloque }}</span> - Lote <span class="badge bg-primary">{{ $l->nombre }}</span></small>
                            </div>
                            <div class="text-end text-azul fw-bold" style="font-size: 0.85rem;">L. {{ number_format($l->precio, 2) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i data-feather="calendar" width="16" height="16"></i> Plan de Pagos</h5>
                    <button class="btn btn-light btn-sm" id="btn_abonar"><i data-feather="plus" width="14" height="14"></i> Abonar</button>
                </div>
                <div class="card-body">
                    <div class="px-2 py-2 bg-light rounded mb-2 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i data-feather="check-circle" width="14" height="14" class="text-success"></i> <strong>Cobrado:</strong> L. <span class="text-success fw-bold" id="lbl_total_cobrado">0.00</span></span>
                            <span><i data-feather="clock" width="14" height="14" class="text-warning"></i> <strong>Pendiente:</strong> L. <span class="text-warning fw-bold" id="lbl_total_pendiente">0.00</span></span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar bg-success" id="barra_progreso" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_plan_pagos" border="1">
                            <thead class="bg-azul-oscuro text-white">
                                <tr>
                                    <th class="text-white">#</th>
                                    <th class="text-white">Fecha Cobro</th>
                                    <th class="text-white">Estado</th>
                                    <th class="text-white">Valor Pagado</th>
                                    <th class="text-white">Fecha Pago</th>
                                    <th class="text-white">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cobros as $index => $c)
                                    <tr style="font-size: small" data-id="{{ $c->id }}" data-cuota-mensual="{{ $venta->cuota_mensual }}" data-pendiente="{{ $c->estado == 'Cola' ? ($venta->cuota_mensual - $c->cantidad_pago) : ($c->estado == 'Pagado' ? 0 : $venta->cuota_mensual) }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $c->fecha_cobro }}</td>
                                        <td>
                                            <span class="badge {{ $c->estado == 'Pagado' ? 'bg-success' : ($c->estado == 'Cola' ? 'bg-info text-dark' : ($c->estado == 'Atrasado' ? 'bg-danger' : 'bg-warning text-dark')) }}">
                                                {{ $c->estado }}
                                            </span>
                                        </td>
                                        <td>{{ $c->cantidad_pago ? number_format($c->cantidad_pago, 2) : '-' }}</td>
                                        <td>{{ $c->fecha_pago ?? '-' }}</td>
                                        <td>
                                            @if ($c->estado == 'Cola' && $c->id == $ultima_cuota_pagada_id)
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-azul btn-xs btn_pagar" data-id="{{ $c->id }}">
                                                        <i data-feather="check" width="12" height="12"></i> Cobrar
                                                    </button>
                                                    <button class="btn btn-danger btn-xs btn_revertir" data-id="{{ $c->id }}">
                                                        <i data-feather="rotate-ccw" width="12" height="12"></i> Revertir
                                                    </button>
                                                </div>
                                            @elseif ($c->estado == 'Cola')
                                                <button class="btn btn-azul btn-xs btn_pagar" data-id="{{ $c->id }}">
                                                    <i data-feather="check" width="12" height="12"></i> Cobrar
                                                </button>
                                            @elseif ($c->estado != 'Pagado')
                                                <button class="btn btn-azul btn-xs btn_pagar" data-id="{{ $c->id }}">
                                                    <i data-feather="check" width="14" height="14"></i> Cobrar
                                                </button>
                                            @elseif ($c->id == $ultima_cuota_pagada_id)
                                                <button class="btn btn-danger btn-xs btn_revertir" data-id="{{ $c->id }}">
                                                    <i data-feather="rotate-ccw" width="14" height="14"></i> Revertir
                                                </button>
                                            @else
                                                <span></span>
                                            @endif
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
<div class="modal fade" id="modalAbonar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-azul text-white">
                <h6 class="modal-title text-white">Abonar a Venta #{{ $venta->id }}</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Monto a abonar (L.)</label>
                <input type="text" class="form-control form-control-lg currency-input" id="input_monto_abono" placeholder="0.00">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-azul" id="btn_procesar_abono"><i data-feather="check" width="14" height="14"></i> Aplicar Abono</button>
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
        $(document).ready(function() {
            $('#tbl_plan_pagos').DataTable({
                language: { url: "{{ asset('assets/plugins/datatables-net/i18n/Spanish.json') }}" }
            });
            actualizarBalance();
            actualizarBotones();

            $(document).on('click', '.btn_pagar', function() {
                var btn = $(this);
                var id = btn.data('id');
                Swal.fire({
                    title: '¿Confirmar cobro?',
                    text: "Se registrará el cobro de esta cuota.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cobrar'
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/ventas/pagar-cuota') }}",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { id: id },
                        success: function(res) {
                            if (res.msgError) {
                                Swal.fire('Error', res.msgError, 'error');
                                return;
                            }
                            var row = btn.closest('tr');
                            var hoy = new Date().toISOString().split('T')[0];
                            var yaPagado = parseFloat(row.find('td:eq(3)').text().replace(/,/g, '')) || 0;
                            var totalPagado = yaPagado + (parseFloat(row.data('pendiente')) || 0);
                            row.find('td:eq(2)').html('<span class="badge bg-success">Pagado</span>');
                            row.find('td:eq(3)').text(totalPagado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                            row.find('td:eq(4)').text(hoy);
                            row.attr('data-pendiente', '0');
                            actualizarBotones();
                            actualizarEstadoVenta();
                            actualizarBalance();
                            Swal.fire('Éxito', res.msgSuccess, 'success');
                        },
                        error: function() {
                            Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                        }
                    });
                })
            });

            $(document).on('click', '.btn_revertir', function() {
                var btn = $(this);
                var id = btn.data('id');
                var row = btn.closest('tr');
                var esCola = row.find('.badge.bg-info').length > 0;
                var pagadas = $('#tbl_plan_pagos').find('.badge.bg-success').length;
                var estadoActual = $('.card-body .badge').first().text().trim();
                var estadoFinal = (pagadas <= 1) ? 'Activo' : estadoActual;
                var texto = esCola ? 'Se revertirá el ajuste de esta cuota y se eliminará el abono parcial que la originó.' : 'Se eliminará el pago de esta cuota. La venta pasará a estado <strong>' + estadoFinal + '</strong>.';
                Swal.fire({
                    title: '¿Revertir cobro?',
                    html: texto,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, revertir'
                }).then((result) => {
                    if (!result.isConfirmed) return;
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/ventas/revertir-cuota') }}",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { id: id },
                        success: function(res) {
                            if (res.msgError) {
                                Swal.fire('Error', res.msgError, 'error');
                                return;
                            }
                            if (esCola) {
                                location.reload();
                                return;
                            }
                            var cm = parseFloat(row.data('cuota-mensual')) || 0;
                            row.find('td:eq(2)').html('<span class="badge bg-warning text-dark">Pendiente</span>');
                            row.find('td:eq(3)').text('-');
                            row.find('td:eq(4)').text('-');
                            row.attr('data-pendiente', cm);
                            actualizarBotones();
                            actualizarEstadoVenta();
                            actualizarBalance();
                            Swal.fire('Éxito', res.msgSuccess, 'success');
                        },
                        error: function() {
                            Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                        }
                    });
                })
            });

            function actualizarEstadoVenta() {
                var pagadas = $('#tbl_plan_pagos').find('.badge.bg-success').length;
                var total = $('#tbl_plan_pagos tbody tr').length;
                var $badge = $('.card:has(.card-header:contains("Resumen")) .badge').first();
                if (!$badge.length) $badge = $('.card-body .badge').first();
                if (pagadas >= total) {
                    $badge.removeClass('bg-warning text-dark').addClass('bg-success').text('Pagado');
                } else {
                    $badge.removeClass('bg-success').addClass('bg-warning text-dark').text('Activo');
                }
            }

            function actualizarBotones() {
                var ultimaConMonto = null;
                $('#tbl_plan_pagos tbody tr').each(function() {
                    if ($(this).find('td:eq(3)').text().trim() !== '-') ultimaConMonto = $(this);
                });
                $('#tbl_plan_pagos tbody tr').each(function() {
                    var id = $(this).data('id');
                    var pagado = $(this).find('.badge.bg-success').length > 0;
                    var cola = $(this).find('.badge.bg-info').length > 0;
                    var esUltima = ultimaConMonto && $(this).is(ultimaConMonto);
                    var accion = '';
                    if (cola && esUltima) {
                        accion = '<div class="d-flex gap-1"><button class="btn btn-azul btn-xs btn_pagar" data-id="' + id + '"><i data-feather="check" width="12" height="12"></i> Cobrar</button><button class="btn btn-danger btn-xs btn_revertir" data-id="' + id + '"><i data-feather="rotate-ccw" width="12" height="12"></i> Revertir</button></div>';
                    } else if (cola) {
                        accion = '<button class="btn btn-azul btn-xs btn_pagar" data-id="' + id + '"><i data-feather="check" width="12" height="12"></i> Cobrar</button>';
                    } else if (pagado && esUltima) {
                        accion = '<button class="btn btn-danger btn-xs btn_revertir" data-id="' + id + '"><i data-feather="rotate-ccw" width="14" height="14"></i> Revertir</button>';
                    } else if (pagado) {
                        accion = '<span></span>';
                    } else {
                        accion = '<button class="btn btn-azul btn-xs btn_pagar" data-id="' + id + '"><i data-feather="check" width="14" height="14"></i> Cobrar</button>';
                    }
                    $(this).find('td:eq(5)').html(accion);
                });
                feather.replace();
            }

            function actualizarBalance() {
                var cobrado = 0, pendiente = 0;
                $('#tbl_plan_pagos tbody tr').each(function() {
                    var valorPagado = parseFloat($(this).find('td:eq(3)').text().replace(/,/g, '')) || 0;
                    cobrado += valorPagado;
                    if (!$(this).find('.badge.bg-success').length) {
                        pendiente += parseFloat($(this).attr('data-pendiente')) || 0;
                    }
                });
                var total = cobrado + pendiente;
                var pct = total > 0 ? Math.round(cobrado / total * 100) : 0;
                $('#lbl_total_cobrado').text(cobrado.toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#lbl_total_pendiente').text(pendiente.toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#barra_progreso').css('width', pct + '%').attr('aria-valuenow', pct);
            }

            $('#btn_abonar').on('click', function() {
                $('#input_monto_abono').val('');
                $('#modalAbonar').modal('show');
            });

            $('#btn_procesar_abono').on('click', function() {
                var monto = parseCurrency($('#input_monto_abono').val());
                if (!monto || monto <= 0) {
                    Swal.fire('Error', 'Ingrese un monto válido.', 'error');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('/ventas/abonar') }}",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { id_venta: {{ $venta->id }}, monto: monto },
                    success: function(res) {
                        $('#modalAbonar').modal('hide');
                        if (res.msgError) {
                            Swal.fire('Error', res.msgError, 'error');
                            return;
                        }
                        Swal.fire('Éxito', res.msgSuccess, 'success').then(function() {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                    }
                });
            });
        });
    </script>
@endpush
