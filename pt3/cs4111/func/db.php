<?php

function getConn()
{
	$server = "";
	$user = "";
	$pwd = "";
	$db = "";
	
	$conn = new mysqli($server, $user, $pwd, $db);
	
	if ($conn->connect_errno)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	return $conn;
}

?>