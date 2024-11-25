<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre del sector', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('area', 'Área') !!}
    {!! Form::number('area', null, ['class' => 'form-control', 'placeholder' => 'Área', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('district_id', 'Distrito') !!}
    {!! Form::select('district_id', $districts, null, ['class' => 'form-control', 'required']) !!}
</div>
<div class="form-group">
    {!! Form::label('description', 'Descripción') !!}
    {!! Form::textarea('description', null, [
        'class' => 'form-control',
        'placeholder' => 'Descripción del sector',
    ]) !!}
</div>