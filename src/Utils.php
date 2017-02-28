<?php
/*
 * creator: maigohuang
 * */
namespace EasyLib;

use Psr7Middlewares\Middleware\ClientIp;

class Utils 
{
    public static function guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }

    public static function clientIp()
    {
        $headers = [
            'Forwarded',
            'Forwarded-For',
            'Client-Ip',
            'X-Forwarded',
            'X-Forwarded-For',
            'X-Cluster-Client-Ip',
        ];

        foreach (self::getheaders() as $name => $value) {
            if (in_array($name, $headers)) {
                return $value;
            }
        }
        return '0.0.0.0';
    }

    public static function localIp()
    {
        return file_get_contents('http://ipecho.net/plain');
    }

    public static function getheaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }else {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
    }

    public static function password($f, $t, $chars)
    {
        $len = rand($f, $t);
        $l = strlen($chars);
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $chars[rand()%$l];
        }
        return $str;
    }

    public static function fileContentType($file) {
        $info = pathinfo($file);
        switch (strtolower(trim($info['extension']))) {
            case 'css':
                return 'text/css';
            case 'js':
                return 'text/javascript';
            case 'html':
                return 'text/html';
            case 'htm':
                return 'text/htm';
            case 'png':
                return 'image/png';
            case 'jpeg':
                return 'image/jpeg';
            case 'jpg':
                return 'image/jpg';
            default:
                return 'application/octet-stream';
        }
    }

    public static function xmlEncode($data)
    {
        $result = "";
        foreach ($data as $key=>$value) {
            if (is_numeric($key)) {
                $left = '<Item>';
                $right = '</Item>';
            }else {
                $left = "<$key>";
                $right = "</$key>";
            }

            if (is_array($value)) {
                $result .= $left.self::xmlEncode($value).$right;
            }else {
                $result .= "$left$value$right";
            }
        }
        return $result;
    }

    public static function arrayColumn(array $array, $column)
    {
        $result = [];
        foreach ($array as $item) {
            if (isset($item[$column])) {
                $result[] = $item[$column];
            }
        }
        return $result;
    }

    public static function obj2Array($obj)
    {
        if (is_object($obj) == false) {
            return $obj;
        }

        $_array = get_object_vars($obj);

        $array = [];
        foreach ($_array as $key => $value) {
            $value = (is_object($value)) ? self::obj2Array($value) : $value;
            $array[$key] = $value;
        }
         
        return $array;
    }

    public static function encrypt($string, $operation, $key='')
    { 
        $key=md5($key); 
        $key_length=strlen($key); 
          $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string; 
        $string_length=strlen($string); 
        $rndkey=$box=array(); 
        $result=''; 
        for($i=0;$i<=255;$i++){ 
               $rndkey[$i]=ord($key[$i%$key_length]); 
            $box[$i]=$i; 
        } 
        for($j=$i=0;$i<256;$i++){ 
            $j=($j+$box[$i]+$rndkey[$i])%256; 
            $tmp=$box[$i]; 
            $box[$i]=$box[$j]; 
            $box[$j]=$tmp; 
        } 
        for($a=$j=$i=0;$i<$string_length;$i++){ 
            $a=($a+1)%256; 
            $j=($j+$box[$a])%256; 
            $tmp=$box[$a]; 
            $box[$a]=$box[$j]; 
            $box[$j]=$tmp; 
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256])); 
        } 
        if($operation=='D'){ 
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){ 
                return substr($result,8); 
            }else{ 
                return''; 
            } 
        }else{ 
            return str_replace('=','',base64_encode($result)); 
        } 
    }
}
