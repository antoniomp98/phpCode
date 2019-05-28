<?php

  function get_json(){
    $str64 = $_GET['json'];
    $json_app_str = base64_decode($str64);
    $json_app = json_decode($json_app_str, true);

    return $json_app;
  }

   function cercana($latitude, $longitude)
   {
     $jsonurl = "https://csv.telematics.tomtom.com/extern?lang=en&account=pruebas&username=web_pruebas&password=539fnQmKDwZqBXh&apikey=c2bab10d-38da-4ecd-b509-02b7b0764c26&action=showNearestVehicles&objectgroupname=uc3m&latitude=".$latitude."&longitude=".$longitude."&outputformat=json";

   	 $json = file_get_contents($jsonurl);
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

   $json_app = get_json();
   $latitude = $json_app['latitude'];
   $longitude = $json_app['longitude'];
   $fecha = getdate();
   $json_ambulancia = cercana($latitude, $longitude);
   $orden_url = "https://csv.telematics.tomtom.com/extern?lang=en&account=pruebas&username=web_pruebas&password=539fnQmKDwZqBXh&apikey=c2bab10d-38da-4ecd-b509-02b7b0764c26&action=sendDestinationOrderExtern&objectuid=".$json_ambulancia['objectuid']."&orderid=".$fecha[0]."&ordertext=TEST&latitude=".$latitude."&longitude=".$longitude;
   file_get_contents($orden_url);

?>
