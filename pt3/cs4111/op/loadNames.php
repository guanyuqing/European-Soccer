<?php
	include_once dirname(__FILE__) . "/../func/name.php";
	
	if (!array_key_exists("type", $_REQUEST))
	{
		die("Type is missing");
	}
	
	$type = $_REQUEST["type"];
	
	if ($type == "fa")
	{
		$names = loadFaNames();
	
		header('Content-Type: application/json');
		echo json_encode($names);
	}
	else
	{
		if (!array_key_exists("id", $_REQUEST))
		{
			die("ID is missing");
		}
		
		if ($type == "league")
		{
			$names = loadLeagueNamesByFa($_REQUEST["id"]);
	
			header('Content-Type: application/json');
			echo json_encode($names);
		}
		else if ($type == "club")
		{
			$names = loadClubNamesByLeague($_REQUEST["id"]);
	
			header('Content-Type: application/json');
			echo json_encode($names);
		}
		else
		{
			die("Wrong type");
		}
	}
?>
