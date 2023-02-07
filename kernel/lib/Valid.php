<?php
/**
 * Data validation class library
 *
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.2.0
 */
Namespace Lib;

/**
 * Data validation class
 */
class Valid {

	/**
	 * Check, is string contains only alpha
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function alpha($data) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		if(preg_match("/^([a-z])+$/i", $data)) {
			return true;
		} else {
			return false;
		}

	} // end func alpha

	/**
	 * Check string is alpha-numeric
	 * with underscores and dashes.
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function alphaNumeric($data) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		if(preg_match("/^([-a-z0-9_-])+$/i", $data)) {
			return true;
		} else {
			return false;
		}

	} // end func alphaNumeric

    /**
     * Check is valid credit card.
     * Return card type if number is true.
     *
	 * @static
     * @access public
     * @param mixed
     * @return string|bool false
     */
    public static function creditCard($data) {

        if(!is_scalar($data)) {
            return false;
        }

        $cardType = '';
        $cardRegexes = [
            "/^4\d{12}(\d\d\d){0,1}$/" => 'visa',
            "/^5[12345]\d{14}$/"       => 'mastercard',
            "/^3[47]\d{13}$/"          => 'amex',
            "/^6011\d{12}$/"           => 'discover',
            "/^30[012345]\d{11}$/"     => 'diners',
            "/^3[68]\d{12}$/"          => 'diners',
        ];

        foreach($cardRegexes as $regex => $type) {
            if(preg_match($regex, $data)) {
                $cardType = $type;
                break;
            }
        }

        if(!$cardType) {
            return false;
        }

        # mod 10 checksum algorithm
        $revCode = strrev($data);
        $checksum = 0;

        for($i = 0; $i < strlen($revCode); $i++) {

        	$current_num = intval($revCode[$i]);

        	if($i & 1) {  /* Odd  position */
                $current_num *= 2;
            }

            # Split digits and add
            $checksum += $current_num % 10;

        	if($current_num >  9) {
                $checksum += 1;
            }
        }

        if($checksum % 10 == 0) {
            return $cardType;
        } else {
            return false;
        }

    } // end func creditCard

	/**
	 * Check is valid date in given format(s)
	 *
	 * @static
	 * @access public
	 * @param  mixed $data
	 * @param  string $format = 'Y-m-d'
	 * @return bool
	 */
	public static function date($data, string $format = 'Y-m-d') : bool {

		if(!is_scalar($data)) {
			return false;
		}

		$d = date_create_from_format($format, $data);

		if($d and date_format($d, $format) === $data) {
			return true;
		}

		return false;

	} // end func date

	/**
	 * Check if the given date is between required dates
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @param string $startDate
	 * @param string $endDate
	 * @param string $format
	 * @return bool
	 */
	public static function dateBetween($data, string $startDate, string $endDate, string $format = 'Y-m-d') : bool {

		# For the first, let's check, if this is valid date.
		if(!static::date($data, $format)) {
			return false;
		}

		$intDate = strtotime($data);
		$intStartDate = strtotime($startDate);
		$intEndDate = strtotime($endDate);

		# Check if date is equal or before
		if($intDate >= $intStartDate and $intDate <= $intEndDate) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the given date is equal or after required date
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @param string $checkDate
	 * @param string $format
	 * @return bool
	 */
	public static function dateEqualOrAfter($data, string $checkDate, string $format = 'Y-m-d') : bool {

		# For the first, let's check, if this is valid date.
		if(!static::date($data, $format)) {
			return false;
		}

		$intDate = strtotime($data);
		$intCheckDate = strtotime($checkDate);

		# Check if date is equal or after
		if($intDate >= $intCheckDate) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the given date is equal or before required date
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @param string $checkDate
	 * @param string $format
	 * @return bool
	 */
	public static function dateEqualOrBefore($data, string $checkDate, string $format = 'Y-m-d') : bool {

		# For the first, let's check, if this is valid date.
		if(!static::date($data, $format)) {
			return false;
		}

		$intDate = strtotime($data);
		$intCheckDate = strtotime($checkDate);

		# Check if date is equal or before
		if($intDate <= $intCheckDate) {
			return true;
		}

		return false;
	}

	/**
	 * Check is valid date in ISO format
	 * Format: YYYY-MM-DD (eg 1997-07-16)
	 * ISO: 8601
     *
	 * @static
	 * @access public
	 * @param  mixed $data
	 * @return bool
	 */
	public static function dateIso($data) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		$time = strtotime($data);

		if(date('Y-m-d', $time) == $data) {
			return true;
		} else {
			return false;
		}

	} // End func dateIso

    /**
     * Check is valid date and time in ISO format
     * Format: YYYY-MM-DD H:i:s (eg 1997-07-16 13:10:36)
     * ISO: 8601
     *
     * @static
     * @access public
     * @param  mixed $dataTime
     * @return bool
     */
    public static function dateTimeIso($dataTime) : bool {

        if(!is_scalar($dataTime)) {
            return false;
        }

        $time = strtotime($dataTime);

        if(date('Y-m-d H:i:s', $time) == $dataTime) {
            return true;
        } else {
            return false;
        }

    } // End func dateTimeIso

	/**
	 * Check string if contains only digits
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function digits($data) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		if(!preg_match("/^[0-9]+$/", $data)) {
			return false;
		}

		return true;

	} // End func digits

	/**
	 * Check is valid e-mail address
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function email($data) : bool {

		if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
			return true;
		}

		return false;

	} // End func email

	/**
	 * Check if valid float value in given range
	 *
	 * @static
	 * @access public
	 * @param mixed $value
	 * @param float|null $min
	 * @param float|null $max
	 * @return bool
	 */
	public static function floatRange($value, ?float $min = NULL, ?float $max = NULL) : bool {

		if(!is_numeric($value)) {
			return false;
		}

		# Convert to float or false
		$number = ($value === (float) $value) ? (float) $value : false;

		# The value is not float
		if($number === false) {
			return false;
		}

		# Less than minimal value
		if(!is_null($min) and $number < $min) {
			return false;
		}

		# Greater than maximal value
		if(!is_null($max) and $number > $max) {
			return false;
		}

		return true;
	}

	/**
	 * Check is valid hostname.
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function httpHost($data) : bool {

		return filter_var($data, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);

	} // end func httpHost

	/**
	 * Check is valid.
	 * By default, will check from 1.
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function id($data) : bool {

		# Convert to int or false
		$number = ($data == (int) $data) ? (int) $data : false;

		# If not int
		if(!$number) {
			return false;
		}

		# If not valid id number
		if($number < 1) {
			return false;
		}

		return self::digits($data);

	} // end func id

	/**
	 * Check if valid int value in given range
	 *
	 * @static
	 * @access public
	 * @param mixed $value
	 * @param int|null $min
	 * @param int|null $max
	 * @return boolean
	 */
	public static function intRange($value, ?int $min = NULL, ?int $max = NULL) : bool {

		if(!is_numeric($value)) {
			return false;
		}

		# Convert to int or false
		$number = ($value == (int) $value) ? (int) $value : false;

		# The value is not int
		if($number === false) {
			return false;
		}

		# Less than minimal value
		if(!is_null($min) and $number < $min) {
			return false;
		}

		# Greater than maximal value
		if(!is_null($max) and $number > $max) {
			return false;
		}

		return true;
	}

	/**
	 * Check is correct IP Address
	 *
	 * @static
	 * @access public
	 * @param mixed $data
	 * @return bool
	 */
	public static function ipAddress($data) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		$val_0_to_255 = "(25[012345]|2[01234]\d|[01]?\d\d?)";
		$pattern = "#^($val_0_to_255\.$val_0_to_255\.$val_0_to_255\.$val_0_to_255)$#";

		if(preg_match($pattern, $data, $matches)) {
			return true;
		} else {
			return false;
		}

	} // End func ipAddress

