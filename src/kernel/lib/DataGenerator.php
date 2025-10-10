<?php
/**
 * Data Generation class library
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.4.2
 */
namespace Lib;

use Exception;
use Random\RandomException;

/**
 * Class DataGenerator
 *
 * @package Lib
 */
class DataGenerator {

    /**
     * Create random UUID
     *
     * Examples: b10bb1a0-0afd-11ec-a08f-1b3182194747
     *
     * @static
     * @access public
     * @return string
     * @throws Exception
     */
    public static function createUUID() : string {

        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff)
        );

    }

    /**
     * Create random generated Integer number
     * Due to the new PHP function random_int() this method will return exactly the result of that function.
     *
     * @static
     * @access public
     * @param int $min
     * @param int $max
     * @return int
     * @throws Exception
     */
    public static function createIntegerNumber(int $min = 1, int $max = 10) : int {
        return random_int($min, $max);
    }

    /**
     * Create random generated Float number
     *
     * @static
     * @access public
     * @param  float $min
     * @param  float $max
     * @return float
     */
    public static function createFloatNumber(float $min = 1.00, float $max = 10.00) : float {
        return ($min + lcg_value()*(abs($max - $min)));
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

        if($is_num and strlen($str) < $max and !str_contains($str, '.') and !str_contains($str, '-')) {
            $str .= mt_rand(11, 999);
        }

        return $str;

    }

    /**
     * Create random generated email address
     *
     * @static
     * @access public
     * @param string|null $baseName
     * @param  string|null $domain
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
     * Create random phone number
     *
     * @static
     * @access public
     * @return string
     * @throws Exception
     */
    public static function createPhoneNumber() : string {

        $countryCode = random_int(1, 999);
        $areaCode = random_int(10, 999);
        $lineNumber = random_int(100000, 9999999);

        return '+' . $countryCode . $areaCode . $lineNumber;
    }

    /**
     * Create random generated domain name
     *
     * @static
     * @access public
     * @param  string|null $country
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

        // Set first char, that cannot be a symbol.
        $str = static::createName($min, $max, false, '-.');

        // Set country prefix
        $str .= '.' . $country;

        return $str;

    }

    /**
     * @throws RandomException
     */
    public static function createCouponCode(int $length = 8) : string {

        $chars_alpha_upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars_numbers = "0123456789";

        $chars = $chars_alpha_upper;
        $chars .= $chars_numbers;

        $i = 0;

        $str = '';

        while ($i < $length) {

            $num = random_int(0, (strlen($chars) - 1));
            $random_char = substr($chars, $num, 1);
            $str .= $random_char;
            $i++;

        }

        return $str;

    }

    /**
     * Create random string a-z, A-Z, 0-9, and symbols.
     *
     * @static
     * @access public
     * @param int|null $min = 6
     * @param int|null $max
     * @param bool $uppercase = true
     * @param bool $numbers = true
     * @param bool $symbols = true
     * @param string|null $chars_allowed
     * @return string
     */
    public static function createString(?int $min = 6, ?int $max = 12, ?bool $uppercase = true, ?bool $numbers = true, ?bool $symbols = true, ?string $chars_allowed = NULL) : string {

        $chars_alpha = "abcdefghijklmnopqrstuvwxyz";
        $chars_alpha_upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars_numbers = "0123456789";
        $chars_symbols = "~`!@#$%^&*()_|=-.,?;:]}[}<>";

        $chars = $chars_alpha;

        if($uppercase) {
            $chars .= $chars_alpha_upper;
        }

        if($numbers) {
            $chars .= $chars_numbers;
        }

        if($symbols) {
            $chars .= $chars_symbols;
        }

        if(!is_null($chars_allowed)) {
            $chars .= $chars_allowed;
        }

        //srand(microtime()*1000000);

        $i = 0;

        $str = '';

        $length = mt_rand($min, $max);
        $chars_count = 0;

        while ($i < $length) {
            $num = mt_rand(0, (strlen($chars) - 1));

            $random_char = substr($chars, $num, 1);

            if(!str_contains($chars_symbols, $random_char)) {
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
     * Create a random sentence by given text.
     *
     * @static
     * @access public
     * @param string|null $str
     * @return string
     */
    public static function createSentence(?string $str = NULL) : string {

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

        if($uc_first) {
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

    /**
     * Given a $centre (latitude, longitude) co-ordinates and a
     * distance $radius (miles), returns a random point (latitude, longitude)
     * which is within $radius miles of $centre.
     *
     * @static
     * @access public
     * @param  array $centre Numeric array of floats. First element is
     *                       latitude, second is longitude.
     * @param  float $radius The radius (in miles).
     * @return array         Numeric array of floats (lat/lng). First
     *                       element is latitude, second is longitude.
     */
    public static function createLatLng(array $centre, float $radius) : array {

        // Miles
        $radius_earth = 3959;

        // Pick random distance within $distance;
        $distance = lcg_value() * $radius;

        // Convert degrees to radians.
        $centre_rads = array_map('deg2rad', $centre);

        // First suppose our point is the northern pole.
        // Find a random point $distance miles away
        $lat_rads = (pi() / 2) - $distance / $radius_earth;
        $lng_rads = lcg_value() * 2 * pi();

        // ($lat_rads, $lng_rads) is a point on the circle which is
        // $distance miles from the northern pole. Convert to Cartesian
        $x1 = cos($lat_rads) * sin($lng_rads);
        $y1 = cos($lat_rads) * cos($lng_rads);
        $z1 = sin($lat_rads);

        // Rotate that sphere so that the northern pole is now at $centre.

        // Rotate in x axis by $rot = (pi() / 2) - $centre_rads[0];
        $rot = (pi() / 2) - $centre_rads[0];
        $x2 = $x1;
        $y2 = $y1 * cos($rot) + $z1 * sin($rot);
        $z2 = -$y1 * sin($rot) + $z1 * cos($rot);

        // Rotate in z axis by $rot = $centre_rads[1]
        $rot = $centre_rads[1];
        $x3 = $x2 * cos($rot) + $y2 * sin($rot);
        $y3 = -$x2 * sin($rot) + $y2 * cos($rot);
        $z3 = $z2;

        // Finally convert this point to polar co-ords
        $lng_rads = atan2($x3, $y3);
        $lat_rads = asin($z3);

        return array_map( 'rad2deg', array($lat_rads, $lng_rads));
    }

}