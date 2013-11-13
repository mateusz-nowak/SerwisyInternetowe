<?php

namespace SiBundle;

class Session
{
    public function __construct()
    {
        session_start();
    }
    
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    public function get($key)
    {
        if (!array_key_exists($key, $_SESSION)) {
            return '';
        }
        
        return $_SESSION[$key];
    }
}