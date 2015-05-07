<?php

	include_once dirname(__FILE__) . "/../func/match.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("League ID is missing");
	}
	if (!array_key_exists("date", $_REQUEST))
	{
		die("Match Date is missing");
	}
	$matches = loadMatchesByLeagueAndDate($_REQUEST["id"], $_REQUEST["date"]);
	
	header('Content-Type: application/json');
	echo json_encode($matches);
?>
