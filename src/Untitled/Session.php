<?php

namespace Untitled;

// This class is just a wrapper for PHP's inbuilt session methods.
class Session
{
	public static function start($config)
	{
        session_name($config['name']);
        session_start();
        session_regenerate_id();
    } 

    // Write things to the session.
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Get things from the session.
    public static function get($key)
    {
    	if (isset($_SESSION[$key])) {
	        return $_SESSION[$key];
	    }
    }

    // Destroy the session.
    public static function destroy()
    {
        session_destroy();
    }

    // Dump out the session.
    public static function dump()
    {
        var_dump($_SESSION);
    }
}
