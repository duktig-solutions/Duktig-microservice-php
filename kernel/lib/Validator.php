<?php
/**
 * Validator class
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.5.0
 */
namespace Lib;

use \Lib\Valid;

/**
 * Class Validator
 *
 * @package Lib
 */
class Validator {

    /**
     * Validate a given value with specified rule
     *
     * Rule format: {ruleName}:[value1]:[valueN]
     * Example: password:6:256
     *      Should be password - min 6 - max 256
     *
     *
     * @param $value
     * @param string $rule
     * @param bool $isset
     * @return string
     * @throws \Exception
     */
	public static function validateRule($value, string $rule, ?bool $isset = true) : string {

		$errorMessage = '';
		$ruleName = $rule;
		$ruleOptions = [];
		$errorMessageSuffix = '';

		# Cut the rule name from Rule as first item in string.
		# i.e. cut "range" from "range:10:35:!required"
		if(strpos($rule, ":") !== false) {
			$ruleOptions = explode(':', $rule);
			$ruleName = array_shift($ruleOptions);
		}

		# !required
		# Check, if rule has !required (Not required) flag.
		$required = true;

		if(in_array('!required', $ruleOptions)) {
			$errorMessageSuffix = ' or not set';
			$required = false;
		}

		# !empty
		# Check if rule has !empty (Not set or Not empty) flag.
		$allowEmpty = true;
		if(in_array('!empty', $ruleOptions)) {
			$errorMessageSuffix = ' or not set';
			$allowEmpty = false;
		}

		# Check if value is empty
		$valueIsEmpty = false;
				
		if(is_scalar($value) and (string) $value == '') {
			$valueIsEmpty = true;
		} elseif((is_array($value) or is_object($value)) and empty($value)) {
			$valueIsEmpty = true;
		}

		# Check if value is empty
		if(!$allowEmpty and $valueIsEmpty and $isset) {
			$errorMessage = 'Requires a not empty value or not set';
			return $errorMessage;
		}

		# The value is not set in validation data structure and empty and is not required
		# Assuming this can be an empty value
		if(!$required and $valueIsEmpty and !$isset) {
			return $errorMessage;
		}

		# Value is not empty, let's validate it
		switch($ruleName) {

			case 'required':
				
				if(empty($value)) {
					$errorMessage = 'Required value';
				}

				break;
			
			case 'credit_card':

				if(!Valid::creditCard($value)) {
					$errorMessage = 'Required valid Credit card number' . $errorMessageSuffix;
				}
				
				break;

			case 'password':

				# Check and set default min length
				if(isset($ruleOptions[0]) and is_numeric($ruleOptions[0])) {
					$pswMinLength = (int) $ruleOptions[0];
				} else {
					$pswMinLength = 6;
				}

				# Check and set default max length
				if(isset($ruleOptions[1]) and is_numeric($ruleOptions[1])) {
					$pswMaxLength = (int) $ruleOptions[1];
				} else {
					$pswMaxLength = 128;
				}

				# Password Strength: 1=weak, 2=not weak, 3=acceptable, 4=strong
				if(isset($ruleOptions[2]) and is_numeric($ruleOptions[2])) {
					$pswStrength = (int) $ruleOptions[2];
				} else {
					$pswStrength = 2;
				}

				if(Valid::passwordStrength($value, $pswMinLength, $pswMaxLength) < $pswStrength) {
					$errorMessage = 'Required not weak Password Strength between ' . $pswMinLength . ' - ' . $pswMaxLength . ' chars' . $errorMessageSuffix;
				}
				
				break;	

			case 'email':
				
				if(!Valid::email($value)) {
					$errorMessage = 'Required valid email address' . $errorMessageSuffix;
				}

				break;

			case 'id':
				
				if(!Valid::id($value)) {
					$errorMessage = 'Required valid ID number starting from 1' . $errorMessageSuffix;
				}

				break;	

			case 'digits':
				
				if(!Valid::digits($value)) {
					$errorMessage = 'Required digits' . $errorMessageSuffix;
				}

				break;

			case 'digits_separated':

				# Example: digits_separated:{min-items}:{max-items}:{separator}:{!required}

				$minItems = (int) $ruleOptions[0];
				$maxItems = (int) $ruleOptions[1];
				$separator = $ruleOptions[2];

				if(isset($ruleOptions[3]) and $ruleOptions[3] == '!required') {
					$isRequired = False;
				} else {
					$isRequired = True;
				}

				# In case if this is not required value
				if(empty($value) and !$isRequired) {
					$errorMessage = '';
					break;
				}

				$initialErrorMessage = 'Required digits separated by `'.$separator.'` ';

				if($minItems > 0) {
					$initialErrorMessage .= " min " . $minItems . ' items';
				}

				if($maxItems > 0) {
					$initialErrorMessage .= " max " . $maxItems . ' items';
				}

				$digitsArr = explode($separator, $value);

				if($minItems > 0 and count($digitsArr) < $minItems) {
					$errorMessage = $initialErrorMessage;
					break;
				}

				if($maxItems > 0 and count($digitsArr) > $maxItems) {
					$errorMessage = $initialErrorMessage;
					break;
				}

				foreach ($digitsArr as $digit) {
					if(!Valid::digits($digit)) {
						$errorMessage = $initialErrorMessage;
						break;
					}
				}

				break;

			case 'int_range':

				$initialErrorMessage = 'Required int value';
				$initialRangeStr = '';

				# Set min range if defined
				if(isset($ruleOptions[0]) and $ruleOptions[0] != '') {
					$minRange = (int) $ruleOptions[0];
					$initialRangeStr .= ' min ' . $ruleOptions[0];
				} else {
					$minRange = NULL;
				}

				# Set max range if defined
				if(isset($ruleOptions[1]) and $ruleOptions[1] != '') {
					$maxRange = (int) $ruleOptions[1];
					$initialRangeStr .= ' max ' . $ruleOptions[1];
				} else {
					$maxRange = NULL;
				}

				if(!Valid::intRange($value, $minRange, $maxRange)) {
					$errorMessage = $initialErrorMessage . $initialRangeStr . $errorMessageSuffix;
				}
				
				break;

			case 'float_range':

				$initialErrorMessage = 'Required float value';
				$initialRangeStr = '';

				# Set min if defined
				if(isset($ruleOptions[0]) and $ruleOptions[0] != '') {
					$minRange = (float) $ruleOptions[0];
					$initialRangeStr .= ' min ' . $ruleOptions[0];
				} else {
					$minRange = NULL;
				}

				# Set max if required
				if(isset($ruleOptions[1]) and $ruleOptions[1] != '') {
					$maxRange = (float) $ruleOptions[1];
					$initialRangeStr .= ' max ' . $ruleOptions[1];
				} else {
					$maxRange = NULL;
				}

				if(!Valid::floatRange($value, $minRange, $maxRange)) {
					$errorMessage = $initialErrorMessage . $initialRangeStr . $errorMessageSuffix;
				}
				
				break;

			case 'ip_address':

				if(!Valid::ipAddress($value)) {
					$errorMessage = 'Required valid IP Address' . $errorMessageSuffix;
				}

				break;

			case 'http_host':
				
				if(!Valid::httpHost($value)) {
					$errorMessage = 'Required valid HTTP Host' . $errorMessageSuffix;
				}

				break;	

			case 'alphanumeric':
				
				if(!Valid::alphaNumeric($value)) {
					$errorMessage = 'Required alpha-numeric value' . $errorMessageSuffix;
				}

				break;	

			case 'alpha':
				
				if(!Valid::alpha($value)) {
					$errorMessage = 'Required Alpha value' . $errorMessageSuffix;
				}

				break;
			
			case 'string_length':

				$initialErrorMessage = 'Required string';
				$initialRangeStr = '';

				# Set the min length if defined
				if(isset($ruleOptions[0]) and $ruleOptions[0] != '') {
					$minLength = (int) $ruleOptions[0];
					$initialRangeStr .= ' min ' . $ruleOptions[0];
				} else {
					$minLength = NULL;
				}

				# Set the max length if defined
				if(isset($ruleOptions[1]) and $ruleOptions[1] != '') {
					$maxLength = (int) $ruleOptions[1];
					$initialRangeStr .= ' max ' . $ruleOptions[1];
				} else {
					$maxLength = NULL;
				}

				if(!Valid::stringLength($value, $minLength, $maxLength)) {
					$errorMessage = $initialErrorMessage . $initialRangeStr . $errorMessageSuffix;
				}
				
				break;

			case 'url':

				$initialErrorMessage = 'Required valid URL';

				if(isset($ruleOptions[0]) and is_numeric($ruleOptions[0])) {

					$flags = [
						FILTER_FLAG_PATH_REQUIRED => 'URL must have a path after the domain name (like www.example.com/example1/)',
						FILTER_FLAG_QUERY_REQUIRED => 'URL must have a query string (like "example.php?name=David&age=39")'
					];

					$flag = $ruleOptions[0];

					if(isset($flags[$flag])) {
						$initialErrorMessage = $flags[$flag];
					}

				} else {
					$flag = NULL;
				}

				if(!Valid::url($value, $flag)) {
					$errorMessage = $initialErrorMessage . $errorMessageSuffix;
				}

				break;

			case 'date_iso':
				
				if(!Valid::dateIso($value)) {
					$errorMessage = 'Required valid date with ISO format: Y-m-d' . $errorMessageSuffix;
				}

				break;

            case 'date_time_iso':

                if(!Valid::dateTimeIso($value)) {
                    $errorMessage = 'Required valid date-time with ISO format: Y-m-d H:i:s' . $errorMessageSuffix;
                }

                break;

			case 'date':

				# Set default format as Y-m-d if not defined
				if(empty($ruleOptions[0])) {
					$ruleOptions[0] = 'Y-m-d';
				}

				if(!Valid::date($value, $ruleOptions[0])) {
					$errorMessage = 'Required valid date with format: ' . $ruleOptions[0] . $errorMessageSuffix;
				}

				break;

			case 'date_equal_or_after':

				if(!Valid::dateEqualOrAfter($value, $ruleOptions[0], $ruleOptions[1])) {
					$errorMessage = 'Required valid date equal or after ' .
						date($ruleOptions[1], strtotime($ruleOptions[0])) .
						' with format: ' . $ruleOptions[1] .
						$errorMessageSuffix;
				}

				break;

			case 'date_equal_or_before':

				if(!Valid::dateEqualOrBefore($value, $ruleOptions[0], $ruleOptions[1])) {
					$errorMessage = 'Required valid date equal or before ' .
						date($ruleOptions[1], strtotime($ruleOptions[0])) .
						' with format: ' . $ruleOptions[1] .
						$errorMessageSuffix;
				}

				break;

			case 'date_between':

				if(!Valid::dateBetween($value, $ruleOptions[0], $ruleOptions[1], $ruleOptions[2])) {
					$errorMessage = 'Required valid date between ' .
						date($ruleOptions[2], strtotime($ruleOptions[0])) . ' - ' .
						date($ruleOptions[2], strtotime($ruleOptions[1])) .
						' with format: ' . $ruleOptions[2] .
						$errorMessageSuffix;
				}

				break;

			case 'json_string':
				
				$jsonValidateResult = Valid::jsonString($value);

				if($jsonValidateResult !== true) {
					$errorMessage = $jsonValidateResult . $errorMessageSuffix;
				}

				break;
			
			case 'equal_to':
				
				if($value != $ruleOptions[0]) {
					$errorMessage = 'Required value equal to ' . $ruleOptions[0] . $errorMessageSuffix;
				}

				break;	
			
			case 'not_equal_to':
				
				if($value == $ruleOptions[0]) {
					$errorMessage = 'Required value not equal to ' . $ruleOptions[0] . $errorMessageSuffix; 
				}

				break;	

			case 'one_of':

				# Remove the "!required option if defined
				if(in_array('!required', $ruleOptions)) {
					unset($ruleOptions[array_search('!required', $ruleOptions)]);
				}

				if(!in_array($value, $ruleOptions)) {
					$errorMessage = 'Required value equal to ' . implode(' | ', $ruleOptions) . $errorMessageSuffix;
				}

				break;

			case 'not_one_of':

				# Remove the "!required option if defined
				if(in_array('!required', $ruleOptions)) {
					unset($ruleOptions[array_search('!required', $ruleOptions)]);
				}

				if(in_array($value, $ruleOptions)) {
					$errorMessage = 'Required value not equal to ' . implode(' | ', $ruleOptions) . $errorMessageSuffix;
				}

				break;

            case 'array':
				
            	# Initializing possible Error messages
				$initialErrorMessage = 'Required an Array';

				if(in_array('!required', $ruleOptions)) {
					$errorMessageSuffix = ' or empty';
				} else {
					$errorMessageSuffix = '';
				}

				# Check, the array size for minimum elements
	            if(isset($ruleOptions[0]) and (int) $ruleOptions[0] > 0) {
		            $initialErrorMessage .= ', min elements ' . $ruleOptions[0];
	            }

	            # Check, the array size for maximum elements
	            if(isset($ruleOptions[1]) and (int) $ruleOptions[1] > 0) {
		            $initialErrorMessage .= ', max elements ' . $ruleOptions[1];
	            }

	            # Validation

				# Just check, if it is an array
				if(!is_array($value)) {
					$errorMessage = $initialErrorMessage . $errorMessageSuffix;
					break;
				}

				# Check, the array size for minimum elements
				if(isset($ruleOptions[0]) and (int) $ruleOptions[0] > 0) {
					if(count($value) < $ruleOptions[0]) {
						$errorMessage = $initialErrorMessage . $errorMessageSuffix;
						break;
					}
				}

				# Check, the array size for maximum elements
				if(isset($ruleOptions[1]) and (int) $ruleOptions[1] > 0) {
					if(count($value) > $ruleOptions[1]) {
						$errorMessage = $initialErrorMessage . $errorMessageSuffix;
						break;
					}
				}

	            # Validate ids if required
                if(in_array('ids', $ruleOptions)) {
	                foreach ($value as $id) {
		                if (!Valid::id($id)) {
			                $errorMessage = 'Required an Array containing Id numbers';
			                break;
		                }
	                }
                }

				# Check, if required an array with unique values
	            if(in_array('unique', $ruleOptions) and $value != array_unique($value)) {
		            $errorMessage = 'Required an Array containing unique values';
		            break;
	            }

				break;

			case 'latitude':

				if(!Valid::latitude($value)) {
					$errorMessage = 'Required a valid latitude between -90 and 90';
				}

				break;

			case 'longitude':

				if(!Valid::longitude($value)) {
					$errorMessage = 'Required a valid longitude between -180 and 90';
				}

				break;	

			default:
				throw new \Exception('Unknown Validation rule: ' . $ruleName);
				break;	

		}

		return $errorMessage;

	}

