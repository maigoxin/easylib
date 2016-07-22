<?php
/**
 * User: maigoxin 
 */
namespace EasyLib;

class BaseDB
{
    /**
     * @var ContainerInterface
     */
    protected $_medoo;
    
    public function __construct($config)
    {
        $config = array_merge([
            'charset' => 'utf8',
            ], $config);

        $this->_medoo = new \medoo($config);
    }

    final public function __get($name)
    {
        return $this->_medoo->get($name);
    }
    
    final public function __isset($name)
    {
        return $this->_medoo->has($name);
    }

    final public function __call($func, $args)
    {
        if ($this->_medoo && method_exists($this->_medoo, $func)) {
            $r = new RunTimeUtil();
            $ret = call_user_func_array(array($this->_medoo, $func), $args);
            $time = $r->spent();
            $logs = $this->_medoo->log();
            Log::Info('cache', "#method:$func#sql:".end($logs)."#ret:".json_encode($ret)."#runtime:$time");
            return $ret;
        }else {
            Log::Error('cache', "#method$func#args".json_encode($args)."#message:no funcs or client is null");
            return null;
        }
    }
}
