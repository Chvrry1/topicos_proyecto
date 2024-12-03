<div class="container" style="display: flex; gap: 20px;">
    <div class="form-container" style="flex: 1;">
        <div class="form-group">
            {!! Form::label('name', 'Nombre') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la ruta', 'required']) !!}
        </div>
        
        <div class="form-group">
            {!! Form::label('zone', 'Seleccionar Zonas') !!}
            <select name="zone[]" class="form-control" multiple>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}" 
                        @if(!empty($selectedZones) && in_array($zone->id, $selectedZones)) selected @endif>
                        {{ $zone->name }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Presiona Ctrl (Windows) o Cmd (macOS) para seleccionar varias zonas.</small>
        </div>                              

        <div class="form-row">
            <div class="form-group col-6">
                <label for="latitud_start">Punto de inicio (Latitud)</label>
                {!! Form::text('latitud_start', null, [
                    'id' => 'latitud_start',
                    'class' => 'form-control',
                    'placeholder' => 'Latitud',
                    'required',
                    'readonly',
                ]) !!}
            </div>
            <div class="form-group col-6">
                <label for="longitude_start">Punto de inicio (Longitud)</label>
                {!! Form::text('longitude_start', null, [
                    'id' => 'longitude_start',
                    'class' => 'form-control',
                    'placeholder' => 'Longitud',
                    'required',
                    'readonly',
                ]) !!}
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <label for="latitude_end">Punto final (Latitud)</label>
                {!! Form::text('latitude_end', null, [
                    'id' => 'latitude_end',
                    'class' => 'form-control',
                    'placeholder' => 'Latitud',
                    'required',
                    'readonly',
                ]) !!}
            </div>
            <div class="form-group col-6">
                <label for="longitude_end">Punto final (Longitud)</label>
                {!! Form::text('longitude_end', null, [
                    'id' => 'longitude_end',
                    'class' => 'form-control',
                    'placeholder' => 'Longitud',
                    'required',
                    'readonly',
                ]) !!}
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('status', 'Estado') !!}
            <div class="form-check">
                {!! Form::checkbox('status', '1', isset($route) && $route->status == 1 ? true : false, ['id' => 'status', 'class' => 'form-check-input']) !!}
                <label for="status" class="form-check-label">Activo</label>
            </div>
        </div>              
    </div>

    <div class="map-container" style="flex: 1;">
        <div id="map" class="card" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<script>
    var startLat = {{ $latitud_start ?? 'null' }};
    var startLng = {{ $longitude_start ?? 'null' }};
    var endLat = {{ $latitude_end ?? 'null' }};
    var endLng = {{ $longitude_end ?? 'null' }};

    var startMarker, endMarker, line;

    function initMap() {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            var mapOptions = {
                center: { lat: userLat, lng: userLng },
                zoom: 15
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

            if (startLat !== null && startLng !== null) {
                startMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(startLat, startLng),
                    map: map,
                    title: 'Punto de Inicio',
                    draggable: true, // Hacer el marcador arrastrable
                });
                document.getElementById('latitud_start').value = startLat;
                document.getElementById('longitude_start').value = startLng;
            }

            if (endLat !== null && endLng !== null) {
                endMarker = new google.maps.Marker({
                    position: new google.maps.LatLng(endLat, endLng),
                    map: map,
                    title: 'Punto Final',
                    draggable: true, // Hacer el marcador arrastrable
                });
                document.getElementById('latitude_end').value = endLat;
                document.getElementById('longitude_end').value = endLng;

                if (startMarker) {
                    drawLine(map);
                }
            }

            if (startMarker) {
                google.maps.event.addListener(startMarker, 'dragend', function (event) {
                    startLat = event.latLng.lat();
                    startLng = event.latLng.lng();
                    document.getElementById('latitud_start').value = startLat;
                    document.getElementById('longitude_start').value = startLng;
                    drawLine(map);
                });
            }

            if (endMarker) {
                google.maps.event.addListener(endMarker, 'dragend', function (event) {
                    endLat = event.latLng.lat();
                    endLng = event.latLng.lng();
                    document.getElementById('latitude_end').value = endLat;
                    document.getElementById('longitude_end').value = endLng;
                    drawLine(map);
                });
            }

            google.maps.event.addListener(map, 'click', function (event) {
                var latLng = event.latLng;
                var lat = latLng.lat();
                var lng = latLng.lng();

                if (!startMarker) {
                    if (confirm('¿Deseas guardar este punto como Punto de Inicio?')) {
                        startLat = lat;
                        startLng = lng;
                        startMarker = new google.maps.Marker({
                            position: latLng,
                            map: map,
                            title: 'Punto de Inicio',
                            draggable: true // Hacer el marcador arrastrable
                        });
                        document.getElementById('latitud_start').value = startLat;
                        document.getElementById('longitude_start').value = startLng;

                        // Listener para actualizar al mover
                        google.maps.event.addListener(startMarker, 'dragend', function (event) {
                            startLat = event.latLng.lat();
                            startLng = event.latLng.lng();
                            document.getElementById('latitud_start').value = startLat;
                            document.getElementById('longitude_start').value = startLng;
                            drawLine(map);
                        });
                    }
                } else if (!endMarker) {
                    if (confirm('¿Deseas guardar este punto como Punto Final?')) {
                        endLat = lat;
                        endLng = lng;
                        endMarker = new google.maps.Marker({
                            position: latLng,
                            map: map,
                            title: 'Punto Final',
                            draggable: true // Hacer el marcador arrastrable
                        });
                        document.getElementById('latitude_end').value = endLat;
                        document.getElementById('longitude_end').value = endLng;

                        // Listener para actualizar al mover
                        google.maps.event.addListener(endMarker, 'dragend', function (event) {
                            endLat = event.latLng.lat();
                            endLng = event.latLng.lng();
                            document.getElementById('latitude_end').value = endLat;
                            document.getElementById('longitude_end').value = endLng;
                            drawLine(map);
                        });

                        drawLine(map);
                    }
                }
            });

            function drawLine(map) {
                if (line) {
                    line.setMap(null); // Eliminar línea anterior si existe
                }

                if (startLat !== null && startLng !== null && endLat !== null && endLng !== null) {
                    var startLocation = new google.maps.LatLng(startLat, startLng);
                    var endLocation = new google.maps.LatLng(endLat, endLng);

                    line = new google.maps.Polyline({
                        path: [startLocation, endLocation],
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 1.0,
                        strokeWeight: 2,
                        map: map
                    });
                }
            }
        }, function (error) {
            console.error("Error al obtener la ubicación del usuario:", error);
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>