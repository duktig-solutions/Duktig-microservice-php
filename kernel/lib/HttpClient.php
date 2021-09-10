<?php
/**
 * HTTP Client Class to Send and Receive data via HTTP Protocol
 */
namespace Lib;

use System\Config;

/**
 * Class HttpClient
 *
 * @package Lib
 */
class HttpClient {

	/**
	 * Send request and get response data
	 *
	 * @static
	 * @access public
	 * @param string $url
	 * @param string $method
	 * @param mixed array | string | null $data
	 * @param mixed array | null $headers
	 * @return mixed
	 */
	public static function sendRequest(string $url, string $method = 'GET', $data = '',  ?array $headers = []) : array {

		# Initialize the CURL
		$handle = curl_init();

		# Define CURL Options
		$curlOptions = [
			// CURLINFO_HEADER_OUT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_USERAGENT => 'Duktig.Microservice HTTP Client',

			# This is not secure, but now need to avoid certification issue
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0
		];

		# Initialize headers
		if(!empty($headers)) {
			$curlOptions[CURLOPT_HTTPHEADER] = [];
			foreach($headers as $key => $value) {
				$curlOptions[CURLOPT_HTTPHEADER][] = sprintf("%s:%s", $key, $value);
			}
		}

		# Set POST/PUT/DELETE Data if not GET request
		if($method != 'GET') {

			$curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);

			if(is_array($data)) {
				$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
			} else {
				$curlOptions[CURLOPT_POSTFIELDS] = $data;
			}

		}

		# Set request URL
		$curlOptions[CURLOPT_URL] = $url;

		# Set all options to CURL
		curl_setopt_array($handle, $curlOptions);

		# Execute the request and get response
		$result = curl_exec($handle);

		# Set Error/Info
		$info = (object) curl_getinfo($handle);
		$error = curl_error($handle);

		# Close The CURL Connection
		curl_close($handle);

		return [
			'result' => $result,
			'info' => $info,
			'error' => $error
		];

	}

	/**
	 * Send Request by curl binary async
	 *
	 * Command will look like:
	 *   curl -X POST -H 'Content-Type: application/json' \
	 *   -d '{"batch":[{"secret":"testsecret","userId":"some_user","event":"PHP Fork Queued Event","properties":null,"timestamp":"2013-01-30T14:34:50-08:00","context":{"library":"analytics-php"},"action":"track"}],"secret":"testsecret"}' \
	 *  'https://api.segment.com/v1/import' > /dev/null 2>&1 &
	 *
	 * @static
	 * @access public
	 * @param $url
	 * @param string $method
	 * @param mixed array | string | null $data
	 * @param mixed array | null $headers
	 * @return void
	 */
	public static function sendRequestAsync(string $url, string $method = 'GET', $data = '', ?array $headers) : void {

		# Command to execute
		$cmd = Config::get('app')['Executables']['curl'];

		# Request method
		$cmd .= " -X ".$method;

		# Headers
		if(!empty($headers)) {
			foreach ($headers as $key => $value) {

				# Example: -H 'Content-Type: application/json'";
				$cmd .= " -H '" . $key . ": " . $value . "'";
			}
		}

		# Data
		if(is_array($data)) {
			# -d "param1=value1&param2=value2"
			$cmd .= " -d '" . http_build_query($data) . "'";
		} else {
			$cmd .= " -d '" . $data . "'";
		}

		# URL
		$cmd .= " '" . $url . "'";

		# Send to null (no response output)
		$cmd .= " > /dev/null 2>&1 &";

		exec($cmd);

	}

	/*
	public function fast_request($url)
	{
		$parts=parse_url($url);
		$fp = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:80,$errno, $errstr, 30);
		$out = "GET ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Length: 0"."\r\n";
		$out.= "Connection: Close\r\n\r\n";

		fwrite($fp, $out);
		fclose($fp);
	}
	*/

}