	/**
	 * Validate Json Structure with values
	 *
	 * @static
	 * @access public
	 * @param mixed $jsonStringToValidate
	 * @param array $validationRules
     * @param array|null $extraRules
     * @throws \Exception
	 * @return array
	 */
	public static function validateJson($jsonStringToValidate, array $validationRules = [], ?array $extraRules = []) : array {


		# By Default, Errors array is empty.
		$validationErrors = [];

		# Validate the json string.
		$validateIfCorrectJson = Valid::jsonString($jsonStringToValidate);

		# No valid Json string
		if($validateIfCorrectJson !== true) {
			$validationErrors[] = $validateIfCorrectJson;
			return $validationErrors;
		}

		# There are no any rule, just validated json format
		if(empty($validationRules)) {
			return $validationErrors;
		}

		# Now validate json structure
		# We have to compare Json structure and data types
		$arrayToValidate = json_decode($jsonStringToValidate, true);

		$validationErrors = static::validateDataStructure($arrayToValidate, $validationRules, $extraRules);

		# Continue with validating Json Structure
		return $validationErrors;

	}

    /**
     * Validate Array data structure
     * This method used for any type of validation.
     * When we calling json validation, the json will converted to array and use this method to validate json structure.
     *
     * @static
     * @access public
     * @param array $dataToValidate
     * @param array $validationRules
     * @param array|null $extraRules
     * @return array
     * @throws \Exception
     * @return array
     */
	public static function validateDataStructure(array $dataToValidate, array $validationRules, ?array $extraRules = []) : array {

		# By default Validation Errors is empty
		$validationErrors = [];

		# Validate rules step by step
		foreach($validationRules as $dataName => $rulesPackage) {

			# Case when validation rule is an array containing rules (tree).
			if(is_array($rulesPackage)) {

				if(isset($dataToValidate[$dataName]) and is_array($dataToValidate[$dataName])) {
                    $validationValue = $dataToValidate[$dataName];
                } else {
                    $validationValue = [];
                }

			    $validationResult = self::validateDataStructure($validationValue, $rulesPackage);

				if(!empty($validationResult)) {
					$validationErrors[$dataName] = $validationResult;
				}

				continue;
			}

			# The rule definition is not an array, so let's validate it.
			$rules = explode('|', $rulesPackage);

            foreach($rules as $rule) {

			    if(isset($dataToValidate[$dataName])) {
                    $validationResult = static::validateRule($dataToValidate[$dataName], $rule, true);
                } else {
				    $validationResult = static::validateRule('', $rule, false);
                }

				if(!empty($validationResult)) {
					$validationErrors[$dataName][] = $validationResult;
				}
			}
		}

		# After first stage validation, the $validationErrors will contain errors if eny.

		# Check, if there are extra rules
		# The main purpose of extra rules is that the extra rules not related to any item (i.e. request[first_name] etc...)
		# Extra Rules works for total validation data.
        if(!empty($extraRules)) {

		    $validationErrors = array_merge(
                $validationErrors,
                static::validateExtraRules($dataToValidate, $validationRules, $extraRules)
            );

        }

		return $validationErrors;

	}

