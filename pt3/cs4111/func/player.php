<?php

require_once dirname(__FILE__) . "/db.php";

class Player
{
	public $playerId = 0;
	public $name = "";
	public $nationality = "";
	public $no = 0;
	public $position = "";
	public $clubId = 0;
	public $rating = 0;
}

function loadPlayer($id)
{
	$player = new Player();
	$player->playerId = $id;
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		P.name,
		P.nationality,
		P.no,
		P.position,
		P.clubid,
		IF (
			ISNULL(C.rating),
			3.00,
			TRUNCATE(C.rating, 2)
		) AS rating
	FROM
		Players P
		LEFT JOIN (
			SELECT pid, AVG(rating) AS rating
			FROM Comments
			GROUP BY pid
		) C
		ON P.pid = C.pid
	WHERE
		P.pid = ?
	");
	
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($player->name, $player->nationality, $player->no, $player->position, $player->clubId, $player->rating);
	
	if ($stmt->fetch());
	$stmt->close();
	
	$conn->close();	
	
	return $player;
}

function loadPlayersByClub($clubId)
{
	$players = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		P.pid,
		P.name,
		P.nationality,
		P.no,
		P.position,
		IF (
			ISNULL(C.rating),
			3.00,
			TRUNCATE(C.rating, 2)
		) AS rating
	FROM
		Players P
		LEFT JOIN (
			SELECT pid, AVG(rating) AS rating
			FROM Comments
			GROUP BY pid
		) C
		ON P.pid = C.pid
	WHERE
		P.clubid = ?
	ORDER BY P.no
	");
	
	$stmt->bind_param("i", $clubId);
	$stmt->execute();
	$stmt->bind_result($playerId, $name, $nationality, $no, $position, $rating);
	
	while ($stmt->fetch())
	{
		$player = new Player();
		
		$player->playerId = $playerId;
		$player->name = $name;
		$player->nationality = $nationality;
		$player->no = $no;
		$player->position = $position;
		$player->clubId = $clubId;
		$player->rating = $rating;
		
		array_push($players, $player);
	}
	
	$stmt->close();
	
	$conn->close();	
	
	return $players;
}

?>