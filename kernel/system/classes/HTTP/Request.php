<?php
/**
 * Base HTTP Request Class for Systems
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System\HTTP;

/**
 * Class Request
 *
 * @package Kernel\System\Classes
 */
class Request {

	/**
	 * Request method GET, POST, PUT, DELETE, etc...
	 * 
	 * @access protected
	 * @var string 
	 */
	protected string $method;

	/**
	 * HTTP Request headers
	 * 
	 * @access protected
	 * @var array
	 */
	protected array $headers = [];

	/**
     * Parsed Request Data from POST, PUT, etc...
     * 
     * @access protected
     * @var array
     */
    protected array $input = [];

    /**
     * Raw Request data from php://input
     * 
     * @access protected
     * @var string
     */
    protected string $rawInput = '';
    
    /**
     * URI Request path
     *
     * @access protected
     * @var array
     */
    protected array $paths = [];

    /**
     * URI query parameters
     *
     * @access protected
     * @var array
     */
    protected array $queryParams = [];

    /**
     * $_SERVER 
     *
     * @access protected
     * @var array
     */
    protected array $_server = [];

	/**
	 * Class Constructor
	 *
	 * @access public
     */
	public function __construct() {

		# $_SERVER
		$this->_server = $_SERVER;

		# Request method
		if(isset($_SERVER['REQUEST_METHOD'])) {
			$this->method = $_SERVER['REQUEST_METHOD'];
		}

		# Headers. In PHP-FPM this will not work.
		if (function_exists('getallheaders')) {
			$this->headers = getallheaders();
		} else {

			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$this->headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}

		}

		# Parse Paths and query params
		# Paths: /users/23/posts/322/
		# Query params: limit=2&count=22
		$tmp = explode('?', $_SERVER['REQUEST_URI']);
        
        # Filter the paths
        $this->paths = array_values(array_filter(explode('/', $tmp[0]), 'urldecode'));
        $this->paths = array_map('urldecode', $this->paths);

        # Parse also GET parameters if specified
		if(count($tmp) == 2) {
			parse_str($tmp[1], $this->queryParams);
		} 

		# Parse Raw Request Data
		$this->rawInput = file_get_contents('php://input');

		# Get Content type from headers to determine parsing mode
		$contentType = $this->headers('Content-Type');

		Switch ($contentType) {
			case 'application/json':
				$this->input = json_decode($this->rawInput, true);
				break;
			default:
				parse_str(file_get_contents('php://input'), $this->input);
				break;
		}

	}

	/**
     * Return request method name
     *
     * @access public
     * @return string
	 */
	public function method() : string {
		return $this->method;
	}

    /**
     * Return request URI
     *
     * @access public
     * @return string
     */
    public function uri() : string {
		return $_SERVER['REQUEST_SCHEME'] . '://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

    /**
     * Return parsed paths from URI
     *
     * @access public
     * @param int|null $index
     * @return mixed
     */
    public function paths(int $index = NULL) {

        # Return array of paths
        if(is_null($index)) {
	        return $this->paths;
        }

        # Return specified path by index
        if(isset($this->paths[$index])) {
	        return $this->paths[$index];
        }

        # Given path not exists
        return false;

    }

    /**
     * Return parsed Query parameters
     *
     * @access public
     * @param string|null $item
     * @param mixed $default
     * @return mixed
     */
    public function get(?string $item = NULL, $default = '') {

	    if(is_null($item)) {
	        return $this->queryParams;
        }

        if(isset($this->queryParams[$item])) {
	        return $this->queryParams[$item];
        }

        return $default;

    }

    /**
     * Return Request values
     *
     * @access public
     * @param string|null $item
     * @param mixed $default
     * @return mixed
     */
	public function input(?string $item = NULL, $default = '') {

	    if(is_null($item)) {
	        return $this->input;
        }
		
        if(isset($this->input[$item])) {
	        return $this->input[$item];
        }

        return $default;

    }

    /**
     * Return raw input data (i.w. whole json content from client).
     * 
     * @access public
     * @return false|string
     */
	public function rawInput() {
	    return $this->rawInput;
    }

    /**
     * Return values from global _SERVER
     *
     * @access public
     * @param string|null $item
     * @param mixed $default
     * @return mixed
     */
	public function server(?string $item = NULL, $default = '') {

	    if(is_null($item)) {
	        return $this->_server;
        }

        if(isset($this->_server[$item])) {
	        return $this->_server[$item];
        }

        return $default;

	}

    /**
     * Get Request headers
     *
     * @access public
     * @param string|null $header
     * @param mixed $default
     * @return mixed
     */
    public function headers(?string $header = NULL, $default = '') {
        
        if(is_null($header)) {
            return $this->headers;
        }

		if(isset($this->headers[$header])) {
			return $this->headers[$header];
		}

	    # In case if header items comes with lower case.
	    if(isset($this->headers[strtolower($header)])) {
		    return $this->headers[strtolower($header)];
	    }

        return $default;

    }

	/**
	 * Return true if Ajax request
	 *
	 * @access public
	 * @return bool
	 */
	public function isAjax() : bool {
		return (isset($this->_server['HTTP_X_REQUESTED_WITH']) and $this->_server['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}

}