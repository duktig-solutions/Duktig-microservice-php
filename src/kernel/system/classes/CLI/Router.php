<?php
/**
 * CLI Routing executable class
 * 
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */
namespace System\CLI;

use \System\Logger;
use \System\Config;

/**
 * Class Router
 *
 * @package System
 */
class Router {

	/**
	 * CLI input object
	 *
	 * @static
	 * @access protected
	 * @var Input
	 */
	protected static Input $input;

	/**
	 * CLI output object
	 *
	 * @static
	 * @access protected
	 * @var Output
	 */
	protected static Output $output;

	/**
	 * Parsed Route
	 *
	 * @static
	 * @access private
	 * @var mixed
	 */
	private static mixed $route = NULL;

	/**
	 * Running file descriptor
	 *
	 * @static
	 * @access private
	 * @var resource
	 */
	private static $lockFileDescriptor;

	/**
	 * Lock file lifetime.
	 * If passed as 0, the file time will not be checked.
	 *
	 * @static
	 * @access private
	 * @var int
	 */
	private static int $lockFileLifeTime = 0;

	/**
	 * Process lock file name
	 *
	 * @static
	 * @access private
	 * @var string
	 */
	private static string $lockFile;

	/**
	 * Initialize route 
	 * This method gets configured routes from /app/config/http-routes.json and tries to parse/compare with Request paths.
	 * i.e. Compare/parse configured "/users/{id}/posts/{num}" with request "users/15/posts/69"
	 * If the route matches, static::$route will set.
	 *
	 * @static
	 * @access public
	 * @param Input $input
	 * @param Output $output
	 * @return void
	 */
	public static function init(Input $input, Output $output) : void {
		
		# set Input and Output objects for next steps
		static::$input = $input;
		static::$output = $output;

		# First, let's check, if the route is not empty.
        if(empty($input->route())) {
            $output->usage();
            exit();
        }

		# Get Route Configuration
        $routesConfig = Config::get('cli-routes');

        if(empty($routesConfig)) {
            Logger::Log('The configuration of CLI routes is empty. Unable to handle the process. See ./app/Config/cli-routes.php', Logger::ERROR);
            $output->stderr('The configuration of CLI routes is empty. Unable to handle the process. See ./app/Config/cli-routes.php');
		}

		if(!isset($routesConfig[$input->route()])) {
            Logger::Log('Route `' . $input->route() . '` not exists to process.', Logger::ERROR);
            $output->stderr('Route `' . $input->route() . '` not exists to process.');
        }

        static::$route = $routesConfig[$input->route()];

        # Check, if current process already running and it was started as unique.
		if(isset(static::$route['executeUniqueProcessLifeTime']) and static::$route['executeUniqueProcessLifeTime'] > 0) {
			static::runProcessUnique($output);
		}

		# Some route found. Let's execute it.
        # If route has middleware, then execute
        $middlewareResult = static::executeRouteMiddleware();

        # Finally, execute Route Controller
        static::executeRouteController($middlewareResult);

        # Unlock process if locked
		static::unlockProcess();

	}

	/**
	 * Run this process as unique.
	 *
	 * @static
	 * @access private
	 * @param Output $output
	 * @return void
	 */
	private static function runProcessUnique(Output $output) : void {

		# Define lock file id
		static::$lockFile = '/tmp/' . md5(static::$route['controller'])  . '.lock';

		static::$lockFileLifeTime = static::$route['executeUniqueProcessLifeTime'];

		# Check if another instance of this process is already running.
		if(!static::checkProcessLocked($output)) {
            return;
        }

		$fp = fopen(static::$lockFile, 'w+');

		if(!$fp) {
			$output->stderr('Unable to create lock file resource for ' . static::$route['controller']);
		}

		if (!flock($fp, LOCK_EX | LOCK_NB)) {
			$output->stderr('Unable to lock file for ' . static::$route['controller']);
		}

		static::$lockFileDescriptor = $fp;

	}

	/**
	 * Check if the current process already locked
	 *
	 * @static
	 * @access private
	 * @param Output $output
	 * @return bool
	 */
	private static function checkProcessLocked(Output $output) : bool {

		# Check if lock file exists
		if(!file_exists(static::$lockFile)) {
			return true;
		}

		# Define file creation time
		$fileTime = filectime(static::$lockFile);
		$fileLiveTime = (time() - $fileTime); // Seconds

		# Check file time if expired, delete the lock file.
		# i.e. The file created more than 15 seconds ago.
		if($fileLiveTime >= static::$lockFileLifeTime) {
			$output->stdout('Process Lock file exists and expired. Deleting ...');
			unlink(static::$lockFile);
			return true;
		}

		# The process still in regular run.
		$output->stderr('Process already running. Can run again after ' . (static::$lockFileLifeTime - $fileLiveTime) . ' seconds.');

		return false;

	}

	/**
	 * Unlock the process if locked
	 *
	 * @static
	 * @access public
	 * @return void
	 */
	public static function unlockProcess() : void {

		if(is_resource(static::$lockFileDescriptor) and file_exists(static::$lockFile)) {

			fclose(static::$lockFileDescriptor);
			unlink(static::$lockFile);

			static::$lockFileDescriptor = null;
			static::$lockFile = '';
		}

	}

	/**
	 * Execute route Middleware if configured
	 *
	 * @static
	 * @access private
	 * @return array
	 */
	private static function executeRouteMiddleware() : array {
		
		# By default, the result is an empty array
		$result = [];

		# If there is no any middleware
		if(empty(static::$route['middleware'])) {
			return $result;
		}

		# Walk through each middleware and execute
		foreach(static::$route['middleware'] as $middleware) {
			
			$middlewareConfig = explode('->', $middleware);
			
			$className = "\\App\\Middleware\\$middlewareConfig[0]";
			$methodName = $middlewareConfig[1];

			# Create new Middleware Object and execute
			$middlewareObject = new $className();
			$result = $middlewareObject->$methodName(static::$input, static::$output, $result);
			
		}

		return $result;
		
	}

	/**
	 * Finally Execute Route Controller
	 *
	 * @static 
	 * @access private
	 * @param array $middlewareResult
	 * @return void
	 */
	private static function executeRouteController(array $middlewareResult) : void {

		$ctrlConfig = explode('->', static::$route['controller']);

		$className = "\\App\\Controllers\\$ctrlConfig[0]";
		$methodName = $ctrlConfig[1];

		# Create New Controller Object and execute
		$ctrlObject = new $className();
        $ctrlObject->$methodName(static::$input, static::$output, $middlewareResult);
		
	}

	/**
	 * Return matched route
	 *
	 * @static
	 * @access public
	 * @return mixed
	 */
	public static function getRoute(): mixed {
		return static::$route;
	}

}