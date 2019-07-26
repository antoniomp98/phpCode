<?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  function get_json(){
    $str64 = $_GET['json'];
    $json_app_str = base64_decode($str64);
    $json_app = json_decode($json_app_str, true);
    return $json_app;
  }

  function comprobar_valor($heart_rate)
  {
  if ($heart_rate < 20){
	 return true;
    }
    else{
	  return false;
	}
  } 

  $json_app = get_json();
  $heart = $json_app['valor'];
  $latitude = $json_app['latitude'];
  $longitude = $json_app['longitude'];
  $date = $json_app['date'];

  $params = array(
	'bpm' => $heart,
	'PhoneId' => 'SamsungS8',
	'position' =>[
		'lat'=> $latitude,
		'long'=> $longitude
		],
	'date' => $date
 );

  $ch = curl_init();

  $paramsEncoded = json_encode($params);
  curl_setopt($ch, CURLOPT_URL, "http://163.117.166.81:5601/api/console/proxy?path=/ehealth_bpm/1&method=POST");
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsEncoded);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'kbn-xsrf: reporting'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($ch);
  curl_close($ch);

  if(comprobar_valor($heart))
  {
     $command = 'nohup php /var/www/html/temporizador.php '.$latitude.' '.$longitude.' > /dev/null 2>&1 & echo $!';
     exec($command ,$op);
     $pid = (int)$op[0];
     echo $pid;
  }
  else
  {
     echo 0;
  }
?>
