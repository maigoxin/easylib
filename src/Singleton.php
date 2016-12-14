<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;

class Singleton
{
    private static $instances = array();
    protected function __construct() {}
    protected function __clone() {}
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize');
    }

    public static function getInstance()
    {
        $cls = get_called_class();
        $extend = implode('^_^', func_get_args());
        if (!isset(self::$instances[$cls.$extend]))
        {
            if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
                self::$instances[$cls.$extend] = new static(...func_get_args());
            }else {
                $rcStatic=new \ReflectionClass($cls);
                self::$instances[$cls.$extend] = $rcStatic->newInstanceArgs(func_get_args());
            }
        }
        return self::$instances[$cls.$extend];
    }
}
