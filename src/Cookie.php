<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;
class Cookie {
    /**
     * set cookie
     * @param string $key
     * @param string $value
     * @param int unsigned $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure, default is false.
     * @param bool $httponly, default is false.
     * @return bool, true is success, false on failure.
     */
    public static function set($key, $value, $expire=0, $path="/", $domain=HOST_NAME, $secure=false, $httponly=false) 
    {
        return @setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
    }
    /**
     * get cookie
     * @param string $key
     * @return string or null
     */
    public static function get($key) 
    {
        return @$_COOKIE[$key];
    }
}

