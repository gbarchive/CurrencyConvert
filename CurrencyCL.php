<?php
	/*
	 * 	A quick command land interface for currency conversion.
	 *
	 *	Requires a class called Currency which provides a method getConversion(to, date).
	 *
	 */

	require_once "Currency.php";

	date_default_timezone_set("America/Los_Angeles");
	
	$from_currency = strtoupper($argv[1]);
	$to_currency = strtoupper($argv[2]);
	$date = $argv[3];

	if($argc !== 4)
		die(synopsis());

	$converter = new Currency($from_currency);
	$result = $converter->getConversion($to_currency, $date);
	if($result === false)
		echo "No results available.\n";
	else 
		echo "{$date} - {$from_currency} -> {$to_currency} = $result\n";
	
	function synopsis() {
		// return the synopsis
		return "Please run as php CurrencyCL.php [FROM] [TO] [DATE]\n";
	}
?>
