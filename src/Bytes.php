<?php
/**
 * byte数组与字符串转化类
 * Created on 2011-7-15
 */

namespace EasyLib;
class Bytes {

    /**
     * 转换一个String字符串为byte数组
     * @param $str 需要转换的字符串
     * @param $bytes 目标byte数组
     */
    public static function getBytes($str) {
        $len = strlen($str);
        $bytes = array();
        for($i=0;$i<$len;$i++) {
            if(ord($str[$i]) >= 128){
                $byte = ord($str[$i]) - 256;
            }else{
                $byte = ord($str[$i]);
            }
            $bytes[] =  $byte ;
        }
        return $bytes;
    }
   
    /**
     * 将字节数组转化为String类型的数据
     * @param $bytes 字节数组
     * @param $str 目标字符串
     * @return 一个String类型的数据
     */
    public static function toStr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }

        return $str;
    }
   
    /**
     * 转换一个int为byte数组
     * @param $byt 目标byte数组
     * @param $val 需要转换的字符串
     */
    public static function integerToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        $byt[2] = ($val >> 16 & 0xff);
        $byt[3] = ($val >> 24 & 0xff);
        return $byt;
    }
   
    /**
     * 从字节数组中指定的位置读取一个Integer类型的数据
     * @param $bytes 字节数组
     * @param $position 指定的开始位置
     * @return 一个Integer类型的数据
     */
    public static function bytesToInteger($bytes, $position) {
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;
        return $val;
    }

    /**
     * 转换一个shor字符串为byte数组
     * @param $byt 目标byte数组
     * @param $val 需要转换的字符串
     */
    public static function shortToBytes($val) {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        return $byt;
    }
   
    /**
     * 大端字节序转换一个16位数字为字节数组
     * @param type $val
     * @return 目标Bytes数组
     */
    public static function shortToBytesBigEnd($val){
        $byt = array();
        $byt[0] = ($val >> 8 & 0xff);
        $byt[1] = ($val & 0xff);
        return $byt;
    }
    
    /**
     * 从字节数组中指定的位置读取一个Short类型的数据。
     * @param $bytes 字节数组
     * @param $position 指定的开始位置
     * @return 一个Short类型的数据
     */
    public static function bytesToShort($bytes, $position) {
        $val = $bytes[$position + 1] & 0xFF;
        $val = $val << 8;
        $val |= $bytes[$position] & 0xFF;
        return $val;
    }
    
    /**
     * 以大端顺序将两个字节数据转为一个short值
     * @param array $bytes   字节数组
     * @param int $position    指定的开始位置
     * @return short 一个Short类型的数据
     */
    public static function bytesToShortBigEnd($bytes, $position) {
        $val = $bytes[$position] & 0xFF;
        $val = $val << 8;
        $val |= $bytes[$position+1] & 0xFF;
        return $val;
    }
    
    /**
     * 将一个字符串追加到指定的字节数组中
     * @param array $byteArray
     * @param String $str
     * @return array 追加数据后的字节数组
     */
    public static function appendString($byteArray, $str){
        $len = strlen($str);
        for($i=0;$i<$len;$i++) {
            if(ord($str[$i]) >= 128){
                $byte = ord($str[$i]) - 256;
            }else{
                $byte = ord($str[$i]);
            }
            $byteArray[] =  $byte ;
        }
        return $byteArray;
    }
    
    public static function hexStringToBytes($hex) {
        $len = (strlen($hex) / 2);
        $hex = strtoupper($hex);

        $result = array();

        $achar = self::getBytes($hex);
        for ($i = 0; $i < $len; $i++) {
            $pos = i * 2;
            $result[i] = ($achar[$pos] << 4 | $achar[$pos + 1]);
        }
        return $result;
    }
    
    
    private static $hexStr = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
    public static function bytesToHexstring($bytesArray){
        if(!is_array($bytesArray)){
            return null;
        }
        $len = sizeof($bytesArray);
        $str = "";
        $i = 0;
        for($i=0;$i<$len;$i++){
            $str = $str.self::$hexStr[($bytesArray[$i]&0xF0)>>4];
            $str = $str.self::$hexStr[($bytesArray[$i]&0x0F)>>0];
        }
        return $str;
    }
    
}

