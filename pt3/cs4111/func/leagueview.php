<?php

require_once dirname(__FILE__) . "/db.php";

class LeagueView
{
	public $leagueId = 0;
	public $name = "";
	public $up = 0;
	public $down = 0;
	public $faId = 0;
	public $faName = "";
	public $holderId = 0;
	public $holderName = "";
}

function loadLeagueView($id)
{
	$leagueView = new LeagueView();
	$leagueView->leagueId = $id;
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		L.name,
		F.fid,
		F.name,
		L.last_champion,
		C.name,
		L.no_of_upgrade,
		L.no_of_downgrade
	FROM
		Leagues L LEFT OUTER JOIN Clubs C ON L.last_champion = C.clubid,
		Football_associations F
	WHERE
		L.lid = ?
		AND L.fid = F.fid
	");
	
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($leagueView->name, $leagueView->faId, $leagueView->faName, $leagueView->holderId, $leagueView->holderName,
		$leagueView->up, $leagueView->down);
	
	if ($stmt->fetch());
	$stmt->close();
	
	$conn->close();
	
	return $leagueView;
}
?>