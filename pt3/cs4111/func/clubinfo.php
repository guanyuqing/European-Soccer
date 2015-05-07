<?php

require_once dirname(__FILE__) . "/db.php";
require_once dirname(__FILE__) . "/stadium.php";
require_once dirname(__FILE__) . "/player.php";
require_once dirname(__FILE__) . "/staff.php";

class ClubInfo
{
	public $clubId = 0;
	public $yearFounded = 0;
	public $europeanQualification = NULL;
	public $stadium = NULL;
	public $captain = NULL;
	public $staffs = NULL;
}

function loadClubInfo($id)
{
	$clubInfo = new ClubInfo();
	$clubInfo->clubId = $id;
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		C.year_founded,
		C.european_qualification,
		C.stadiumid,
		C.captain
	FROM
		Clubs C
	WHERE
		C.clubid = ?
	");
	
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->bind_result($clubInfo->yearFounded, $clubInfo->europeanQualification, $stadiumId, $captainId);
	
	if ($stmt->fetch());
	$stmt->close();
	
	$conn->close();
	
	if ($stadiumId)
	{
		$clubInfo->stadium = loadStadium($stadiumId);
	}
	
	if ($captainId)
	{
		$clubInfo->captain = loadPlayer($captainId);
	}
	
	$clubInfo->staffs = loadStaffByClub($id);
	
	return $clubInfo;
}

?>