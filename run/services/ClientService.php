<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/23
 * Time: 15:11
 */

class ClientService
{
    /**
     * @param $serv swoole_server
     * @param $fd
     * @param $data
     */
      public static function receive($serv,$fd,$data)
      {
          LogHandle::client_log($serv,$fd,$data);
          //TODO 业务逻辑

          $r = RedisLong::command('hmSet','array1',['name','jooook','age',15]);
          var_dump($r);
          $r = RedisLong::command('hmSet','array2',['name'=>'jokkkkk','age'=>55]);
          var_dump($r);
          $return = RedisLong::command('hGetAll','array1');
          var_dump($return);
          $return = RedisLong::command('hGetAll','array2');
          var_dump($return);
          $serv->send($fd,json_encode($return));
      }
}