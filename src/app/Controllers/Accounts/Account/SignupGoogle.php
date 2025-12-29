<?php
/**
 * User Signup Controller by Google
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
*/
namespace App\Controllers\Accounts\Account;

use System\HTTP\Request;
use System\HTTP\Response;

/**
 * Class Signup
 *
 * @package App\Controllers
 */
class SignupGoogle {

    /**
     * Register user account
     *
     * @access public
     * @param Request $request
     * @param Response $response
     * @param array $middlewareData
     * @throws \Exception
     * @return bool
     */
    public function process(Request $request, Response $response, array $middlewareData) : bool {

        $response->sendJson(['under maintenance SignupGoogle'], 503);

        return true;
    }    
}
