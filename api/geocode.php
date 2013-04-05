<?php
include('../bin/googlemaps.php');
$lat = $_GET['lat']; $lon = $_GET['lon'];

$address = geocode("$lat, $lon");
$response['address'] = $address->Response->Placemark->address;

echo $response['address'];

?>