<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, [
        'class' => 'form-control', 
        'placeholder' => 'Nombre del horario', 
        'required'
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('time_start', 'Hora Inicio') !!}
    <div class="input-group">
        {!! Form::input('time', 'time_start', null, [
            'class' => 'form-control',
            'required'
        ]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('time_end', 'Hora Fin') !!}
    <div class="input-group">
        {!! Form::input('time', 'time_end', null, [
            'class' => 'form-control',
            'required'
        ]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'placeholder' => 'Descripción del horario'
    ]) !!}
</div>