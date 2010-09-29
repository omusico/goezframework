<?php

class GoEz_DbTest extends PHPUnit_Framework_TestCase
{
    protected $_db = null;

    public function setUp()
    {
        $options = array(
            'dbname' => 'wacdm',
            'host' => '127.0.0.1',
            'username' => 'www',
            'password' => '123456',
        );
        $this->_db = Goez_Db::factory('mysql', $options);
    }

    public function tearDown()
    {
        
    }

    public function testQuery()
    {
    }

    public function testInsert()
    {
    }

    public function testUpdate()
    {
    }

    public function testDelete()
    {
    }
}