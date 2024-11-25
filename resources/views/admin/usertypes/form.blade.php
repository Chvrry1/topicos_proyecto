<form id="tipoForm" action="{{ isset($usertype) ? route('admin.usertypes.update', $usertype->id) : route('admin.usertypes.store') }}" method="POST">
    @csrf
    @if(isset($usertype))
        @method('PUT')
    @endif
    <div class="form-group">
        <label for="name">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese el nombre" value="{{ $usertype->name ?? '' }}" required>
    </div>
    <div class="form-group">
        <label for="description">Descripción</label>
        <textarea class="form-control" id="description" name="description" placeholder="Ingrese la descripción">{{ $usertype->description ?? '' }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
