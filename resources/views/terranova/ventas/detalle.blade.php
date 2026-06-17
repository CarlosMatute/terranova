@extends('layout.master')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-2">Detalle de Venta #{{ $venta->id }}</h3>
                    <h4 class="text-muted">Cliente: {{ $venta->cliente_nombre }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-secondary mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="text-white mb-0"><i data-feather="info" width="16" height="16"></i> Resumen</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Tipo de Pago:</span> <strong>{{ $venta->tipo_pago }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Estado:</span> <span class="badge {{ $venta->estado == 'Pagado' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $venta->estado }}</span></div>
                    <div class="d-flex justify-content-between mb-2"><span>Total Contado:</span> <strong>{{ number_format($venta->total_contado, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Prima:</span> <strong>{{ number_format($venta->prima, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Intereses:</span> <strong>{{ number_format($venta->total_intereses, 2) }}</strong></div>
                    <hr>
                    <h4 class="text-center text-primary">Total: {{ number_format($venta->total_pagar, 2) }}</h4>
                </div>
            </div>

            <div class="card border-secondary">
                <div class="card-header bg-primary text-white">
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
            <div class="card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="text-white mb-0"><i data-feather="calendar" width="16" height="16"></i> Plan de Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha Cobro</th>
                                    <th>Estado</th>
                                    <th>Monto</th>
                                    <th>Acción</th>
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
                                                <button class="btn btn-success btn-xs btn_pagar" data-id="{{ $c->id }}">
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
