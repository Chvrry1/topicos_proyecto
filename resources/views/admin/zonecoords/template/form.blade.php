{!! Form::hidden('zone_id', $zone_id) !!}

<div class="form-row">
    <div class="form-group col-6">
        {!! Form::label('latitude', 'Latitud') !!}
        {!! Form::text('latitude', optional($lastCoords)->lat, [
            'class' => 'form-control',
            'placeholder' => 'Latitud',
            'required',
            'readonly',
        ]) !!}
    </div>
    <div class="form-group col-6">
        {!! Form::label('longitude', 'Longitud') !!}
        {!! Form::text('longitude', optional($lastCoords)->lng, [
            'class' => 'form-control',
            'placeholder' => 'Longitud',
            'required',
            'readonly',
        ]) !!}
    </div>
</div>
<div id="map" class="card" style="width: 100%; height:500px;"></div>
<script>
    var latInput = document.getElementById('latitude');
    var lonInput = document.getElementById('longitude');

    function initMap() {

        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lonInput.value);

        if (isNaN(lat) || isNaN(lng)) {
            // Obtener ubicación actual si los campos están vacíos o no contienen valores numéricos válidos
            navigator.geolocation.getCurrentPosition(function(position) {
                lat = position.coords.latitude;
                lng = position.coords.longitude;
                latInput.value = lat;
                lonInput.value = lng;
                displayMap(lat, lng);
            });
        } else {
            // Utilizar las coordenadas de los campos de entrada
            displayMap(lat, lng);
        }
    }

    function displayMap(lat, lng) {
        var mapOptions = {
            center: { lat: lat, lng: lng },
            zoom: 18
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        // Polígono de la zona actual
        var perimeterCoords = @json($vertice);
        var perimeterPolygon = new google.maps.Polygon({
            paths: perimeterCoords,
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35
        });

        perimeterPolygon.setMap(map);

        // Polígonos de zonas existentes
        var existingZones = @json($existingZones);
        Object.values(existingZones).forEach(coords => {
            var zonePolygon = new google.maps.Polygon({
                paths: coords,
                strokeColor: '#808080',
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: '#808080',
                fillOpacity: 0.5
            });
            zonePolygon.setMap(map);
        });

        var marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: 'Ubicación',
            draggable: true
        });

        // Verificar si el marcador está en un área restringida
        function isInsideRestrictedArea(latLng) {
            return Object.values(existingZones).some(coords => {
                var zonePolygon = new google.maps.Polygon({ paths: coords });
                return google.maps.geometry.poly.containsLocation(latLng, zonePolygon);
            });
        }

        // Actualizar coordenadas al mover el marcador
        google.maps.event.addListener(marker, 'dragend', function(event) {
            var latLng = event.latLng;
            if (isInsideRestrictedArea(latLng)) {
                alert("No puedes seleccionar este punto, está dentro de una zona restringida.");
                marker.setPosition({ lat: lat, lng: lng }); // Revertir a la posición anterior
            } else {
                lat = latLng.lat();
                lng = latLng.lng();
                latInput.value = lat;
                lonInput.value = lng;
            }
        });
    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&callback=initMap" async defer></script>