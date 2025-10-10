<?php
/**
 * Data processing class
 *
 * @version 1.2.0
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

    /**
     * Format/Cleanup Convert String to username/slug/id string
     *
     * @static
     * @access public
     * @param string $data
     * @return string
     */
    public static function formatIdStr(string $data) : string {

        // Lowercase the string
        $data = strtolower($data);

        // Replace spaces with dashes
        $data = str_replace(' ', '-', $data);

        // Remove unwanted characters (keep letters, numbers, and dashes)
        $data = preg_replace('/[^a-z0-9\-]/', '', $data);

        // Replace multiple consecutive dashes with a single dash
        $data = preg_replace('/-+/', '-', $data);

        // Trim dashes from the start and end of the string
        $data = trim($data, '-');

        return $data;
    }

    public static function titleToURL(string $title) : string {
        // Convert to lowercase
        $slug = strtolower($title);

        // Replace special characters
        $slug = str_replace(
            ['ä', 'ö', 'ü', 'ß', '“', '”', '„', '"', "'", ':'],
            ['ae', 'oe', 'ue', 'ss', '', '', '', '', '', ''],
            $slug
        );

        // Replace spaces and other unwanted characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Trim hyphens from the beginning and end
        return trim($slug, '-');
    }

}