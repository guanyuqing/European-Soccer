<?php

require_once dirname(__FILE__) . "/db.php";

class Staff
{
	public $staffId = 0;
	public $name = "";
	public $nationality = "";
	public $type = "";
	public $clubId = 0;
}

function loadStaffByClub($clubId)
{
	$staffs = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		S.staffid,
		S.name,
		S.nationality,
		S.type
	FROM
		Staff S
	WHERE
		S.clubid = ?
	ORDER BY S.staffid
	");
	
	$stmt->bind_param("i", $clubId);
	$stmt->execute();
	$stmt->bind_result($staffId, $name, $nationality, $type);
	
	while ($stmt->fetch())
	{
		$staff = new Staff();
		
		$staff->staffId = $staffId;
		$staff->name = $name;
		$staff->nationality = $nationality;
		$staff->type = $type;
		$staff->clubId = $clubId;
		
		array_push($staffs, $staff);
	}
	
	$stmt->close();
	
	$conn->close();	
	
	return $staffs;
}

?>