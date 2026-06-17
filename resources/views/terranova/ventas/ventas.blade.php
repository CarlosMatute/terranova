@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h3 class="mb-2">Ventas</h3>
                    <p class="text-muted">Gestión de ventas y financiamientos registrados.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card border-secondary">
                <div class="card-header bg-dark text-white">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-white" id="pendientes-tab" data-bs-toggle="tab" href="#pendientes" role="tab" aria-selected="true">Pendientes / Crédito</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" id="pagadas-tab" data-bs-toggle="tab" href="#pagadas" role="tab" aria-selected="false">Pagadas / Contado</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tbl_ventas_pendientes">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Total</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ventas_pendientes as $v)
                                            <tr style="font-size: small">
                                                <td>{{ $v->id }}</td>
                                                <td>{{ $v->fecha_venta }}</td>
                                                <td>{{ $v->cliente }}</td>
                                                <td><span class="badge bg-warning text-dark">{{ $v->tipo_pago }}</span></td>
                                                <td><strong>{{ number_format($v->total_pagar, 2) }}</strong></td>
                                                <td>
                                                    <a href="{{ url('/ventas/detalle/' . $v->id) }}" class="btn btn-info btn-xs">
                                                        <i data-feather="eye" width="14" height="14"></i> Ver Detalle
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pagadas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover" id="tbl_ventas_pagadas">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Total</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ventas_pagadas as $v)
                                            <tr style="font-size: small">
                                                <td>{{ $v->id }}</td>
                                                <td>{{ $v->fecha_venta }}</td>
                                                <td>{{ $v->cliente }}</td>
                                                <td><span class="badge bg-success">{{ $v->tipo_pago }}</span></td>
                                                <td><strong>{{ number_format($v->total_pagar, 2) }}</strong></td>
                                                <td>
                                                    <a href="{{ url('/ventas/detalle/' . $v->id) }}" class="btn btn-info btn-xs">
                                                        <i data-feather="eye" width="14" height="14"></i> Ver Detalle
                                                    </a>
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
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            var lang = { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" };
            $('#tbl_ventas_pendientes').DataTable({ language: lang });
            $('#tbl_ventas_pagadas').DataTable({ language: lang });
        });
    </script>
@endpush
