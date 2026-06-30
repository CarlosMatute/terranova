@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-net/responsive/responsive.dataTables.min.css') }}">
<style>
.stat-card { transition: transform 0.2s, box-shadow 0.2s; cursor: default; border-radius: 12px; overflow: hidden; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(63,89,129,0.15) !important; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.8rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; }
.progress-styled { height: 12px; border-radius: 6px; background: rgba(63,89,129,0.12); overflow: hidden; }
.progress-styled .progress-bar { border-radius: 6px; background: linear-gradient(90deg, var(--ins-azul), #05a34a); transition: width 1s ease; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.75rem; }
.info-item { text-align: center; padding: 0.75rem 0.5rem; border-radius: 8px; background: rgba(63,89,129,0.04); }
.info-item .label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.4px; color: #888; }
.info-item .value { font-size: 1.2rem; font-weight: 700; margin-top: 2px; }
</style>
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('residenciales') }}">Residenciales</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $residencial->nombre }} &mdash; Estad&iacute;sticas</li>
    </ol>
</nav>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul));">
            <div class="card-body d-flex align-items-start justify-content-between py-3">
                <div class="d-flex align-items-start">
                    <img src="{{ asset('storage/residenciales/res_' . $residencial->id . '/' . $residencial->imagen) }}"
                         class="wd-70 ht-70 rounded me-3 border border-white border-2"
                         alt="{{ $residencial->nombre }}"
                         onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                    <div>
                        <h3 class="mb-1 fw-bold text-white">{{ $residencial->nombre }}</h3>
                        <p class="mb-0 text-white-50" style="opacity: 0.7; font-size: 0.9rem;">
                            <i data-feather="info" width="14" height="14" class="me-1"></i> {{ $residencial->descripcion ?? 'Sin descripci&oacute;n' }}
                        </p>
                    </div>
                </div>
                <div class="d-none d-md-flex gap-2">
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="layers" width="14" height="14" class="me-1"></i> {{ $residencial->total_bloques }} Bloques
                    </span>
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="grid" width="14" height="14" class="me-1"></i> {{ $residencial->total_lotes }} Lotes
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(5,163,74,0.12); color: #05a34a;">
                        <i data-feather="check-circle" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label text-success">Cobros Hoy</div>
                        <div class="stat-value text-success">L {{ number_format($hoy->total_cobrado_hoy, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem;">{{ $hoy->total_cobros }} transacci&oacute;n(es)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(220,53,69,0.12); color: #dc3545;">
                        <i data-feather="alert-triangle" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label text-danger">Atrasados</div>
                        <div class="stat-value text-danger">L {{ number_format($atrasados->total_moroso, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem;">{{ $atrasados->total_atrasados }} cuota(s) vencida(s)</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(63,89,129,0.12); color: var(--ins-azul);">
                        <i data-feather="trending-up" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label" style="color: var(--ins-azul);">Eficiencia</div>
                        <div class="stat-value" style="color: var(--ins-azul);">{{ $stats->porcentaje }}%</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem;">
                        L {{ number_format($stats->total_cobrado, 2) }} de L {{ number_format($stats->total_esperado, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(255,193,7,0.15); color: #d4a017;">
                        <i data-feather="clock" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label" style="color: #d4a017;">Por Cobrar</div>
                        <div class="stat-value" style="color: #d4a017;">L {{ number_format($stats->restante, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem;">Restante de {{ $stats->mes_actual }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="grid" width="16" height="16" class="me-1"></i> Resumen del Residencial
                </h6>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Bloques</div>
                        <div class="value" style="color: var(--ins-azul);">{{ $residencial->total_bloques }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Lotes</div>
                        <div class="value" style="color: var(--ins-azul);">{{ $residencial->total_lotes }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Vendidos</div>
                        <div class="value" style="color: #05a34a;">{{ $residencial->lotes_vendidos }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Apartados</div>
                        <div class="value" style="color: #d4a017;">{{ $residencial->lotes_apartados }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Disponibles</div>
                        <div class="value" style="color: var(--ins-azul);">{{ max(0, $residencial->total_lotes - $residencial->lotes_vendidos - $residencial->lotes_apartados) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Ventas</div>
                        <div class="value" style="color: var(--ins-azul);">{{ $total_ventas }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="bar-chart-2" width="16" height="16" class="me-1"></i>
                    Progreso Mensual &mdash; {{ $stats->mes_actual }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" style="font-size: 0.85rem;">Recaudado: <strong>L {{ number_format($stats->total_cobrado, 2) }}</strong></span>
                            <span class="text-muted" style="font-size: 0.85rem;">Esperado: <strong>L {{ number_format($stats->total_esperado, 2) }}</strong></span>
                        </div>
                        <div style="width:100%;height:12px;background:rgba(63,89,129,0.12);border-radius:6px;overflow:hidden;">
                            <div style="width:{{ $stats->porcentaje }}%;height:100%;background:linear-gradient(90deg,var(--ins-azul),#05a34a);border-radius:6px;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size: 0.75rem; color: var(--ins-azul); font-weight: 600;">{{ $stats->porcentaje }}% completado</span>
                            <span style="font-size: 0.75rem; color: #dc3545; font-weight: 600;">Restan L {{ number_format($stats->restante, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: #05a34a;">{{ $residencial->lotes_vendidos }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Vendidos</div>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: #d4a017;">{{ $residencial->lotes_apartados }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Apartados</div>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: var(--ins-azul);">{{ $total_ventas }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Ventas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="activity" width="16" height="16" class="me-1"></i>
                    Ingresos &mdash; &Uacute;ltimos 12 Meses
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartLineResidencial" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="pie-chart" width="16" height="16" class="me-1"></i>
                    Distribuci&oacute;n de Cobros
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartDonutResidencial" height="180"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-xl-12">
        <div class="card border-azul">
            <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">
                    <i class="text-white icon-lg pb-3px" data-feather="dollar-sign"></i> Ventas &mdash; {{ $residencial->nombre }}
                </h5>
                <span class="badge bg-white text-azul px-3 py-1.5 rounded-pill">{{ $total_ventas }} venta(s)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="jambo_table table table-hover" id="tbl_ventas" border="1" width="100%">
                        <thead class="bg-azul-oscuro">
                            <tr class="headings">
                                <th scope="col" class="text-white">#</th>
                                <th scope="col" class="text-white">Cliente</th>
                                <th scope="col" class="text-white">Identidad</th>
                                <th scope="col" class="text-white">Lotes</th>
                                <th scope="col" class="text-white">Tipo</th>
                                <th scope="col" class="text-white">Estado</th>
                                <th scope="col" class="text-white">Total</th>
                                <th scope="col" class="text-white">Cobrado</th>
                                <th scope="col" class="text-white text-center">%</th>
                                <th scope="col" class="text-white text-center">Progreso</th>
                                <th scope="col" class="text-white text-center">Acci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/chartjs/chart.umd.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-net/responsive/dataTables.responsive.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
$(function() {
    feather.replace();

    var chartData = @json($chart_data);
    var totalEsperado = {{ $stats->total_esperado }};
    var totalCobrado = {{ $stats->total_cobrado }};
    var totalRestante = {{ $stats->restante }};

    var meses = chartData.map(function(d) { return d.mes_label; });
    var esperado = chartData.map(function(d) { return parseFloat(d.total_esperado); });
    var cobrado = chartData.map(function(d) { return parseFloat(d.total_cobrado); });

    new Chart(document.getElementById('chartLineResidencial'), {
        type: 'line',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Esperado',
                    data: esperado,
                    borderColor: '#3f5981',
                    backgroundColor: 'rgba(63,89,129,0.08)',
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3f5981',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    borderWidth: 2.5
                },
                {
                    label: 'Cobrado',
                    data: cobrado,
                    borderColor: '#05a34a',
                    backgroundColor: 'rgba(5,163,74,0.08)',
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#05a34a',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    tension: 0.35,
                    borderWidth: 2.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    labels: { color: '#323232', font: { size: 12 }, padding: 14, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#2c3f5c',
                    padding: 10,
                    cornerRadius: 6,
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': L ' + parseFloat(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: true, color: 'rgba(63,89,129,0.06)' }, ticks: { color: '#555', font: { size: 11 } } },
                y: { grid: { display: true, color: 'rgba(63,89,129,0.06)' }, ticks: { color: '#555', font: { size: 11 }, callback: function(v) { return 'L ' + v.toLocaleString(); } } }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });

    new Chart(document.getElementById('chartDonutResidencial'), {
        type: 'doughnut',
        data: {
            labels: ['Cobrado', 'Pendiente'],
            datasets: [{
                data: [totalCobrado, Math.max(0, totalRestante)],
                backgroundColor: ['#05a34a', 'rgba(63,89,129,0.2)'],
                borderColor: ['#fff', '#fff'],
                borderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#323232', font: { size: 12 }, padding: 14, usePointStyle: true }
                },
                tooltip: {
                    backgroundColor: '#2c3f5c',
                    padding: 10,
                    cornerRadius: 6,
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            var pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return ctx.label + ': L ' + ctx.parsed.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    var dt_ventas = $('#tbl_ventas').DataTable({
        "aLengthMenu": [
            [10, 30, 50, 100, -1],
            [10, 30, 50, 100, "Todo"]
        ],
        "iDisplayLength": 10,
        responsive: true,
        serverSide: true,
        processing: true,
        language: {
            url: "{{ asset('assets/plugins/datatables-net/i18n/Spanish.json') }}"
        },
        ajax: {
            url: '{{ url('/residenciales/' . $residencial->id . '/estadisticas/datos') }}',
        },
        columns: [
            { data: 'id' },
            { data: 'cliente', render: function(d) { return '<span class="badge-dot" style="background: var(--ins-azul);"></span> ' + (d || ''); } },
            { data: 'identidad' },
            { data: 'lotes' },
            { data: 'tipo_pago', render: function(d, t, r) { return '<span class="badge bg-' + r.badge_tipo + ' bg-opacity-10 text-' + r.badge_tipo + ' px-2 py-1">' + d + '</span>'; } },
            { data: 'estado', render: function(d, t, r) { return '<span class="badge bg-' + r.badge_estado + ' bg-opacity-10 text-' + r.badge_estado + ' px-2 py-1">' + d + '</span>'; } },
            { data: 'total_pagar', render: function(d) { return 'L ' + d; } },
            { data: 'total_cobrado', render: function(d) { return '<span style="color:#05a34a;">L ' + d + '</span>'; } },
            { data: 'porcentaje', className: 'text-center', render: function(d, t, r) {
                var hex = r.color_pct;
                var bg = hex === '#05a34a' ? 'rgba(5,163,74,0.12)' : hex === '#d4a017' ? 'rgba(212,160,23,0.12)' : 'rgba(220,53,69,0.12)';
                return '<span class="badge rounded-pill px-2 py-1" style="background:' + bg + ';color:' + hex + ';">' + d + '%</span>';
            } },
            { data: 'porcentaje', className: 'text-center', render: function(d, t, r) {
                var pct = parseFloat(d);
                var color = r.color_pct;
                return '<div style="width:80px;height:6px;background:rgba(63,89,129,0.12);border-radius:3px;overflow:hidden;display:inline-block;">' +
                       '<div style="width:' + pct + '%;height:100%;background:' + color + ';border-radius:3px;"></div>' +
                       '</div>';
            } },
            { data: 'detalle_url', orderable: false, searchable: false, render: function(d) { return '<a href="' + d + '" class="btn btn-azul btn-xs"><i data-feather="eye" width="14" height="14"></i> Ver</a>'; } }
        ],
        order: [[0, 'desc']],
        drawCallback: function() { feather.replace(); }
    });
    $('#tbl_ventas').each(function() {
        var datatable = $(this);
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Buscar');
        search_input.removeClass('form-control-sm');
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.removeClass('form-control-sm');
    });
});
</script>
@endpush
