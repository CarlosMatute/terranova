@extends('layout.master')

@push('plugin-styles')
<style>
.stat-card { transition: transform 0.2s, box-shadow 0.2s; cursor: default; border-radius: 12px; overflow: hidden; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(63,89,129,0.15) !important; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.8rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; }
.progress-styled { height: 12px; border-radius: 6px; background: rgba(63,89,129,0.12); overflow: hidden; }
.progress-styled .progress-bar-custom { height: 100%; border-radius: 6px; transition: width 1s ease; }
.table-dashboard th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--ins-azul-oscuro); border-bottom-width: 1px; }
.table-dashboard td { font-size: 0.85rem; vertical-align: middle; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.card-header-dash { background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-bottom: none; padding: 0.85rem 1.25rem; }
.card-header-dash h6 { font-weight: 600; font-size: 0.85rem; letter-spacing: 0.3px; }
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
.cal-day { min-height: 70px; border-radius: 6px; padding: 4px 6px; font-size: 0.75rem; cursor: default; transition: box-shadow 0.15s; background: #f8f9fa; border: 1px solid #e9ecef; }
.cal-day:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.cal-day.today { border-color: var(--ins-azul); box-shadow: 0 0 0 2px rgba(63,89,129,0.2); }
.cal-day.other-month { opacity: 0.35; }
.cal-day .day-num { font-weight: 700; font-size: 0.85rem; margin-bottom: 2px; }
.cal-day .cal-stat { font-size: 0.6rem; line-height: 1.4; }
.cal-day .cal-stat i { width: 10px; text-align: center; margin-right: 2px; }
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
                            <div class="progress-bar-custom" role="progressbar" 
                                 style="width: {{ $stats->porcentaje }}%; background: linear-gradient(90deg, var(--ins-azul), #05a34a);" 
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header card-header-dash rounded-top d-flex align-items-center justify-content-between">
                <h6 class="text-white mb-0">
                    <i data-feather="calendar" width="16" height="16" class="me-1"></i> 
                    Calendario de Cobros
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-light" id="btn_cal_prev"><i data-feather="chevron-left" width="14" height="14"></i></button>
                    <span class="text-white fw-semibold" id="cal_month_label" style="min-width:140px;text-align:center;">{{ $calendario->month_name }} {{ $calendario->year }}</span>
                    <button class="btn btn-sm btn-light" id="btn_cal_next"><i data-feather="chevron-right" width="14" height="14"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-2 flex-wrap" style="font-size:0.75rem;">
                    <span><span class="badge-dot" style="background:#05a34a;"></span> Cobrado</span>
                    <span><span class="badge-dot" style="background:#ffc107;"></span> Pendiente</span>
                    <span><span class="badge-dot" style="background:#dc3545;"></span> Atrasado</span>
                    <span class="text-muted ms-auto" id="cal_resumen_mes"></span>
                </div>
                <div class="d-grid gap-1 mb-2" style="grid-template-columns:repeat(7,1fr);font-size:0.7rem;font-weight:600;text-transform:uppercase;text-align:center;color:#6c757d;">
                    <div>Lun</div><div>Mar</div><div>Mi&eacute;</div><div>Jue</div><div>Vie</div><div>S&aacute;b</div><div>Dom</div>
                </div>
                <div class="cal-grid" id="cal_grid"></div>
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
<div class="modal fade" id="modalDetalleDia" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-azul text-white">
                <h5 class="modal-title text-white"><i data-feather="list" width="16" height="16" class="me-1"></i> Cuotas del <span id="detalle_fecha"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="detalle_contenido" class="p-3 text-center text-muted py-5">Cargando...</div>
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

    /* Calendario mensual */
    var calMonth = {{ $calendario->month }};
    var calYear = {{ $calendario->year }};

    function cargarCalendario(month, year) {
        $.getJSON('{{ url("/dashboard/calendario") }}', { month: month, year: year }, function(data) {
            $('#cal_month_label').text(data.month_name + ' ' + data.year);
            calMonth = data.month;
            calYear = data.year;
            var html = '';
            var blanks = data.start_day - 1;
            for (var i = 0; i < blanks; i++) {
                html += '<div class="cal-day other-month"></div>';
            }
            var totalMesCobrado = 0, totalMesPendiente = 0;
            for (var d = 1; d <= data.num_days; d++) {
                var dateStr = data.year + '-' + String(data.month).padStart(2,'0') + '-' + String(d).padStart(2,'0');
                var info = data.dias[dateStr] || {};
                var cls = 'cal-day';
                var hoy = new Date();
                var todayStr = hoy.getFullYear() + '-' + String(hoy.getMonth()+1).padStart(2,'0') + '-' + String(hoy.getDate()).padStart(2,'0');
                if (dateStr === todayStr) cls += ' today';
                var cob = info.cobradas || 0;
                var pen = info.pendientes || 0;
                var atr = info.atrasadas || 0;
                var totalCob = info.total_cobrado || 0;
                totalMesCobrado += totalCob;
                totalMesPendiente += (info.total_pendiente || 0);
                html += '<div class="' + cls + '" data-day="' + d + '">';
                html += '<div class="day-num">' + d + '</div>';
                if (cob > 0) html += '<div class="cal-stat text-success"><i data-feather="check" width="10" height="10"></i>' + cob + ' L ' + totalCob.toLocaleString(undefined,{minimumFractionDigits:2}) + '</div>';
                if (pen > 0) html += '<div class="cal-stat text-warning"><i data-feather="clock" width="10" height="10"></i>' + pen + ' cuota(s)</div>';
                if (atr > 0) html += '<div class="cal-stat text-danger"><i data-feather="alert-triangle" width="10" height="10"></i>' + atr + ' atrasada(s)</div>';
                html += '</div>';
            }
            var totalCells = blanks + data.num_days;
            var rem = totalCells % 7;
            var fill = rem === 0 ? 0 : 7 - rem;
            for (var i = 0; i < fill; i++) html += '<div class="cal-day other-month"></div>';
            $('#cal_grid').html(html);
            $('#cal_resumen_mes').text('Mes: L ' + totalMesCobrado.toLocaleString(undefined,{minimumFractionDigits:2}) + ' cobrado / L ' + totalMesPendiente.toLocaleString(undefined,{minimumFractionDigits:2}) + ' pendiente');
            feather.replace();
        });
    }

    cargarCalendario(calMonth, calYear);

    $(document).on('click', '.cal-day:not(.other-month)', function() {
        var d = $(this).data('day');
        if (!d) return;
        var dateStr = String(calYear).padStart(4,'0') + '-' + String(calMonth).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        $('#detalle_fecha').text(dateStr);
        $('#detalle_contenido').html('<div class="text-center text-muted py-5">Cargando...</div>');
        $('#modalDetalleDia').modal('show');
        $.ajax({
            url: '{{ url("/dashboard/calendario/dia") }}',
            data: { date: dateStr },
            dataType: 'json',
            success: function(data) {
            if (!data.ventas.length) {
                $('#detalle_contenido').html('<div class="text-center text-muted py-5"><i data-feather="inbox" width="40" height="40" class="mb-2"></i><p class="mb-0">No hay cuotas para esta fecha.</p></div>');
                feather.replace();
                return;
            }
            var totalCobradas = 0, totalPendientes = 0, totalAtrasadas = 0;
            var html = '';
            $.each(data.ventas, function(i, v) {
                var cobradas = v.cuotas.filter(function(c){ return c.estado === 'cobrada'; }).length;
                var pendientes = v.cuotas.filter(function(c){ return c.estado === 'pendiente'; }).length;
                var atrasadas = v.cuotas.filter(function(c){ return c.estado === 'atrasada'; }).length;
                totalCobradas += cobradas;
                totalPendientes += pendientes;
                totalAtrasadas += atrasadas;
                var telefono = v.telefono || v.telefono_2 || '';
                var waMsg = '';
                if (telefono) {
                    var nums = telefono.replace(/[^0-9]/g,'');
                    if (nums.length >= 8) {
                        var cuotaRef = v.cuotas.length > 0 ? 'L ' + v.cuotas[0].monto.toLocaleString(undefined,{minimumFractionDigits:2}) : '';
                        waMsg = 'Hola ' + v.cliente.split(' ')[0] + ', le recordamos que su cuota de ' + cuotaRef + ' correspondiente al lote ' + v.lote + ' en ' + v.residencial + ' está pendiente de pago. ¡Póngase al día! Saludos.';
                    }
                }
                html += '<div class="border-bottom p-3">' +
                    '<div class="d-flex align-items-start justify-content-between mb-2">' +
                    '<div><strong>' + v.cliente + '</strong><br>' +
                    '<span class="badge bg-primary">' + v.bloque + '</span> <span class="badge bg-primary">' + v.lote + '</span> <small class="text-muted">' + v.residencial + '</small></div>' +
                    '<div class="d-flex flex-column align-items-end gap-1">' +
                    '<span class="fw-semibold" style="font-size:0.85rem;">L ' + v.cuota_mensual.toLocaleString(undefined,{minimumFractionDigits:2}) + '</span>' +
                    (waMsg ? '<a href="https://wa.me/504' + nums + '?text=' + encodeURIComponent(waMsg) + '" target="_blank" class="btn btn-success btn-xs" style="font-size:0.7rem;"><i data-feather="message-circle" width="12" height="12"></i> WhatsApp</a>' : '') +
                    '</div></div>' +
                    '<div class="d-flex gap-2 mb-2" style="font-size:0.7rem;">' +
                    '<span class="text-muted">Total pagar: L ' + v.total_pagar.toLocaleString(undefined,{minimumFractionDigits:2}) + '</span>' +
                    '</div>';
                if (v.cuotas.length) {
                    html += '<div class="table-responsive"><table class="table table-sm mb-0" style="font-size:0.75rem;">' +
                        '<thead><tr><th>#</th><th class="text-end">Monto</th><th>Estado</th></tr></thead><tbody>';
                    $.each(v.cuotas, function(j, c) {
                        html += '<tr>' +
                            '<td>' + (j+1) + '</td>' +
                            '<td class="text-end">L ' + c.monto.toLocaleString(undefined,{minimumFractionDigits:2}) + '</td>' +
                            '<td><span class="badge ' + c.badge + '" style="font-size:0.65rem;">' + c.estado_label + '</span></td>' +
                            '</tr>';
                    });
                    html += '</tbody></table></div>';
                }
                html += '<div class="d-flex gap-2" style="font-size:0.7rem;">' +
                    (cobradas ? '<span class="text-success">' + cobradas + ' cobrada(s)</span>' : '') +
                    (pendientes ? '<span class="text-warning">' + pendientes + ' pendiente(s)</span>' : '') +
                    (atrasadas ? '<span class="text-danger">' + atrasadas + ' atrasada(s)</span>' : '') +
                    '</div>';
                html += '</div>';
            });
            html += '<div class="p-3 border-top d-flex gap-3 justify-content-center bg-light" style="font-size:0.8rem;">' +
                '<span class="text-success"><strong>' + totalCobradas + '</strong> cobrada(s)</span>' +
                '<span class="text-warning"><strong>' + totalPendientes + '</strong> pendiente(s)</span>' +
                '<span class="text-danger"><strong>' + totalAtrasadas + '</strong> atrasada(s)</span>' +
                '</div>';
            $('#detalle_contenido').html(html);
            feather.replace();
            },
            error: function(xhr) {
                $('#detalle_contenido').html('<div class="text-center text-danger py-5"><i data-feather="alert-circle" width="40" height="40" class="mb-2"></i><p class="mb-0">Error al cargar: ' + (xhr.responseJSON?.message || xhr.statusText) + '</p></div>');
                feather.replace();
            }
        });
    });

    $('#btn_cal_prev').on('click', function() {
        var m = calMonth - 1;
        var y = calYear;
        if (m < 1) { m = 12; y--; }
        cargarCalendario(m, y);
    });
    $('#btn_cal_next').on('click', function() {
        var m = calMonth + 1;
        var y = calYear;
        if (m > 12) { m = 1; y++; }
        cargarCalendario(m, y);
    });
});
</script>
@endpush
