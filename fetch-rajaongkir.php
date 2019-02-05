<?php

function connectDB () {
	$credent = new stdClass();
	$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
	if ($fh = fopen("{$path}/wp-config.php", 'r')) {
	  while (!feof($fh)) {
	    $line = fgets($fh);
	    if (strpos($line, "define('DB_NAME', '") > -1) $credent->DB_NAME = str_replace("');\r\n", '', end(explode("define('DB_NAME', '", $line)));
	    if (strpos($line, "define('DB_USER', '") > -1) $credent->DB_USER = str_replace("');\r\n", '', end(explode("define('DB_USER', '", $line)));
	    if (strpos($line, "define('DB_PASSWORD', '") > -1) $credent->DB_PASSWORD = str_replace("');\r\n", '', end(explode("define('DB_PASSWORD', '", $line)));
	    if (strpos($line, "define('DB_HOST', '") > -1) $credent->DB_HOST = str_replace("');\r\n", '', end(explode("define('DB_HOST', '", $line)));
	  }
	  fclose($fh);
	}

	$credent->DB_HOST = 'localhost' === $credent->DB_HOST ? '127.0.0.1': $credent->DB_HOST;
	$con = new mysqli($credent->DB_HOST, $credent->DB_USER, $credent->DB_PASSWORD, $credent->DB_NAME);
	if (!$con) die('Could not connect: ' . mysql_error());
	else return $con;
}

function fetcherSendRequest ($opt = array(), $apikey) {
  $curl       = curl_init();
  $api_url    = 'http://pro.rajaongkir.com/api/';
  $optDefault = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'content-type: application/x-www-form-urlencoded',
      'key: ' . $apikey
    ),
  );
  if ( strpos($opt[CURLOPT_URL], $api_url) === false) $opt[CURLOPT_URL] = $api_url . $opt[CURLOPT_URL];
  foreach ($optDefault as $key => $value) {
    if (!isset($opt[$key])) $opt[$key] = $value;
  }
  curl_setopt_array($curl, $opt);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);
  $response = json_decode($response, false);
  return 200 == $response->rajaongkir->status->code ? $response->rajaongkir->results : array();
}

function fetchProvince ($apikey) {
	$con = connectDB();
  foreach (fetcherSendRequest(array(CURLOPT_URL => 'province'), $apikey) as $record) {
  	mysqli_query($con, "INSERT INTO caldera_ongkir_province (province_id, province) VALUES ('{$record->province_id}', '{$record->province}')");
  }
}

function fetchCity ($apikey) {
  $con = connectDB();
  $result = $con->query("SELECT province_id FROM caldera_ongkir_province");
  if ($result->num_rows > 0) while ($row = $result->fetch_assoc()) {
    foreach (fetcherSendRequest(array(CURLOPT_URL => 'city?province=' . $row["province_id"]), $apikey) as $record) {
      mysqli_query($con, "
        INSERT INTO caldera_ongkir_city (city_id, city_name, type, province_id) 
        VALUES ('{$record->city_id}', '{$record->city_name}', '{$record->type}', '{$record->province_id}')
      ");
    }
  }
}

function fetchSubDistrict ($apikey) {
  $con = connectDB();
  $result = $con->query("SELECT city_id FROM caldera_ongkir_city");
  if ($result->num_rows > 0) while ($row = $result->fetch_assoc()) {
    foreach (fetcherSendRequest(array(CURLOPT_URL => 'subdistrict?city=' . $row["city_id"]), $apikey) as $record) {
      mysqli_query($con, "
        INSERT INTO caldera_ongkir_subdistrict (subdistrict_id, subdistrict_name, city_id) 
        VALUES ('{$record->subdistrict_id}', '{$record->subdistrict_name}', '{$record->city_id}')
      ");
    }
  }
}

$apikey = end($argv);
fetchProvince ($apikey);
fetchCity ($apikey);
fetchSubDistrict ($apikey);