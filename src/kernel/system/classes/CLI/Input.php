<?php
/**
 * CLI Class to parse / get console data input
 *
 * @author David A. <support@duktig.solutions>
 * @license see License.md
 * @version 1.0.2
 */
namespace System\CLI;

/**
 * Class Input
 *
 * @package System
 */
class Input {

    /**
     * Console input arguments
     * Keep as original
     *
     * @access protected
     * @var array
     */
    protected array $args = [];

    /**
     * The first argument after file name is the route path
     * This can be any string in command line like a command name.
     * i.e. php ./exec.php the/command --param1 value1
     *
     * @access protected
     * @var string
     */
    protected string $route = '';

    /**
     * Console input parsed arguments from:
     * --argument value ...
     *
     * @access protected
     * @var array
     */
    protected array $parsedArgs = [];

    /**
     * Input constructor.
     *
     * @access public
     * @param array $args
     */
    public function __construct(array $args) {

        # Let's keep originals
        $this->args = $args;

        # Remove first item from array. It is file name.
        array_shift($args);

        # Parse the route command
        if(!empty($args)) {
            $this->route = array_shift($args);
        }

        if(!empty($args)) {

            // Start to parse arguments
            // Example:
            //      --argument1 arg_value1 --argument2 arg_value2
            // Notice:
            //      All argument names will be replaced from --argument to argument
            for($i = 0; $i < count($args); $i+=2) {

                $argumentName = substr($args[$i], 2);

                if(isset($args[$i+1])) {
                    $this->parsedArgs[$argumentName] = $args[$i+1];
                } else {
                    $this->parsedArgs[$argumentName] = '';
                }

            }
        }

    }

    /**
     * Return route: the first argument after file name.
     *
     * @access public
     * @return string
     */
    public function route() : string {
        return $this->route;
    }

    /**
     * Get Original arguments from cli
     *
     * @access public
     * @param int|Null $arg
     * @param string|null $default
     * @return array|mixed|string
     */
    public function args(?int $arg = Null, ?string $default = ''): mixed
    {

        if(is_null($arg)) {
            return $this->args;
        }

        if(isset($this->args[$arg])) {
            return $this->args[$arg];
        }

        return $default;

    }

    /**
     * Get Parsed arguments from cli
     *
     * @access public
     * @param string|Null $arg
     * @param string|null $default
     * @return array|mixed|string
     */
    public function parsed(?string $arg = Null, ?string $default = ''): mixed
    {

        if(is_null($arg)) {
            return $this->parsedArgs;
        }

        if(isset($this->parsedArgs[$arg])) {
            return $this->parsedArgs[$arg];
        }

        return $default;

    }

    /**
     * Read data from command line
     *
     * @access public
     * @return string
     */
    public function stdin(): string
    {
        return trim(fgets(STDIN));
    }

}

