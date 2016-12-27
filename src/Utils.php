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
}
