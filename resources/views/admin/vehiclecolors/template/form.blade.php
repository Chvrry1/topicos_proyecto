<div class="form-group">
    {!! Form::label('name', 'Nombre') !!}
    {!! Form::text('name', isset($vehiclecolor) ? $vehiclecolor->name : null, ['class' => 'form-control', 'placeholder' => 'Nombre del color de vehículo', 'required']) !!}
</div>

<div class="form-group" style="display: flex; align-items: center; gap: 15px;">
    <div style="display: flex; flex-direction: column; align-items: center;">
        {!! Form::label('color_picker', 'Seleccione un color') !!}
        <input type="color" id="color_picker" class="form-control" 
               value="{{ isset($vehiclecolor) ? $vehiclecolor->rgb_value : '#ea1010' }}" 
               style="width: 100px; height: 35px; padding: 0;">
    </div>
    
    <div style="display: flex; flex-direction: column; align-items: center;">
        {!! Form::label('hex_code_label', 'Código RGB') !!}
        <label id="hex_code_label" class="form-control" 
               style="width: 150px; height: 35px; display: flex; align-items: center; justify-content: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ isset($vehiclecolor) ? $vehiclecolor->rgb_value : 'rgb(234, 16, 132)' }}
        </label>
    </div>

    @if(isset($vehiclecolor))
    <div style="display: flex; flex-direction: column; align-items: center;">
        {!! Form::label('color_display', 'Color Registrado') !!}
        <label class="form-control" 
               style="background-color: {{ $vehiclecolor->rgb_value }}; width: 100px; height: 35px;">
        </label>
    </div>
    @endif
</div>







<!-- Campo oculto para almacenar el código RGB -->
<input type="hidden" id="rgb_value" name="rgb_value" value="{{ isset($vehiclecolor) ? $vehiclecolor->rgb_value : '' }}">

<div class="form-group">
    {!! Form::label('description', 'Descripción adicional') !!}
    {!! Form::textarea('description', isset($vehiclecolor) ? $vehiclecolor->description : null, [
        'class' => 'form-control',
        'placeholder' => 'Descripción adicional sobre el color...',
    ]) !!}
</div>

<script>
    document.getElementById('color_picker').addEventListener('input', function() {
        const color = this.value;
        document.getElementById('hex_code_label').textContent = color;

        // Convertir a RGB
        const r = parseInt(color.substr(1, 2), 16);
        const g = parseInt(color.substr(3, 2), 16);
        const b = parseInt(color.substr(5, 2), 16);
        const rgbText = `rgb(${r}, ${g}, ${b})`;

        // Actualiza el campo oculto
        document.getElementById('rgb_value').value = rgbText;
    });
</script>
