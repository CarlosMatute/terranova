@extends('layout.master')

@push('plugin-styles')
<style>
.stat-card { transition: transform 0.2s, box-shadow 0.2s; cursor: default; border-radius: 12px; overflow: hidden; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(63,89,129,0.15) !important; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.8rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; }
.progress-styled { height: 12px; border-radius: 6px; background: rgba(63,89,129,0.12); overflow: hidden; }
.progress-styled .progress-bar { border-radius: 6px; background: linear-gradient(90deg, var(--ins-azul), #05a34a); transition: width 1s ease; }
.table-dashboard th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--ins-azul-oscuro); border-bottom-width: 1px; }
.table-dashboard td { font-size: 0.85rem; vertical-align: middle; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.card-header-dash { background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-bottom: none; padding: 0.85rem 1.25rem; }
.card-header-dash h6 { font-weight: 600; font-size: 0.85rem; letter-spacing: 0.3px; }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul));">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <h3 class="mb-1 text-white fw-bold">Dashboard</h3>
                    <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                        <i data-feather="calendar" width="14" height="14" class="me-1"></i> {{ now()->format('d/m/Y') }} &mdash; {{ $stats->mes_actual }}
                    </p>
                </div>
                <div class="d-none d-md-flex gap-2">
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="grid" width="14" height="14" class="me-1"></i> {{ $conteos->lotes_disponibles }} Disp.
                    </span>
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="users" width="14" height="14" class="me-1"></i> {{ $conteos->clientes_totales }} Clientes
                    </span>
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="dollar-sign" width="14" height="14" class="me-1"></i> {{ $conteos->ventas_activas }} Activas
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(5,163,74,0.12); color: #05a34a;">
                        <i data-feather="check-circle" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label text-success">Cobros del D&iacute;a</div>
                        <div class="stat-value text-success">L {{ number_format($hoy->total_cobrado_hoy, 2) }}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="text-muted" style="font-size: 0.8rem;"><strong>{{ $hoy->total_cobros }}</strong> transacci&oacute;n(es) registrada(s) hoy</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
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
                    <span class="text-muted" style="font-size: 0.8rem;"><strong>{{ $atrasados->total_atrasados }}</strong> cuota(s) vencida(s) sin pago</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="stat-icon" style="background: rgba(63,89,129,0.12); color: var(--ins-azul);">
                        <i data-feather="trending-up" width="22" height="22"></i>
                    </div>
                    <div>
                        <div class="stat-label" style="color: var(--ins-azul);">Eficiencia del Mes</div>
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
    <div class="col-md-6 col-xl-3">
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
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
                            <span class="text-muted" style="font-size: 0.85rem;">Meta: <strong>L {{ number_format($stats->total_esperado, 2) }}</strong></span>
                        </div>
                        <div class="progress-styled">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $stats->porcentaje }}%;" 
                                 aria-valuenow="{{ $stats->porcentaje }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size: 0.75rem; color: var(--ins-azul); font-weight: 600;">{{ $stats->porcentaje }}% completado</span>
                            <span style="font-size: 0.75rem; color: #dc3545; font-weight: 600;">Faltan L {{ number_format($stats->restante, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: #05a34a;">{{ $stats->porcentaje }}%</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Efectividad</div>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: var(--ins-azul);">{{ $conteos->ventas_activas }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Cr&eacute;ditos</div>
                            </div>
                            <div class="vr"></div>
                            <div>
                                <div class="fw-bold" style="font-size: 1.5rem; color: #d4a017;">{{ $conteos->ventas_completadas }}</div>
                                <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Pagados</div>
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
                    Ingresos vs Cobros &mdash; &Uacute;ltimos 12 Meses
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartLineMonthly" height="110"></canvas>
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
                <canvas id="chartDonut" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="calendar" width="16" height="16" class="me-1"></i> 
                    Pr&oacute;ximos Cobros (7 d&iacute;as)
                </h6>
            </div>
            <div class="card-body p-0">
                @if (count($proximos) > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-dashboard mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Vencimiento</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proximos as $p)
                            <tr>
                                <td>
                                    <span class="badge-dot" style="background: var(--ins-azul);"></span>
                                    {{ $p->cliente }}
                                </td>
                                <td>{{ $p->fecha_cobro_fmt }}</td>
                                <td class="text-end fw-semibold">L {{ number_format($p->cuota_mensual, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i data-feather="calendar" width="40" height="40" class="mb-2"></i>
                    <p class="mb-0">No hay cobros programados en los pr&oacute;ximos 7 d&iacute;as.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top">
                <h6 class="text-white mb-0">
                    <i data-feather="users" width="16" height="16" class="me-1"></i> 
                    Top Morosos
                </h6>
            </div>
            <div class="card-body p-0">
                @if (count($top_morosos) > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-dashboard mb-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th class="text-center">Atrasadas</th>
                                <th class="text-end">Adeudo</th>
                                <th>Desde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($top_morosos as $m)
                            <tr>
                                <td>
                                    <span class="badge-dot" style="background: #dc3545;"></span>
                                    {{ $m->cliente }}
                                </td>
                                <td class="text-center"><span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ $m->cuotas_atrasadas }}</span></td>
                                <td class="text-end fw-semibold text-danger">L {{ number_format($m->total_adeudado, 2) }}</td>
                                <td style="font-size: 0.75rem;">{{ $m->fecha_mas_antigua }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i data-feather="smile" width="40" height="40" class="mb-2"></i>
                    <p class="mb-0">No hay clientes con pagos atrasados. &iexcl;Excelente!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/chartjs/chart.umd.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
$(function() {
    var chartData = @json($chart_data);
    var totalEsperado = {{ $stats->total_esperado }};
    var totalCobrado = {{ $stats->total_cobrado }};
    var totalRestante = {{ $stats->restante }};

    var meses = chartData.map(function(d) { return d.mes_label; });
    var esperado = chartData.map(function(d) { return parseFloat(d.total_esperado); });
    var cobrado = chartData.map(function(d) { return parseFloat(d.total_cobrado); });

    new Chart(document.getElementById('chartLineMonthly'), {
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

    new Chart(document.getElementById('chartDonut'), {
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
});
</script>
@endpush
