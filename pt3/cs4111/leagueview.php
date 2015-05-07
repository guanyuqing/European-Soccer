<?php
	include_once dirname(__FILE__) . "/func/name.php";
	include_once dirname(__FILE__) . "/func/leagueview.php";
	
	if (!array_key_exists("id", $_REQUEST))
	{
		$id = 1;
	}
	else
	{
		$id = $_REQUEST["id"];
	}
	
	$leagueView = loadLeagueView($id);
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
			echo $leagueView->name;
		?> - League Info</title>
		
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/theme.bootstrap_2.min.css" rel="stylesheet">
		
		<link href="css/style.css" rel="stylesheet">
		
		<link rel="icon" href="img/league/<?php
			echo $leagueView->leagueId;
		?>.png">
		
		<script src="js/ie-emulation-modes-warning.js"></script>
		
		<script type="text/javascript"><?php
				echo "\n";
				echo "\t\t\tvar faId = " . $leagueView->faId . ";\n";
				echo "\t\t\tvar leagueId = " . $leagueView->leagueId . ";\n";
				echo "\t\t\tvar up = " . $leagueView->up . ";\n";
				echo "\t\t\tvar down = " . $leagueView->down . ";\n";
			?>
		</script>
		
		<style>
			body {
				background-image:url(op/loadStadiumPhoto.php?type=league&id=<?php
					echo $leagueView->leagueId;
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
								echo $leagueView->faName;
							?> <span class="caret"></span></a>
							<ul id="nav-fa-menu" class="dropdown-menu" role="menu">
							</ul>
						</li>
						<li id="league-dropdown" class="dropdown">
							<a id="nav-league-name" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php
								echo $leagueView->name;
							?> <span class="caret"></span></a>
							<ul id="nav-league-menu" class="dropdown-menu" role="menu">
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="container-fluid" id="main" role="main">

			<div class="jumbotron">
				<div class="row">
					<div class="col-md-10 jumbotron-name">
						<h1><?php
							echo $leagueView->name;
						?></h1>
						<p><?php
							if ($leagueView->holderId)
							{
								echo "Holder: <span class=\"title-last-champion\"><a href=\"clubview.php?id=" .
									$leagueView->holderId . "\">" . $leagueView->holderName . "</a></span>";
							}
						?></p>
					</div>
					<div class="col-md-2 pull-right jumbotron-logo">
						<img src="img/league/<?php
							echo $leagueView->leagueId;
						?>.png" />
					</div>
				</div>
			</div>
			
			<div class="row">
								
				<div class="col-md-7">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title">Standing Table</h3>
						</div>
						<div class="panel-body">
							
							<table id="table-standing" class="table table-hover">
								<thead>
									<tr>
										<th data-toggle="tooltip" data-placement="top" title="Rank">#</th>
										<th data-toggle="tooltip" data-placement="top" title="Name">Name</th>
										<th data-toggle="tooltip" data-placement="top" title="Played" class="standing-num">Pld</th>
										<th data-toggle="tooltip" data-placement="top" title="Won" class="standing-num">W</th>
										<th data-toggle="tooltip" data-placement="top" title="Drawn" class="standing-num">D</th>
										<th data-toggle="tooltip" data-placement="top" title="Lost" class="standing-num">L</th>
										<th data-toggle="tooltip" data-placement="top" title="Goals For" class="standing-num">GF</th>
										<th data-toggle="tooltip" data-placement="top" title="Goals Against" class="standing-num">GA</th>
										<th data-toggle="tooltip" data-placement="top" title="Goal Difference" class="standing-num">GD</th>
										<th data-toggle="tooltip" data-placement="top" title="Points" class="standing-num">Pts</th>
									</tr>
								</thead>
								<tbody id="standing"> 
								</tbody>
							</table>
						</div>
					</div>
				</div>
				
        <div class="col-md-5">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h3 class="panel-title">Match Results</h3>
						</div>
						<div class="panel-body">
							<div id="match-date-row" class="row">
								<div id="match-date-tip" class="col-md-3">
								Match Date:
								</div>
								<div class="col-md-4">
								<select id="match-date" class="form-control" onchange="switchDate()">
								</select>
								</div>
							</div>
							<table class="table table-condensed">
								<caption>
								</caption>
								<tbody id="matches">
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
				
			</div>
			
		</div>
		
		<footer>
			<p>Yuqing Guan, Xiaofan Yang 2015, <em>Introduction to Databases</em></p>
		</footer>

		<script src="js/jquery-2.0.3.min.js"></script>
		
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.tablesorter.min.js"></script>
		<script src="js/jquery.tablesorter.widgets.min.js"></script>
		
		<script src="js/ie10-viewport-bug-workaround.js"></script>
		
		<script src="js/leagueview.js"></script>
	</body>
</html>
