<?php
/**
 * Data Generation class library
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 *
 * @todo Some of values should generate with encryption algorithm to avoid duplication
 */
namespace Lib;

/**
 * Class Generator
 *
 * @package Lib
 */
class Generator {

    /**
     * Create random generated number
     *
     * @static
     * @access public
     * @param  int $length
     * @return int
     */
    public static function createNumber(int $length) : int {

        $min = 1 . str_repeat(0, $length - 1);
        $max = str_repeat(9, $length);
        return mt_rand($min, $max);

    }

    /**
     * Create random generated password
     *
     * @static
     * @access public
     * @param  int $min
     * @param  int $max
     * @param  bool $uppercase
     * @return string
     */
    public static function createPassword(int $min = 6, int $max = 15, bool $uppercase = true) : string {

        return static::createString($min, $max, $uppercase, true, true);

    }

    /**
     * Create random generated username
     *
     * @static
     * @access public
     * @param  int $min
     * @param  int $max
     * @return string
     */
    public static function createUsername(int $min = 6, int $max = 12) : string {

        $str = static::createName($min, $max, false, '-.');
        $is_num = mt_rand(0, 1);

        if($is_num == true and strlen($str) < $max and strpos($str, '.') === false and strpos($str, '-') == false) {
            $str .= mt_rand(11, 999);
        }

        return $str;

    }

    /**
     * Create random generated email address
     *
     * @static
     * @access public
     * @param string $baseName = NULL
     * @param  string $domain = NULL
     * @return string
     */
    public static function createEmail(?string $baseName = NULL, ?string $domain = NULL) : string {

        $min = 3;
        $max = 12;

        // Generate domain if null.
        if(is_null($domain)) {
            $domain = static::createDomain();
        }

        // Generate name if not set
        if(is_null($baseName)) {
            // 24-05-2019 - Listening - "Pink Floyd - The wall - Empty Spaces"
            // Let's travel to Bologna, Italy and get some Pizza, Pasta ... Enzo!
            $baseName = static::createName($min, $max, false, '-_.');
        }

        $baseName = strtolower($baseName);

        return $baseName . '@' . $domain;

    }

    /**
     * Create random generated domain name
     *
     * @static
     * @access public
     * @param  string $country = NULL
     * @return string
     */
    public static function createDomain(?string $country = NULL) : string {

        $min = 3;
        $max = 12;

        // Generate $country if null.
        if(is_null($country)) {
            $countries_arr = ['com', 'org', 'net', 'gov', 'ru', 'am', 'fr', 'io'];
            $country = $countries_arr[array_rand($countries_arr)];
        }

        // Set first char, that cannot be symbol.
        $str = static::createName($min, $max, false, '-.');

        // Set country prefix
        $str .= '.' . $country;

        return $str;

    }

    /**
     * Create random string a-z, A-Z, 0-9, and symbols.
     *
     * @static
     * @access public
     * @param  int $min
     * @param  int $max
     * @param  bool $uppercase = true
     * @param  bool $numbers = true
     * @param  bool $symbols = true
     * @param  string $chars_allowed = NULL
     * @return string
     */
    public static function createString(int $min = 6, int $max = 12, bool $uppercase = true, bool $numbers = true, bool $symbols = true, string $chars_allowed = NULL) : string {

        $chars_alpha = "abcdefghijklmnopqrstuvwxyz";
        $chars_alpha_upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars_numbers = "0123456789";
        $chars_symbols = "~`!@#$%^&*()_|=-.,?;:]}[}<>";

        $chars = $chars_alpha;

        if($uppercase == true) {
            $chars .= $chars_alpha_upper;
        }

        if($numbers == true) {
            $chars .= $chars_numbers;
        }

        if($symbols == true) {
            $chars .= $chars_symbols;
        }

        if(!is_null($chars_allowed)) {
            $chars .= $chars_allowed;
        }

        srand((double)microtime()*1000000);

        $i = 0;

        $str = '';

        $length = mt_rand($min, $max);
        $chars_count = 0;

        while ($i < $length) {
            $num = mt_rand(0, (strlen($chars) - 1));

            $random_char = substr($chars, $num, 1);

            if(strpos($chars_symbols, $random_char) === false) {
                $str .= $random_char;
                $i++;
            } else {
                if($chars_count < 2) {
                    $str .= $random_char;
                    $i++;
                    $chars_count++;
                }
            }

        }

        return $str;

    }

    /**
     * Create random sentence by given text.
     *
     * @static
     * @access public
     * @param  string $str
     * @return string
     */
    public static function createSentence(string $str = NULL) : string {

        if(is_null($str)) {
            $str = "{Hello!|Hi!|Hola!} {maybe|actually|fortunately} this is {your|our|my} {best|nice|well|cool|lucky} {chance|option|case|moment} {to|for} {make|take|get|generate|create|give|catch|bring} a random {sentence|string|expression}.";
        }

        $pattern = "/{([^{}]*)}/";

        while (preg_match_all($pattern, $str, $matches) > 0) {
            for ($i = 0; $i < count($matches[1]); $i++) {

                $options = explode('|', $matches[1][$i]);
                $rand_option = $options[rand(0, count($options)-1)];
                $str = str_replace($matches[0][$i], $rand_option, $str);

            }
        }

        return $str;

    }

    /**
     * Create random date between two dates.
     *
     * @static
     * @access public
     * @param string $start_date
     * @param string $end_date
     * @param string $format
     * @return string
     */
    public static function createDate(string $start_date, string $end_date, string $format = 'Y-m-d H:i:s') : string {

        $d1 = strtotime($start_date);
        $d2 = strtotime($end_date);

        $random = mt_rand($d1, $d2);

        return date($format, $random);

    }

    /**
     * Generate name
     *
     * @static
     * @access public
     * @param int $min
     * @param int $max
     * @param bool $uc_first
     * @param string $chars
     * @return string
     */
    public static function createName(int $min = 3, int $max = 8, bool $uc_first = true, string $chars = '') : string {

        $a = 'AEIOU'; // W
        $b = 'BCDFGHJKLMNPQRSTVXYZ';

        $start = mt_rand(1, 2);

        if($start == 1) {
            $string = $a;
        } else {
            $string = $b;
        }

        $name = '';

        $name_len = mt_rand($min, $max);
        $half = 0;
        $char = '';
        $is_char = mt_rand(0, 1);

        if($name_len >= 5 and $chars != '' and $is_char > 0) {
            $half = ceil($name_len / 2);

            if(strlen($chars) == 1) {
                $char = $chars;
            } else {
                $char = substr(
                    $chars,
                    mt_rand(0, strlen($chars) - 1),
                    1
                );
            }
        }

        for($i = 1; $i <= $name_len; $i++) {

            if($half > 0 and $char != '' and $half == $i) {
                $name .= $char;
            } else {
                $name .= substr(
                    $string,
                    mt_rand(0, strlen($string) - 1),
                    1
                );
            }

            if($string == $a) {
                $string = $b;
            } else {
                $string = $a;
            }
        }

        $name = strtolower($name);

        if($uc_first == true) {
            $name = ucfirst($name);
        }

        return $name;

    }

    /**
     * Create IP Address
     *
     * @static
     * @access public
     * @return string;
     */
    public static function createIP() : string {

        $str = '';
        $str .= mt_rand(100, 192);
        $str .= '.';
        $str .= mt_rand(95, 168);
        $str .= '.';
        $str .= mt_rand(0, 100);
        $str .= '.';
        $str .= mt_rand(0, 100);

        return $str;

    }

}