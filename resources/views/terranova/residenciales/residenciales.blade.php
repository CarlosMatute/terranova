@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/cropperjs/cropper.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/easymde/easymde.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<style>
   .file-upload {
      border: 2px dashed #ccc;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: 0.2s;
    }
    .file-upload:hover {
      background-color: #f9f9f9;
    }
    .file-list {
      margin-top: 15px;
    }
    .file-item {
      background: #f2f2f2;
      border-radius: 6px;
      padding: 8px 12px;
      margin-bottom: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
    }
    .file-item button {
      background: none;
      border: none;
      color: #d33;
      font-weight: bold;
      cursor: pointer;
      font-size: 16px;
    }
</style>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Residenciales</h4>
                <p class="text-muted">
                    En este modulo puede registrar y administrar todas las residenciales, sus bloques y sus lotes.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md-12 col-xl-12">
        <div class="card border-secondary">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">
                    <i class="text-white icon-lg pb-3px" data-feather="home"></i> Residenciales Registradas
                </h5>
                <button
                    class="btn btn btn-light btn-xs"
                    id="btn_agregar_permiso"
                    data-bs-toggle="modal"
                    data-bs-target="#modal_agregar_residencial"
                >
                    <i class="btn-icon-prepend" data-feather="plus"></i> Registrar Residencial
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="jambo_table table table-hover" id="tbl_residenciales" border="1">
                        <thead class="bg-secondary">
                            <tr class="headings">
                                <th scope="col" class="text-white">Id</th>
                                <th scope="col" class="text-white">Imagen</th>
                                <th scope="col" class="text-white">Nombre</th>
                                <th scope="col" class="text-white">Descripcion</th>
                                <th scope="col" class="text-white">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($residenciales as $row)
                            <tr style="font-size: small">
                                <td scope="row">{{$row->id}}</td>
                                <td scope="row">
                                    <div class="me-3">
                                        <img class="wd-30 ht-30 rounded-circle" src="{{ url(asset('/assets/images/')) }}/{{ $row->imagen }}" alt="user" onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/homes.png')) }}';">
                                    </div>
                                </td>
                                <td scope="row">{{$row->nombre}}</td>
                                <td scope="row">{{$row->descripcion}}</td>
                                <td scope="row">
                                    <button
                                        type="button"
                                        class="btn btn-warning btn-icon btn-xs btn_editar_rol"
                                        data-bs-toggle="modal"
                                        data-bs-target=".modal_agregar_residencial"
                                    >
                                        <i data-feather="check-square"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-icon btn-xs"
                                        data-bs-toggle="modal"
                                        data-bs-target=".modal_eliminar_permiso"
                                    >
                                        <i data-feather="trash-2"></i>
                                    </button>
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

