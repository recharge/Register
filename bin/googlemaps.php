<?php
// Your Google Maps API key
$key = "ABQIAAAATtkzWTPFhtU8DuaNCEpX3BRi_j0U6kJrkFvY4-OX2XYmEAa76BS7PTWJrZfok6YgMOiX98dhJERFWg";

function curlPost($url) {
	// open curl connection
	$ch = curl_init();
	
	// set the url
	curl_setopt($ch,CURLOPT_URL,$url);
	
	// tell curl to pass the result when complete
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
	
	// execute and store response into variable
	$result = curl_exec($ch);
	
	// close connection
	curl_close($ch);
	
	return $result;
}

function geocode($address) {
	global $key;
	
	$address = urlencode($address);
	
	$url = "http://maps.google.com/maps/geo?q=$address&output=xml&key=$key";
	
	// Retrieve the URL contents
	$page = curlPost($url);
	
	//echo $page;
	
	// Parse the returned XML file
	return new SimpleXMLElement($page);
}
// Parse the coordinate string
//list($longitude, $latitude, $altitude) = explode(",", $xml->Response->Placemark->Point->coordinates);
//$foundaddress = $xml->Response->Placemark->address;
?>