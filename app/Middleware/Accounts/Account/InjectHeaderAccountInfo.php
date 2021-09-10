<?php
/**
 * Middleware class to Get User account information from headers and inject into middleware data
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware\Accounts\Account;

use System\HTTP\Request;
use System\HTTP\Response;
use System\Config;

/**
 * Class InjectHeaderAccountInfo
 *
 * @package App\Middleware
 */
class InjectHeaderAccountInfo {

    public function injectFromHeaders(Request $request, Response $response, array $middlewareData) {

        $data = json_decode($request->headers('X-Account-Info'), true);

        if(!$data) {
            
            $response->sendJson([
                'status' => 'error',
                'message' => 'Invalid header data'
            ], 400);

            $response->sendFinal();

            return false;
        }

        $middlewareData['account'] = $data;
        
        return $middlewareData;

    }
}
