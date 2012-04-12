<?php
	// Takes a CSV of dates and adds a column beside each with the exchange rate.
	define("FROM_CURRENCY", "USD");
	define("TO_CURRENCY", "CAD");
	define("FILE", "dates.csv");
	define("FILE_OUT", "dates.out.csv");
	define("NULL_PLACEHOLDER", "");

	require_once "Currency.php";

	date_default_timezone_set("America/Los_Angeles");
	$converter = new Currency(FROM_CURRENCY);

	$dates = array();
	
	$fh = fopen(FILE, "r");
	
	while(($date = fgetcsv($fh)) !== false) {
		$date = $date[0];
		$exchange = $converter->getConversion(TO_CURRENCY, $date);
		if($exchange === false) {
			$exchange = NULL_PLACEHOLDER;
			echo "Failed to find exchange rate on {$date}\n";
		}
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
