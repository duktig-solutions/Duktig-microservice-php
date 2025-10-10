<?php
/**
 * HTTP Client Class to Send and Receive data via HTTP Protocol
 *
 * @version 2.1 (Send files functionality added, Parse response headers)
 */
namespace Lib\HTTP;

use System\Config;

/**
 * Class Client
 *
 * @package Lib
 */
class Client {

    /**
	 * Send request and get response data
	 *
	 * @static
	 * @access public
	 * @param string $url
	 * @param string $method
	 * @param array|string|null $data
	 * @param array|null $headers
	 * @param array|null $files
	 * @return mixed
	 */
	public static function sendRequest(string $url, string $method = 'GET', array|string|null $data = '', ?array $headers = [], ?array $files = []) : array {

		# Initialize the CURL
		$handle = curl_init();

		# Define CURL Options
		$curlOptions = [
			// CURLINFO_HEADER_OUT => 1,
            CURLOPT_HEADER => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_USERAGENT => $headers['X-User-Agent'] ?? 'Duktig.Microservice HTTP Client',

			# This is not secure, but now need to avoid certification issue
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0
		];

		# Set POST/PUT/DELETE Data if not GET request
		if($method != 'GET') {

			$curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($method);


            $requestData = $data;

            /*
             * @todo Figure out this
            if(is_array($data)) {
            	$curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
			} else {
				$curlOptions[CURLOPT_POSTFIELDS] = $data;
			}
            */

			# Files attachment
			if(!empty($files)) {

				$headers['Content-Type'] = 'multipart/form-data';

				foreach($files as $index => $file) {
					$fileObj = curl_file_create($file);
					$requestData[$index] = $fileObj;
				}
			}

			$curlOptions[CURLOPT_POSTFIELDS] = $requestData;
		}

		# Initialize headers
		if(!empty($headers)) {
			$curlOptions[CURLOPT_HTTPHEADER] = [];
			foreach($headers as $key => $value) {
				$curlOptions[CURLOPT_HTTPHEADER][] = sprintf("%s:%s", $key, $value);
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

        // After curl_exec
        $header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $responseHeadersStr = substr($result, 0, $header_size);
        $result = substr($result, $header_size);
		$error = curl_error($handle);

        # Close The CURL Connection
		curl_close($handle);

		return [
            'headers' => static::parseResponseHeaders($responseHeadersStr),
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
	 *   -d '{"batch":[{"secret":"testsecret","uid":"some_user","event":"PHP Fork Queued Event","properties":null,"timestamp":"2013-01-30T14:34:50-08:00","context":{"library":"analytics-php"},"action":"track"}],"secret":"testsecret"}' \
	 *   -F 'fileX=@/path/to/fileX' -F 'fileY=@/path/to/fileY' \
	 *  'https://api.segment.com/v1/import' > /dev/null 2>&1 &
	 *
	 * @static
	 * @access public
	 * @param string $url
	 * @param string $method
	 * @param array|string|null $data
	 * @param array|null $headers
	 * @param array|null $files
	 * @return void
	 */
	public static function sendRequestAsync(string $url, string $method = 'GET', array|string|null $data = '', ?array $headers = [], ?array $files = []) : void {

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

		if(!empty($files)) {

			$cmd .= " -H 'Content-Type: multipart/form-data'";

			# -F 'fileX=@/path/to/fileX' -F 'fileY=@/path/to/fileY'
			foreach($files as $index => $path) {
				$cmd .= " -F '".$index."=@".$path."'";
			}
		}

		# URL
		$cmd .= " '" . $url . "'";

		# Send to null (no response output)
		$cmd .= " > /dev/null 2>&1 &";

		exec($cmd);

	}

    /**
     * Parse Response Headers
     *
     * @static
     * @access private
     * @param string $headers
     * @return array
     */
    private static function parseResponseHeaders(string $headers) : array {

        $responseHeaders = [];
        $headersTmp = explode("\n", $headers);

        if(!empty($headersTmp)) {
            foreach ($headersTmp as $headerLine) {

                $headerLine = trim($headerLine);

                if(empty($headerLine)) {
                    continue;
                }

                $pos = strpos($headerLine, ':');

                if($pos !== false) {
                    $responseHeaders[
                    substr($headerLine, 0, $pos)
                    ] = trim(substr($headerLine, $pos+1));
                }

            }
        }

        return $responseHeaders;
    }

}
