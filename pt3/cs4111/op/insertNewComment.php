<?php
	include_once dirname(__FILE__) . "/../func/comment.php";
	
	if (!array_key_exists("player", $_REQUEST))
	{
		die("Player ID is missing");
	}
	
	if (!array_key_exists("content", $_REQUEST))
	{
		die("Comment content is missing");
	}
	
	$playerId = $_REQUEST["player"];
	
	if (!array_key_exists("commenterName", $_REQUEST))
	{
		$commenterName = NULL;
	}
	else
	{
		$commenterName = trim($_REQUEST["commenterName"]);
		
		if ($commenterName == "")
		{
			$commenterName = NULL;
		}
	}
	
	if (!array_key_exists("rating", $_REQUEST))
	{
		$rating = NULL;
	}
	else
	{
		$rating = $_REQUEST["rating"];
		
		if ($rating == 0)
		{
			$rating = NULL;
		}
	}
	
	$content = trim($_REQUEST["content"]);
	
	$array = insertNewComment($playerId, $commenterName, $rating, $content);
	
	header('Content-Type: application/json');
	echo json_encode($array);
?>
