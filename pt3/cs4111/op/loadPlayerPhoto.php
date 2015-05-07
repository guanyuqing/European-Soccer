<?php
	
	if (!array_key_exists("id", $_REQUEST))
	{
		$id = 0;
	}
	else
	{
		$id = $_REQUEST["id"];	
	}
	
	
	$path = dirname(__FILE__) . "/../img/player/" . $id . ".jpg";
	if (!file_exists($path))
	{
		$path = dirname(__FILE__) . "/../img/player/0.jpg";
	}
	
	header('Content-Type: image/jpeg');
	readfile($path);
?>
