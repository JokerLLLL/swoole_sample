<?php
/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/18
 * Time: 0:27
 */

require_once 'Scheduler.php';
require_once 'Task.php';

$scheduler = new Scheduler();
$scheduler->ticker();

Task::$result = [];
function test_a(){

    echo 'start'.PHP_EOL;
    sleep(3);
    Task::$result[0] = '第一个执行结果';
    echo Task::$result[0].PHP_EOL;
    $aaa = (yield 'aaaa@@@@@@'.PHP_EOL);


    echo 'getData:'.$aaa;
    sleep(3);
    Task::$result[1] = '第二个执行结果';
    $bbb = (yield 'bbbb@@@@@'.PHP_EOL);

    echo 'getData2:'.$bbb;
    sleep(3);
    Task::$result[2] = '第三执行结果';
    $ccc = (yield 'cccc@@@@@@@'.PHP_EOL);
    echo 'end!'.PHP_EOL;
};

$result = test_a();
//var_dump($result() instanceof Iterator);


/* @var $gen Generator */
//$gen = $result();
//var_dump($gen->valid());
//echo $gen->key().' - '.$gen->current()."\n";
//$gen->send($gen->current());
//var_dump($gen->valid());
//echo $gen->key().' - '.$gen->current()."\n";
//$gen->send($gen->current());
//var_dump($gen->valid());
//echo $gen->key().' - '.$gen->current()."\n";
//$gen->send($gen->current());
//var_dump($gen->valid());


//var_dump(Task::$result);

if($result instanceof Generator) {
    $task = new Task($result);
    $scheduler->newTask($task);
}

