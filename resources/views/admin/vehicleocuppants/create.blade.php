{!! Form::open(['route' => ['admin.vehicleocuppants.store', $vehicleId], 'method' => 'POST', 'id' => 'formNuevoOcupante']) !!}

@if($hasConductor)
    <div class="alert alert-info" id="alertConductor">
        Este vehículo ya tiene un conductor asignado. No se puede registrar otro.
    </div>
@endif

@include('admin.vehicleocuppants.template.form')

<button type="submit" class="btn btn-success" id="btnRegistrar"><i class="fas fa-save"></i> Registrar</button>
<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-arrow-alt-circle-left"></i> Cerrar</button>

{!! Form::close() !!}
