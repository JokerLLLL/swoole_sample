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
     public static  $db;


     //通过 yield 和 调度器实现 异步代码同步化
     public static function receive_bp($serv,$fd,$data)
     {

         echo $data.PHP_EOL;
         $name  = function (){};

         if($data == 'name') {
             $name = function () use($fd,$serv){
                 $name1 = yield MysqlLong::query('select titlle,sleep(1) from `book` where id =1');
                 $name2 = yield MysqlLong::query('select titlle,sleep(1) from `book` where id=2');
                 $name3 = yield MysqlLong::query('select titlle,sleep(1) from `book` where id=3');
                 $serv->send($fd,json_encode([$name1,$name2,$name3]));
             };
         }
         if($data == 'content') {
             $name = function () use($fd,$serv){
                 $name1 = yield MysqlLong::query('select content,sleep(1) from `book` where id =4');
                 $name2 = yield MysqlLong::query('select content,sleep(1) from `book` where id=5');
                 $name3 = yield MysqlLong::query('select content,sleep(1) from `book` where id=6');
                 $serv->send($fd,json_encode([$name1,$name2,$name3]));
             };
         }
         $func = $name();
         if($func instanceof Generator) {
             echo 'gen_start:'.PHP_EOL;
             $task = new Task($func);
             Run::$scheduler->newTask($task);
         }

         var_dump(MysqlLong::$result_data);
         /*
         $result = test_a();
         if($result instanceof Generator) {
             $task = new Task($result);
             $scheduler->newTask($task);
         }
        */
        /* @var $serv swoole_server
         $sql = 'select * from `book`';
         MysqlLong::query_callback($sql,function ($res) use($serv,$fd){
             if(empty($res)) {
                 return false;
             }
             //继续嵌套逻辑
             $serv->send($fd,json_encode($res));
         });

        $serv->send($fd,'I already get data:'.$data);
        $serv->task(['mission'=>'eat','fd'=>$fd]);
         */
     }

     public static function registerDb()
     {
         $swoole_mysql = new swoole_mysql();
         $swoole_mysql->connect([
             'host' => '127.0.0.1',
             'port' => 3306,
             'user' => 'root',
             'password' => '',
             'database' => 'test',
             'charset' => 'utf8mb4',
             'timeout' => 2,
         ], function ($db, $res) {
             echo 'db is ok'.PHP_EOL;
             if ($res == false) {
                 throw new Exception("数据库连接错误::" . $db->connect_errno . $db->connect_error);
             }
             self::$db = $db;
         });
     }

    //接收数据
    public static function receive($serv,$fd,$data)
    {
        /*
        go(function()use($serv,$fd,$data) {
            echo 'getData:'.$data.PHP_EOL;
            $swoole_mysql = new Swoole\Coroutine\MySQL();
            $swoole_mysql->connect([
                'host' => '127.0.0.1',
                'user' => 'root',
                'password' => '',
                'database' => 'test'
            ]);
            $res = $swoole_mysql->query('select titlle,sleep(1) from `book` where id =1');
            var_dump($res);
            $res = $swoole_mysql->query('select titlle,sleep(1) from `book` where id =2');
            var_dump($res);
            $res = $swoole_mysql->query('select titlle,sleep(1) from `book` where id =3');
            var_dump($res);
            $res = $swoole_mysql->query('select titlle,sleep(1) from `book` where id =4');
//        echo $res.PHP_EOL;
          $serv->send($fd,json_encode($res));
        });
        */
        go(function() use($serv,$fd,$data) {
            echo 'getData:' . $data . PHP_EOL;

            echo 'test'.PHP_EOL;
            /* @var $swoole_mysql swoole_mysql */
//            sleep(3);
            $swoole_mysql = self::$db;
            $swoole_mysql->query('select titlle,sleep(1) from `book` where id =1',
                function ($db, $result) use ($serv, $fd) {
                    var_dump($result);
                });

            $swoole_mysql->query('select titlle,sleep(2) from `book` where id =2',
                function ($db, $result) use ($serv, $fd) {
                    var_dump($result);
                });

            $swoole_mysql->query('select titlle from `book` where id =4',
                function ($db, $result) use ($serv, $fd) {
                    $serv->send($fd, json_encode($result));
                });
            echo 'end!!!' . PHP_EOL;
            $serv->send($fd, 'end!!!');
        });
    }

     //关闭fd进程
     public static function close($serv,$fd)
     {

     }

     //注册task 异步进程
     public static function task($serv,$data)
     {
         $serv->send($data['fd'],'I\'m in task!');
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
         return null;
     }


     // 注册finish 异步进程结束的返回值
     public static function finish($serv,$data)
     {
         echo $data.PHP_EOL;
     }

}