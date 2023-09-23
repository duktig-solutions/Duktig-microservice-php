<?php
/**
 * Data processing class
 *
 * @version 1.0.0
 */
namespace Lib;

/**
 * Class Data
 *
 * @package Lib
 */
class Data {

    /**
     * Return 1 if string is matched in string
     *
     * Example pattern: *Test string*ABC*
     * Example of strings:
     *  'Test string - Som string - ABC - End' return 1
     *  'Test stringABCEnd' return 1
     *  'Test string - Som string - ABC - end | Test string - Som string - ABC - end' return 1
     *  'Test string' return 0
     *
     * @static
     * @access public
     * @param string $wildcardPattern
     * @param string $data
     * @return int
     */
    public static function matchWildcard(string $wildcardPattern, string $data) : int {

        $regex = str_replace(
            array("\*", "\?"), // wildcard chars
            array('.*','.'),   // regexp chars
            preg_quote($wildcardPattern)
        );

        return preg_match('/^'.$regex.'$/is', $data);
    }

}