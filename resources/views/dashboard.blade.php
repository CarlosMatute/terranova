@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body" style="background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-radius: 5px;">
                <h3 class="mb-1 text-white">Dashboard Principal</h3>
                <p class="mb-0" style="color: rgba(255,255,255,0.75);">Resumen operativo y financiero del proyecto Terranova.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow-1">
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0 text-azul">Lotes Disponibles</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2" style="color: var(--ins-azul);">{{ $lotes_disponibles->total }}</h3>
                <p class="text-gris mb-0"><i data-feather="grid" class="icon-sm"></i> Inventario total</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0 text-azul">Clientes Registrados</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2" style="color: var(--ins-azul-claro);">{{ $clientes_totales->total }}</h3>
                <p class="text-gris mb-0"><i data-feather="users" class="icon-sm"></i> Base de datos activa</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0 text-azul">Ventas Activas</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2" style="color: var(--ins-azul-oscuro);">{{ $ventas_activas->total }}</h3>
                <p class="text-gris mb-0"><i data-feather="dollar-sign" class="icon-sm"></i> Financiamientos en curso</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card border-0 shadow-sm">
      <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-bottom: none;">
          <h5 class="text-white mb-0"><i data-feather="trending-up" width="16" height="16"></i> Rendimiento de Cobros - {{ $stats->mes_actual }}</h5>
      </div>
      <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <p class="text-gris mb-1">Total a Cobrar</p>
                <h4 class="text-negro">{{ number_format($stats->total_cobrar, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-gris mb-1">Cobrado</p>
                <h4 style="color: #05a34a;">{{ number_format($stats->total_pagado, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-gris mb-1">Por Cobrar</p>
                <h4 style="color: #dc3545;">{{ number_format($stats->restante, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-gris mb-1">Eficiencia</p>
                <h4 style="color: var(--ins-azul-claro);">{{ $stats->porcentaje }}%</h4>
            </div>
        </div>
        <div class="progress ht-15">
          <div class="progress-bar" role="progressbar" style="width: {{ $stats->porcentaje }}%; background: linear-gradient(90deg, var(--ins-azul), #05a34a);" aria-valuenow="{{ $stats->porcentaje }}" aria-valuemin="0" aria-valuemax="100">{{ $stats->porcentaje }}%</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card border-0 shadow-sm">
      <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-bottom: none;">
        <h5 class="text-white mb-0"><i data-feather="activity" width="16" height="16"></i> Ingresos vs Cobros - Últimos 12 Meses</h5>
      </div>
      <div class="card-body">
        <canvas id="chartLineMonthly" height="100"></canvas>
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

  var meses = chartData.map(function(d) { return d.mes_label; });
  var cobrar = chartData.map(function(d) { return parseFloat(d.total_cobrar); });
  var pagado = chartData.map(function(d) { return parseFloat(d.total_pagado); });

  new Chart(document.getElementById('chartLineMonthly'), {
    type: 'line',
    data: {
      labels: meses,
      datasets: [
        {
          label: 'Total a Cobrar',
          data: cobrar,
          borderColor: '#3f5981',
          backgroundColor: 'rgba(63,89,129,0.08)',
          fill: true,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#3f5981',
          pointBorderWidth: 2,
          pointHoverBorderWidth: 4,
          pointRadius: 4,
          pointHoverRadius: 6,
          tension: 0.35,
          borderWidth: 3
        },
        {
          label: 'Total Pagado',
          data: pagado,
          borderColor: '#05a34a',
          backgroundColor: 'rgba(5,163,74,0.08)',
          fill: true,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#05a34a',
          pointBorderWidth: 2,
          pointHoverBorderWidth: 4,
          pointRadius: 4,
          pointHoverRadius: 6,
          tension: 0.35,
          borderWidth: 3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          labels: {
            color: '#323232',
            font: { size: 13, family: "'Roboto', sans-serif" },
            padding: 16,
            usePointStyle: true
          }
        },
        tooltip: {
          backgroundColor: '#2c3f5c',
          titleFont: { size: 13, family: "'Roboto', sans-serif" },
          bodyFont: { size: 12, family: "'Roboto', sans-serif" },
          padding: 12,
          cornerRadius: 6,
          callbacks: {
            label: function(ctx) {
              return ctx.dataset.label + ': L ' + parseFloat(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
          }
        }
      },
      scales: {
        x: {
          display: true,
          grid: { display: true, color: 'rgba(63,89,129,0.1)', borderColor: 'rgba(63,89,129,0.2)' },
          ticks: { color: '#555555', font: { size: 12 } }
        },
        y: {
          display: true,
          grid: { display: true, color: 'rgba(63,89,129,0.1)', borderColor: 'rgba(63,89,129,0.2)' },
          ticks: {
            color: '#555555',
            font: { size: 12 },
            callback: function(value) { return 'L ' + value.toLocaleString(); }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  });
});
</script>
@endpush
