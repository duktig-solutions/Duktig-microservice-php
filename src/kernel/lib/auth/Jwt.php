<?php
/**
 * JWT generation and auth library
 *
 * ------------------------------------------
 * Method to process | Supported in this lib
 * ------------------------------------------
 * Sign              | Y
 * Verify            | Y
 *
 * iss check         | Y
 * sub check         | Y
 * aud check         | Y
 * exp check         | Y
 * nbf check         | Y
 * iat check         | Y
 * jti check         | Y
 *
 * ------------------------------------------
 * Algorithm to sign | Supported in this lib
 * ------------------------------------------
 * HS256             | Y
 * HS384             | Y
 * HS512             | Y
 * RS256             | Y
 * RS384             | Y
 * RS512             | Y
 * ES256             | N
 * ES384             | N
 * ES512             | N
 * PS256             | N
 * PS384             | N
 * PS512             | N
 * ------------------------------------------
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */
namespace Lib\Auth;

use Exception;

/**
 * Class Jwt
 *
 * @package Lib\Auth
 */
class Jwt {

    /**
     * Possible encryption types
     *
     * @static
     * @access protected
     * @var array
     */
    protected static array $supportedAlgorithms = [
        'HS256' => ['hash_hmac', 'SHA256'],
        'HS512' => ['hash_hmac', 'SHA512'],
        'HS384' => ['hash_hmac', 'SHA384'],
        'RS256' => ['openssl', 'SHA256'],
        'RS384' => ['openssl', 'SHA384'],
        'RS512' => ['openssl', 'SHA512']
    ];

    /**
     * Encode JWT
     *
     * @static
     * @access public
     * @param array $payload
     * @param string $key
     * @param string $alg
     * @param array|null $addHeaders
     * @return string
     * @throws Exception
     * @return string
     */
    public static function encode(array $payload, string $key, string $alg = 'HS256', ?array $addHeaders = []) : string {

        # Default headers to send
        $sendHeaders = [
            # Token type
            'typ' => 'JWT',

            # Content type
            'cty' => 'JWT',

            # Message authentication code algorithm
            'alg' => $alg
        ];

        # Merge created headers
        if(!empty($addHeaders)) {
            $sendHeaders = array_merge($addHeaders, $sendHeaders);
        }

        # Encode
        $jwtString = static::base64UrlEncode(json_encode($sendHeaders, JSON_NUMERIC_CHECK)) . '.';
        $jwtString .= static::base64UrlEncode(json_encode($payload, JSON_NUMERIC_CHECK));
        $signature = static::sign($jwtString, $key, $alg);

        return $jwtString . '.' . static::base64UrlEncode($signature);

    }

    /**
     * Decode JWT
     *
     * @static
     * @access public
     * @param string $jwt
     * @param array $config
     * @return bool|mixed|string
     * @throws Exception
     * @return array
     */
    public static function decode(string $jwt, array $config) : array {

        # This timestamp will be used for nbf, iat, exp
        $timestamp = time();

        # Default error message in case if Invalid token
        $defaultErrorMessage = 'Invalid token.';

        # Default result
        $result = [
            'status' => 'ok',
            'message' => 'JWT Decoded successfully',
            'payload' => []
        ];

        # Parse jwt parts
        $tokens = explode('.', $jwt);

        # Check jwt parts
        if(count($tokens) != 3) {

            $result['status'] = 'error';
            $result['message'] = $defaultErrorMessage;

            return $result;
        }

        # Decode jwt parts
        $head = json_decode(static::base64UrlDecode($tokens[0]), true);
        $payload = json_decode(static::base64UrlDecode($tokens[1]), true);
        $signature = static::base64UrlDecode($tokens[2]);

        # Check jwt parts after decode
        if(!$head or !$payload or !$signature) {

            $result['status'] = 'error';
            $result['message'] = $defaultErrorMessage;

            return $result;
        }

        # Check the algorithm if empty
        if(empty($head['alg'])) {

            $result['status'] = 'error';
            $result['message'] = $defaultErrorMessage;

            return $result;

        }

        # Check the algorithm if allowed
        if(!isset(static::$supportedAlgorithms[$head['alg']])) {

            $result['status'] = 'error';
            $result['message'] = $defaultErrorMessage;

            return $result;
        }

        # Check the payload data
        #
        # Check: | iss | aud | sub | jti | nbf | iat | exp
        # -------------------------------------------------
        # empty  | Y   | Y   | Y   | Y   | Y   | Y   | Y
        # format | N   | N   | N   | N   | Y   | Y   | Y
        # valid  | Y   | Y   | Y   | Y   | Y   | Y   | Y

        # Items in payload that will be checked if empty
        $validatePayloadEmpty = [
            'iss', 'aud', 'sub', 'jti', 'nbf', 'iat', 'exp'
        ];

        foreach ($validatePayloadEmpty as $plItem) {
            if(!isset($payload[$plItem])) {

                $result['status'] = 'error';
                $result['message'] = $defaultErrorMessage;

                return $result;
            }
        }

        # Validate Payload Unix items timestamp
        $validatePayloadTimeStamps = [
            'nbf', 'iat', 'exp'
        ];

        foreach ($validatePayloadTimeStamps as $plItem) {
            if(!is_numeric($payload[$plItem]) or strtotime(date('d-m-Y H:i:s', $payload[$plItem])) !== (int) $payload[$plItem]) {

                $result['status'] = 'error';
                $result['message'] = $defaultErrorMessage;

                return $result;

            }
        }

        # Check payload items by app configuration
        $validatePayloadConfig = [
            'iss', 'aud', 'sub'
        ];

        foreach ($validatePayloadConfig as $plItem) {

            # Check the iss (Issuer)
            if($payload[$plItem] != $config[$plItem]) {

                $result['status'] = 'error';
                $result['message'] = $defaultErrorMessage;

                return $result;
            }

        }

        # Verify the Signature
        if (!static::verify($tokens[0].'.'.$tokens[1], $signature, $config['secretKey'], $head['alg'])) {

            $result['status'] = 'error';
            $result['message'] = $defaultErrorMessage;

            return $result;
        }

        # Check, if the token started to use early then allowed.
        # nbf = Not before given time
        if ($payload['nbf'] > $timestamp) {

            $result['status'] = 'error';
            $result['message'] = 'The Access-Token will be able to use not before ' . date('Y-m-d H:i:s', $payload['nbf']);

            return $result;
        }

        # Check, if this token created before now.
        if ($payload['iat'] > $timestamp) {

            $result['status'] = 'error';
            $result['message'] = 'The Access-Token issued date is incorrect';

            return $result;
        }

        # Check if the token expired
        if ($timestamp >= $payload['exp']) {

            $result['status'] = 'error';
            $result['message'] = 'Token expired';
                        
            return $result;
        }

        # Successfully decoded.
        $result['payload'] = $payload;

        return $result;
    }

