<?php

class GoEz_DbTest extends PHPUnit_Framework_TestCase
{
    protected $_db = null;

    public function setUp()
    {
        $options = array(
            'dbname' => 'goez_test',
            'host' => '127.0.0.1',
            'username' => 'www',
            'password' => '123456',
        );
        $this->_db = Goez_Db::factory('mysql', $options);
    }

    public function tearDown()
    {
        
    }

    public function testFactory()
    {
        $options = array();
        $db = Goez_Db::factory($options);
        $this->assertEquals('Goez_Db', get_class($db));
        $this->assert

        $options = array(
            'type' => 'mysql',
            'params' => array(
                'dbname' => 'goez_test',
                'host' => '127.0.0.1',
                'username' => 'www',
                'password' => '123456',
        ));
        $db = Goez_Db::factory($options);
        $this->assertEquals('Goez_Db', get_class($db));
    }

    public function testQuery()
    {
//        print_r($this->_db->fetchAll('SELECT * FROM users'));
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