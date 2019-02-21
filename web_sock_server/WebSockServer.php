<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 9:43
 */
class WebSockServer{
    /* @var swoole_websocket_server */
    public $server;

    public function __construct()
    {
        $server = new swoole_websocket_server('0.0.0.0',50002);
        $server->set([
            'daemonize'=>false,
            'worker_num'=>2,
            'task_worker_num' =>2,
        ]);
        $server->on('open',[$this,'onOpen']);
        $server->on('message',[$this,'onMessage']);
        $server->on('close',[$this,'onClose']);
        $server->on('task',[$this,'onTask']);
        $server->on('finish',[$this,'onFinish']);
        $server->start();
    }

    public function onOpen($server,$request)
    {
        // var_dump($request); new  swoole_http_request();
        echo '链接生成fd:'.$request->fd.PHP_EOL;
    }

    public function onMessage($server,$frame)
    {

        $timer = swoole_timer_tick(1000, function($timer) use ($server, $frame){
            $time = date("H:i:s");
            $message = "水滴穿过人类星际飞船，犹如石头穿过水中一样，一连串的飞船都报废了... {$time}";
            $server->push($frame->fd, $message);
        });

        swoole_timer_after(5000, function() use($server, $frame, $timer) {
            $server->push($frame->fd, "攻击结束，除了蓝色空间和青铜时代两艘飞船逃逸之外，其他都挂了，地球文明快完蛋了" . date('H:i:s'));
            swoole_timer_clear($timer);
        });

        $server->push($frame->fd, "水滴已经被人类捕获，开始启动攻击指令，他们还以为是我们送来的表示和平的礼物，甚至称其为圣母的眼泪，哈哈哈...");




        // var_dump($frame); new swoole_websocket_frame();
        echo '收到数据fd:'.$frame->fd.'数据：'.$frame->data;
        $server->push($frame->fd,'我收到数据了');
        $server->task([
            'fd'=>$frame->fd,
            'data'=>$frame->data
        ]);

    }

    public function onTask($server,$task_id,$word_id,$data)
    {
            $time1 = time();

            // 不能在里面调用 $server 否则会断开链接 改after 不影响现在的任务 只是投递任务
            swoole_timer_after(1000,function() use($time1,$server,$data) {
                echo 'time_after:'.(time() - $time1).PHP_EOL;
                $server->push($data['fd'],'time_after');
                return true;
            });

            $time2 = time();
            echo "time:".($time2 - $time1).PHP_EOL;
            sleep(3);
            echo 'time2:'.(time()-$time2).PHP_EOL;
            return $data;

    }

    public function onFinish($server,$task_id,$data)
    {
        if($server->exist($data['fd'])) {
            $server->push($data['fd'],'任务执行成功');
        }
    }

    public function onClose($server,$fd)
    {
         echo '链接断开fd:'.$fd.PHP_EOL;
         $server->push($fd,'链接断开');
    }

}

$ser = new WebSockServer();