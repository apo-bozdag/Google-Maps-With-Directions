<?php include "header.php"; ?>
<body>
<?php
    if(isset($_POST)){
        $start = $_POST["start"];
        $waypoints = $_POST["waypoints"] == '' ? '""' : json_encode($_POST["waypoints"]);
        $end = $_POST["end"];

        $folder = "history/".$ip;
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        //$filename = md5(date("d-m-Y-H-i-s"));

        if($_POST["save"]){
            $filename = $_POST["file"];
        }else{
            $filename = date("d-m-Y-H-i-s").".json";
            //$second = date("s");

        }

        if($_POST["waypoints"]){
            $txt = '{
                    "origin": "'.$_POST["start"].'",
                    "destination": "'.$_POST["end"].'",
                    "waypoints": '.json_encode($_POST["waypoints"]).'
                }';
        }else{
            $txt = '{
                    "origin": "'.$_POST["start"].'",
                    "destination": "'.$_POST["end"].'"
                }';
        }
        $myfile = fopen($folder."/".$filename, "w") or die("Unable to open file!");
        fwrite($myfile, $txt);

    }
?>
<div class="row" style="margin-right: 0px; margin-left: 0px;">
    <div id="map" class="col-12 col-md-10" style="height: 900px"></div>
    <div id="right-panel" class="col-12 col-md-2 card" style="height: auto; overflow: auto">
        <br>
        <a href="edit.php?file=<?php echo $filename; ?>" class="btn btn-success">Haritayı Düzenle</a>
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