    /**
     * Validate Extra Values and rules
     * The main goal of this method is to validate general (total) data which is not related to any specific item. i.e. $_POST
     *     Writing this method and listening: 'Night Drive' - Relaxing Deep House & Progressive House Mix
     *     Summer 2019 before the trip to Bologna ;)
     *     https://www.youtube.com/watch?v=U3SPkP4y-rY
     *
     * @static
     * @access private
     * @param array $dataToValidate
     * @param array $validationRules
     * @param array $extraRules
     * @return array
     * @throws \Exception
     * @return array
     */
	private static function validateExtraRules(array $dataToValidate, array $validationRules, array $extraRules) : array {

	    # By default Validation Errors is empty
        $validationErrors = [];

        # Get trough each rule and validate
        foreach ($extraRules as $item => $rules_str) {

            $rules = explode('|', $rules_str);

            foreach ($rules as $rule) {

				# Cut the rule name from Rule as first item in string.
				# i.e. cut "range" from "range:10:35:!required"
				if(strpos($rule, ":") !== false) {
					$ruleOptions = explode(':', $rule);
					$ruleName = array_shift($ruleOptions);
				} else {
					$ruleOptions = [];
					$ruleName = $rule;
				}

                switch ($ruleName) {


                	# Case when at least one value required.
                    case 'at_least_one_value':

						# If there is no fields required to check, this will check all rules.
						if(empty($ruleOptions)) {
							$ruleOptions = array_keys($validationRules);
						}

                        $validationResult = static::validateAtLeastOneValue($dataToValidate, $ruleOptions);

                        if (!empty($validationResult)) {
                            $validationErrors[$item][] = $validationResult;
                        }

                        break;

                    # Case when required all values to be exact in array as defined in rules
                    case 'exact_keys_values':

                        $validationResult = static::validateExactKeysValues($dataToValidate, $validationRules);

                        if (!empty($validationResult)) {
                            $validationErrors[$item][] = $validationResult;
                        }

                        break;

                    # Case when there can be some values missed (not all required) but no any extra value excepts defined in rules.
                    case 'no_extra_values':

                        $validationResult = static::validateNoExtraValues($dataToValidate, $validationRules);

                        if (!empty($validationResult)) {
                            $validationErrors[$item][] = $validationResult;
                        }

                        break;

                    default:

                        throw new \Exception('Unknown extra validation rule: ' . $rule);

                        break;
                }

            }

        }

        return $validationErrors;
    }

