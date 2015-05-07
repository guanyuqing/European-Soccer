<?php

require_once dirname(__FILE__) . "/db.php";
require_once dirname(__FILE__) . "/player.php";

class ClubView
{
	public $clubId = 0;
	public $name = "";
	public $rank = 0;
	public $stadiumId = NULL;
	public $captainId = NULL;
	public $leagueId = 0;
	public $leagueName = "";
	public $faId = 0;
	public $faName = "";
}

function loadClubView($id)
{
	$clubView = new ClubView();
	$clubView->clubId = $id;
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		C.name,
		C.stadiumid,
		C.captain,
		L.lid,
		L.name,
		F.fid,
		F.name
	FROM
		Clubs C,
		Leagues L,
		Football_associations F
	WHERE
		C.clubid = ?
		AND C.lid = L.lid
		AND L.fid = F.fid
	");
	
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($clubView->name, $clubView->stadiumId, $clubView->captainId, $clubView->leagueId, $clubView->leagueName, $clubView->faId, $clubView->faName);
	
	if ($stmt->fetch());
	$stmt->close();
	
	$stmt = $conn->prepare("
	SELECT
		T2.rank
	FROM (
		SELECT
			@rank := @rank + 1 AS rank,
			C.clubid,
			C.win * 3 + C.draw AS pts
		FROM
			Clubs C, (SELECT @rank := 0) T1
		WHERE
			C.lid = ?
		ORDER BY pts DESC) T2
	WHERE
		T2.clubid = ?
	");
	
	$stmt->bind_param("ii", $clubView->leagueId, $id);
	$stmt->execute();
	$stmt->bind_result($clubView->rank);
		
	if ($stmt->fetch());
	$stmt->close();
	
	$conn->close();
	
	return $clubView;
}

?>