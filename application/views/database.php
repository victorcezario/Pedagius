<?php
$query = $this->db->query('SELECT * FROM pedagio WHERE lat != "" ');
  $cor_lat = array();
  $cor_lng = array();
  $praca = array();
  foreach ($query->result_array() as $row)
  {
     $cor_lat[] = $row['LAT'];
     $cor_lng[] = $row['LNG'];
     $praca[] = $row['PRACA'];
  }
require_once "./assets/MathUtil.php";
require_once "./assets/PolyUtil.php";
require_once "./assets/SphericalUtil.php";
require_once "./assets/Polyline.php";
$json = file_get_contents('https://maps.googleapis.com/maps/api/directions/json?origin=%22Londrina,PR%22&destination=%22Curitiba,PR%22');
$data = json_decode($json,true);
$encoded = $data['routes'][0]['overview_polyline']['points'];
//echo "<pre>";
//int_r($Geonames);
$pointsx = Polyline::decode($encoded);
$points = Polyline::pair($pointsx);
echo "<pre>";
//print_r($points);
$array_lat = array();
$array_lng = array();
$bounds = "";
for ($x = 0; $x < count($points); $x++) {
    $array_lat[$x] = $points[$x][0];
    $array_lng[$x] = $points[$x][1];
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polylines</title>
    <style>
      html, body, #map-canvas {
        height: 100%;
        margin: 0px;
        padding: 0px
      }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyBRZ5R_9jVlX9jUmsUd__71K3hzroBcIpk&libraries=geometry"></script>
    <script>
// This example creates a 2-pixel-wide red polyline showing
// the path of William Kingsford Smith's first trans-Pacific flight between
// Oakland, CA, and Brisbane, Australia.


function initialize() {
  var latitude = [<?php echo implode(',',  $array_lat) ?>];
  //console.log(latitude);
  var longitude = [<?php echo implode(',',  $array_lng) ?>];
  var points = new Array();
       for(var i=0; i<latitude.length; i++){
          points[i] = new google.maps.LatLng(latitude[i], longitude[i]);
          //console.log(latitude[i], longitude[i]);
       }
  var mapOptions = {
    zoom: 3,
    center: new google.maps.LatLng(-23.29653, -51.17328),
    mapTypeId: 'roadmap'
    //mapTypeId: google.maps.MapTypeId.TERRAIN
  };

  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        
   var rad = function(x) {
      return x * Math.PI / 180;
   };
  
   //Get distance in meter
   var getDistance = function(p1, p2) {
       var R = 6378137; // Earth’s mean radius in meter
        var dLat = rad(p2.lat() - p1.lat());
        var dLong = rad(p2.lng() - p1.lng());
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(rad(p1.lat())) * Math.cos(rad(p2.lat())) *
        Math.sin(dLong / 2) * Math.sin(dLong / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
      return d; // returns the distance in meter
    };

        
   var myLine = new google.maps.Polyline({
        path:points,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2 
    });

   myLine.setMap(map);
   var js_lat = [<?php echo '"'.implode('","', $cor_lat).'"' ?>];
   var js_lng = [<?php echo '"'.implode('","', $cor_lng).'"' ?>];
   var praca = [<?php echo '"'.implode('","', $praca).'"' ?>];
   for(i=0;i<js_lng.length;i++){
var x = praca[i];
     console.log(js_lat[i],js_lng[i]);
      var myMarker1 = new google.maps.Marker({
        position: new google.maps.LatLng(js_lat[i],js_lng[i]), 
        map: map
      });
      var infowindow = new google.maps.InfoWindow(), myMarker1;
      google.maps.event.addListener(myMarker1, 'click', (function(myMarker1, i) {
        infowindow.setContent("Praça: "+x);
        infowindow.open(map, myMarker1);
      
    })(myMarker1))
      if (google.maps.geometry.poly.isLocationOnEdge(myMarker1.position, new google.maps.Polyline({path: points}), 0.01)) {
         console.log("Pedagio 1: SIM existe na rota ");
      } else {
         console.log("");
      }
   }
    
                
   
}
        
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
  </head>
  <body>
    <div id="map-canvas"></div>
  </body>
</html>