<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map Example</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        /* Make sure the map has a visible height */
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Initialize the map and set its view
        var map = L.map('map').setView([14.127086, 121.165985], 15);

        // // Protected Zone
        // var polygon = L.polygon([
        //     [14.13288, 121.192086],
        //     [14.131993, 121.172001],
        //     [14.122592, 121.173886],
        // ]).addTo(map);

        // // Protected Zone
        // var polygon = L.polygon([
        //     [14.122592, 121.173886],
        //     [14.118451, 121.168454],
        //     [14.113807, 121.160274], //start purok 7 (bottom part)
        //     [14.128788, 121.153198],
        //     [14.128642, 121.155237],
        //     [14.128463, 121.155733],
        //     [14.128216, 121.157517],
        //     [14.133018, 121.169653],
        //     [14.133122, 121.171775]
        // ]).addTo(map);

        // Add a tile layer (the actual map visuals)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Optional: add a marker
        // L.marker([14.10576, 121.14903]).addTo(map)
        //     .bindPopup('San Antonio, Sto Tomas, Batangas')
        //     .openPopup();

        var popup = L.popup();

        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("You clicked the map at " + e.latlng.toString())
                .openOn(map);
        }

        map.on('click', onMapClick);
    </script>
</body>

</html>