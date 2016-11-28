<?php
namespace EasyLib;
class Ks3 extends Singleton 
{
    private $client = null;

    public function __construct($ak, $sk, $endPoint)
    {
        //是否使用VHOST
        define("KS3_API_VHOST", false);
        //是否开启日志(写入日志文件)
        define("KS3_API_LOG", false);
        //是否显示日志(直接输出日志)
        define("KS3_API_DISPLAY_LOG", false);
        //是否使用HTTPS
        define("KS3_API_USE_HTTPS", false);
        //是否开启curl debug模式
        define("KS3_API_DEBUG_MODE", false);
        require_once(dirname(__FILE__).'/ks3-php-sdk/Ks3Client.class.php');

        $this->client = new \Ks3Client($ak, $sk, $endPoint);
    }

    public function uploadFile($bucket, $path, $filePath, $acl = 'public-read', $contentType = 'application/octet-stream')
    {
        if (!is_file($filePath)) {
            return false;
        }

        $args = [
            'Bucket' => $bucket,
            'Key' => $path,
            'Content' => [
                'content' => $filePath,
                'seek_position' => 0
            ],
            'ACL' => $acl,
            'ObjectMeta' => [
                'Content-Type' => $contentType
            ],
        ];

        $res = $this->client->putObjectByFile($args);
        return isset($res['ETag']) ? $res['ETag'] : false;
    }

    public function uploadContent($bucket, $path, $content, $acl = 'public-read', $contentType = 'application/octet-stream')
    {
        $args = [
            'Bucket' => $bucket,
            'Key' => $path,
            'Content' => $content,
            'ACL' => $acl,
            'ObjectMeta' => [
                'Content-Type' => $contentType
            ],
        ];

        $res = $this->client->putObjectByContent($args);
        return isset($res['ETag']) ? $res['ETag'] : false;
    }

    public function uploadDir($bucket, $path, $dirPath, $acl = 'public-read', $contentType = 'application/octet-stream')
    {
        if (!is_dir($dirPath)) {
            return false;
        }

        $box = [];
        $this->goDir($dirPath, $box);
        $prefixLen = strlen($dirPath);
        foreach ($box as $file) {
            $this->uploadFile($bucket, $path . substr($file, $prefixLen), $file, $acl);
        }
        return true;
    }

    public function getTempObjectUrl($bucket, $path, $expire = 60)
    {
        $args = [
            'Bucket' => $bucket,
            'Key' => $path,
            'Options' => [
                'Expires' => $expire,
            ],
        ];

        return $this->client->generatePresignedUrl($args);
    }

    public function getObject($bucket, $path)
    {
        $args = [
            'Bucket' => $bucket, 
            'Key' => $path
        ];

        return $this->client->getObject($args);
    }

    public function exists($bucket, $path)
    {
        $args = [
            'Bucket' => $bucket, 
            'Key' => $path
        ];

        return $this->client->objectExists($args);
    }

    public function goDir($dirPath, array &$box)
    {
        if (is_dir($dirPath)) {
            $dh = opendir($dirPath);
            while (($file = readdir($dh)) != false) {
                if (is_dir($dirPath . '/' . $file) && $file != "." && $file != "..") {
                    $this->goDir($dirPath . '/' . $file, $box);
                }else {
                    if ($file != "." && $file != "..") {
                        $box[] = $dirPath . '/' . $file;
                    }
                }
            }
            closedir($dh);
        }
    }
}
