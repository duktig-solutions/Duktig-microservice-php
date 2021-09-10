<?php
/**
 * Base Response class for systems
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace System\HTTP;

use Exception;

/**
 * Class Response
 *
 * @package Kernel\System\Classes
 */
class Response {

    /**
     * Response Data as Array to send finally
     *
     * @access protected
     * @var array
     */
    protected $responseData = [
	    'status' => 200,
	    'headers' => [],
	    'data' => ''
    ];

	/**
	 * Redis Cache Library
	 *
	 * @access protected
	 * @var object
	 */
	protected $cacheLib;

	/**
	 * Is Caching enabled in response
	 *
	 * @access protected
	 * @var bool
	 */
	protected $cachingEnabled = False;

	/**
	 * Cache key for response
	 *
	 * @access protected
	 * @var string
	 */
	protected $cacheKey;

	/**
     * HTTP Status codes
     * 
     * @var array 
     * @access protected
     */
    protected $statusCodes = [
        // HTTP Status codes
        // 1×× Informational
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // 2×× Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // 3×× Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // 4×× Client Error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        // 5×× Server Error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
	];

    /**
     * Set response status
     *
     * @access public
     * @param int $status
     * @return void
     */
    public function status(int $status) : void {
        $this->responseData['status'] = $status;
    }

    /**
     * Set header
     *
     * @access public
     * @param string $item
     * @param string $value
     * @return void
     */
    public function header(string $item, string $value) : void {
        $this->responseData['headers'][$item] = $value;
    }

    /**
     * Write (Append) HTTP Response content
     *
     * @access public
     * @param string $content
     * @return void
     */
    public function write(string $content) : void {
	    $this->responseData['data'] .= $content;
    }

    /**
     * Set HTTP Json response from array
     *
     * @access public
     * @param array $responseData
     * @param Int|Null $status
     * @return void
     */
    public function sendJson(array $responseData, ?int $status = NULL) : void {

        if(is_null($status)) {
            $status = 200;
        }

        # Set the Content-type anyway
        $this->responseData['status'] = $status;
        $this->responseData['headers']['Content-Type'] = 'application/json';
        $this->responseData['data'] = json_encode($responseData);

    }

	/**
	 * Set HTTP Json response as string
	 *
	 * @access public
	 * @param string $responseData
	 * @param int|null $status
	 * @return void
	 */
    public function sendJsonString(string $responseData, ?int $status = NULL) : void {

	    if(is_null($status)) {
		    $status = 200;
	    }

	    # Set the Content-type anyway
	    $this->responseData['status'] = $status;
	    $this->responseData['headers']['Content-Type'] = 'application/json';
	    $this->responseData['data'] = $responseData;

    }

	/**
	 * Send File
	 *
	 * @access public
	 * @param string $filePath
	 * @return void
	 */
	public function sendFile(string $filePath) : void {

        if(!file_exists($filePath)) {
            throw new Exception('File `'.$filePath.'` does not exists!');            
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        # Flush system output buffer
        flush();
        readfile($filePath);
        exit();
		
	}

	/**
	 * Send final Data and Reset.
	 *
	 * @access public
	 * @return void
	 */
	public function sendFinal() : void {

		$status = $this->responseData['status'];

		$phpSapiName  = substr(php_sapi_name(), 0, 3);
		$statusMessage = $this->statusCodes[$status];

		if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
			$statusHeader = "Status: $status $statusMessage";
		} else {
			// Define Server Protocol.
			if(isset($_SERVER['SERVER_PROTOCOL'])) {
				$serverProtocol = $_SERVER['SERVER_PROTOCOL'];
			} else {
				$serverProtocol = 'HTTP/1.0';
			}

			$statusHeader = "$serverProtocol $status $statusMessage";
		}

		# Send header status by code
		header($statusHeader);

		if(!empty($this->responseData['headers'])) {
			foreach ($this->responseData['headers'] as $key => $value) {
				header($key.": " .$value);
			}
		}

		# Will set cache data if enabled and the status is not error.
		if($this->cachingEnabled and $this->responseData['status'] < 400) {
			$this->cacheLib->setArray($this->cacheKey, $this->responseData);
		}

		echo $this->responseData['data'];

		exit();

	}

	/**
	 * With Enabling The caller should provide cache configuration and key.
	 * This method called once from HttpRoute Class, but can be used from any middleware.
	 *
	 * @access public
	 * @param array $config
	 * @param string $key
	 * @return void
	 */
	public function enableCaching(array $config, string $key) : void {

		$this->cacheLib = new \Lib\Cache\Redis($config);
		$this->cacheKey = $key;

		$responseData = $this->cacheLib->getArray($key);

		# If there are data from cache, let's send response.
		if(!empty($responseData)) {

			# Replace the response data
			$this->responseData = $responseData;

			# Add special Header to tell client about cached content
            $this->header('X-Content-Cached', 'Yes');

			# No need to cache already cached data.
			$this->cachingEnabled = False;
			$this->sendFinal();
		}

		# Still not have data from cache, so do caching.
		$this->cachingEnabled = True;
	}

    /**
     * Replace Response data
     *
     * @access public
     * @param string|null $content
     * @param Int|Null $status
     * @param array|Null $headers
     * @return void
     */
    public function replace(?string $content = null, ?int $status = NULL, ?array $headers = []) : void {

        if(!is_null($status)) {
            $this->responseData['status'] = $status;
        }

        if(!is_null($content)) {
            $this->responseData['data'] = $content;
        }

        if(!empty($headers)) {
            $this->responseData['headers'] = [];
            foreach ($headers as $key => $value) {
                $this->responseData['headers'][$key] = [$value];
            }
        }

    }

    /**
     * Reset total Response data
     *
     * @access public
     * @return void
     */
    public function reset() {

        $this->responseData = [
            'status' => 200,
            'headers' => [],
            'data' => ''
        ];

    }
	
}