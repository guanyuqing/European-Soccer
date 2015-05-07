<?php
	
	if (!array_key_exists("id", $_REQUEST))
	{
		$id = 0;
	}
	else
	{
		$id = $_REQUEST["id"];
		
		if (array_key_exists("type", $_REQUEST))
		{
			if ($_REQUEST["type"] == "league")
			{
				switch ($id)
				{
				case 2:
					$id = 75;
					break;
				case 3:
					$id = 58;
					break;
				case 5:
					$id = 91;
					break;
				case 6:
					$id = 115;
					break;
				default:
					$id = 0;
				}
			}
		}
	}
	
	
	$path = dirname(__FILE__) . "/../img/stadium/" . $id . ".jpg";
	if (!file_exists($path))
	{
		$path = dirname(__FILE__) . "/../img/stadium/0.jpg";
	}
	
	header('Content-Type: image/jpeg');
	readfile($path);
?>
