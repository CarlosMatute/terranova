@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .table thead.bg-azul-oscuro th { font-weight: 500; font-size: 0.85rem; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card bg-azul text-white border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="card-body position-relative" style="background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; background: rgba(255,255,255,0.15);">
                                <i data-feather="dollar-sign" width="28" height="28" class="text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Ventas</h3>
                            <p class="mb-0 text-white-50" style="opacity: 0.8;">Gestión de ventas y financiamientos registrados.</p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="dollar-sign" width="120" height="120" class="text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" href="#pendientes" role="tab" aria-selected="true">Pendientes / Crédito</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pagadas-tab" data-bs-toggle="tab" href="#pagadas" role="tab" aria-selected="false">Pagadas / Contado</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="pendientes" role="tabpanel">
                            <div class="table-responsive">
                                <table class="jambo_table table table-hover" id="tbl_ventas_pendientes" border="1" width="100%">
                                    <thead class="bg-azul-oscuro">
                                        <tr class="headings">
                                            <th scope="col" class="text-white">ID</th>
                                            <th scope="col" class="text-white">Fecha</th>
                                            <th scope="col" class="text-white">Cliente</th>
                                            <th scope="col" class="text-white">Tipo</th>
                                            <th scope="col" class="text-white">Total</th>
                                            <th scope="col" class="text-white">Opciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pagadas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="jambo_table table table-hover" id="tbl_ventas_pagadas" border="1" width="100%">
                                    <thead class="bg-azul-oscuro">
                                        <tr class="headings">
                                            <th scope="col" class="text-white">ID</th>
                                            <th scope="col" class="text-white">Fecha</th>
                                            <th scope="col" class="text-white">Cliente</th>
                                            <th scope="col" class="text-white">Tipo</th>
                                            <th scope="col" class="text-white">Total</th>
                                            <th scope="col" class="text-white">Opciones</th>
                                        </tr>
                                    </thead>
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
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@endpush

@push('custom-scripts')
    <script>
        var ventasDT = {};
        $(document).ready(function() {
            var lang = { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" };
            var cols = [
                { data: 'id' },
                { data: 'fecha_venta' },
                { data: 'cliente' },
                { data: 'tipo_pago', render: function(d, t, r) { return '<span class="badge ' + r.badge + '">' + d + '</span>'; } },
                { data: 'total_pagar', render: function(d) { return '<strong>' + d + '</strong>'; } },
                { data: 'detalle_url', render: function(d) { return '<a href="' + d + '" class="btn btn-azul-claro btn-xs"><i data-feather="eye" width="14" height="14"></i> Ver Detalle</a>'; }, orderable: false }
            ];
            var dtOpts = {
                responsive: true,
                serverSide: true,
                processing: true,
                language: lang,
                ajax: {
                    url: "{{ url('/ventas/datos') }}",
                    data: function(d) { d.estado = $(this).closest('.tab-pane').attr('id') == 'pagadas' ? 'Pagado' : 'Activo'; }
                },
                columns: cols,
                order: [[1, 'desc']],
                drawCallback: function() { feather.replace(); }
            };
            ventasDT.pendientes = $('#tbl_ventas_pendientes').DataTable($.extend({}, dtOpts, {
                ajax: { url: "{{ url('/ventas/datos') }}", data: function(d) { d.estado = 'Activo'; } }
            }));

            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                if (target == '#pagadas' && !ventasDT.pagadas) {
                    ventasDT.pagadas = $('#tbl_ventas_pagadas').DataTable($.extend({}, dtOpts, {
                        ajax: { url: "{{ url('/ventas/datos') }}", data: function(d) { d.estado = 'Pagado'; } }
                    }));
                }
            });
        });
    </script>
@endpush
