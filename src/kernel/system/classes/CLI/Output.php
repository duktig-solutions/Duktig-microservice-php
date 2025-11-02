<?php
/**
 * CLI Class to output to console
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.1
 */
namespace System\CLI;

/**
 * Class Output
 *
 * @package System
 */
class Output {

    public function __construct() {

    }

    /**
     * Output content to console
     *
     * @access public
     * @param mixed $outputString
     * @param bool $newLine = true
     * @return void
     */
    public function stdout(mixed $outputString, bool $newLine = true) : void {

        fwrite(STDOUT, (string) $outputString);

        if($newLine) {
            fwrite(STDOUT, "\n");
        }

    }

    /**
     * Output Error message to console.
     * Notice: The application will exit after this function call.
     *
     * @access public
     * @param string $message
     * @return void
     */
    public function stderr(string $message) : void {

        fwrite(STDERR, $message . "\n");
        exit(1);

    }

    /**
     * Output cli usage info on screen and exit.
     *
     * @access public
     * @return void
     */
    public function usage() : void {

        # Build the message string.
        $message = "Invalid command line arguments.\n";
        $message .= "Usage: php " . DUKTIG_ROOT_PATH . "cli/exec.php {route} --param1 value1 --paramN valueN";

        # Output this message as an error message and exit.
        static::stderr($message);

    }

}