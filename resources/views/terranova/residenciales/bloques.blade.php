@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('residenciales') }}">Residenciales</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bloques</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <img src="{{ asset('storage/residenciales/res_' . $residencial->id . '/' . $residencial->imagen) }}"
                            class="wd-90 ht-90 me-3" alt="..."
                            onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                        <div>
                            <h3 class="mb-2">Residencial: {{ $residencial->nombre }}</h3>
                            <p class="text-muted">Gestión de bloques para esta residencial.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12 col-xl-12">
            <div class="card border-secondary">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">
                        <i class="text-white icon-lg pb-3px" data-feather="square"></i> Bloques Registrados
                    </h5>
                    <button type="button" class="btn btn-light btn-xs" id="btn_agregar_bloque" data-bs-toggle="modal"
                        data-bs-target="#modal_agregar_bloque">
                        <i data-feather="plus" width="16" height="16"></i> Registrar Bloque
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="jambo_table table table-hover" id="tbl_bloques" border="1">
                            <thead class="bg-secondary">
                                <tr class="headings">
                                    <th scope="col" class="text-white">Id</th>
                                    <th scope="col" class="text-white">Bloque</th>
                                    <th scope="col" class="text-white">Lotes</th>
                                    <th scope="col" class="text-white">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bloques as $row)
                                    <tr style="font-size: small">
                                        <td>{{ $row->id }}</td>
                                        <td><h4><span class="badge bg-primary">{{ $row->bloque }}</span></h4></td>
                                        <td>{{ $row->lotes }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if ($row->ultimo)
                                                    <button type="button" class="btn btn-danger btn-xs btn_eliminar_bloque" data-id="{{ $row->id }}" data-bloque="{{ $row->bloque }}">
                                                        <i data-feather="trash-2" width="14" height="14"></i> Eliminar
                                                    </button>
                                                @endif
                                                <a href="{{ url('residenciales/' . $residencial->id . '/bloques/' . $row->id) }}" class="btn btn-success btn-xs">
                                                    <i data-feather="grid" width="14" height="14"></i> Lotes
                                                </a>
                                            </div>
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

    <!-- Modal Bloque -->
    <div class="modal fade" id="modal_agregar_bloque" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h6 class="modal-title h6 text-white">Registrar Bloque</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Siguiente Bloque</label>
                            <button type="button" class="btn btn-primary form-control">{{ $bloque_siguiente->nombre }}</button>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Cantidad Lotes</label>
                            <input id="modal_bloque_cantidad" class="form-control" type="number" value="1" />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Precio x Lote</label>
                            <input id="modal_bloque_precio" class="form-control" type="number" />
                        </div>
                    </div>
                    <hr>
                    <h6>Colindancias y Area</h6>
                    <div class="row mt-2">
                        <div class="col-md-3 mb-3"><label>Norte</label><input id="modal_bloque_norte" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label>Sur</label><input id="modal_bloque_sur" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label>Este</label><input id="modal_bloque_este" class="form-control" type="number" /></div>
                        <div class="col-md-3 mb-3"><label>Oeste</label><input id="modal_bloque_oeste" class="form-control" type="number" /></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Area (m²)</label><input id="modal_bloque_area" class="form-control" type="number" /></div>
                        <div class="col-md-6 mb-3"><label>Financiamiento (años)</label><input id="modal_bloque_finan" class="form-control" type="number" /></div>
                    </div>
                </div>
                <div class="modal-footer bg-secondary">
                    <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal"><i data-feather="x" width="16" height="16"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary btn-xs" id="btn_confirmar_guardar"><i data-feather="save" width="16" height="16"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            $('#tbl_bloques').DataTable({
                responsive: true,
                language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json" }
            });
        });

        $('#btn_confirmar_guardar').on('click', function() {
            var data = {
                id_residencial: {{ $residencial->id }},
                id_bloque_siguiente: {{ $bloque_siguiente->id }},
                cantidad_lotes: $('#modal_bloque_cantidad').val(),
                precio_lote: $('#modal_bloque_precio').val(),
                norte: $('#modal_bloque_norte').val(),
                sur: $('#modal_bloque_sur').val(),
                este: $('#modal_bloque_este').val(),
                oeste: $('#modal_bloque_oeste').val(),
                area: $('#modal_bloque_area').val(),
                financiamiento: $('#modal_bloque_finan').val(),
                accion: 1
            };

            $.ajax({
                type: "POST",
                url: "{{ url('/residenciales/bloques/guardar') }}",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: data,
                success: function(res) {
                    if (res.msgError) Swal.fire('Error', res.msgError, 'error');
                    else Swal.fire('Éxito', res.msgSuccess, 'success').then(() => location.reload());
                }
            });
        });

        $(document).on('click', '.btn_eliminar_bloque', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: '¿Eliminar bloque?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "{{ url('/residenciales/bloques/guardar') }}",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: { id: id, accion: 3 },
                        success: function(res) { location.reload(); }
                    });
                }
            });
        });
    </script>
@endpush
