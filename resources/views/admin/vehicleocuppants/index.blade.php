@extends('adminlte::page')

@section('title', 'ReciclaUSAT')

{{-- @section('content_header')
  <h1>Marcas</h1>
@stop --}}

@section('content')
    <div class="p-2"></div>
    <div class="card">
        <div class="card-header">
            {{-- Botón "Nuevo" siempre deshabilitado si se alcanza la capacidad máxima --}}
            <button class="btn btn-success float-right" id="btnNuevo" @if ($disableNewButton) disabled @endif>
                <i class="fas fa-plus"></i> Nuevo
            </button>
            <h3>Ocupantes del {{ $vehicleName }}</h3>
        </div>

        {{-- Mostrar alerta si se ha alcanzado la capacidad máxima --}}
        @if (isset($capacityReachedMessage))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ $capacityReachedMessage }}
            </div>
        @endif

        {{-- Mostrar alerta si falta un conductor y se alcanzó la capacidad máxima --}}
        @if ($needsConductorAlert)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                El vehículo ha alcanzado su capacidad máxima, pero no tiene un conductor registrado. Por favor, registre un
                conductor.
            </div>
        @endif

        <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>TIPO DE USUARIO</th>
                        <th>USUARIO</th>
                        <th>ESTADO</th>
                        <th width="10"></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de los ocupantes del {{ $vehicleName }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
@section('js')
    <script>
        /*$(document).ready(function() {
                                                                                    $('#datatable').DataTable({
                                                                                        language: {
                                                                                            url: '//cdn.datatables.net/plug-ins/2.1.7/i18n/es-MX.json',
                                                                                        },
                                                                                    });
                                                                                })*/

        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                "ajax": "{{ url('admin/vehicleocuppants') }}/{{ $vehicleId }}", // La ruta que llama al controlador vía AJAX
                "columns": [{
                        "data": "id",
                    },
                    {
                        "data": "usertype",
                    },
                    {
                        "data": "user",
                    },

                    {
                        "data": "status",
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "searchable": false,
                    }
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });

        $('#btnNuevo').click(function() {
            $.ajax({
                url: "{{ url('admin/vehicleocuppants/create') }}/{{ $vehicleId }}",
                type: "GET",
                success: function(response) {
                    $("#formModal #exampleModalLabel").html(
                        "Nuevo ocupante del vehículo {{ $vehicleName }}");
                    $("#formModal .modal-body").html(response);
                    $("#formModal").modal("show");

                    $("#formNuevoOcupante").on("submit", function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.type === 'info') {
                                    // Si es un mensaje informativo
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Información',
                                        text: response.message,
                                    });
                                } else {
                                    // Si es un registro exitoso
                                    $("#formModal").modal("hide");
                                    refreshTable();
                                    Swal.fire('Proceso exitoso', response.message,
                                        'success');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 400) {
                                    // Manejo de errores 400 (capacidad máxima alcanzada)
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON.message ||
                                            'No se pueden agregar más ocupantes.',
                                    });
                                } else if (xhr.status === 500) {
                                    // Manejo de errores 500 (error del servidor)
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error en el servidor',
                                        text: 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.',
                                    });
                                } else {
                                    // Otros errores
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON.message ||
                                            'Algo salió mal.',
                                    });
                                }
                            }

                        });

                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cargar el formulario', 'error');
                }
            });
        });



        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr("id");

            $.ajax({
                url: "{{ route('admin.vehicleocuppants.edit', ':id') }}".replace(':id', id), // Ruta para la edición
                type: "GET",
                success: function(response) {
                    $("#formModal #exampleModalLabel").html("Modificar Ocupante");
                    $("#formModal .modal-body").html(response); // Cargar el formulario en el modal
                    $("#formModal").modal("show"); // Mostrar el modal

                    // Manejo del formulario
                    $("#formEditarOcupante").on("submit", function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var formData = new FormData(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $("#formModal").modal("hide");
                                refreshTable(); // Recargar la tabla
                                Swal.fire('Éxito',
                                    'Ocupante actualizado correctamente.',
                                    'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Ocurrió un error al actualizar.', 'error');
                            }
                        });
                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo cargar el formulario de edición.', 'error');
                }
            });
        });


        $(document).on('submit', '.frmEliminar', function(e) {
            e.preventDefault();
            var form = $(this);
            Swal.fire({
                title: "Está seguro de eliminar?",
                text: "Está acción no se puede revertir!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        success: function(response) {
                            refreshTable();
                            Swal.fire('Proceso existoso', response.message, 'success');
                        },
                        error: function(xhr) {
                            var response = xhr.responseJSON;
                            Swal.fire('Error', response.message, 'error');
                        }
                    });
                }
            });
        });

        function refreshTable() {
            var table = $('#datatable').DataTable();
            table.ajax.reload(null, false); // Recargar datos sin perder la paginación
        }
    </script>

@endsection
