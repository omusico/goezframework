<?php
require_once dirname(dirname(__FILE__)) . '/GoEz/Sql.php';
require_once dirname(dirname(__FILE__)) . '/GoEz/Sql/Select.php';
require_once dirname(dirname(__FILE__)) . '/GoEz/Sql/Insert.php';
require_once dirname(dirname(__FILE__)) . '/GoEz/Sql/Update.php';
require_once dirname(dirname(__FILE__)) . '/GoEz/Sql/Delete.php';

class GoEz_SqlTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $select = GoEz_Sql::select()
                          ->from('users');
        $this->assertEquals('SELECT * FROM `users`', (string) $select);

        $select = GoEz_Sql::select('name')
                          ->from('users');
        $this->assertEquals('SELECT `name` FROM `users`', (string) $select);

        $select = GoEz_Sql::select(array('name', 'age'))
                          ->from('users');
        $this->assertEquals('SELECT `name`, `age` FROM `users`', (string) $select);

        $select = GoEz_Sql::select()
                          ->distinct()
                          ->from('users');
        $this->assertEquals('SELECT DISTINCT * FROM `users`', (string) $select);

        $select = GoEz_Sql::select(array('name', 'age'))
                          ->distinct()
                          ->from('users');
        $this->assertEquals('SELECT DISTINCT `name`, `age` FROM `users`', (string) $select);

        $select = GoEz_Sql::select()
                          ->from('users')
                          ->where('name = ?', 'John')
                          ->where('age = ?', 20);
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') AND (age = 20)", (string) $select);

        $select = GoEz_Sql::select()
                          ->from('users')
                          ->where('name = ?', 'John')
                          ->where('age = ?', 20)
                          ->group('name');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') AND (age = 20) GROUP BY `name`", (string) $select);

        $select = GoEz_Sql::select()
                          ->from('users')
                          ->where('name = ?', 'John')
                          ->group('name')
                          ->order('age');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` ASC", (string) $select);

        $select = GoEz_Sql::select()
                          ->from('users')
                          ->where('name = ?', 'John')
                          ->group('name')
                          ->order('age', 'DESC');
        $this->assertEquals("SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` DESC", (string) $select);
    }

    public function testInsert()
    {

    }
}