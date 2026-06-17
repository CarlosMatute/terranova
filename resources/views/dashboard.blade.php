@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h3 class="mb-2">Dashboard Principal</h3>
                <p class="text-muted">Resumen operativo y financiero del proyecto Terranova.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12 col-xl-12 stretch-card">
    <div class="row flex-grow-1">
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-secondary">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Lotes Disponibles</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2 text-primary">{{ $lotes_disponibles->total }}</h3>
                <p class="text-muted"><i data-feather="grid" class="icon-sm"></i> Inventario total</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-secondary">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Clientes Registrados</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2 text-info">{{ $clientes_totales->total }}</h3>
                <p class="text-muted"><i data-feather="users" class="icon-sm"></i> Base de datos activa</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card border-secondary">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline">
              <h6 class="card-title mb-0">Ventas Activas</h6>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <h3 class="mb-2 text-warning">{{ $ventas_activas->total }}</h3>
                <p class="text-muted"><i data-feather="dollar-sign" class="icon-sm"></i> Financiamientos en curso</p>
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
    <div class="card border-secondary">
      <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <h5 class="text-white mb-0"><i data-feather="trending-up" width="16" height="16"></i> Rendimiento de Cobros - {{ $stats->mes_actual }}</h5>
      </div>
      <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <p class="text-muted mb-1">Total a Cobrar</p>
                <h4 class="text-dark">{{ number_format($stats->total_cobrar, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Cobrado</p>
                <h4 class="text-success">{{ number_format($stats->total_pagado, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Por Cobrar</p>
                <h4 class="text-danger">{{ number_format($stats->restante, 2) }}</h4>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Eficiencia</p>
                <h4 class="text-info">{{ $stats->porcentaje }}%</h4>
            </div>
        </div>
        <div class="progress ht-15">
          <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats->porcentaje }}%" aria-valuenow="{{ $stats->porcentaje }}" aria-valuemin="0" aria-valuemax="100">{{ $stats->porcentaje }}%</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
