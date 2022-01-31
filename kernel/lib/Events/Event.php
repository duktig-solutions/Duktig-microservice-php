<?php
/**
 * Intermediate Data center Event Class
 * This class uses as an Event data structure template to Publish an events
 * 
 * @author David A. <software@duktig.dev>
 * @license see License.md
 * @version 1.0.0
 */  

namespace Lib\Events;

# Use of Redis configuration
use System\Config;

/**
 * Class Event
 */
class Event {

    /**
     * Main Data container for Event
     * 
     * @access private
     * @var array
     */
    private $data = [];

    /**
     * Event name
     * 
     * @access private
     * @var string $eventName
     */
    private $eventName = 'Unknown';

    /**
     * Event channel 
     * 
     * @access private
     * @var string $channel
     */
    private $channel;

    /**
     * The service name defined in configuration for given microservice
     * 
     * @access private
     * @var string
     */
    private $service;

    /**
     * Class Constructor
     * This will assign service name defined in application configuration
     * 
     * @param string|null $eventName
     * @param string|null $channel
     */
    public function __construct(?string $eventName, ?string $channel = 'main') {

        if(!is_null($eventName)) {
            $this->eventName = $eventName;
        }
        
        $this->channel = $channel;
        $this->service = Config::get()['Microservice'];

    }
    
    /**
     * Set a value to event
     * 
     * @access public
     * @param string $item
     * @param mixed $value
     * @return void
     */
    public function set(string $item, $value) {
        $this->data[$item] = $value;
    }

    /**
     * Set value as an Array
     * This will merge current data value with this new
     * 
     * Listening TRANCE 2021 VOL 3 [FULL ALBUM]
     * https://www.youtube.com/watch?v=v4IbgT4_aps
     * Location: Dubai, UAE - 27 June 2021
     * 
     * @access public
     * @param array $data
     * @return void
     */
    public function setArr(array $data) {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Return Event Data as a Json String including the system name.
     * 
     * @access public
     * @return string
     */
    public function getDataJson() : string {
        
        return json_encode([
            'service' => $this->service,
            'time' => date('Y-m-d H:i:s'),
            'event' => $this->eventName,
            'data' => $this->data
        ]);

    }

    /**
     * Publish the Event
     * 
     * @access public
     * @return void
     */
    public function publish() {
        \Lib\Events\IntermediateEvents::publish($this->channel, $this->getDataJson());
    }

}
