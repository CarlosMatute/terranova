@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <style>
        .profile-header {
            background: linear-gradient(135deg, var(--ins-azul) 0%, var(--ins-azul-oscuro) 100%);
            border-radius: 12px 12px 0 0;
            padding: 40px 30px 60px;
            position: relative;
        }
        .profile-avatar-wrapper {
            position: relative;
            background: white;
            border-radius: 0 0 12px 12px;
            padding: 0 30px 20px;
            margin-top: -40px;
        }
        .profile-avatar {
            border: 4px solid white;
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-top: -50px;
            display: inline-block;
        }
        .card-header.bg-azul h5 { font-size: 0.95rem; }
        .table thead.bg-azul-oscuro th { font-weight: 500; font-size: 0.85rem; }
        .info-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #6c757d; letter-spacing: 0.5px; }
        .info-value { font-size: 0.9rem; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card border-0 overflow-hidden" style="border-radius: 12px;">
                <div class="profile-header">
                    <div class="position-absolute end-0 top-0 opacity-10" style="transform: translate(20%, -20%);">
                        <i data-feather="user" width="140" height="140" class="text-white"></i>
                    </div>
                </div>
                <div class="profile-avatar-wrapper">
                    <div class="d-flex align-items-end justify-content-between">
                        <div class="d-flex align-items-end">
                            <img src="{{ $cliente->imagen ? asset('storage/clientes/cli_' . $cliente->id . '/' . $cliente->imagen) : asset('/assets/images/placeholder_user.png') }}"
                                class="profile-avatar"
                                onerror="this.onerror=null; this.src='{{ asset('/assets/images/placeholder_user.png') }}';">
                            <div class="ms-3" style="padding-bottom: 4px;">
                                <h3 class="fw-bold mb-0" style="color: var(--ins-azul-oscuro);">{{ $cliente->nombre_completo }}</h3>
                                <p class="text-muted mb-0">{{ $cliente->identidad }}</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ url('clientes') }}" class="btn btn-azul btn-xs">
                                <i data-feather="arrow-left" width="14" height="14"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card border-azul mb-3">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="info" width="16" height="16"></i> Información Personal</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="info-label">Nombre Completo</div>
                        <div class="info-value">{{ $cliente->nombre_completo }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Identidad</div>
                        <div class="info-value">{{ $cliente->identidad }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Teléfono 1</div>
                        <div class="info-value">{{ $cliente->contacto_telefonico }}</div>
                    </div>
                    @if ($cliente->contacto_telefonico_2)
                    <div class="mb-3">
                        <div class="info-label">Teléfono 2</div>
                        <div class="info-value">{{ $cliente->contacto_telefonico_2 }}</div>
                    </div>
                    @endif
                    @if ($cliente->correo_electronico)
                    <div class="mb-3">
                        <div class="info-label">Correo Electrónico</div>
                        <div class="info-value">{{ $cliente->correo_electronico }}</div>
                    </div>
                    @endif
                    <div class="mb-0">
                        <div class="info-label">Dirección</div>
                        <div class="info-value">{{ $cliente->direccion }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-azul mb-3">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="users" width="16" height="16"></i> Referencias</h5>
                </div>
                <div class="card-body p-0">
                    @if (count($referencias) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="bg-azul-oscuro">
                                    <tr>
                                        <th class="text-white">Nombre</th>
                                        <th class="text-white">Teléfono</th>
                                        <th class="text-white">Dirección</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($referencias as $r)
                                        <tr>
                                            <td>{{ $r->nombre_completo }}</td>
                                            <td>{{ $r->contacto_telefonico }}</td>
                                            <td>{{ $r->direccion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">Sin referencias registradas</div>
                    @endif
                </div>
            </div>

            <div class="card border-azul mb-3">
                <div class="card-header bg-azul text-white">
                    <h5 class="text-white mb-0"><i data-feather="heart" width="16" height="16"></i> Beneficiarios</h5>
                </div>
                <div class="card-body p-0">
                    @if (count($beneficiarios) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="bg-azul-oscuro">
                                    <tr>
                                        <th class="text-white">Nombre</th>
                                        <th class="text-white">Identidad</th>
                                        <th class="text-white">Parentezco</th>
                                        <th class="text-white">Teléfono</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($beneficiarios as $b)
                                        <tr>
                                            <td>{{ $b->nombre_completo }}</td>
                                            <td>{{ $b->identidad }}</td>
                                            <td>{{ $b->parentezco }}</td>
                                            <td>{{ $b->contacto_telefonico }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">Sin beneficiarios registrados</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-azul">
                <div class="card-header bg-azul text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0"><i data-feather="shopping-cart" width="16" height="16"></i> Ventas</h5>
                </div>
                <div class="card-body">
                    @if (count($ventas) > 0)
                        <div class="table-responsive">
                            <table class="jambo_table table table-hover" id="tbl_ventas">
                                <thead class="bg-azul-oscuro">
                                    <tr>
                                        <th class="text-white"># Venta</th>
                                        <th class="text-white">Fecha</th>
                                        <th class="text-white">Tipo Pago</th>
                                        <th class="text-white">Estado</th>
                                        <th class="text-white">Total</th>
                                        <th class="text-white">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ventas as $v)
                                        <tr>
                                            <td>{{ $v->id }}</td>
                                            <td>{{ $v->fecha_venta }}</td>
                                            <td>{{ $v->tipo_pago }}</td>
                                            <td>
                                                <span class="badge {{ $v->estado == 'Pagado' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ $v->estado }}
                                                </span>
                                            </td>
                                            <td>L {{ number_format($v->total_pagar, 2) }}</td>
                                            <td>
                                                <a href="{{ url('ventas/detalle/' . $v->id) }}" class="btn btn-azul-claro btn-xs">
                                                    <i data-feather="eye" width="14" height="14"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i data-feather="shopping-cart" width="48" height="48" class="text-muted mb-2"></i>
                            <p class="text-muted mb-0">Este cliente no tiene ventas registradas</p>
                        </div>
                    @endif
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
            if ($.fn.dataTable.isDataTable('#tbl_ventas')) {
                $('#tbl_ventas').DataTable().destroy();
            }
            if ($('#tbl_ventas tbody tr').length > 0) {
                $('#tbl_ventas').DataTable({
                    language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" },
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]]
                });
            }
        });
    </script>
@endpush
