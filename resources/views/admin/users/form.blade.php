<form id="tipoForm" action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <div class="row">
        <!-- Primera columna -->
        <div class="col-md-6">
            <!-- Campo Nombre -->
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese el nombre" 
                       value="{{ old('name', $user->name ?? '') }}" required>
            </div>

            <!-- Campo Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password"
                @isset($user) value="Encryptada" @endisset placeholder="Ingrese la contraseña"
                autocomplete="new-password" 
                @isset($user) readonly @endisset>
            </div>

            <!-- Campo Tipo de Usuario -->
            <div class="form-group">
                <label for="usertype_id">Tipo de Usuario</label>
                <select class="form-control" id="usertype_id" name="usertype_id">
                    <option value="">Seleccione un tipo</option>
                    @foreach($usertypes as $usertype)
                        <option value="{{ $usertype->id }}" 
                                {{ old('usertype_id', $user->usertype_id ?? '') == $usertype->id ? 'selected' : '' }}>
                            {{ $usertype->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Segunda columna -->
        <div class="col-md-6">
            <!-- Campo Email -->
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese el correo" 
                value="{{ old('email', $user->email ?? '') }}" required>
            </div>
            
            <!-- Campo Confirmar Contraseña -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                @isset($user) value="Encryptada" @endisset placeholder="Confirme la contraseña" autocomplete="new-password" 
                @isset($user) readonly @endisset>
            </div>

            <!-- Campo Zona -->
            <div class="form-group">
                <label for="zone_id">Zona</label>
                <select class="form-control" id="zone_id" name="zone_id">
                    <option value="">Seleccione una zona</option>
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" 
                                {{ old('zone_id', $user->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Botón de Guardar -->
    <div class="row">
        <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary">Guardar Personal</button>
        </div>
    </div>
</form>
