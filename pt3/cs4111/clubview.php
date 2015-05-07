<?php
	include_once dirname(__FILE__) . "/func/name.php";
	include_once dirname(__FILE__) . "/func/clubview.php";
	include_once dirname(__FILE__) . "/func/util.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		$id = 4;
	}
	else
	{
		$id = $_REQUEST["id"];
	}
	
	$clubView = loadClubView($id);
	
	$faNames = loadFaNames();
	$leagueNames = loadLeagueNamesByFa($clubView->faId);
	$clubNames = loadClubNamesByLeague($clubView->leagueId);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		
		<meta name="description" content="">
		<meta name="author" content="Yuqing Guan">
		
		<title><?php
			echo $clubView->name;
		?> - Club Info</title>
		
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/bootstrapvalidator.min.css" rel="stylesheet">
		<link href="css/theme.bootstrap_2.min.css" rel="stylesheet">
		<link href="css/star-rating.min.css" rel="stylesheet">
		
		<link href="css/style.css" rel="stylesheet">
		
		<link rel="icon" href="img/club/<?php
			echo $clubView->clubId;
		?>.png">
		
		<script src="js/ie-emulation-modes-warning.js"></script>
		
		<script type="text/javascript"><?php
				echo "\n";
				echo "\t\t\tvar faId = " . $clubView->faId . ";\n";
				echo "\t\t\tvar leagueId = " . $clubView->leagueId . ";\n";
				echo "\t\t\tvar clubId = " . $clubView->clubId . ";\n";
				
				$captainId = $clubView->captainId;
				if (is_null($captainId))
				{
					$captainId = 0;
				}
				
				echo "\t\t\tvar captainId = " . $captainId . ";\n";
			?>
		</script>
		
		<style>
			body {
				background-image:url(op/loadStadiumPhoto.php?id=<?php
					echo $clubView->stadiumId;
				?>);
			}
		</style>
	</head>

	<body role="document">
		
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#"><span><img src="img/soccer.png" /> European Soccer</span></a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li id="fa-dropdown" id="fa-dropdown" class="dropdown">
							<a id="nav-fa-name" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php
								echo $clubView->faName;
							?> <span class="caret"></span></a>
							<ul id="nav-fa-menu" class="dropdown-menu" role="menu">
							</ul>
						</li>
						<li id="league-dropdown" class="dropdown">
							<a id="nav-league-name" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php
								echo $clubView->leagueName;
							?> <span class="caret"></span></a>
							<ul id="nav-league-menu" class="dropdown-menu" role="menu">
							</ul>
						</li>
						<li id="club-dropdown"	class="dropdown">
							<a id="nav-club-name" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php
								echo $clubView->name;
							?> <span class="caret"></span></a>
							<ul id="nav-club-menu" class="dropdown-menu" role="menu">
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="container-fluid" id="main" role="main">

			<div class="jumbotron">
				<div class="row">
					<div class="col-md-8 club-name">
						<h1><?php
							echo $clubView->name;
						?></h1>
						<p><?php
							echo ordinal($clubView->rank) . " place in <span class=\"title-league\"><a href=\"javascript:switchNavLeague(" .
								$clubView->leagueId . ")\">" . $clubView->leagueName . "</a></span>";
						?></p>
					</div>
					<div class="col-md-2 pull-right jumbotron-logo">
						<img src="img/club/<?php
							echo $clubView->clubId;
						?>.png" />
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-8">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title">First Team</h3>
						</div>
						<div class="panel-body">
							
							<table id="table-squad" class="table table-striped table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Name</th>
										<th>Nationality</th>
										<th>Position</th>
										<th>Rating</th>
									</tr>
								</thead>
								<tbody id="squad">
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h3 class="panel-title">Detailed Information</h3>
						</div>
						<div class="panel-body">
							
							<table class="table table-condensed">
								<caption>Basic Information</caption>
								<tbody id="basic-info">
								</tbody>
							</table>
							
							<table class="table table-condensed">
								<caption>Stadium</caption>
								<tbody id="stadium-info">
								</tbody>
							</table>
							
							<table class="table table-condensed">
								<caption>Staff Members</caption>
								<tbody id="staff-info">
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
			</div>
		
		</div>
		
		<div id="comment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="comment-modal" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
	
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="modal-player-name"></h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-3">
								 <img src="op/loadPlayerPhoto.php" class="img-rounded img-thumbnail" id="player-photo" alt="Photo" /> 
							</div>
							<div class="col-md-9">
								<form id="new-comment" action="javascript:submitComment()">
									<div class="row">
										<div class="form-group col-md-7">
											<label for="commenterName">Commenter Name</label>
											<input id="comment-commentername" type="text" class="form-control" name="commenterName" placeholder="Enter your name">
										</div>
										<div class="form-group col-md-5">
											<label for="rating">Rating for this player</label>
											<input id="comment-rating" class="form-control rating" name="rating" />
										</div>
									</div>
									<div class="form-group">
										<label for="content">Comment</label>
										<textarea class="form-control" rows="3" id="comment-content" name="content"></textarea>
									</div>
									<div class="text-right">
										<input type="hidden" class="form-control" name="player" id="new-comment-player" /> 
										<button type="submit" class="btn btn-primary">Submit</button>
									</div>
								</div>
							</div>
						</form>
						
						<hr>
						
						<table class="table table-condensed">
							<caption>Previous Comments</caption>
							<thead>
									<tr>
										<th class="col-md-2">Commenter</th>
										<th class="col-md-2">Rating</th>
										<th class="col-md-5">Content</th>
									</tr>
								</thead>
							<tbody id="previous-comment">
							</tbody>
						</table>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
	
				</div>
			</div>
		</div>
		
		<footer>
			<p>Yuqing Guan, Xiaofan Yang 2015, <em>Introduction to Databases</em></p>
		</footer>

		<script src="js/jquery-2.0.3.min.js"></script>
		
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrapvalidator.min.js"></script>
		<script src="js/star-rating.min.js"></script>
		<script src="js/jquery.tablesorter.min.js"></script>
		<script src="js/jquery.tablesorter.widgets.min.js"></script>
		
		<script src="js/ie10-viewport-bug-workaround.js"></script>
		
		<script src="js/clubview.js"></script>
	</body>
</html>
