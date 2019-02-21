<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/19
 * Time: 14:52
 */

class Analysis
{

    /** string转Hex   ||    真正的Hex 转成 16进制字符串 || "\x7E\x7E" 转变 "7e7e"
     * @param $str
     * @return bool|string
     */
    public function SetToHexString($str)
    {
        if(!$str)return false;
        $tmp="";
        for($i=0;$i<strlen($str);$i++)
        {
            $ord=ord($str[$i]);
            $tmp.=$this->SingleDecToHex(($ord-$ord%16)/16);
            $tmp.=$this->SingleDecToHex($ord%16);
        }
        return $tmp;
    }

    public function SingleDecToHex($dec)
    {
        $tmp="";
        $dec=$dec%16;
        if($dec<10)
            return $tmp.$dec;
        $arr=array("a","b","c","d","e","f");
        return $tmp.$arr[$dec-10];
    }

    /** Hex转string  ||  16进制转成 真正的Hex  ||  将 "7e7e" 转换成 "\x7e\x7e"
     * @param $str
     * @return bool|string
     */
    public function UnsetFromHexString($str)
    {
        if(!$str)return false;
        $tmp="";
        for($i=0;$i<strlen($str);$i+=2)
        {
            $tmp.=chr($this->SingleHexToDec(substr($str,$i,1))*16+$this->SingleHexToDec(substr($str,$i+1,1)));
        }
        return $tmp;
    }

    public function SingleHexToDec($hex)
    {
        $v=ord($hex);
        if(47<$v&$v<58)
            return $v-48;
        if(96<$v&$v<103)
            return $v-87;

    }

    /** crc16 加密解密(4位)
     * @param $string
     * @return string
     */
    public function crc16($string)
    {
        $crc = 0xFFFF;
        for ($x = 0; $x < strlen ($string); $x++) {
            $crc = $crc ^ ord($string[$x]);
            for ($y = 0; $y < 8; $y++) {
                if (($crc & 0x0001) > 0) {
                    $crc = (($crc >> 1) ^ 0xA001);
                } else { $crc = $crc >> 1; }
            }
        }
        return substr('0000'.dechex($crc), -4);
    }


    /** 异或校验
     * @param $string
     * @return bool|string
     */
    public function crcor($string) {
        $crc = 0x7E;
        for ($x = 0; $x < strlen ($string); $x++) {
            $crc = $crc ^ ord($string[$x]);
        }
        return substr('00'.dechex($crc),-2);
    }


}