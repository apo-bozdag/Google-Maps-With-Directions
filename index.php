<?php include "header.php"; ?>

<body>
<div class="row" style="margin-right: 0px; margin-left: 0px;">
    <div id="right-panel" class="col-12 col-md-2" style="height: 900px; overflow: auto">
        <form action="maps.php" method="post">
            <b>Başlangıç</b>
            <br>
            <div class="input-group mb-3">
                <input type="text" name="start" class="form-control" id="start" aria-label="Location" aria-describedby="basic-addon2" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary find" type="button">Bul</button>
                </div>
            </div>
            <br>
            <div id="app">
                <b>Ara Noktalar:</b> <br>
                <div class="input-group mb-3" v-for="(find, index) in finds">
                    <input type="text" v-model="find.value" name="waypoints[]" class="form-control" aria-label="Location" aria-describedby="basic-addon2" required>
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
            <input type="text" name="end" class="form-control" placeholder="Şehir İsmi">
            <br>
            <input type="submit" class="btn btn-primary" name="new" id="submit" value="Yol Tarifi Oluştur">
        </form>
        <?php include "history.php"; ?>
    </div>
    <div id="map" class="col-12 col-md-10" style="height: 900px"></div>
</div>
<?php include "footer.php"; ?>
<script>
    const map = new Vue({
        el: '#app',
        data: {
            finds: [],
            lat: 39.0704257,
            lng: 32.9348536
        },
        methods: {
            LatLngChange (lat, lng) {
                this.lat = lat;
                this.lng = lng;
            },
            addFind: function () {
                this.finds.push({ value: '' });
            },
            deleteFind: function (index) {
                console.log(index);
                console.log(this.finds);
                this.finds.splice(index, 1);
            },
            latF () {
                return this.lat;
            },
            lngF () {
                return this.lng;
            }
        }
    });



    var latC = map.latF();
    var latF = map.lngF();

    function initMap() {
        var directionsService = new google.maps.DirectionsService;
        var geocoder= new google.maps.Geocoder();
        var directionsDisplay = new google.maps.DirectionsRenderer;
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: {lat: latC, lng: latF}
        });

        $("body").on("click",".find", function f() {
            navigator.geolocation.getCurrentPosition(positidetect);
            
            function find() {
                var address = document.getElementById('start').value;
                geocoder.geocode( { 'address': address}, function(results, status) {
                    if (status == 'OK') {
                        map.setCenter(results[0].geometry.location);
                        var marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location
                        });
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            }

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

        document.getElementById('submit').addEventListener('click', function() {
            calculateAndDisplayRoute(directionsService, directionsDisplay);
        });
    }

    function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        var waypts = [];
        var checkboxArray = document.getElementById('waypoints');
        for (var i = 0; i < checkboxArray.length; i++) {
            if (checkboxArray.options[i].selected) {
                waypts.push({
                    location: checkboxArray[i].value,
                    stopover: true
                });
            }
        }

        directionsService.route({
            origin: document.getElementById('start').value,
            destination: document.getElementById('end').value,
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
                    summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
                        '</b><br>';
                    summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
                    summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
                    summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
                }
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUlFOmw8MOX160eXmYYd1VN-qcw-8rnh8&callback=initMap">
</script>
</body>
</html>
