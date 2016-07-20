<?php
/**
 *    时间差工具类
 * 
 **/
namespace EasyLib;
class RunTimeUtil 
{
    private $_startTime = 0;
    private $_stopTime = 0;
    private $_message = '';
    
    public function __construct($message = '')
    {
        $this->message = $message;
        $this->start();
    }

    public function __destruct()
    {
        $this->stop();
        $time = round(($this->_stopTime - $this->_startTime) * 1000, 2);
        if ($this->message != '')
            Log::Info('request', $this->message . ', spend(' . $time.'ms)');
    }

    public function start()
    {
        $this->_startTime= microtime(true);
    }

    public function stop()
    {
        $this->_stopTime = microtime(true);
    }

    //返回时间差  ms
    public function spent()
    {
        $this->stop();
        return round(($this->_stopTime - $this->_startTime) * 1000, 2);
    }

}
