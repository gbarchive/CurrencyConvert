<?php
	define("CURRENCY_URL", "http://www.x-rates.com/cgi-bin/hlookup.cgi");

	class Currency {
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

		private function pullPage($output_currency, $date) {
			$output_currency = strtoupper($output_currency);
			
			$resource = curl_init(CURRENCY_URL);
			curl_setopt($resource, CURLOPT_POST, true);

			$time = strtotime($date);
			
			$month = date("m", $time);
			$day = date("d", $time);
			$year = date("Y", $time);

			$post_data = array(
				"ccode2"=>$this->currency,
				"ccode"=>$output_currency,
				"frMonth"=>$month-1,
				"frDay"=>$day,
				"frYear"=>$year
			);

			curl_setopt($resource, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($resource, CURLOPT_REFERER, CURRENCY_URL);
			curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($resource);
			curl_close($resource);

			if(preg_match("/no data/", $data)) {
				return false;
			}
			
			$currency_pattern = "/([0-9]*\.[0-9]*) $output_currency/";
			if(preg_match("/<tr bgcolor=#ccffcc>(.*?)<\/tr>/sim", $data, $matches)) {
				// we have an exact match
				preg_match($currency_pattern, $matches[0], $result);
				$exchange = ($result[1]);
			} else {
				preg_match_all("/([0-9]*\.[0-9]*) $output_currency/", $data, $matches);
				// average them
				$exchange = array_sum($matches[1]) / count($matches[1]);

			}
			return $exchange;
		}
	}
?>
