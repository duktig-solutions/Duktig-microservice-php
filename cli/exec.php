<?php
declare(strict_types=1);
/**
 * Command Line Interface.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System\CLI;

use \System\Logger;
use \System\Config;

# Define project root path
define('DUKTIG_ENV', 'cli');
define('DUKTIG_ROOT_PATH', realpath(__DIR__ . '/../') . '/');
define('DUKTIG_APP_PATH', realpath(DUKTIG_ROOT_PATH . 'app') . '/');

# Include Constants file
require_once (DUKTIG_APP_PATH . 'config/constants.php');

# Include Autoloader
require_once (DUKTIG_ROOT_PATH . 'vendor/autoload.php');

/**
 * Because this is command line interface,
 * the process can run without time limitation.
 */
set_time_limit(0);

# Set Configuration
ini_set('log_errors', (string) Config::get()['LogErrors']);
ini_set('display_errors', (string) Config::get()['DisplayErrors']);
date_default_timezone_set((string) Config::get()['DateTimezone']);

# Initialize Input/Output
$input = new Input($argv);
$output = new Output();

# Set error handler
set_error_handler(function($code, $message, $file, $line) use ($output) {

    # This will return true, if not notice
    # In case if this is not a notice, we throwing Exception
    if(\System\Ehandler::processError($message, $code, $file, $line)) {
        $output->stderr("\nError! " . $message);
    }

});

try {

    # Check if the service is under maintenance
    if(Config::get()['DisableCLI'] == 1) {

        $output->stdout('CLI access is rejected due to the disabled flag. See /app/config/app.php - DisableCLI.');
        Logger::Log('CLI access is rejected due to the disabled flag. See /app/config/app.php - DisableCLI', Logger::WARNING, __FILE__, __LINE__);

        exit();

    }

    # Initialize Route
    # If no route match/found to run, Routing will automatically exit with error
    \System\CLI\Router::init($input, $output);

} catch(\Throwable $e) {

    \System\Ehandler::processError($e->getMessage(), 0, $e->getFile(), $e->getLine());

	# In some cases, the process can be locked.
	# Let's try to unlock
	\System\CLI\Router::unlockProcess();

    $output->stderr($e->getMessage());

}
