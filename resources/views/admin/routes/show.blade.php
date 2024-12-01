<div id="map" class="card" style="width: 100%; height: 600px;"></div>

<script>
    var route = @json($route);
    var zones = @json($zonesWithCoords);

    function initMap() {
        // Crear el mapa
        var mapOptions = {
            center: {
                lat: parseFloat(route.latitud_start), // Latitud inicial
                lng: parseFloat(route.longitude_start) // Longitud inicial
            },
            zoom: 14
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        // Ajustar los límites del mapa
        var bounds = new google.maps.LatLngBounds();

        if (Object.keys(zones).length === 0) {
            // Si no hay zonas, centrar en la ubicación actual del usuario
            navigator.geolocation.getCurrentPosition(function (position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;

                var userLocation = new google.maps.LatLng(userLat, userLng);

                // Marcar la ubicación actual del usuario
                new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: 'Tu ubicación actual'
                });

                // Ajustar el centro del mapa
                map.setCenter(userLocation);
                map.setZoom(15);
            }, function (error) {
                console.error("Error al obtener la ubicación del usuario:", error);
                alert("No se pudo obtener tu ubicación actual.");
            });
        } else {
            // Si hay zonas, dibujar polígonos
            Object.keys(zones).forEach(function (zoneId) {
                var zone = zones[zoneId];
                var zoneName = zone[0].name; // Nombre de la zona
                var polygonCoords = zone.map(function (coord) {
                    return { lat: parseFloat(coord.latitude), lng: parseFloat(coord.longitude) };
                });

                // Crear un polígono
                var polygon = new google.maps.Polygon({
                    paths: polygonCoords,
                    strokeColor: '#FF0000',
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.2
                });
                polygon.setMap(map);

                // Calcular el centro del polígono para mostrar el nombre de la zona
                var boundsPolygon = new google.maps.LatLngBounds();
                polygonCoords.forEach(function (coord) {
                    boundsPolygon.extend(coord);
                });
                var center = boundsPolygon.getCenter();

                // Añadir un marcador en el centro del polígono con el nombre de la zona
                var marker = new google.maps.Marker({
                    position: center,
                    map: map,
                    title: 'Zona: ' + zoneName
                });

                var infowindow = new google.maps.InfoWindow({
                    content: `<b>${zoneName}</b>`
                });

                // Mostrar el nombre de la zona al hacer clic en el marcador
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.open(map, marker);
                });

                // Extender los límites del mapa para incluir el polígono
                polygon.getPath().forEach(function (latLng) {
                    bounds.extend(latLng);
                });
            });

            map.fitBounds(bounds); // Ajustar la vista del mapa para incluir todas las zonas
        }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>