<div class="modal fade bd-example modal_agregar_residencial" id="modal_agregar_residencial" tabindex="-1" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h6 class="modal-title h6 text-white" id="myExtraLargeModalLabel"><i class="icon-lg pb-3px" data-feather="plus"></i> Registrar Nueva Residencial</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <label for="modal_agregar_residencial_nombre" class="form-label">Nombre</label>
                                        <input id="modal_agregar_residencial_nombre" class="form-control" type="text" placeholder="Nombre de la residencial..."/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="modal_agregar_residencial_bloques" class="form-label">Cantidad de Bloques</label>
                                        <input id="modal_agregar_residencial_bloques" class="form-control" type="number" placeholder="¿Cuantos bloques?"/>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="modal_agregar_residencial_descripcion" class="form-label">Descripción <small class="text-muted">(Opcional)</small></label>
                                        </div>
                                        <textarea class="form-control" name="tinymce" id="modal_agregar_residencial_descripcion"
                                            rows="4"
                                            placeholder="Escriba aquí..."></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                                        <div class="card-body">
                                            <h6 class="card-title">Imagen de la Residencial <small class="text-muted">(Opcional)</small></h6>
                                            <!-- <p class="text-muted mb-3">Arrastra y suelta tus archivos aquí, o haz clic para seleccionarlos y cargarlos.</p>
                                            <form action="#" class="dropzone" id="adjuntos_solicitud"></form> -->


                                            <div class="file-upload" id="fileUpload">
                                            <p>Arrastra o haz clic para seleccionar la imagen</p>
                                            <input type="file" id="inputArchivos" accept="image/*" hidden>
                                            </div>

                                            <div id="fileList" class="file-list"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="modal_agregar_permiso_pagina" class="form-label">Página</label>
                                        <select class="form-select" id="modal_agregar_permiso_pagina">
                                            <option selected disabled>Seleccione una página</option>
                                           
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="modal_agregar_permiso_requisito" class="form-label">Requisito</label>
                                        <select class="form-select" id="modal_agregar_permiso_requisito">
                                            <option selected disabled>Seleccione un permiso</option>
                                           
                                        </select>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-secondary">
                <button type="button" class="btn btn-danger btn-xs" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary btn-xs" id="btn_guardar_permiso">Guardar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/cropperjs/cropper.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/easymde/easymde.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/cropper.js') }}"></script>
  <script src="{{ asset('assets/js/data-table.js') }}"></script>
  <script src="{{ asset('assets/js/dropzone.js') }}"></script>
  <script src="{{ asset('assets/js/tinymce.js') }}"></script>
  <script src="{{ asset('assets/js/easymde.js') }}"></script>
  <script src="{{ asset('assets/js/alertas_propias.js') }}"></script>
  <script type="text/javascript">
    var table = null; 
    var accion = null;
    var btn_activo = true;
    var id = null;
    var nombre = null;
    var bloques = null;
    var descripcion = null;
    var id_pagina = null;
    var id_permiso_requisito = null;
    var url_guardar_permiso = "{{url('/setic/permisos/guardar')}}"; 
    var rowNumber=null;
    var id_seleccionar = localStorage.getItem("tbl_permisos_id_seleccionar");
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        table = $('#tbl_residenciales').DataTable({
                "aLengthMenu": [
                    [10, 30, 50, 100,-1],
                    [10, 30, 50, 100,"Todo"]
                ],
                "iDisplayLength": 10,
                language: {
                    processing:     "Procesando...",
                    search:         "Buscar:",
                    lengthMenu:     "Mostrar _MENU_ registros",
                    info:           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty:      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered:   "(filtrado de un total de _MAX_ registros)",
                    infoPostFix:    "",
                    loadingRecords: "Cargando...",
                    zeroRecords:    "No se encontraron resultados",
                    emptyTable:     "Ningún dato disponible en esta tabla",
                    paginate: {
                        first:      "Primero",
                        previous:   "Anterior",
                        next:       "Siguiente",
                        last:       "Último"
                    },
                    aria: {
                        sortAscending:  ": Activar para ordenar la columna de manera ascendente",
                        sortDescending: ": Activar para ordenar la columna de manera descendente"
                    }
                }
                });
            $('#tbl_residenciales').each(function() {
                var datatable = $(this);
                // SEARCH - Add the placeholder for Search and Turn this into in-line form control
                var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
                search_input.attr('placeholder', 'Buscar');
                search_input.removeClass('form-control-sm');
                // LENGTH - Inline-Form control
                var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
                length_sel.removeClass('form-control-sm');
                });

            $("#tbl_residenciales tbody").on( "click", "tr", function () { 
                rowNumber=parseInt(table.row( this ).index()); 
                accion=2; 
                table.$('tr.selected').removeClass('selected'); 
                $(this).addClass('selected'); 
                localStorage.setItem("tbl_permisos_id_seleccionar",table.row( this ).data()[0]); 
            });

  });
        
        const inputArchivos = document.getElementById('inputArchivos');
        const fileUpload = document.getElementById('fileUpload');
        const fileList = document.getElementById('fileList');

        let archivoSeleccionado = null;

        // Abrir selector
        fileUpload.addEventListener('click', () => inputArchivos.click());

        // Drag & Drop
        fileUpload.addEventListener('dragover', e => {
        e.preventDefault();
        fileUpload.style.backgroundColor = '#eef';
        });

        fileUpload.addEventListener('dragleave', () => {
        fileUpload.style.backgroundColor = '';
        });

        fileUpload.addEventListener('drop', e => {
        e.preventDefault();
        fileUpload.style.backgroundColor = '';
        manejarArchivo(e.dataTransfer.files);
        });

        // Selección manual
        inputArchivos.addEventListener('change', e => manejarArchivo(e.target.files));

        // 🔥 Nueva función (solo 1 archivo)
        function manejarArchivo(files) {
        const file = files[0]; // solo el primero

        if (!file) return;

        // Validar que sea imagen
        if (!file.type.startsWith('image/')) {
            alert('Solo se permiten imágenes');
            inputArchivos.value = '';
            return;
        }

        // Reemplaza el archivo anterior
        archivoSeleccionado = file;

        mostrarArchivo();
        }

        // Mostrar archivo
        function mostrarArchivo() {
        fileList.innerHTML = '';

        if (!archivoSeleccionado) return;

        const item = document.createElement('div');
        item.className = 'file-item';

        item.innerHTML = `
            <span>${archivoSeleccionado.name} (${(archivoSeleccionado.size/1024).toFixed(1)} KB)</span>
            <button onclick="eliminarArchivo()">&times;</button>
        `;

        fileList.appendChild(item);
        }

        // Eliminar archivo
        function eliminarArchivo() {
        archivoSeleccionado = null;
        inputArchivos.value = '';
        mostrarArchivo();
        }

        $("#btn_guardar_permiso").on("click", function () {
            nombre = $("#modal_agregar_residencial_nombre").val();
            bloques = $("#modal_agregar_residencial_bloques").val();
            descripcion = $("#modal_agregar_permioso_descripcion").val();

                if(nombre == null || nombre == ''){
                    Toast.fire({
                        icon: 'error',
                        title: 'Valor requerido para Nombre.'
                    })
                    return true;
                }

                if(bloques == null || bloques == ''){
                    Toast.fire({
                        icon: 'error',
                        title: 'Valor requerido para Cantidad de Bloques.'
                    })
                    return true;
                }

                // if(descripcion == null || descripcion == ''){
                //     Toast.fire({
                //         icon: 'error',
                //         title: 'Valor requerido para Descripción.'
                //     })
                //     return true;
                // }
                
                if(btn_activo){
                    //guardar_permiso();
                    alert('Función de guardar en construcción');
                }
                
        });
  </script>
@endpush