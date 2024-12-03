<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombres Completos de la persona', 'required']) !!}
</div>

<div class="form-group">
    {!! Form::label('dni', 'DNI') !!}
    {!! Form::text('dni', null, [
        'class' => 'form-control',
        'placeholder' => 'DNI de la persona',
        'required',
        'maxlength' => '8',
    ]) !!}
</div>
<div class="form-group">
    {!! Form::label('email', 'Correo Electronico') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Correo Electronico de la persona', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('usertype_id', 'Tipo de Usuario') !!}
    {!! Form::select('usertype_id', $usertypes, null, [
        'class' => 'form-control',
        'id' => 'usertype_id',
        'required',
    ]) !!}
</div>
<div class="form-group">
    {!! Form::label('license', 'Licencia') !!}
    {!! Form::text('license', null, ['class' => 'form-control', 'placeholder' => 'Licencia de la persona']) !!}
</div>
<div class="form-group">
    {!! Form::label('zone_id', 'Zona') !!}
    {!! Form::select('zone_id', $zones, null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('password', 'Contraseña') !!}
    {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña de la persona']) !!}
</div>
<div class="form-group">
    {!! Form::label('password_confirmation', 'Verificar Contraseña') !!}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Reingrese la contraseña']) !!}
</div>
