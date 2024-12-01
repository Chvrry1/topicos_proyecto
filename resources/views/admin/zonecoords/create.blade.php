{!! Form::open(['route' => 'admin.zonecoords.store', 'id' => 'zonecoords_form']) !!}
@include('admin.zonecoords.template.form')
<button type="submit" id= submit_button class="btn btn-success"><i class="fas fa-save"></i> Agregar</button>
<button type="button" id="undo_button" class="btn btn-warning">
    <i class="fas fa-undo"></i> Deshacer
</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-arrow-alt-circle-left"></i> Cerrar</button>
{!! Form::close() !!}


@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop
