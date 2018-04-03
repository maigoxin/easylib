<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;
class RedisClient 
{
    private $redis_;

    public function __construct($config) 
    {
        $this->redis_ = new \Redis();
        $this->redis_->connect($config['host'], $config['port'], $config['timeout']);
        
        if (isset($config['auth'])) {
            $this->redis_->auth($config['auth']);
        }
        
        if (isset($config['db'])) {
            $this->redis_->select($config['db']);
        }
    }

    private function __clone() {
    }

    public function __call($func, $args) {
        if ($this->redis_ && method_exists($this->redis_, $func)) {
            $r = new RunTimeUtil();
            $ret = call_user_func_array(array($this->redis_, $func), $args);
            $time = $r->spent();

            Log::Info('cache', "#method:$func#args:".json_encode($args)."#ret:".json_encode($ret)."#runtime:$time");
            return $ret;
        }else {
            Log::Error('cache', "#method$func#args".json_encode($args)."#message:no funcs or client is null");
            return null;
        }
    }
}
