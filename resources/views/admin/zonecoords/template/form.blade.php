{!! Form::hidden('zone_id', $zone_id) !!}
<input type="hidden" name="coordinates" id="coordinates_input">
<div id="map" class="card" style="width: 100%; height:500px;"></div>

<script>
    var polygonCoords = [];

    function initMap() {
        navigator.geolocation.getCurrentPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            var mapOptions = {
                center: { lat: lat, lng: lng },
                zoom: 18
            };

            var map = new google.maps.Map(document.getElementById('map'), mapOptions);

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

            var polygon = new google.maps.Polygon({
                strokeColor: '#00FF00',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#00FF00',
                fillOpacity: 0.35
            });
            polygon.setMap(map);

            google.maps.event.addListener(map, 'click', function(event) {
                var latLng = event.latLng;
                polygonCoords.push(latLng);

                polygon.setPaths(polygonCoords);

                marker.setPosition(latLng);
            });

            function isInsideRestrictedArea(latLng) {
                return Object.values(existingZones).some(coords => {
                    var zonePolygon = new google.maps.Polygon({ paths: coords });
                    return google.maps.geometry.poly.containsLocation(latLng, zonePolygon);
                });
            }

            google.maps.event.addListener(marker, 'dragend', function(event) {
                var latLng = event.latLng;
                if (isInsideRestrictedArea(latLng)) {
                    alert("No puedes seleccionar este punto, está dentro de una zona restringida.");
                    marker.setPosition(latLng); // Revertir a la posición anterior
                } else {
                    polygonCoords.push(latLng);
                    polygon.setPaths(polygonCoords);
                }
            });

            document.getElementById('undo_button').addEventListener('click', function() {
                if (polygonCoords.length > 0) {
                    polygonCoords.pop(); // Eliminar el último punto
                    polygon.setPaths(polygonCoords); // Actualizar el polígono

                    if (polygonCoords.length > 0) {
                        marker.setPosition(polygonCoords[polygonCoords.length - 1]);
                    } else {
                        marker.setPosition({ lat: lat, lng: lng });
                    }
                }
            });

            document.getElementById('submit_button').addEventListener('click', function(e) {
                e.preventDefault();

                if (polygonCoords.length === 0) {
                    alert("Por favor, seleccione al menos un punto.");
                    return;
                }

                var coordinates = polygonCoords.map(function(latLng) {
                    return { lat: latLng.lat(), lng: latLng.lng() };
                });

                document.getElementById('coordinates_input').value = JSON.stringify(coordinates);

                var form = $('#zonecoords_form');
                var formData = new FormData(form[0]);

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#formModal").modal("hide");
                        refreshTable();
                        Swal.fire('Proceso exitoso', response.message, 'success');
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire('Error', response.message, 'error');
                    }
                });
            });

        }, function(error) {
            alert("No se pudo obtener la ubicación actual del usuario.");
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry&callback=initMap" async defer></script>