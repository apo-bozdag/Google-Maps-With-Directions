<?php include "header.php"; ?>
<body>
<?php
    if(isset($_GET["file"])){
        $file = $_GET["file"];
        $folder = "history/".$ip.'/'.$file;

        $string = file_get_contents($folder);
        $json_a = json_decode($string, true);

        $start = $json_a["origin"];
        $waypoints = $json_a["waypoints"] == '' ? '""' : json_encode($json_a["waypoints"]);
        $end = $json_a["destination"];
    }
?>
<div class="row" style="margin-right: 0px; margin-left: 0px;">
    <div id="map" class="col-12 col-md-10" style="height: 900px"></div>
    <div id="right-panel" class="col-12 col-md-2 card" style="height: auto; overflow: auto">
        <div id="directions-panel" class="card-body" style="max-height: 900px"></div>
    </div>
</div>

<?php include "footer.php"; ?>
<script>
    function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: {lat: 39.0704257, lng: 32.9348536}
        });
        directionsDisplay.setMap(map);

        calculateAndDisplayRoute(directionsService, directionsDisplay);
    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        var waypts = [];
        var myObject = <?php echo $waypoints ?>;

        var count = Object.keys(myObject).length;

        for (var i = 0; i < count; i++) {
            console.log(myObject[i]);
            waypts.push({
                location: myObject[i],
                stopover: true
            });
        }

        directionsService.route({
            origin: "<?php echo $start ?>",
            destination: "<?php echo $end; ?>",
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: 'DRIVING'
        }, function(response, status) {
            if (status === 'OK') {
                directionsDisplay.setDirections(response);
                var route = response.routes[0];
                var summaryPanel = document.getElementById('directions-panel');
                summaryPanel.innerHTML = '';
                // For each route, display summary information.
                for (var i = 0; i < route.legs.length; i++) {
                    var routeSegment = i + 1;
                    summaryPanel.innerHTML += '<b>Rota Bilgisi: ' + routeSegment +
                        '</b><br>';
                    summaryPanel.innerHTML += route.legs[i].start_address + '\'den ';
                    summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                    summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                }
            } else {
                window.alert('Yol tarifi isteği nedeniyle başarısız oldu ' + status);
                window.location = "https://www.sanalyer.com/maps";
            }
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUlFOmw8MOX160eXmYYd1VN-qcw-8rnh8&callback=initMap">
</script>
</body>
</html>
