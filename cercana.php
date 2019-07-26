<?php

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];

   function cercana($latitude, $longitude)
   {
     $jsonurl = "https://csv.telematics.tomtom.com/extern?lang=en&account=pruebas&username=web_pruebas&password=pruebas&apikey=c2bab10d-38da-4ecd-b509-02b7b0764c26&action=showNearestVehicles&objectgroupname=uc3m&latitude=".$latitude."&longitude=".$longitude."&outputformat=json";

     $json = file_get_contents($jsonurl);
     echo $json;
   	 $json_content = json_decode($json, true);

   	 $ambulancia = $json_content[0];

   	 foreach($json_content as $o)
   	 {
       	 	if($ambulancia['lineardistance'] > $o['lineardistance'])
     	 	{
    			  $ambulancia = $o;
     	 	}
   	 }
   	 return $ambulancia;
   }

   $fecha = getdate();
   $json_ambulancia = cercana($latitude, $longitude);
   $orden_url = "https://csv.telematics.tomtom.com/extern?lang=en&account=pruebas&username=web_pruebas&password=pruebas&apikey=c2bab10d-38da-4ecd-b509-02b7b0764c26&action=sendDestinationOrderExtern&objectuid=".$json_ambulancia['objectuid']."&orderid=".$fecha[0]."&ordertext=TEST&latitude=".$latitude."&longitude=".$longitude;
   file_get_contents($orden_url);
   echo $latitude;
   echo " ".$longitude;	   
?>
