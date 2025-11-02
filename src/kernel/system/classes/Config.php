<?php
/**
 * Application/System Configuration class
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.0
 */
namespace System;

/**
 * Class Config
 *
 * @package Kernel\System\Classes
 */
class Config {

    /**
     * Loaded Configurations
     *
     * @static
     * @access private
     * @var array
     */
    private static array $loaded = [];

    /**
     * Get Configurations
     * Will load and return Application configuration file by default: /app/config/app.php
     *
     * @static
     * @access public
     * @param string $filename
     * @return array
     */
    public static function get(string $filename = 'app') : array {

        // Load the file if not loaded
        if(!isset(self::$loaded[$filename])) {
            self::load($filename);
        }

        return self::$loaded[$filename];

    }

    /**
     * Load configuration file
     *
     * @static
     * @access protected
     * @param string $filename
     * @return void
     */
    protected static function load(string $filename) : void {
        self::$loaded[$filename] = require_once DUKTIG_APP_PATH . 'config/'.str_replace('..', '', $filename).'.php';
    }

}