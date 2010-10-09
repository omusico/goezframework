<?php

class Goez_Db_SelectTest extends PHPUnit_Framework_TestCase
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
        $this->_db->closeConnection();
    }

}