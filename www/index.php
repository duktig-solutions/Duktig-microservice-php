<?php
declare(strict_types=1); 
/**
 * HTTP entrypoint for Web Servers such as Apache, Nginx.
 * To set up the environment with web server you have to set ./www as web public directory.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System\HTTP;

use Exception;
use System\Config;
use System\Env;

# Define project root path
define('DUKTIG_ENV', 'http');
define('DUKTIG_ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('DUKTIG_APP_PATH', realpath(DUKTIG_ROOT_PATH . 'app') . '/');

# Include Constants file
require_once (DUKTIG_APP_PATH . 'config/constants.php');

# Include Auto loader
require_once (DUKTIG_ROOT_PATH . 'vendor/autoload.php');

# Load environment variables
Env::load(DUKTIG_ROOT_PATH.'.env');

# Initialize Request/Response
$request = new Request();
$response = new Response();

set_exception_handler(function($e) use($response) {

    # This will return true, if not notice
    # In case if this is not a notice, we are throwing an Exception
        
    # Reset Response data, Set new data and output.
    $response->reset();
    $response->sendJson(
        \System\Ehandler::getDetailed($e->getMessage(), $e->getCode()),
        500
    );
    $response->sendFinal();

});

# Set error handler
set_error_handler(
    /**
     * @throws Exception
     */
    function($code, $message, $file, $line) use($response) {
    
        // # This will return true, if not notice
        // # In case if this is not a notice, we throw an Exception
        if(\System\Ehandler::processError($message, $code, $file, $line)) {
            throw new Exception($message . " - ".$file.":".$line);
        }

    }
);

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
	Router::init($request, $response);

	# Finally, send response to client.
    $response->sendFinal();

} catch(\Throwable $e) {
    
    \System\Ehandler::processError($e->getMessage(), 0, $e->getFile(), $e->getLine());

    # Reset Response data, Set new data and output.
    $response->reset();
    $response->sendJson(
        \System\Ehandler::getDetailed($e->getMessage(), $e->getCode()),
        500
    );
    $response->sendFinal();

}
