<?php
	include_once dirname(__FILE__) . "/../func/clubinfo.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("Club ID is missing");
	}
	
	$clubInfo = loadClubInfo($_REQUEST["id"]);
	
	header('Content-Type: application/json');
	echo json_encode($clubInfo);
?>
