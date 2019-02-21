<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/19
 * Time: 0:14
 */
class RedisLong
{

    /*
     * redis 命令使用使用
     */
    public static function command($func,$key)
    {
        /** @var  $redis Redis */
        $redis  = RedisPool::getInstance()->pop();
        $params = func_get_args();
        array_shift($params);

        if(!method_exists($redis,$func)){
            RedisPool::getInstance()->push($redis);
            throw new Exception('Class Redis method: '.$func.' not exist!');
        }
        $result = call_user_func_array([$redis,$func],$params);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /*
     * get
     */
     public static function get($key)
     {
         /* @var $redis Swoole\Coroutine\Redis */
         $redis  = RedisPool::getInstance()->pop();
         $result = $redis->get($key);
         RedisPool::getInstance()->push($redis);
         return $result;
     }

     /*
      * set
      */
     public static function set($key,$value,$time_out = 0)
     {
         /* @var $redis Swoole\Coroutine\Redis */
         $redis  = RedisPool::getInstance()->pop();
         $result = $redis->set($key,$value,$time_out);
         RedisPool::getInstance()->push($redis);
         return $result;
     }

     /*
      * delete
      */
    public static function delete($key)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result = $redis->delete($key);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /*
     * hset
     */
    public static function hSet($key,$hashKey,$value)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result =  $redis->hSet($key,$hashKey,$value);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /*
     * hGet
     */
    public static function hGet($key,$hashKey)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result =  $redis->hGet($key,$hashKey);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /**
     * @param $key
     * @param $hashKeys
     * @return array
     */
    public static function hMGet($key, $hashKeys)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result =  $redis->hMGet($key,$hashKeys);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /*
     * hgetall
     */
    public static function hGetAll($key)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result =  $redis->hGetAll($key);
        RedisPool::getInstance()->push($redis);
        return $result;
    }

    /*
     * hdel
     */
    public static function hDel($key,$hashKey1)
    {
        /* @var $redis Swoole\Coroutine\Redis */
        $redis  = RedisPool::getInstance()->pop();
        $result =  $redis->hDel($key,$hashKey1);
        RedisPool::getInstance()->push($redis);
        return $result;
    }



}