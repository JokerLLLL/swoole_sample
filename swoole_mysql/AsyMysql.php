<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 15:54
 */
class AsyMysql {
    public $db;
    public $config = [];
    public function __construct()
    {
        $this->db = new swoole_mysql();
        $this->config = [
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'root',
            'password' => '',
            'database' => 'test',
            'charset' => 'utf8mb4',
            'timeout' => 1  //默认超时1s
        ];
    }

    public function execute($id, $content) {
        try {
            $this->db->connect($this->config, function ($db, $result) use ($id, $content) {
                if ($result === false) {
                    // 连接失败
                    /* @var $db Swoole\Mysql */
                    var_dump($db->connect_error);
                }
                $sql = "update book set `content` = '{$content}' where id = {$id}";
                $db->query($sql, function ($db, $result) {
                    if ($result === false) {
                        var_dump($db->error);
                    } else if ($result === true) {
                        var_dump($db->affected_rows);
                    } else {
                        var_dump($result);
                    }
                });
                echo '执行成功' . PHP_EOL;
                $db->close();
            });
        } catch (\Swoole\Mysql\Exception $e) {
            echo $e->getMessage();
        }

        return true;
    }
}

$obj = new AsyMysql();
$flag = $obj->execute(1,'大刘写的科幻小说');
echo "开始执行" . PHP_EOL;

