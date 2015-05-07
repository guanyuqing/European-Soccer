<?php

require_once dirname(__FILE__) . "/db.php";

class Stadium
{
	public $stadiumId = 0;
	public $name = "";
	public $city = "";
	public $country = "";
	public $capacity = 0;
}

function loadStadium($id)
{
	$stadium = new Stadium();
	$stadium->stadiumId = $id;
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		S.name,
		S.city,
		S.country,
		S.capacity
	FROM
		Stadiums S
	WHERE
		S.stadiumid = ?
	");
	
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($stadium->name, $stadium->city, $stadium->country, $stadium->capacity);
	
	if ($stmt->fetch());
	$stmt->close();
	
	$conn->close();	
	
	return $stadium;
}

?>