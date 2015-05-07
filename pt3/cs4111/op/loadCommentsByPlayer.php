<?php
	include_once dirname(__FILE__) . "/../func/comment.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		die("Player ID is missing");
	}
	
	$playerId = $_REQUEST["id"];
	
	if (!array_key_exists("after", $_REQUEST))
	{
		$afterCommentId = 0;
	}
	else
	{
		$afterCommentId = $_REQUEST["after"];
	}
	
	$comments = loadCommentsByPlayerAfter($playerId, $afterCommentId);
	
	header('Content-Type: application/json');
	echo json_encode($comments);
?>
