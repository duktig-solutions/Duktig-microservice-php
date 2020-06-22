<?php
/**
 * Setup Middleware Class for different purposes
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Middleware;

use \System\Input;
use \System\Output;

/**
 * Class Setup
 *
 * @package App\Middleware
 */
class Setup {

    public function setupGeneral(Input $input, Output $output, array $middlewareData) {
        return $middlewareData;
    }

}
