<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;
use App\Config\Redis as ConfigRedis;

class RedisClient 
{
    private static $redis_client_;
    private $redis_;

    private function __construct($redis_group) 
    {
        $config = ConfigRedis::get(ENV)[$redis_group];

        if ($config == null) return ;

        $this->redis_ = new \Redis();
        $this->redis_->connect($config['host'], $config['port'], $config['timeout']);

        if (isset($config['db'])) {
            $this->redis_->select($config['db']);
        }
    }

    public static function getInstance($redis_group) 
    {
        if (!isset(self::$redis_client_[$redis_group])) {
            self::$redis_client_[$redis_group] = new RedisClient($redis_group);
        }
        return self::$redis_client_[$redis_group];
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
