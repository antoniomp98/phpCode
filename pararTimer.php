<?php

$pid = $_GET['pid'];
$option = $_GET['option'];
$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];

$command = 'kill '.$pid;
exec($command);

if($option == 1){
	$cercana = "http://163.117.166.81/cercana.php?latitude=".$latitude."&longitude=".$longitude;
	echo file_get_contents($cercana);
}

echo $option;
?>
