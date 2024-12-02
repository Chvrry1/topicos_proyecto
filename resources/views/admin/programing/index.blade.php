@extends('adminlte::page')

@section('title', 'ReciclaUSAT')

@section('content')
<div class="container">

    <form id="programming-form" method="GET" action="{{ route('admin.programing.index') }}">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="vehicle_id">Vehículo</label>
                <select id="vehicle_id" name="vehicle_id" class="form-control">
                    <option value="">Seleccione un vehículo</option>
                    @foreach($vehicle as $id => $name)
                        <option value="{{ $id }}" {{ request('vehicle_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="route_id">Ruta</label>
                <select id="route_id" name="route_id" class="form-control">
                    <option value="">Seleccione una ruta</option>
                    @foreach($route as $id => $name)
                        <option value="{{ $id }}" {{ request('route_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="schedule_id">Turno</label>
                <select id="schedule_id" name="schedule_id" class="form-control">
                    <option value="">Seleccione un turno</option>
                    @foreach($schedule as $id => $name)
                        <option value="{{ $id }}" {{ request('schedule_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="date_start">Fecha Inicio</label>
                <input type="date" id="date_start" name="date_start" class="form-control" value="{{ request('date_start') }}">
            </div>

            <div class="col-md-3">
                <label for="date_end">Fecha Fin</label>
                <input type="date" id="date_end" name="date_end" class="form-control" value="{{ request('date_end') }}">
            </div>

            <div class="col-md-3">
                <label for="fines_type">Tipo de Fines</label>
                <select id="fines_type" name="fines_type" class="form-control">
                    <option value="0" {{ request('fines_type') == '0' ? 'selected' : '' }}>Con fines</option>
                    <option value="1" {{ request('fines_type') == '1' ? 'selected' : '' }}>Sin fines</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="programming_type">Tipo de Programación</label>
                <select id="programming_type" name="programming_type" class="form-control">
                    <option value="0" {{ request('programming_type') == '0' ? 'selected' : '' }}>Normal</option>
                    <option value="1" {{ request('programming_type') == '1' ? 'selected' : '' }}>Intercalado</option>
                </select>
            </div>            

            <div class="col-md-3">
                <label for="time_route">Hora de la ruta</label>
                <input type="time" id="time_route" name="time_route" class="form-control" value="{{ request('time_route') }}">
            </div>
        </div>

        <!-- Botones -->
        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" id="buscarBtn">Buscar</button>
                <button type="button" class="btn btn-success" id="generarBtn">Generar</button>
                <button type="button" class="btn btn-warning" id="editarBtn">Editar</button>
                <button type="button" class="btn btn-danger" id="eliminarBtn">Eliminar</button>
            </div>
        </div>
    </form>

    <!-- Tabla para mostrar las rutas generadas -->
    <table class="table table-striped" id="programming-table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Vehículo</th>
                <th>Ruta</th>
                <th>Fecha</th>
                <th>Turno</th>
                <th>Horario</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargarán dinámicamente con DataTables -->
        </tbody>
    </table>
</div>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#select-all').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('.select-record').prop('checked', isChecked);
        });

        var table = $('#programming-table').DataTable({
            processing: true,
            serverSide: true,
            "ajax": {
                "url": '{{ route('admin.programing.index') }}',
                "data": function(d) {
                    d.vehicle_id = $('#vehicle_id').val();
                    d.route_id = $('#route_id').val();
                    d.schedule_id = $('#schedule_id').val();
                    d.date_end = $('#date_end').val();
                    d.sin_fines = $('#fines_type').val();
                    d.intercalado = $('#programming_type').val();
                },
                "dataSrc": function (json) {
                    return json.data || [];
                }
            },
            "columns": [
                {
                    data: 'id',
                    render: function(data) {
                        return '<input type="checkbox" class="select-record" data-id="' + data + '">';
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'vehicle.name' },
                { data: 'route.name' },
                { data: 'date_route' },
                { data: 'schedule.name' },
                { data: 'time_route' },
            ]
        });

        $('#buscarBtn').on('click', function() {
            table.ajax.reload();
        });

        $('#generarBtn').on('click', function() {
            $('#loading-spinner').show();
            $.ajax({
                url: '{{ route('admin.programing.store') }}',
                method: 'POST',
                data: $('#programming-form').serialize(),
                success: function(response) {
                    alert(response.message);
                    table.ajax.reload();
                },
                error: function(response) {
                    alert('Error al generar programación: ' + response.responseJSON.message);
                },
                complete: function() {
                    $('#loading-spinner').hide();  // Oculta el spinner después de la respuesta
                }
            });
        });


        // Editar
        $('#editarBtn').on('click', function() {
            var selectedIds = getSelectedIds();
            if (selectedIds.length > 0) {
                $.ajax({
                    url: '{{ route('admin.programing.update') }}',
                    method: 'PUT',
                    data: {
                        ids: selectedIds,
                        time_route: $('#time_route').val(),
                        vehicle_id: $('#vehicle_id').val(),
                        route_id: $('#route_id').val(),
                        schedule_id: $('#schedule_id').val(),
                        
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        table.ajax.reload();
                    },
                    error: function(response) {
                        alert('Error al editar: ' + response.responseJSON.message);
                    }
                });
            } else {
                alert('Seleccione al menos un registro para editar.');
            }
        });

        // Eliminar
        $('#eliminarBtn').on('click', function() {
            var selectedIds = getSelectedIds();
            if (selectedIds.length > 0) {
                if (confirm('¿Está seguro de que desea eliminar los registros seleccionados?')) {
                    $.ajax({
                        url: '{{ route('admin.programing.destroy') }}',
                        method: 'DELETE',
                        data: {
                            ids: selectedIds,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert(response.message);
                            table.ajax.reload();
                        },
                        error: function(response) {
                            alert('Error al eliminar: ' + response.responseJSON.message);
                        }
                    });
                }
            } else {
                alert('Seleccione al menos un registro para eliminar.');
            }
        });

        // Función para obtener los IDs de los registros seleccionados
        function getSelectedIds() {
            var selectedIds = [];
            $('.select-record:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });
            return selectedIds;
        }
    });
</script>


@endsection
