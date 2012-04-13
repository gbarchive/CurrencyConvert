<?php
	/*	Currency CSV Filler
	 * 
	 *	Takes a CSV of dates and adds a column beside each with the exchange rate.
	 *	Configured entirely based on these 6 constants. Requires a class called Currency
	 *  which provides a constructor that takes the source currency (string) and a method
	 * 	called getConversion(destination currency, date)
	 *
	 *	Loads FILE, which is a list of dates, and creates FILE_OUT, a list of date, exchange rate pairs 
	 *	in CSV format. 
	 */

	define("FROM_CURRENCY", "USD");
	define("TO_CURRENCY", "CAD");
	define("FILE", "dates.csv");
	define("FILE_OUT", "dates.out.csv");
	define("NULL_PLACEHOLDER", "");
	define("TZ", "America/Los_Angeles");

	require_once "Currency.php";

	if(defined("TZ") && TZ !== "")
		date_default_timezone_set(TZ);

	$converter = new Currency(FROM_CURRENCY);

	$dates = array();
	
	$fh = fopen(FILE, "r");
	
	while(($date = fgetcsv($fh)) !== false) {
		$date = $date[0];
		$exchange = $converter->getConversion(TO_CURRENCY, $date);
		if($exchange === false) {
			$exchange = NULL_PLACEHOLDER;
			echo "WARN: Failed to find exchange rate on {$date}\n";
		}

		// create the array we will eventually output.
		$dates[] = array($date, $exchange);


		echo "{$date} : {$exchange}\n";
	}

	fclose($fh);
	$fh = fopen(FILE_OUT, "w+");

	foreach($dates as $date) {
		fputcsv($fh, $date);
	}

	fclose($fh);
?>
