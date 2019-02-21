<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/19
 * Time: 15:04
 */

class LogicDeal
{
     //数据分发
     static $routs = [
        'client' => ['ClientService','receive'],
        'device' => ['DeviceService','receive'],
        ];

    /** 初始化数据包
     * @param $data
     * @return array|bool
     */
      public static function init($data)
      {
            $ana = new Analysis();
            $res_data = $ana->SetToHexString($data);
            return self::unlockData($res_data);
      }

    /** 不同的解释权用不同的方法
     * @param $string
     * @return array|bool
     */
      public static function unlockData($string)
      {
            $result = [];
            $result['row'] = $string;
            $result['head'] = substr($string,0,2);
            $result['type'] = substr($string,2,4);
            $result['sn'] = substr($string,4,8);
            return $result;

            $check = (new Analysis())->crc16('7e7e7e7e7e');
            if(empty($result) || $result['check'] !== $check) {
                return false;
            }
            return $result;

      }

      /*
       * 判断是否是json
       */
      public static function isJson($data)
      {
          try{
              json_decode($data);
          }catch (\Exception $e){
              return false;
          }
          return (json_last_error() == JSON_ERROR_NONE);
      }


    /** 路由分发
     * @param $serv
     * @param $fd
     * @param $data
     * @return mixed
     */
      public static function rout($serv,$fd,$data)
      {
           if(self::isJson($data)) {
               $rount = self::$routs['client'];
           }else{
               $rount = self::$routs['device'];
           }
           return call_user_func_array($rount,[$serv,$fd,$data]);
      }

}