	/**
	 * Validate Json content
	 * If some Error in json parsing process, an Error message string will be returned.
	 * The bool true will return in successfully json parse process.
	 *
	 * @static
	 * @access public
	 * @param mixed $string
	 * @return mixed
	 */
	public static function jsonString($string) {

		$initialErrorMessage = 'Required valid json string';

		# This should be not empty string
		if(!is_string($string) or empty($string)) {
			return $initialErrorMessage;
		}

		# decode the JSON data and get error code
		$result = json_decode($string);
		$jsonErrorCode = json_last_error();
		$jsonErrorMessage = json_last_error_msg();

		# Check the result of decoding
		# If it is decoded to actual object and there is no json error, then OK!
		if(is_object($result) and $jsonErrorCode == JSON_ERROR_NONE) {
			return true;
		}

		# Return Json Error
		return 'Json Error ' . $jsonErrorCode . ': '. $jsonErrorMessage . '. ' . $initialErrorMessage;

	}

	/**
	 * Check password strength
	 *
	 * Return values
	 * -1 = Incorrect value
	 * 0 = not match
	 * 1  = weak
	 * 2  = not weak
	 * 3  = acceptable
	 * 4  = strong
	 *
	 * @static
	 * @access public
	 * @param  mixed $data
	 * @param  int $min
	 * @param  int $max
	 * @return int
	 */
	public static function passwordStrength($data, int $min = 6, int $max = 128) : int {

		if(!is_scalar($data)) {
			return -1;
		}

		if(strlen($data) < $min or strlen($data) > $max) {
			return 0;
		}

		$strength = 0;
		$patterns = ['#[a-z]#','#[A-Z]#','#[0-9]#','/[¬!"£$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/'];

		foreach($patterns as $pattern) {
			if(preg_match($pattern, $data)) {
				$strength++;
			}
		}

		return $strength;

	} // end func passwordStrength

	/**
	 * Check string with required length.
	 *
	 * @static
	 * @access public
	 * @param  mixed $data
	 * @param  int|null $min
	 * @param  int|null $max
	 * @return bool
	 */
	public static function stringLength($data, ?int $min = NULL, ?int $max = NULL) : bool {

		if(!is_scalar($data)) {
			return false;
		}

		if(!is_null($min) and mb_strlen($data) < $min) {
			return false;
		}

		if(!is_null($max) and mb_strlen($data) > $max) {
			return false;
		}

		return true;

	} // End func stringLength

    /**
     * Check is valid url
     *
	 * @static
     * @access public
     * @param mixed $data
     * @param int $flag
     *     FILTER_FLAG_PATH_REQUIRED - URL must have a path after the domain name (like www.example.com/example1/)
     *     FILTER_FLAG_QUERY_REQUIRED - URL must have a query string (like "example.php?name=David&age=39")
     * @return bool
     */
    public static function url($data, ?int $flag = NULL) : bool {

	    if (filter_var($data, FILTER_VALIDATE_URL, $flag)) {
		    return true;
	    } else {
		    return false;
	    }

    } // End func url

	/**
	 * Check is valid Latitude: -90 and 90
	 *
	 * @static
     * @access public
	 * @param mixed $lat
	 * @return bool
	 */
	public static function latitude($lat) : bool {
		return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', $lat);
	}

	/**
	 * Check is valid Longitude: -180 and 90
	 *
	 * @static
     * @access public
	 * @param mixed $lng
	 * @return bool
	 */
	public static function longitude($lng) : bool {
		return preg_match('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $lng);
	}

}
