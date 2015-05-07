<?php

function ordinal($num)
{
	if ($num >= 11 && $num <= 13)
	{
		return $num . "th";
	}
	
	$lastDigit = $num % 10;
	
	if ($lastDigit == 0 || $lastDigit > 3)
	{
		return $num . "th";
	}
	
	switch ($lastDigit)
	{
		case 1:
			return $num . "st";
		case 2:
			return $num . "nd";
		case 3:
			return $num . "rd";
	}
}


?>