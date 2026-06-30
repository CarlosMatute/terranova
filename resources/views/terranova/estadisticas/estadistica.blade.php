@extends('layout.master')

@push('plugin-styles')
<style>
.stat-card { transition: transform 0.2s, box-shadow 0.2s; cursor: default; border-radius: 12px; overflow: hidden; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(63,89,129,0.15) !important; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; }
.stat-label { font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; }
.progress-stats { height: 8px; border-radius: 4px; background: rgba(63,89,129,0.1); }
.progress-stats .progress-bar { border-radius: 4px; transition: width 1s ease; }
.card-residencial { border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; border: none; }
.card-residencial:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(63,89,129,0.18) !important; }
.residencial-img { width: 100%; height: 140px; object-fit: cover; border-radius: 12px 12px 0 0; }
.card-header-dash { background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul)); border-bottom: none; padding: 0.85rem 1.25rem; }
.card-header-dash h6 { font-weight: 600; font-size: 0.85rem; letter-spacing: 0.3px; }
.badge-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.table-dashboard th { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--ins-azul-oscuro); border-bottom-width: 1px; }
.table-dashboard td { font-size: 0.85rem; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, var(--ins-azul-oscuro), var(--ins-azul));">
            <div class="card-body d-flex align-items-center justify-content-between py-3">
                <div>
                    <h3 class="mb-1 text-white fw-bold">
                        <i data-feather="bar-chart-2" width="24" height="24" class="me-2"></i>Estad&iacute;sticas
                    </h3>
                    <p class="mb-0" style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                        {{ count($residenciales) }} residencial(es) registrado(s)
                    </p>
                </div>
                <div class="d-none d-md-flex gap-2">
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="home" width="14" height="14" class="me-1"></i> {{ count($residenciales) }} Residenciales
                    </span>
                    <span class="badge bg-white text-azul px-3 py-2 rounded-pill">
                        <i data-feather="dollar-sign" width="14" height="14" class="me-1"></i>
                        L {{ number_format(collect($residenciales)->sum('total_esperado'), 2) }} Esperado
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    @forelse ($residenciales as $r)
    @php
        $pct = $r->total_esperado > 0 ? round(($r->total_cobrado * 100 / $r->total_esperado), 1) : 0;
        $color = $pct >= 75 ? '#05a34a' : ($pct >= 40 ? '#d4a017' : '#dc3545');
        $disponibles = $r->total_lotes - $r->lotes_vendidos - $r->lotes_apartados;
    @endphp
    <div class="col-xl-4 col-md-6">
        <a href="{{ url('/residenciales/' . $r->id . '/estadisticas') }}" class="text-decoration-none">
            <div class="card card-residencial shadow-sm h-100">
                <div style="position: relative;">
                    <img src="{{ $r->imagen ? asset('storage/' . $r->imagen) : asset('assets/images/placeholder_user.png') }}"
                         alt="{{ $r->nombre }}"
                         class="residencial-img"
                         style="{{ !$r->imagen ? 'object-fit:contain; padding: 20px; opacity: 0.3;' : '' }}">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(255,255,255,0.9); color: var(--ins-azul-oscuro); font-weight: 600;">
                            {{ $r->lotes_vendidos }}/{{ $r->total_lotes }} lotes
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold mb-1" style="color: var(--ins-azul-oscuro);">{{ $r->nombre }}</h5>
                    <p class="text-muted mb-3" style="font-size: 0.8rem;">{{ Str::limit($r->descripcion, 80) }}</p>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-4">
                            <div class="fw-bold" style="font-size: 1.1rem; color: #05a34a;">{{ $r->lotes_vendidos }}</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Vendidos</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold" style="font-size: 1.1rem; color: #d4a017;">{{ $r->lotes_apartados }}</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Apartados</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold" style="font-size: 1.1rem; color: var(--ins-azul);">{{ max(0, $disponibles) }}</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">Disponibles</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 0.75rem; font-weight: 600; color: var(--ins-azul);">
                            L {{ number_format($r->total_cobrado, 0) }}
                        </span>
                        <span style="font-size: 0.75rem; color: #888;">
                            de L {{ number_format($r->total_esperado, 0) }}
                        </span>
                    </div>
                    <div class="progress-stats">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $pct }}%; background: {{ $color }};"
                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span style="font-size: 0.7rem; color: {{ $color }}; font-weight: 600;">{{ $pct }}% recaudado</span>
                        <span style="font-size: 0.7rem; color: #888;">{{ $r->total_ventas }} venta(s)</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i data-feather="bar-chart-2" width="60" height="60" class="mb-3" style="color: #ccc;"></i>
                <h5 class="text-muted">No hay residenciales registrados</h5>
                <p class="text-muted mb-0">Crea un residencial para ver estad&iacute;sticas.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection

@push('custom-scripts')
<script>
$(function() {
    feather.replace();
});
</script>
@endpush
