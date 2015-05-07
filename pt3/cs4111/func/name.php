<?php

require_once dirname(__FILE__) . "/db.php";

class FaName
{
	public $faId = 0;
	public $name = "";
}

class LeagueName
{
	public $leagueId = 0;
	public $name = "";
}

class ClubName
{
	public $clubId = 0;
	public $name = "";
}

function loadFaNames()
{
	$faNames = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		F.fid,
		F.name
	FROM
		Football_associations F
	ORDER BY F.fid
	");
	
	$stmt->execute();
	$stmt->bind_result($faId, $name);
	
	while ($stmt->fetch())
	{
		$faName = new FaName();
		
		$faName->faId = $faId;
		$faName->name = $name;
		
		array_push($faNames, $faName);
	}
	
	$stmt->close();
	
	$conn->close();
	
	return $faNames;
}

function loadLeagueNamesByFa($faId)
{
	$leagueNames = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		L.lid,
		L.name
	FROM
		Leagues L
	WHERE
		L.fid = ?
	ORDER BY L.lid
	");
	
	$stmt->bind_param("i", $faId);
	$stmt->execute();
	$stmt->bind_result($leagueId, $name);
	
	while ($stmt->fetch())
	{
		$leagueName = new LeagueName();
		
		$leagueName->leagueId = $leagueId;
		$leagueName->name = $name;
		
		array_push($leagueNames, $leagueName);
	}
	
	$stmt->close();
	
	$conn->close();
	
	return $leagueNames;
}

function loadClubNamesByLeague($leagueId)
{
	$clubNames = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		C.clubid,
		C.name
	FROM
		Clubs C
	WHERE
		C.lid = ?
	ORDER BY
		C.name
	");
	
	$stmt->bind_param("i", $leagueId);
	$stmt->execute();
	$stmt->bind_result($clubId, $name);
	
	while ($stmt->fetch())
	{
		$clubName = new ClubName();
		
		$clubName->clubId = $clubId;
		$clubName->name = $name;
		
		array_push($clubNames, $clubName);
	}
	
	$stmt->close();
	
	$conn->close();
	
	return $clubNames;
}

?>