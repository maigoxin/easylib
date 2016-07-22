<?php
namespace EasyLib\Tests;
use EasyLib\BaseDB;

class BaseDBTest extends \PHPUnit_Framework_TestCase
{
    public function testSql()
    {
        $client = new BaseDB([
            'database_type' => 'mysql',
            'database_name' => 'pocadmin',
            'server' => '10.0.3.199',
            'username' => 'root',
            'password' => 'hk1997',
            'charset' => 'utf8',
        ]);

        $ret = $client->select('employee', ['id']);
        var_dump($ret);
    }
}
