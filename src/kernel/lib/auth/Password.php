<?php
/**
* Auth Password class to Encrypt and Verify a password
*
* @author David A. <support@duktig.solutions>
* @license see License.md
* @version 1.0.0
*/
namespace Lib\Auth;

/**
 * Class Password
 *
 * @package Lib\Auth
 */
class Password {

    /**
     * Encrypt password
     *
     * @static
     * @access public
     * @param string $password
     * @return string
     */
    public static function encrypt(string $password) : string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify hashed password
     *
     * @static
     * @access public
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verify(string $password, string $hash) : bool {
        return password_verify($password, $hash);
    }

}