 <?php
	$latitude = $argv[1];
	$longitude = $argv[2];
	$total = "";
	for ($segundos = 1; $segundos <= 10; $segundos++)
	{
		echo "<p>".$segundos."</p>";
		//Para cada iteración 1 segundo
		sleep(1);
		$total = $segundos;
	}
	$cercana = "http://163.117.166.81/cercana.php?latitude=".$latitude."&longitude=".$longitude;
	file_get_contents($cercana);

?>

