<?php

	include_once dirname(__FILE__) . "/../func/match.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("League ID is missing");
	}
	
	$dict = loadDatesAndMatchesByLeague($_REQUEST["id"]);
	
	header('Content-Type: application/json');
	echo json_encode($dict);
?>
