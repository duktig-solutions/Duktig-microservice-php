<?php
/**
 * Base Router class for systems
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

use \Lib\Valid;

/**
 * Class Router
 *
 * @package Kernel\System\Classes
 */
class HttpRouter {

	/**
	 * Request Object
	 *
	 * @static
	 * @access protected
	 * @var \System\Request $request
	 */
	protected static $request;

	/**
	 * Response Object
	 *
	 * @static
	 * @access protected
	 * @var Response $response
	 */
	protected static $response;

	/**
	 * Parsed Route
	 *
	 * @static
	 * @access protected
	 * @var mixed
	 */
	protected static $route = NULL;

	/**
	 * Initialize route
	 * This method gets configured routes from /app/config/http-routes.php and tries to parse/compare with Request paths.
	 * i.e. Compare/parse configured "/users/{id}/posts/{num}" with request "users/15/posts/69"
	 * If the route matches, static::$route will set.
	 *
	 * @static
	 * @access public
	 * @param Request $request
	 * @param Response $response
	 * @return bool
	 */
	public static function init(Request $request, Response $response) : bool {

		# set Request and Response object for next steps
		static::$request = $request;
		static::$response = $response;
		static::$route = NULL;

		# Get Route Configuration by Request type. i.e. POST
		$routesConfig = Config::get('http-routes')[static::$request->method()];

		# Case when there are no any configuration for given request
		if(empty($routesConfig)) {

			static::$response->sendJson([
				'status' => 'error',
				'message' => 'Resource not found'
			], 404);

			return false;
		}

		# Walk through each route and try to compare/parse
		foreach($routesConfig as $uri => $route) {

			# Make array with route elements
			$routePaths = explode('/', trim($uri, '/'));

			# Compare route with request paths
			$comparedResult = static::compareRoutes($routePaths, static::$request->paths());

			// OK! Route match
			if($comparedResult !== false) {
				static::$route = $route;
				break;
			}

		}

		# Check, if route detected previously
		if(is_null(static::$route)) {

			static::$response->sendJson([
				'status' => 'error',
				'message' => 'Resource not found'
			], 404);

			return false;
		}

		# Some of route found. Let's execute it.
		# If route has middleware, then execute
		$middlewareResult = static::executeRouteMiddleware();

		# If the middleware result is false, we cannot continue to controller.
		if($middlewareResult === false) {
			return false;
		}

		# Finally execute Route Controller
		static::executeRouteController($middlewareResult);

		return true;

	}

	/**
	 * Compare Configured Route with Request Paths
	 * i.e. /users/{id}}/posts/{num}/comment/{any} -> /users/5/posts/25/comment/maybe-ok
	 *
	 * @static
	 * @access protected
	 * @param array $routePaths
	 * @param array $requestPaths
	 * @return boolean
	 */
	protected static function compareRoutes(array $routePaths, array $requestPaths) : bool {

		# Means that the route items count is not equal to Request paths count.
		if(count($routePaths) != count($requestPaths)) {
			return false;
		}

		# Compare each item in paths
		for($i = 0; $i < count($requestPaths); $i++) {

			# check, if route requires int value
			if($routePaths[$i] == '{id}') {
				if(!Valid::id($requestPaths[$i])) {
					return false;
				}

				continue;
			}

			# check if route requires numeric value
			if($routePaths[$i] == '{num}') {

				if(!is_numeric($requestPaths[$i])) {
					return false;
				}

				continue;

			}

			# if it is {any} assume this can be passed
			if($routePaths[$i] == '{any}') {
				if(!$requestPaths[$i]) {
					return false;
				}

				continue;
			}

			# check if route is equal to path (i.e. /users/ -> /users/ )
			if($routePaths[$i] != $requestPaths[$i]) {
				return false;
			}

		}

		# All conditions matched, so it's OK! Route detected.
		return true;

	}

	/**
	 * Execute route Middleware if configured
	 *
	 * @static
	 * @access protected
	 * @return mixed
	 */
	protected static function executeRouteMiddleware() {

		# By default the result is empty array
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
			$result = $middlewareObject->$methodName(static::$request, static::$response, $result);

			# If a middleware result is false, we cannot continue to next one.
			if($result === false) {
				return false;
			}

		}

		return $result;

	}

	/**
	 * Finally Execute Route Controller
	 *
	 * @static
	 * @access protected
	 * @param array $middlewareResult
	 * @return void
	 */
	protected static function executeRouteController(array $middlewareResult) : void {

		# Let's check, if this route configured with caching
		# If so, the Response object will care about next functionality.
		if(static::isRouteCacheable()) {

			# We get a cache key and cache configuration of route for response functionality.
			self::$response->enableCaching(
				Config::get()[static::$route['cacheConfig']],
				md5(static::$request->uri() . '_' . static::$request->method())
			);
		}

		$ctrlConfig = explode('->', static::$route['controller']);

		$className = "\\App\\Controllers\\$ctrlConfig[0]";
		$methodName = $ctrlConfig[1];

		# Create New Controller Object and execute
		$ctrlObject = new $className();
		$ctrlObject->$methodName(static::$request, static::$response, $middlewareResult);

		# Special note about caching: The response content caching will handled in Response object.
	}

	/**
	 * Return matched route
	 *
	 * @static
	 * @access public
	 * @return mixed
	 */
	public static function getRoute() {
		return static::$route;
	}

	/**
	 * Return true if a Route configured to be cacheable
	 *
	 * @static
	 * @access public
	 * @return bool
	 */
	public static function isRouteCacheable() : bool {

		if(!empty(static::$route['cacheConfig'])) {
			return True;
		}

		return False;
	}

	/**
	 * Return dynamically generated URI
	 *
	 * @static
	 * @access public
	 * @param null|string $next
	 * @return string
	 */
	public static function dynamicURI(?string $next) : string {

		$uri = static::$request->server('REQUEST_SCHEME') . '://' .
			static::$request->server('SERVER_NAME');

		$uri .= !is_null($next) ? $next : '';

		return $uri;

	}
}
