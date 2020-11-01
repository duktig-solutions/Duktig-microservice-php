<?php
declare(strict_types=1); 
/**
 * HTTP entrypoint for Web Servers such as Apache, Nginx.
 * To setup environment with web server you have to set entrypoints/http/ as web public directory.
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

# Define project root path
define('DUKTIG_ENV', 'http');
define('DUKTIG_ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('DUKTIG_APP_PATH', realpath(DUKTIG_ROOT_PATH . 'app') . '/');

# Include Constants file
require_once (DUKTIG_APP_PATH . 'config/constants.php');

# Include Autoloader
require_once (DUKTIG_ROOT_PATH . 'vendor/autoload.php');

# Initialize Request/Response
$request = new Request();
$response = new Response();

# Set error handler
set_error_handler(function($code, $message, $file, $line) use($response) {

    # This will return true, if not notice
    # In case if this is not a notice, we throwing Exception
    if(Ehandler::processError($message, $code, $file, $line)) {

        # Reset Response data, Set new data and output.
        $response->reset();
        $response->sendJson(
            Ehandler::getDetailed($message, $code),
            500
        );
        $response->sendFinal();
    }

});

try {

	ob_start();

	# Set Configuration
	ini_set('log_errors', (string) Config::get()['LogErrors']);
	ini_set('display_errors', (string) Config::get()['DisplayErrors']);
    date_default_timezone_set((string) Config::get()['DateTimezone']);

	# Check if the service is under maintenance
	if(Config::get()['UnderMaintenance'] == 1) {
			
		$response->sendJson([
			'status' => 'ok',
			'message' => 'Under maintenance'
		], 503);
		
	}

	# Initialize Route
	# If no route match/found to run, Routing will automatically send Error 404
	HttpRouter::init($request, $response);

	# Finally send response to client.
    $response->sendFinal();

} catch(\Throwable $e) {

    Ehandler::processError($e->getMessage(), 0, $e->getFile(), $e->getLine());

    # Reset Response data, Set new data and output.
    $response->reset();
    $response->sendJson(
        Ehandler::getDetailed($e->getMessage(), $e->getCode()),
        500
    );
    $response->sendFinal();

}
