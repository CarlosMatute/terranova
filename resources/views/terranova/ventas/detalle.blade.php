@extends('layout.master')

@push('plugin-styles')
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
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Cliente: {{ $venta->cliente_nombre }}</p>
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
                    <div class="d-flex justify-content-between mb-2"><span>Tipo de Pago:</span> <strong>{{ $venta->tipo_pago }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Estado:</span> <span class="badge {{ $venta->estado == 'Pagado' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $venta->estado }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Total Contado:</span> <strong>{{ number_format($venta->total_contado, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Prima:</span> <strong>{{ number_format($venta->prima, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Intereses:</span> <strong>{{ number_format($venta->total_intereses, 2) }}</strong></div>
                    <hr>
                    <h4 class="text-center text-azul">Total: {{ number_format($venta->total_pagar, 2) }}</h4>
                </div>
            </div>

            <div class="card border-azul">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="grid" width="16" height="16"></i> Lotes</h5>
                </div>
                <div class="card-body">
                    @foreach ($lotes as $l)
                        <div class="p-2 border-bottom">
                            <strong>{{ $l->residencial }}</strong><br>
                            <small>Bloque {{ $l->bloque }} - Lote {{ $l->nombre }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="calendar" width="16" height="16"></i> Plan de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-azul-oscuro text-white">
                                <tr>
                                    <th class="text-white">#</th>
                                    <th class="text-white">Fecha Cobro</th>
                                    <th class="text-white">Estado</th>
                                    <th class="text-white">Monto</th>
                                    <th class="text-white">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cobros as $index => $c)
                                    <tr style="font-size: small">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $c->fecha_cobro }}</td>
                                        <td>
                                            <span class="badge {{ $c->estado == 'Pagado' ? 'bg-success' : ($c->estado == 'Atrasado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                {{ $c->estado }}
                                            </span>
                                        </td>
                                        <td>{{ $c->cantidad_pago ? number_format($c->cantidad_pago, 2) : number_format($venta->cuota_mensual, 2) }}</td>
                                        <td>
                                            @if ($c->estado != 'Pagado')
                                                <button class="btn btn-azul btn-xs btn_pagar" data-id="{{ $c->id }}">
                                                    <i data-feather="check" width="14" height="14"></i> Pagar
                                                </button>
                                            @else
                                                <small class="text-muted">{{ $c->fecha_pago }}</small>
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
@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            $('.btn_pagar').on('click', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: '¿Confirmar pago?',
                    text: "Se registrará el pago de esta cuota.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, pagar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/ventas/pagar-cuota') }}",
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { id: id },
                            success: function(res) {
                                location.reload();
                            }
                        });
                    }
                })
            });
        });
    </script>
@endpush
