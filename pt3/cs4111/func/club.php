<?php

require_once dirname(__FILE__) . "/db.php";

class Club
{
	public $clubId = 0;
	public $rank = 0;
	public $name = "";
	public $played = 0;
	public $gf = 0;
	public $ga = 0;
	public $gd = 0;
	public $won = 0;
	public $drawn = 0;
	public $lost = 0;
	public $pts = 0;
	public $leagueId = 0;
}

function loadClubsByLeague($leagueid)
{
	$clubs = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
		SELECT
			C.clubid,
			@rank := @rank + 1 AS RANK,
			C.name AS NAME,
			C.win + C.draw + C.lose AS PLAYED,
			C.goals_for AS GF,
			C.goals_against AS GA,
			C.goals_for - C.goals_against AS GD,
			C.win AS W,
			C.draw AS D,
			C.lose AS L,
			C.win * 3 + C.draw AS PTS
		FROM
			Clubs C, (SELECT @rank := 0) T
		WHERE
			lid = ?
		ORDER BY PTS DESC;
	");
	
	$stmt->bind_param("i", $leagueid);
	$stmt->execute();
	$stmt->bind_result($clubId, $rank, $name, $played, $gf, $ga, $gd, $won, $drawn, $lost, $pts);
	
	while ($stmt->fetch())
	{
		$club = new Club();
		
		$club->clubId = $clubId;
		$club->rank = $rank;
		$club->name = $name;
		$club->played = $played;
		$club->gf = $gf;
		$club->ga = $ga;
		$club->gd = $gd;
		$club->won = $won;
		$club->drawn = $drawn;
		$club->lost = $lost;
		$club->pts = $pts;
		$club->leagueId = $leagueid;
		
		array_push($clubs, $club);
	}
	
	$stmt->close();
	
	$conn->close();
	
	return $clubs;
}

?>