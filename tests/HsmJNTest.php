<?php
namespace EasyLib\Tests;
use EasyLib\HsmJN;

class HsmJNTest extends \PHPUnit_Framework_TestCase
{
    private static $client = null;

    public static function setUpBeforeClass()
    {
        self::$client = HsmJN::getInstance('192.168.140.133', '8018');
    }

    public function testHsmEncrypt()
    {
        $ret = self::$client->blocksEncrypt(0, 0x000, 1, null, 0, null, 0, null, ['helloworld']);
        $this->assertEquals(sizeof($ret), 1);
        return $ret;
    }

    /**
     * @depends testHsmEncrypt
     */
    public function testHsmDecrypt($data)
    {
        $ret = self::$client->blocksDecrypt(0, 0x000, 1, null, 0, null, 0, null, $data);
        $this->assertEquals($ret[0], 'helloworld');
        return $ret;
    }
}
