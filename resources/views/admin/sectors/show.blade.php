<div id="map" class="card" style="width: 100%; height:600px;"></div>
<script>
    var perimeters = @json($perimeter);
 
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
 
                    // Crea un objeto de polígono con los puntos del perímetro
                    var perimeterPolygon = new google.maps.Polygon({
                        paths: perimeterCoords,
                        strokeColor: color,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: color,
                        fillOpacity: 0.35,
                        map: map // Asigna el mapa al polígono para mostrarlo
                    });
 
                    // Calcular el centro del polígono
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
        }, function(error) {
            console.error("Error al obtener la ubicación del usuario:", error);
        });
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>