<?php
/**
 * Environment variables loader from .env file.
 * This will check all variables in .env file and set/load only once that are not set by Docker/System.
 *
 * @author David A. <framework@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

/**
 * Class Env
 */
Class Env {

    /**
     * Loaded Environment variables from .env file
     *
     * @static
     * @access private
     * @var array
     */
    private static array $vars = [];

    /**
     * Load Environment variables from .env file
     *
     * @static
     * @access public
     * @param string $filename
     * @return array
     */
    public static function load(string $filename = '.env') : array {

        $fileContent = file($filename);

        foreach ($fileContent as $line) {

            $line = trim($line);

            // Empty line. ignore
            if(empty($line)) {
                continue;
            }

            // Comment line. ignore
            if(substr($line, 0, 1) == '#') {
                continue;
            }

            $eqPos = strpos($line, '=');

            if($eqPos === false) {
                $item = $line;
            } else {
                $item = substr($line, 0, $eqPos);
            }

            if(!empty(getenv($item))) {
                $value = getenv($item);
            } elseif(!empty(getenv($item, true))) {
                $value = getenv($item, true);
            } else {
                $value = substr($line, $eqPos+1);
                putenv("$item=$value");
            }

            static::$vars[$item] = $value;

        }

        return static::$vars;
    }

    /**
     * Get item(s) from Loaded values
     *
     * @static
     * @access public
     * @param string|null $item
     * @param mixed $default
     * @return mixed
     */
    public static function get(?string $item = null, $default = '') {

        if(!is_null($item)) {
            return static::$vars[$item] ?? $default;
        }

        return static::$vars;
    }
}