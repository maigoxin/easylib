<?php
namespace EasyLib\Tests;
use EasyLib\Bytes;

class BytesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBytes()
    {
        $raw = 'Helloworld';
        $ret = Bytes::getBytes($raw);
        return $this->assertEquals(sizeof($ret), sizeof($raw));
    }
}
