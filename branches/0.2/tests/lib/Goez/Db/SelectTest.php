<?php

class Goez_Db_QueryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $options = array(
            'dbname' => 'goez_test',
            'host' => '127.0.0.1',
            'username' => 'www',
            'password' => '123456',
        );
        $this->_db = Goez_Db::factory('mysql', $options);
        $this->_insertName = 'insert' . time();
    }

    public function tearDown()
    {
        $this->_db->closeConnection();
    }

    public function testSelect()
    {
        $sql = $this->_db->select()
                       ->from('users');
        $this->assertEquals('SELECT * FROM `users`', (string) $sql);

        $sql = $this->_db->select('name')
                          ->from('users');
        $this->assertEquals('SELECT `name` FROM `users`', (string) $sql);

        $sql = $this->_db->select(array('name', 'age'))
                       ->from('users');
        $this->assertEquals('SELECT `name`, `age` FROM `users`', (string) $sql);

        $sql = $this->_db->select(array('name' => 'userName', 'age' => 'userAge'))
                       ->from('users');
        $this->assertEquals('SELECT name AS `userName`, age AS `userAge` FROM `users`', (string) $sql);

        $sql = $this->_db->select()
                       ->distinct()
                       ->from('users');
        $this->assertEquals('SELECT DISTINCT * FROM `users`', (string) $sql);

        $sql = $this->_db->select(array('name', 'age'))
                       ->distinct()
                       ->from('users');
        $this->assertEquals('SELECT DISTINCT `name`, `age` FROM `users`', (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->where('age = ?', 20);
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') AND (age = 20)", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('id IN (?)', array(1, 2, 3));
        $this->assertEquals("SELECT * FROM `users` WHERE (id IN (1, 2, 3))", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name IN (?)', array('John', 'David', 'Bob'));
        $this->assertEquals("SELECT * FROM `users` WHERE (name IN ('John', 'David', 'Bob'))", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->where('age = ?', 20)
                       ->group('name');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') AND (age = 20) GROUP BY `name`", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->orWhere('age = ?', 20)
                       ->group('name');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') OR (age = 20) GROUP BY `name`", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->group('name')
                       ->order('age');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` ASC", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->group('name')
                       ->order('age', 'DESC');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` DESC", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->having('MAX(age) < ?', 10)
                       ->group('name')
                       ->order('age', 'DESC');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` HAVING (MAX(age) < 10) ORDER BY `age` DESC", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->having('MIN(age) < ?', 10)
                       ->having('? < MAX(age)', 10)
                       ->group('name')
                       ->order('age', 'DESC');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` HAVING (MIN(age) < 10) AND (10 < MAX(age)) ORDER BY `age` DESC", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->having('MIN(age) < ?', 10)
                       ->orHaving('? < MAX(age)', 10)
                       ->group('name')
                       ->order('age', 'DESC');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` HAVING (MIN(age) < 10) OR (10 < MAX(age)) ORDER BY `age` DESC", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->group('name')
                       ->order('age', 'DESC')
                       ->limit(3);
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` DESC LIMIT 3", (string) $sql);

        $sql = $this->_db->select()
                       ->from('users')
                       ->where('name = ?', 'John')
                       ->group('name')
                       ->order('age', 'DESC')
                       ->limit(3, 2);
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` DESC LIMIT 2, 3", (string) $sql);
    }
}