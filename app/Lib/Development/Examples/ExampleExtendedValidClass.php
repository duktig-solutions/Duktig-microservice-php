<?php
/**
 * Example: How to extend kernel's library
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */
namespace App\Lib\Development\Examples;

/**
 * Class ExampleExtendedValidClass
 *
 * @package App\Lib
 */
class ExampleExtendedValidClass extends \Lib\Valid {

	/**
	 * Example method for extended library
	 *
	 * @access public
	 * @param int $a
	 * @param int $b
	 * @return bool
	 */
	public function numGreaterThen(int $a, int $b) : bool {
		return $a > $b ? true : false;
	}

    /**
     * Example static method for extended library
     *
     * @access public
     * @param int $a
     * @param int $b
     * @return bool
     */
    public static function numLessThan(int $a, int $b) : bool {
        return $a < $b ? true : false;
    }

}
