<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('usertype_id', 'Tipo de usuario') !!}
        {!! Form::select('usertype_id', $usertypes, null, [
            'class' => 'form-control',
            'id' => 'usertype_id',
            'required',
            'placeholder' => 'Seleccione el tipo de usuario',
        ]) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('user_id', 'Usuario') !!}
        {!! Form::select('user_id', [], null, [
            'class' => 'form-control',
            'id' => 'user_id',
            'required',
            'placeholder' => 'Seleccione el usuario',
        ]) !!}
    </div>
</div>

<div class="form-check">

    {!! Form::checkbox('status', 1, null, [
        'class' => 'form-check-input',
    ]) !!}
    {!! Form::label('status', 'Activo') !!}
</div>

<script>
    $(document).ready(function() {
        $('#usertype_id').on('change', function() {
            const usertypeId = $(this).val();

            if (usertypeId) {
                $.ajax({
                    url: "{{ url('admin/users/filter') }}/" + usertypeId,
                    type: 'GET',
                    success: function(response) {
                        // Actualizar el campo de usuarios
                        $('#user_id').empty();
                        $('#user_id').append(
                            '<option value="">Seleccione un usuario</option>');
                        $.each(response, function(id, name) {
                            $('#user_id').append('<option value="' + id + '">' +
                                name + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error al cargar usuarios.');
                    },
                });
            } else {
                $('#user_id').empty();
                $('#user_id').append('<option value="">Seleccione un usuario</option>');
            }
        });
    });
</script>



<script>
    $(document).ready(function () {
        const hasConductor = {{ $hasConductor ? 'true' : 'false' }};
        const conductorOptionValue = {{ $conductorTypeId }}; // ID del tipo "Conductor"

        function updateButtonState() {
            const selectedType = $('#usertype_id').val(); 
            const isConductorSelected = selectedType == conductorOptionValue;

            if (hasConductor && isConductorSelected) {
                $('#btnRegistrar').attr('disabled', true); 
                $('#alertConductor').show();
            } else {
                $('#btnRegistrar').attr('disabled', false); 
                $('#alertConductor').hide();
            }
        }

        updateButtonState();

        $('#usertype_id').on('change', function () {
            updateButtonState();
        });

        $('#usertype_id').on('change', function () {
            const usertypeId = $(this).val();

            if (usertypeId) {
                $.ajax({
                    url: "{{ url('admin/users/filter') }}/" + usertypeId,
                    type: 'GET',
                    success: function (response) {
                        $('#user_id').empty();
                        $('#user_id').append('<option value="">Seleccione un usuario</option>');
                        $.each(response, function (id, name) {
                            $('#user_id').append('<option value="' + id + '">' + name + '</option>');
                        });
                    },
                    error: function () {
                        alert('Error al cargar usuarios.');
                    },
                });
            } else {
                $('#user_id').empty();
                $('#user_id').append('<option value="">Seleccione un usuario</option>');
            }
        });
    });
</script>

