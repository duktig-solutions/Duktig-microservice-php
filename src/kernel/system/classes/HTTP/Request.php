<?php
/**
 * Base HTTP Request Class for Systems
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 2.1.3
 */
namespace System\HTTP;

use Lib\Valid;

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
     * Files
     *
     * @access protected
     * @var array
     */
    protected array $_files = [];

    /**
     * Post data
     *
     * @access protected
     * @var array
     */
    protected array $_post = [];

	/**
	 * Class Constructor
	 *
	 * @access public
     */
	public function __construct() {

		# $_SERVER
		$this->_server = $_SERVER;
        $this->_files = $_FILES;
        $this->_post = $_POST;

		# Request method
		if(isset($_SERVER['REQUEST_METHOD'])) {
			$this->method = $_SERVER['REQUEST_METHOD'];
		}

		# Headers. In PHP-FPM this will not work.
		if (function_exists('getallheaders')) {
			$this->headers = getallheaders();
		} else {

			foreach ($_SERVER as $name => $value) {
				if (str_starts_with($name, 'HTTP_')) {
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

        # Parse also _GET parameters if specified
		if(count($tmp) == 2) {
			parse_str($tmp[1], $this->queryParams);
		}

		# Parse Raw Request Data
		$this->rawInput = file_get_contents('php://input');

		# Get Content type from headers to determine parsing mode
		$contentType = $this->headers('Content-Type');

		Switch ($contentType) {
			case 'application/json':

                $isValidJson = Valid::jsonString($this->rawInput);

                if($isValidJson === true) {
                    $this->input = json_decode($this->rawInput, true);
                } else {
                    $this->input = [];
                }

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
		return ($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

    /**
     * Return parsed paths from URI
     *
     * @access public
     * @param int|null $index
     * @return mixed
     */
    public function paths(int $index = NULL): mixed
    {

        # Return an array of paths
        if(is_null($index)) {
	        return $this->paths;
        }

        # Return a specified path by index
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
     * @param null|string $default
     * @return mixed
     */
    public function get(?string $item = NULL, null|string $default = ''): mixed {

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
     * @param null|string $default
     * @return mixed
     */
	public function input(?string $item = NULL, null|string $default = ''): mixed {

	    if(is_null($item)) {
	        return $this->input;
        }

        if(isset($this->input[$item])) {
	        return $this->input[$item];
        }

        return $default;

    }

    /**
     * Return POST values
     *
     * @access public
     * @param string|null $item
     * @param null|string $default
     * @return mixed
     */
    public function post(?string $item = NULL, null|string $default = ''): mixed {

        if(is_null($item)) {
            return $this->_post;
        }

        if(isset($this->_post[$item])) {
            return $this->_post[$item];
        }

        return $default;

    }

    /**
     * Return raw input data (i.w. whole json content from client).
     *
     * @access public
     * @return false|string
     */
	public function rawInput(): bool|string {
	    return $this->rawInput;
    }

    /**
     * Return values from global _SERVER
     *
     * @access public
     * @param string|null $item
     * @param null|string $default
     * @return mixed
     */
	public function server(?string $item = NULL, null|string $default = ''): mixed {

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
     * @param null|string $default
     * @return mixed
     */
    public function headers(?string $header = NULL, null|string $default = ''): mixed
    {

        if(is_null($header)) {
            return $this->headers;
        }

		if(isset($this->headers[$header])) {
			return $this->headers[$header];
		}

	    # In case if header items come with lower case.
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

    /**
     * Return request file(s)
     *
     * @access public
     * @param string|null $name
     * @return mixed
     */
    public function files(string $name = NULL): mixed {

        if(is_null($name)) {
            return $this->_files;
        }

        if(isset($this->_files[$name])) {
            return $this->_files[$name];
        }

        return null;
    }

}