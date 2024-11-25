@extends('adminlte::page')

@section('title', 'ReciclaUSAT')

{{-- @section('content_header')
  <h1>Marcas</h1>
@stop --}}

@section('content')
    <div class="p-2"></div>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-success float-right" id="btnNuevo"><i class="fas fa-plus"></i> Nuevo Personal</button>
            <h3>Gestión de Personal</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped" id="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRES</th>
                        <!-- <th>DNI</th> -->
                        <th>CORREO</th>
                        <!-- <th>password</th> -->
                        <th>TIPO</th>
                        <th>ZONA</th>
                        <th>ACCIONES</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Formulario de Personal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se cargará dinámicamente el formulario -->
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
        // Listar todos los registros
        $(document).ready(function() { 
            var table = $('#datatable').DataTable({
                "ajax": "{{ route('admin.users.index') }}",
                "columns": [
                    { "data": "id" },
                    { "data": "name" },
                    // { "data": "dni" },
                    { "data": "email" },
                    { "data": "usertype_id" },
                    { "data": "zone_id" },
                    { "data": "actions", "orderable": false, "searchable": false }
                ],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                }
            });
        });

        //metod Create
        $('#btnNuevo').click(function() {
            $.ajax({
                url: "{{ route('admin.users.create') }}",
                type: "GET",
                success: function(response) {
                    $("#formModal .modal-body").html(response);
                    $("#formModal #exampleModalLabel").text("Registro De Nuevo Personal");
                    $("#formModal").modal("show");

                    // Manejo de la creación
                    $("#tipoForm").on("submit", function(e) {
                        e.preventDefault();
                        var form = $(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function(response) {
                                $("#formModal").modal("hide");
                                refreshTable();
                                Swal.fire('Éxito', response.message, 'success');
                            },
                            error: function(xhr) {
                                console.log("Detalle errror:", xhr.responseText)
                                Swal.fire('Error', 'No se pudo completar el proceso', 'error');
                            }
                        });
                    });
                }
            });
        });
        
        //Metod edit
        $(document).on('click', '.btnEditar', function() {
            var id = $(this).attr("id");
            $.ajax({
                url: `/admin/users/${id}/edit`,
                type: "GET",
                success: function(response) {
                    $("#formModal .modal-body").html(response);
                    $("#formModal #exampleModalLabel").text("Editar Datos De Personal");
                    $("#formModal").modal("show");

                    // Manejo de la edición
                    $("#tipoForm").on("submit", function(e) {
                        e.preventDefault();
                        var form = $(this);

                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            success: function(response) {
                                $("#formModal").modal("hide");
                                refreshTable();
                                Swal.fire('Éxito', response.message, 'success');
                            },
                            error: function(xhr) {                               
                               // console.log("Response:", xhr.responseText);
                                Swal.fire('Error', 'No se pudo completar el proceso', 'error');
                            }
                        });
                    });
                }
            });
        });

        // Método delete (eliminar usuario)
        $(document).on('click', '.btnEliminar', function(e) {
            e.preventDefault();  // Prevenir comportamiento por defecto
            var form = $(this).closest("form");
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Este usuario será eliminado permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminarlo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'DELETE',
                        data: form.serialize(), // Incluye el token CSRF
                        success: function(response) {
                            if (response.message) {
                                Swal.fire('¡Eliminado!', response.message, 'success');
                            } else if (response.error) {
                                Swal.fire('Error', response.error, 'error');
                            }
                            refreshTable(); // Recargar la tabla (si está implementada)
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
                        }
                    });
                }
            });
        });

        function refreshTable() {
            $('#datatable').DataTable().ajax.reload(null, false);
        }
    </script>
@endsection
