<div id="map" class="card" style="width: 100%; height: 600px;"></div>

<script>
    var route = @json($route);

    function initMap() {
        var mapOptions = {
            center: {
                lat: parseFloat(route.latitud_start),
                lng: parseFloat(route.longitude_start)
            },
            zoom: 14
        };

        var map = new google.maps.Map(document.getElementById('map'), mapOptions);

        var routeCoords = [
            { lat: parseFloat(route.latitud_start), lng: parseFloat(route.longitude_start) },
            { lat: parseFloat(route.latitude_end), lng: parseFloat(route.longitude_end) }
        ];

        var routePath = new google.maps.Polyline({
            path: routeCoords,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
        });

        routePath.setMap(map);

        var bounds = new google.maps.LatLngBounds();
        routeCoords.forEach(coord => bounds.extend(coord));
        map.fitBounds(bounds);

        new google.maps.Marker({
            position: routeCoords[0],
            map: map,
            title: 'Inicio: ' + route.name
        });

        new google.maps.Marker({
            position: routeCoords[1],
            map: map,
            title: 'Fin: ' + route.name
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
