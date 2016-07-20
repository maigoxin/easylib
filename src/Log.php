<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;
class Log {
    static private $pid = null;
    public static function configure($conf) {
        \Logger::configure($conf);
    }

    //trace debug info warn error fatal
    //Log::Debug('core', 'maigo is a good boy');
    public static function __callStatic($func, $arguments) {
        $bt = debug_backtrace();
        $file = $bt[0]['file'];
        $line = $bt[0]['line'];
        $func = strtolower($func);
        if (defined('REQUEST_ID')) {
            $requestId = REQUEST_ID;
        }else {
            $requestId = '-';
        }
        if (sizeof($arguments) == 1) {
          \Logger::getLogger('default')->$func("[$file][$line][$requestId] ".$arguments[0]);
        }
        else {
          \Logger::getLogger($arguments[0])->$func("[$file][$line][$requestId] ".$arguments[1]);
        }
    }
}
