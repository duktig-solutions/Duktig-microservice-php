<?php
/**
 * Example library class
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Lib\Development\Examples;

/**
 * Class ExampleLibClass
 *
 * @package App\Lib
 */
class ExampleLibClass {

    /**
     * Just example of library method
     *
     * @access public
     * @param int $a
     * @param int $b
     * @return int
     */
    public function exampleMethod(int $a, int $b) : int {
        return $a + $b;
    }

    /**
     * Example of Library static method
     *
     * @static
     * @access public
     * @param int $a
     * @param int $b
     * @return int
     */
    public static function exampleStaticMethod(int $a, int $b) : int {
        return $a * $b;
    }

}
