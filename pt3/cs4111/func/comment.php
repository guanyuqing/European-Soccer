<?php

require_once dirname(__FILE__) . "/db.php";

class Comment
{
	public $commentId = 0;
	public $playerId = 0;
	public $commenterName = NULL;
	public $rating = NULL;
	public $content = "";
}

function loadCommentsByPlayerAfter($playerId, $afterCommentId)
{
	$comments = array();
	
	$conn = getConn();
	
	$stmt = $conn->prepare("
	SELECT
		C.commentid,
		C.commenter_name,
		C.rating,
		C.content
	FROM
		Comments C
	WHERE
		C.pid = ?
		AND C.commentid > ?
	ORDER BY C.commentid
	");
	
	$stmt->bind_param("ii", $playerId, $afterCommentId);
	$stmt->execute();
	$stmt->bind_result($commentId, $commenterName, $rating, $content);
	
	while ($stmt->fetch())
	{
		$comment = new Comment();
		
		$comment->commentId = $commentId;
		$comment->playerId = $playerId;
		$comment->commenterName = $commenterName;
		$comment->rating = $rating;
		$comment->content = $content;
		
		array_push($comments, $comment);
	}
	
	$stmt->close();
	
	$conn->close();	
	
	return $comments;
}

function insertNewComment($playerId, $commenterName, $rating, $content)
{
	$conn = getConn();
	
	$stmt = $conn->prepare("INSERT INTO Comments VALUES(NULL, ?, ?, ?, ?)");
	
	$stmt->bind_param("isis", $playerId, $commenterName, $rating, $content);
	$stmt->execute();
	
	$stmt->close();

	$array = array();
	$array["playerId"] = $playerId;
	
	$stmt = $conn->prepare("
	SELECT
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
	
	$stmt->bind_param("i", $playerId);
	$stmt->execute();
	$stmt->bind_result($array["rating"]);
	
	if ($stmt->fetch());
	$stmt->close();
		
	$conn->close();	
	
	return $array;
}

?>