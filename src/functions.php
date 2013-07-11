<?php

// helper function to get someone's IP
function getIp(){
    if (getenv("HTTP_CLIENT_IP")) {
        $ip = getenv("HTTP_CLIENT_IP");
    }

    else if (getenv("HTTP_X_FORWARDED_FOR")) {
        $ip = getenv("HTTP_X_FORWARDED_FOR");

        // http://en.wikipedia.org/wiki/X-Forwarded-For#Format
        // We need the first IP in the list.
        $ip = substr($ip, 0, strpos($ip, ','));
    }

    else if (getenv("REMOTE_ADDR")) {
        $ip = getenv("REMOTE_ADDR");
    }

    else $ip = NULL;

    // and return it
    return $ip;
}
