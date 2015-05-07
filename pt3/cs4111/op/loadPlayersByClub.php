<?php
	include_once dirname(__FILE__) . "/../func/player.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("Club ID is missing");
	}
	
	$players = loadPlayersByClub($_REQUEST["id"]);
	
	header('Content-Type: application/json');
	echo json_encode($players);
?>
