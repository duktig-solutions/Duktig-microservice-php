<?php
/**
 * Example of Using Libraries
 *
 * - Use System library
 * - Use Application custom library
 * - Use Application custom library extended from system library
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Controllers\Examples;

use System\Request;
use System\Response;

# Using System libraries
use Lib\Valid;
use Lib\Generator;

# Using Application custom created Libraries
use App\Lib\Examples\ExampleLibClass;
use App\Lib\Examples\ExampleExtendedValidClass;

/**
 * Class LibrariesUsage
 *
 * @package App\Controllers\Examples
 */
class LibrariesUsage {

	/**
	 * Use System Library
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function useSystemLibrary(Request $request, Response $response, array $middlewareData): void {

		# We have to use 2 type of system libraries.
		# The first goes to use with static methods
		# and the second will be created as an object and use.

		# Using `Generator` library as an object
		$generator = new Generator();

		# Using Library methods
		$id = $generator->createNumber(1);
		$email = $generator->createEmail();

		# Using `Valid` Library
		# Checking if generated data is valid using static methods of Class.
		$isValidId = Valid::id($id);
		$isValidEmail = Valid::email($email);

		# Response with results
		$response->sendJson(
			[
				'status' => 'ok',
				'message' => 'Example of Using System Libraries. See '. basename(__FILE__) . ' / ' . __CLASS__ .'->' . __FUNCTION__ . '()',
				'Example results' => [
					'generated_id' => $id,
					'is_valid_id' => $isValidId ? 1 : 0,
					'generated_email' => $email,
					'is_valid_email' => $isValidEmail ? 1 : 0
				]
			]
		);

	}

	/**
	 * Use Application Library
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function useApplicationLibrary(Request $request, Response $response, array $middlewareData): void {

		# In this example we getting to use Application custom created library
		$appLib = new ExampleLibClass();

		# Using object method
		$sum = $appLib->exampleMethod(1,2);

		# Using Library static method
		$multiply = ExampleLibClass::exampleStaticMethod(2, 5);

		# Response with results
		$response->sendJson(
			[
				'status' => 'ok',
				'message' => 'Example of Using Application custom created Library. See '. basename(__FILE__) . ' / ' . __CLASS__ .'->' . __FUNCTION__ . '()',
				'Example results' => [
					'Object method result' => $sum,
					'Static method result' => $multiply
				]
			]
		);

	}

	/**
	 * Use Application Library extended from System Library
	 *
	 * @access public
	 * @param \System\Request $request
	 * @param \System\Response $response
	 * @param array $middlewareData
	 * @return void
	 */
	public function useApplicationExtendedLibrary(Request $request, Response $response, array $middlewareData): void {

		# Using library as an object
		$appLib = new ExampleExtendedValidClass();

		$numGreater = $appLib->numGreaterThen(2, 3);

		# Using library static method
		$numLess = ExampleExtendedValidClass::numLessThan(3,2);

		# Using Extended functionality
		$isValidId = ExampleExtendedValidClass::id(5);

		# Response with results
		$response->sendJson(
			[
				'status' => 'ok',
				'message' => 'Example of Using Application Library extended from System Library. See '. basename(__FILE__) . ' / ' . __CLASS__ .'->' . __FUNCTION__ . '()',
				'Example results' => [
					'numbers_comparision_greater' => $numGreater ? 1 : 0,
					'numbers_comparision_less' => $numLess ? 1 : 0,
					'number_valid_id' => $isValidId ? 1 : 0
				]
			]
		);

	}
}