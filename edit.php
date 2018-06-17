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
    $rewriteKeys = $json_a["waypoints"] == '' ? null : $json_a["waypoints"];

    $newArr = array();
    if($rewriteKeys !== null){
        foreach($rewriteKeys as $key => $value) {
            $newArr[] = (object) array('value' => $value);
        }
        $jsonPoints = json_encode($newArr);
    }else{
        $jsonPoints = '[]';
    }

    $end = $json_a["destination"];
}
?>
<div class="row" style="margin-right: 0px; margin-left: 0px;">
    <div id="right-panel" class="col-12 col-md-2" style="height: 900px; overflow: auto">
        <form action="maps.php" method="post">
            <b>Başlangıç</b>
            <br>
            <input type="hidden" name="file" value="<?php echo $file; ?>">
            <div class="input-group mb-3">
                <input type="text" name="start" class="form-control" id="start" aria-label="Location" aria-describedby="basic-addon2" required value="<?php echo $start; ?>">
                <div class="input-group-append">
                    <button class="btn btn-outline-primary find" type="button">Bul</button>
                </div>
            </div>
            <br>
            <div id="app">
                <b>Ara Noktalar:</b> <br>
                <div class="input-group mb-3" v-for="(find, index) in finds">
                    <input type="text" v-model="find.value" name="waypoints[]" class="form-control" aria-label="Recipient's username" aria-describedby="basic-addon2" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger" @click="deleteFind(index)" type="button">Kaldır</button>
                    </div>
                </div>

                <span @click="addFind" class="btn btn-success">
                Ekle
            </span>
            </div>
            <br>
            <b>Bitiş</b>
            <br>
            <input type="text" name="end" class="form-control" placeholder="Şehir İsmi" value="<?php echo $end; ?>">
            <input id="save" name="save" type="checkbox" value="ok">
            <label for="save">Bu dosya üzerine kaydet</label>
            <br>
            <input type="submit" class="btn btn-primary" id="submit" name="new" value="Yol Tarifi Oluştur">
        </form>
        <div id="directions-panel" class="card-body" style="max-height: 900px"></div>
        <?php include "history.php"; ?>
    </div>
    <div id="map" class="col-12 col-md-10" style="height: 900px"></div>
</div>
<?php include "footer.php"; ?>
<script>
    new Vue({
        el: '#app',
        data: {
            finds: <?php echo $jsonPoints; ?>
        },
        methods: {
            addFind: function () {
                this.finds.push({ value: '' });
            },
            deleteFind: function (index) {
                console.log(index);
                console.log(this.finds);
                this.finds.splice(index, 1);
            }
        }
    });
    function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: {lat: 39.0704257, lng: 32.9348536}
        });






        $("body").on("click",".find", function showPosition() {
            navigator.geolocation.getCurrentPosition(positidetect);

            function positidetect(position){

                var userLatLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                var locApi = "https://maps.googleapis.com/maps/api/geocode/json?latlng="+position.coords.latitude+","+position.coords.longitude+"&sensor=true&key=AIzaSyCUlFOmw8MOX160eXmYYd1VN-qcw-8rnh8";
                $.getJSON(locApi).done(function(response) {
                    document.getElementById("start").value = response.results[0].formatted_address;
                });

                marker = new google.maps.Marker({
                    position: userLatLng,
                    title: "Konumunuz",
                    map: map
                });
                //map.LatLngChange(location.latitude, location.longitude)
            }

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
            origin: "<?php echo $start; ?>",
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
