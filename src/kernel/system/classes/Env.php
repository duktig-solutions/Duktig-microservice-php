<?php
/**
 * Environment variables loader from .env file.
 * This will check all variables in .env file and set/load only once that are not set by Docker/System.
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.1.2
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

        # Load from Env file
        foreach ($fileContent as $line) {

            $line = trim($line);

            // Empty line. ignore
            if(empty($line)) {
                continue;
            }

            // Comment line. ignore
            if(str_starts_with($line, '#')) {
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

            if(strtolower(trim($value)) == 'false') {
                $value = false;
            } elseif(strtolower(trim($value)) == 'true') {
                $value = true;
            } else {
                $value = trim($value);
            }

            static::$vars[$item] = $value;

        }

        # Load from Environment variable
        # This will overwrite existing values in $vars list if already exists
        foreach(getenv() as $item => $value) {

            if(strtolower(trim($value)) == 'false') {
                $value = false;
            } elseif(strtolower(trim($value)) == 'true') {
                $value = true;
            } else {
                $value = trim($value);
            }

            static::$vars[$item] = $value;
        }

        return static::$vars;
    }

    /**
     * Parse ini or .env file content to array
     *
     * Example of content:
     *
     * PRODUCT_NAME=Duktig.Microservices
     * autoload=false
     *
     * @static
     * @access public
     * @param string $content
     * @return array
     */
    public static function content2Array(string $content) : array {

        if(empty(trim($content))) {
            return [];
        }

        $result = [];

        $lines = explode("\n", $content);

        foreach ($lines as $line) {

            $line = trim($line);

            // Empty line. ignore
            if(empty($line)) {
                continue;
            }

            // Ignoring the Comment line in .env file content.
            if(str_starts_with($line, '#')) {
                continue;
            }

            // Ignoring the Comment line in ini file content.
            if(str_starts_with($line, ';')) {
                continue;
            }

            $eqPos = strpos($line, '=');

            if($eqPos === false) {
                $item = $line;
            } else {
                $item = substr($line, 0, $eqPos);
            }

            $result[$item] = trim(substr($line, $eqPos+1));

        }

        return $result;

    }

    /**
     * Get item(s) from Loaded values
     *
     * @static
     * @access public
     * @param string|null $item
     * @param null|string $default
     * @return mixed
     */
    public static function get(?string $item = null, null|string $default = ''): mixed
    {

        if(!is_null($item)) {
            return static::$vars[$item] ?? $default;
        }

        return static::$vars;
    }
}