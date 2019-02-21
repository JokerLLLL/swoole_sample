<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/23
 * Time: 15:11
 */

class DeviceService
{
    /**
     * @param $serv swoole_server
     * @param $fd
     * @param $data
     */
      public static function receive($serv,$fd,$data)
      {
          //终端上传 \16进制类型
          $array = LogicDeal::init($data);
          if($array === false) return;
          //var_dump($array);
          LogHandle::device_log($serv,$fd,$array['row'],json_encode($array));
          // TODO 业务逻辑
          echo 'start:'.date('Y-m-d H:i:s').PHP_EOL;


          // 等待10s 或 获取到数据
          Helper::timeAfter(10,function() {
              return RedisLong::command('get','end');
          });

          $r = RedisLong::command('get','end');
          var_dump($r);

          echo 'ending:'.date('Y-m-d H:i:s').PHP_EOL;


          $serv->send($fd,'getData');
      }
}