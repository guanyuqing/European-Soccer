<?php

	include_once dirname(__FILE__) . "/../func/club.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("League ID is missing");
	}
	
	$clubs = loadClubsByLeague($_REQUEST["id"]);
	
	header('Content-Type: application/json');
	echo json_encode($clubs);
?>