    /**
     * Verify signature
     *
     * @static
     * @access protected
     * @param string $str
     * @param string $signature
     * @param string $key
     * @param string $alg
     * @return bool
     * @throws Exception
     * @return bool
     */
    protected static function verify(string $str, string $signature, string $key, string $alg) : bool
    {

        # Define the method and algorithm to process
        $method = static::$supportedAlgorithms[$alg][0];
        $algorithm = static::$supportedAlgorithms[$alg][1];

        # Group of hash_hmac algorithms
        if ($method == 'hash_hmac') {

            $hash = hash_hmac($algorithm, $str, $key, true);

            if (function_exists('hash_equals')) {
                return hash_equals($signature, $hash);
            }

            $len = min(mb_strlen($signature), mb_strlen($hash));

            $status = 0;

            for ($i = 0; $i < $len; $i++) {
                $status |= (ord($signature[$i]) ^ ord($hash[$i]));
            }

            $status |= (mb_strlen($signature) ^ mb_strlen($hash));

            return ($status === 0);

        # Group of openssl algorithms
        } elseif ($method == 'openssl') {

            $success = openssl_verify($str, $signature, $key, $algorithm);

            // returns 1 on success, 0 on failure, -1 on error.
            if ($success === 1) {
                return true;
            } elseif ($success === 0) {
                return false;
            }

            throw new Exception('OpenSSL error: ' . openssl_error_string());

        } else {
            # The method not defined
            throw new Exception('Unknown method for JWT verification ' . $method);
        }

    }

    /**
     * Sign
     *
     * @static
     * @access protected
     * @param string $str
     * @param string $key
     * @param string $alg
     * @return string
     * @throws Exception
     * @return string
     */
    protected static function sign(string $str, string $key, string $alg) : string {

        # Check if the specified algorithm is allowed
        if(!isset(static::$supportedAlgorithms)) {
            throw new Exception('Unsupported Encryption algorithm ' . $alg);
        }

        # Define method and algorithm
        $method = static::$supportedAlgorithms[$alg][0];
        $algorithm = static::$supportedAlgorithms[$alg][1];

        # Group of hash_hmac algorithms
        if($method == 'hash_hmac') {
            return hash_hmac($algorithm, $str, $key, true);

        # group of openssl algorithms
        } elseif($method == 'openssl') {

            $signature = '';
            $success = openssl_sign($str, $signature, $key, $algorithm);

            if (!$success) {
                throw new Exception("OpenSSL unable to sign data");
            } else {
                return $signature;
            }

        } else {
            # Method not allowed
            throw new Exception('Unknown method ' . $method . ' to sing swt');
        }

    }

    /**
     * URL Base64 encode
     *
     * @static
     * @access protected
     * @param mixed $data
     * @return string
     */
    protected static function base64UrlEncode($data) : string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * URL Base64 decode
     *
     * @static
     * @access protected
     * @param $data
     * @return bool|string
     */
    protected static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}