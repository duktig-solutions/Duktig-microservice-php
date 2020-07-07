<?php
/**
 * Validation Class to test:
 *
 * - validate Request Array From Json
 * - validate Request Multi-Dimensional Array From Json
 * - validate Form Request
 * - validate Get RequestData
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.1.0
 */
namespace App\Controllers\Examples;

use Lib\Validator;
use System\Request;
use System\Response;

/**
 * Class Validation
 *
 * @package App\Controllers\Tests
 */
class Validation {

	/**
	 * Validate Request Array from Json
	 * Test URL: http://localhost/duktig.microservice.1/www/index.php/validate_array_from_json
	 *
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function validateRequestArrayFromJson(Request $request, Response $response, array $middlewareData) : bool {

		// Validate User Data
		$validation = Validator::validateJson(
			$request->rawInput(),
			[
				# Any data
				'string1' => 'required',

				# Credit card
				'credit_card1' => 'credit_card',

				# Credit card Not required
				'credit_card2' => 'credit_card:!required',

				# Password with default requirements: between 6 - 128 chars
				'password1' => 'password',

				# Password with default requirements: between 6 - 128 chars, Not required
				'password2' => 'password:!required',

				# Password with selected parameters
				'password3' => 'password:8:16',

				# Password with selected parameters, Not required
				'password4' => 'password:8:100:!required',

				# email
				'email1' => 'email',

				# email not required
				'email2' => 'email:!required',

				# ID
				'id1' => 'id',

				# ID not required
				'id2' => 'id:!required',

				# Digits
				'digits1' => 'digits',

				# Digits not required
				'digits2' => 'digits:!required',

				# Integer
				'int_range1' => 'int_range',

				# Integer with selected range, min - max
				'int_range2' => 'int_range:1:10',

				# Integer with selected range, min
				'int_range3' => 'int_range:5',

				# Integer with selected range, max
				'int_range4' => 'int_range::10',

				# Integer with selected range, min - max, not required
				'int_range5' => 'int_range:5:15:!required',

				# Integer with selected range, min, not required
				'int_range6' => 'int_range:5::!required',

				# Float
				'float_range1' => 'float_range',

				# Float with selected range, min - max
				'float_range2' => 'float_range:1.50:10.56',

				# Float with selected range, min
				'float_range3' => 'float_range:5.25',

				# Float with selected range, max
				'float_range4' => 'float_range::10.85',

				# Float with selected range, min - max, not required
				'float_range5' => 'float_range:5.01:15.45:!required',

				# Float with selected range, min, not required
				'float_range6' => 'float_range:5.50::!required',

				# IP Address
				'ip_address1' => 'ip_address',

				# IP Address Not required
				'ip_address2' => 'ip_address:!required',

				# HTTP Host
				'http_host1' => 'http_host',

				# HTTP Host not required
				'http_host2' => 'http_host:!required',

				# Alpha-Numeric
				'alphanumeric1' => 'alphanumeric',

				# Alpha-Numeric not required
				'alphanumeric2' => 'alphanumeric:!required',

				# Alpha
				'alpha1' => 'alpha',

				# Alpha not required
				'alpha2' => 'alpha:!required',

				# Alpha not required but cannot be empty
				'alpha3' => 'alpha:!required:!empty',

				# String length - this will accept empty string, because the length not specified
				'string_length1' => 'string_length',

				# String length with specified min, max
				'string_length2' => 'string_length:5:10',

				# String length with specified min
				'string_length3' => 'string_length:5',

				# String length with specified max
				'string_length4' => 'string_length::15',

				# String length with specified min, max, not required
				'string_length5' => 'string_length:2:6:!required',

				# String length with specified min, not required
				'string_length6' => 'string_length:4::!required',

				# String length with specified max, not required
				'string_length7' => 'string_length::10:!required',

				# URL
				'url1' => 'url',

				# URL not required
				'url2' => 'url:!required',

				# URL with flag FILTER_FLAG_PATH_REQUIRED
				'url5' => 'url:'.FILTER_FLAG_PATH_REQUIRED,

				# URL with flag FILTER_FLAG_QUERY_REQUIRED
				'url6' => 'url:'.FILTER_FLAG_QUERY_REQUIRED,

				# URL with flag FILTER_FLAG_QUERY_REQUIRED, not required
				'url7' => 'url:'.FILTER_FLAG_QUERY_REQUIRED.':!required',

				# Date ISO
				'date_iso1' => 'date_iso',

				# Date ISO not required
				'date_iso2' => 'date_iso:!required',

				# Date with default ISO format
				'date1' => 'date',

				# Date with default ISO format, not required
				'date2' => 'date::!required',

				# Date with specified format
				'date3' => 'date:m/d/Y',

				# Date with specified format, not required
				'date4' => 'date:d-m-Y:!required',

				# Date Equal or after required date
				'date_after1' => 'date_equal_or_after:2019-06-10:Y-m-d',

				# Date Equal or after required date with another format
				'date_after2' => 'date_equal_or_after:2019-06-10:m/d/Y',

				# Date Equal or after required date with another format, not required
				'date_after3' => 'date_equal_or_after:2019-06-12:d.m.Y:!required',

				# Date Equal or before required date
				'date_before1' => 'date_equal_or_before:2019-06-11:Y-m-d',

				# Date Equal or before required date with another format
				'date_before2' => 'date_equal_or_before:2019-06-11:d.m.Y',

				# Date between required dates
				'date_between1' => 'date_between:1979-09-22:2019-06-12:Y-m-d',

				# Date between required dates with another format
				'date_between2' => 'date_between:1979-09-22:2019-06-12:m/d/Y',

				# Date between required dates with another format, not required
				'date_between3' => 'date_between:1979-09-22:2019-06-12:d.m.Y:!required',

				# Equal to specified value
				'equal_to1' => 'equal_to:ABC',

				# Equal to specified value, not required
				'equal_to2' => 'equal_to:XYZ:!required',

				# Equal to specified value, not required but cannot be empty
				'equal_to3' => 'equal_to:XYZ:!required:!empty',

				# Not equal to
				'not_equal_to1' => 'not_equal_to:999',

				# Not equal to, not required
				'not_equal_to2' => 'not_equal_to:888:!required',

				# One of
				'one_of1' => 'one_of:Yes:No',

				# One of not required
				'one_of2' => 'one_of:OK:NOK:!required',

				# Not one of
				'not_one_of1' => 'not_one_of:A:B:C',

				# Not one of, not required
				'not_one_of2' => 'not_one_of:X:Y:Z:!required',

				# Array
				'array1' => 'array',

				# Array with specified length, min, max
				'array2' => 'array:2:4',

				# Array with specified length, min
				'array3' => 'array:2',

				# Array with specified length, max
				'array4' => 'array::5',

				# Array with specified length, min, max, not required
				'array5' => 'array:3:5:!required',

				# Array with specified length, min, not required
				'array6' => 'array:2::!required',

				# Array with specified length, max, not required
				'array7' => 'array::3:!required',

				# Array containing ids
				'ids_array' => 'ids_array',

				# Array containing ids with specified length, min, max
				'ids_array1' => 'ids_array:1:5',

				# Array containing ids with specified length min
				'ids_array2' => 'ids_array:2',

				# Array containing ids with specified length max
				'ids_array3' => 'ids_array::3',

				# Array containing ids with specified length min not required
				'ids_array4' => 'ids_array:3::!required',

				# Array containing ids with specified length max not required
				'ids_array5' => 'ids_array::4:!required',

				# Array containing ids not required but cannot be empty
				'ids_array6' => 'ids_array:::!required:!empty',

				# Array containing ids with specified length min, max, not required but cannot be empty
				'ids_array7' => 'ids_array:1:5:!required:!empty',

				####### Mixed Rules to test ########

				# Email with min 4, max 25 chars
				'mixed_email1' => 'email|string_length:4:25',

			],
			[
				# Required exact keys, values
				'general1' => 'exact_keys_values',

				# Requires at last one value
				'general2' => 'at_least_one_value',

				# Requires no extra values
				'general3' => 'no_extra_values'
			]
		);

		// There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$response->sendJson([
			'status' => 'ok'
		], 200);

		return true;

	}

	/**
	 * Validate Multidimensional array from Json
	 * Test URL: http://localhost/duktig.dev/www/index.php/validate_multidimensional_array_from_json
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function validateRequestMultiDimensionalArrayFromJson(Request $request, Response $response, array $middlewareData) : bool {

		// Validate User Data
		$validation = Validator::validateJson(
			$request->rawInput(),
			[
				# Account information
				'name' => 'alpha|string_length:2:10',
				'surname' => 'alpha|string_length:3:10',
				'email' => 'email|string_length:8:25',
				'articles' => [
					'articles_comments_count' => 'digits:!required',
					'articles_count' => 'int_range:1:10',
					'detailed' => [
						'last_article_date' => 'date',
						'last_article_rate' => 'one_of:1:2:3:4:5',
						'last_article_approved' => 'one_of:Yes:No',
						'latest_10_article_ids' => 'array:10:10',
						'latest_5_article_titles' => 'array:5:5:!required'
					]
				],
				'last_access' => [
					'last_login_date' => 'date_between:2019-04-04:'.date('Y-m-d').':Y-m-d',
					'ip_address' => 'ip_address'
				],
				'interests' => 'string_length:25:250:!required'
			]);

		// There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$response->sendJson([
			'status' => 'ok'
		], 200);

		return true;

	}

	/**
	 * Validate Form Data
	 * Test URL: http://localhost/duktig.dev/www/index.php/validate_form_data
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function validateFormRequest(Request $request, Response $response, array $middlewareData) : bool {

		// Validate User Data
		$validation = Validator::validateDataStructure(
			$request->input(),
			[
				# String length with specified min, max
				'name' => 'string_length:3:7',

				# email
				'email' => 'email',

				# String min 5, max 25, not required
				'comment' => 'string_length:5:25:!required',

				# Array
				'test_array' => 'array'
			]
		);

		// There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$response->sendJson([
			'status' => 'ok',
			'data' => [
				'test_array' => $request->input('test_array')
			]
		], 200);

		return true;

	}

	/**
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return bool
	 * @throws \Exception
	 */
	public function validateGetRequestData(Request $request, Response $response, array $middlewareData) : bool {

		// Validate User Data
		$validation = Validator::validateDataStructure(
			$request->get(),
			[
				# String length with specified min, max
				'page' => 'int_range:1:1000',

				# email
				'limit' => 'int_range:5:25'
			]
		);

		// There are errors in validation
		if(!empty($validation)) {
			$response->sendJson($validation, 422);
			return false;
		}

		$response->sendJson([
			'status' => 'ok',
			'data' => $request->get()
		], 200);

		return true;

	}

}
