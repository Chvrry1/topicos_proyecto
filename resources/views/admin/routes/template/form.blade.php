<div class="container" style="display: flex; gap: 20px;">
    <div class="form-container" style="flex: 1;">
        <div class="form-group">
            {!! Form::label('name', 'Nombre') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la ruta', 'required']) !!}
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
                {!! Form::checkbox('status', '1', false, ['id' => 'status', 'class' => 'form-check-input']) !!}
                <label for="status" class="form-check-label">Activo</label>
            </div>
        </div>        
    </div>

    <div class="map-container" style="flex: 1;">
        <div id="map" class="card" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<script>
    var perimeters = @json($perimeter);
    var startMarker, endMarker, line;
    var startLat, startLng, endLat, endLng;

    function initMap() {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            var mapOptions = {
                center: {
                    lat: userLat,
                    lng: userLng
                },
                zoom: 18
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            var bounds = new google.maps.LatLngBounds(); // Objeto para ajustar los límites del mapa

            var colors = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF'];

            // Verifica si hay perímetros
            if (perimeters.length > 0) {
                perimeters.forEach(function(perimeter, index) {
                    var perimeterCoords = perimeter.coords.map(coord => new google.maps.LatLng(coord.lat, coord.lng));
                    var color = colors[index % colors.length]; // Obtiene un color de la matriz de colores

                    var perimeterPolygon = new google.maps.Polygon({
                        paths: perimeterCoords,
                        strokeColor: color,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: color,
                        fillOpacity: 0.35,
                        map: map // Asigna el mapa al polígono para mostrarlo
                    });

                    var polygonBounds = new google.maps.LatLngBounds();
                    perimeterCoords.forEach(function(coord) {
                        polygonBounds.extend(coord);
                        bounds.extend(coord); // También ajusta los límites globales del mapa
                    });
                    var center = polygonBounds.getCenter(); // Centro del polígono

                    // Agregar un marcador con el nombre de la zona
                    new google.maps.Marker({
                        position: center,
                        map: map,
                        label: {
                            text: perimeter.name,
                            color: "black",
                            fontSize: "14px",
                            fontWeight: "bold",
                        },
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 0, // No muestra un ícono visible
                        },
                    });
                });

                map.fitBounds(bounds);
            } else {
                map.setCenter({
                    lat: userLat,
                    lng: userLng
                });
                map.setZoom(18);
            }

            // Evento para agregar marcador de punto de inicio
            google.maps.event.addListener(map, 'click', function(event) {
                var latLng = event.latLng;
                var lat = latLng.lat();
                var lng = latLng.lng();

                var isStartPoint = !startMarker;

                if (isStartPoint) {
                    if (confirm('¿Deseas guardar este punto como Punto de Inicio?')) {
                        startLat = lat;
                        startLng = lng;
                        if (startMarker) {
                            startMarker.setMap(null); // Eliminar marcador anterior
                        }
                        startMarker = new google.maps.Marker({
                            position: latLng,
                            map: map,
                            title: 'Punto de Inicio',
                        });
                        document.getElementById('latitud_start').value = startLat;
                        document.getElementById('longitude_start').value = startLng;

                        // Si ya hay un marcador de final, trazar la línea
                        if (endMarker) {
                            drawLine(map);
                        }
                    }
                } else {
                    if (confirm('¿Deseas guardar este punto como Punto Final?')) {
                        endLat = lat;
                        endLng = lng;
                        if (endMarker) {
                            endMarker.setMap(null); // Eliminar marcador anterior
                        }
                        endMarker = new google.maps.Marker({
                            position: latLng,
                            map: map,
                            title: 'Punto Final',
                        });
                        document.getElementById('latitude_end').value = endLat;
                        document.getElementById('longitude_end').value = endLng;

                        // Si ya hay un marcador de inicio, trazar la línea
                        if (startMarker) {
                            drawLine(map);
                        }
                    }
                }
            });

            function drawLine(map) {
                if (line) {
                    line.setMap(null); // Eliminar línea anterior si existe
                }

                var startLocation = new google.maps.LatLng(startLat, startLng);
                var endLocation = new google.maps.LatLng(endLat, endLng);

                // Crear y dibujar la línea
                line = new google.maps.Polyline({
                    path: [startLocation, endLocation],
                    geodesic: true,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map: map
                });
            }
        }, function(error) {
            console.error("Error al obtener la ubicación del usuario:", error);
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>