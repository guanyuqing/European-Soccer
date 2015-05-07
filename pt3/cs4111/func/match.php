<?php

require_once dirname(__FILE__) . "/db.php";
require_once dirname(__FILE__) . "/util.php";

class Match
{
	public $homeRank = "";
	public $awayRank = "";
	public $homeId = 0;
	public $awayId = 0;
	public $homeName = "";
	public $awayName = "";
	public $homeGoal = 0;
	public $awayGoal = 0;
}

function loadDatesAndMatchesByLeague($leagueId)
{
	$dates = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT DISTINCT
		M.date
	FROM
		Matches M, Clubs C1, Clubs C2
	WHERE
		M.home_team = C1.clubid
		AND M.away_team = C2.clubid
		AND C1.lid = C2.lid
		AND C1.lid = ?
	ORDER BY M.date DESC
	");
	
	$stmt->bind_param("i", $leagueId);
	$stmt->execute();
	$stmt->bind_result($date);
	
	$lastDate = NULL;
	
	while ($stmt->fetch())
	{
		if (is_null($lastDate))
		{
			$lastDate = $date;
		}
		
		array_push($dates, $date);
	}
	
	$stmt->close();
	
	$conn->close();
	
	if (!is_null($lastDate))
	{
		$matches = loadMatchesByLeagueAndDate($leagueId, $lastDate);
	}
	else
	{
		$matches = array();
	}
	
	$dict = array("dates" => $dates, "matches" => $matches);
	
	return $dict;
}

function loadMatchesByLeagueAndDate($leagueId, $date)
{
	$matches = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		T1.rank,
		M.home_team,
		T1.name,
		M.goals_home,
		M.goals_away,
		M.away_team,
		T2.name,
		T2.rank
	FROM
		Matches M,
		(SELECT
			@rank1 := @rank1 + 1 AS rank,
			C1.clubid,
			C1.name,
			C1.win * 3 + C1.draw AS pts
		FROM
			Clubs C1, (SELECT @rank1 := 0) R1
		WHERE
			C1.lid = ?
		ORDER BY pts DESC) T1,
		(SELECT
			@rank2 := @rank2 + 1 AS rank,
			C2.clubid,
			C2.name,
			C2.win * 3 + C2.draw AS pts
		FROM
			Clubs C2, (SELECT @rank2 := 0) R2
		WHERE
			C2.lid = ?
		ORDER BY pts DESC) T2
	WHERE
		T1.clubid = M.home_team
		AND T2.clubid = M.away_team
		AND M.date = ?
	ORDER BY M.mid
	");
	
	$stmt->bind_param("iis", $leagueId, $leagueId, $date);
	$stmt->execute();
	$stmt->bind_result($homeRank, $homeId, $homeName, $homeGoal, $awayGoal, $awayId, $awayName, $awayRank);
	
	while ($stmt->fetch())
	{
		$match = new Match();
		$match->homeRank = ordinal($homeRank);
		$match->homeId = $homeId;
		$match->homeName = $homeName;
		$match->homeGoal = $homeGoal;
		$match->awayGoal = $awayGoal;
		$match->awayName = $awayName;
		$match->awayId = $awayId;
		$match->awayRank = ordinal($awayRank);
	
		array_push($matches, $match);
	}
	
	$stmt->close();
	
	$conn->close();	
	
	return $matches;
}
?>