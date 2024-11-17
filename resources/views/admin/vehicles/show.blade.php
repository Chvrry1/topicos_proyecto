<div class="row">
    @foreach ($images as $image)
        <div class="col-3">
            <div class="card">
                <form action="{{ route('admin.vehicleimages.destroy', $image->id) }}" method="POST" class="imgEliminar">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm close-button"><i class="fas fa-minus-circle"></i></button>
                    <img src={{ asset($image->image) }} alt="" style="width: 100%;height:100%">
                </form>
                <button class="btn btn-sm btn-success btnimageprofile" id='{{ $image->id }}' data-id="{{ $image->vehicle_id }}"><i class="fas fa-image"></i> Perfil</button>
            </div>

        </div>
    @endforeach
</div>
<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-arrow-alt-circle-left"></i> Cerrar</button>

<style>
    /* Clase para asegurar la posición en la esquina superior derecha */
    .close-button {
        position: absolute;
        top: 5px;
        right: 5px;
        color: red;
        cursor: pointer;
    }

    .close-button:hover {
        color: orange;
    }
</style>