	/**
	 * Validate if at least one value is not empty
	 *
	 * @static
	 * @access public
	 * @param mixed $values
	 * @param array $itemsRequired
	 * @return string
	 */
	public static function validateAtLeastOneValue($values, array $itemsRequired) : string {

	    $errorMessage = 'Required at least one value like: ' . implode(', ', $itemsRequired);

	    # Requires a none empty array
        if(!is_array($values) or empty($values)) {
	        return $errorMessage;
        }

        # Check for each value. If there is any one none empty, then OK!
        foreach ($itemsRequired as $required) {
	        if(!empty($values[$required])) {
	            # There is one none empty value found
	            return '';
            }
        }

        return $errorMessage;

    }

	/**
	 * Validate if the given data to validate has exactly the same keys.
	 * @example:
	 *   requires: email, password
	 *   requested: email - Failed
	 *   requested: email, password, notes - failed
	 *   requested: email, password - OK!
	 *
	 * @static
	 * @access public
	 * @param mixed $values
	 * @param array $itemsRequired
	 * @return string
	 */
    public static function validateExactKeysValues($values, array $itemsRequired) : string {

        $errorMessage = 'Required exact values as: ' . implode(', ', array_keys($itemsRequired));

        # Check, if extra values
        foreach ($values as $item => $value) {
            if(!isset($itemsRequired[$item])) {
                return $errorMessage;
            }
        }

        # Check, if missed values
        foreach ($itemsRequired as $item => $value) {
            if(!isset($values[$item])) {
                return $errorMessage;
            }
        }

        return '';

    }

	/**
	 * Validate if there are any extra value
	 * @example:
	 *   requires: email, password
	 *   requested: email, password, notes - failed
	 *   requested: email - OK!
	 *   requested: email, password - OK!
	 *
	 * @static
	 * @access public
	 * @param mixed $values
	 * @param array $itemsRequired
	 * @return string
	 */
    public static function validateNoExtraValues($values, array $itemsRequired) : string {

        $errorMessage = '';
	    $extraValues = [];

        # Check, if extra values
        foreach ($values as $item => $value) {

        	# There is a value which not defined in required items.
        	if(!isset($itemsRequired[$item])) {
		        $extraValues[] = $item;
            }
        }

        if(!empty($extraValues)) {
	        $errorMessage = 'Required no extra values: ' . implode(', ', $extraValues) . '. Allowed only: ' . implode(', ', array_keys($itemsRequired));
        }

        return $errorMessage;

    }

}
