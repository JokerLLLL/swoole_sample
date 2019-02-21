<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 17:35
 */
//require_once ('MysqlPool.php');

class DataHandle
{

     //接收数据
     public static function receive($serv,$fd,$data)
    {
            //路由分发
            LogicDeal::rout($serv,$fd,$data);
            //$serv->send($fd,'ERROR DATA SEND');
    }

     //关闭fd进程
     public static function close($serv,$fd)
     {

     }

     //注册task 异步进程
     public static function task($serv,$data)
     {
         go(function() use($serv,$data){
             /* task进程里使用  协程 无法发送 $serv 信息  */
             sleep(1);

             $data = RedisLong::set('NO1','aaaaa');
             $abc = 'abc';
             echo $abc;
         });

         $serv->send($data['fd'],'tttt');

//         $serv->send($data['fd'],'I\'m in task!');
         /* task + 投递sql 就可与简单实现一个连接池
         static $link = null;
         if ($link == null || mysqli_ping($link) == false) {
             $link = mysqli_connect("127.0.0.1", "root", "", "test");
             if (!$link) {
                 $link = null;
                 $serv->finish("ER:" . mysqli_error($link));
                 return;
             }
         }
         $result = $link->query($data);
         if (!$result) {
             $serv->finish("ER:" . mysqli_error($link));
             return;
         }
         $data = $result->fetch_all(MYSQLI_ASSOC);
         $serv->finish("OK:" . serialize($data));
        */


         /*
          * 耗时任务
          *
         sleep(1);
         $serv->send($data['fd'],$data['msg']);
         return true;
         */
         //返回 NULL 将无法触发finish回调
         return NULL;
     }


     // 注册finish 异步进程结束的返回值
     public static function finish($serv,$data)
     {
         echo $data.PHP_EOL;
     }

}