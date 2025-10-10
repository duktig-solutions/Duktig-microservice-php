<?php
namespace App\Lib;

/**
* Class ExampleExtendedValidClass
*
* @package App\Lib
*/
class Auth {

    public static function CheckPermission($middlewareData, $permission, $response, $auto_response = true) : bool {

        $permit = true;

        if(empty($middlewareData['token_payload']['perms'][$permission])) {
            $permit = false;
        } elseif($middlewareData['token_payload']['perms'][$permission] <= 0) {
            $permit = false;
        }

        if($permit) {
            return true;
        }

        if($auto_response) {

            $response->sendJson([
                'status' => 'error',
                'message' => 'Forbidden ('.$permission.').'
            ], 403);

            # Exit the application
            $response->sendFinal();
            return false;
        }

        return false;

    }

}