<?php

namespace SiBundle;

class Dispatcher
{
    protected $events = array();
    
    public function registerEventListener($eventListener)
    {
        $this->events = array_merge($this->events, $eventListener->registerEvents());
    }
    
    public function trigger($eventType, $object = null)
    {
        if (!array_key_exists($eventType, $this->events)) {
            throw new \RuntimeException('Event dispatcher could not trigger: ' . $eventType);
        }
        
        $event = $this->events[$eventType];
        
        return call_user_func(array($event[0], $event[1]), $object);
    }
}