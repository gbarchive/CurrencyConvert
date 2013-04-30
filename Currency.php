<?php
	/*	Quick currency converter.
	 *
	 *	Written just to quickly fill a CSV with exchange rate data... no sophisticated engineering here
	 *  just a quick scrape of an existing service. The service in question does not allow their
	 *	content to be used for public display or any commercial use, so you should honor their terms if
	 *  you choose to use this library for anything.
	 *
	 *	Example usage:
	 *
	 *		$currency = new Currency("USD");	// create a converter from USD to x.
	 *		$exchange_rate = $currency->getConversion("CAD", "December 1, 2011");
	 *		echo "The exchange rate on December 1, 2011 was {$exchange_rate}."
	 *
	 *	Author: Giuseppe Burtini
	 */

	define("CURRENCY_URL", "http://www.x-rates.com/historical/");

	class Currency {
		protected $lookup = array(
			"CAD" => "Canadian Dollar",
			"EUR" => "Euro",
			"USD" => "US Dollar"
		);
		private $currency;

		// instantiate with a currency code (ex, USD)
		public function Currency($input_currency) {
			$this->currency = strtoupper($input_currency);
		}

		// pass a date (strtotime compatible) and a currency code to get the exchange.
		public function getConversion($output_currency, $date) {
			$pp = $this->pullPage($output_currency, $date);
			return $pp;	
		}

		protected function constructURL($url, $array) {
			if(strpos($url, "?") !== false)
				$url .= "&";
			else
				$url .= "?";

			$url .= http_build_query($array);
			return $url;
		}

		private function pullPage($output_currency, $date) {
			$output_currency = strtoupper($output_currency);
			$output_search = $this->lookup[$output_currency];
			
			$time = strtotime($date);
			$date_string = date("Y-m-d", $time);

			$query = array(
				"from" => $this->currency,
				"amount" => "1.00",
				"date" => $date_string
			);
			

			$resource = curl_init($this->constructURL(CURRENCY_URL, $query));
//			curl_setopt($resource, CURLOPT_POST, true);

			//curl_setopt($resource, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($resource, CURLOPT_REFERER, CURRENCY_URL);
			curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($resource);
			curl_close($resource);

			if(preg_match("/not available/", $data)) {
				return false;
			}
			
			$currency_pattern = "/([0-9]*\.[0-9]*)/"; // $output_currency/";
			$search_pattern = "#<a href='/graph/\?from={$this->currency}&amp;to={$output_currency}'>(.*?)</a>#sim";
			if(preg_match($search_pattern, $data, $matches)) {
				// we have an exact match
				preg_match($currency_pattern, $matches[0], $result);
				$exchange = ($result[1]);
			} else {
//				preg_match_all("/([0-9]*\.[0-9]*) $output_currency/", $data, $matches);
				// average them
//				$exchange = array_sum($matches[1]) / count($matches[1]);
				return false;

			}
			return $exchange;
		}
	}
?>
