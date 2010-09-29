<?php
/**
 * Goez
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * SQL 語法產生器
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db
{
    /**
     * @var PDO
     */
    protected $_connection = null;

    /**
     * @var string
     */
    protected $_pdoType = 'mysql';

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * 識別符
     *
     * @var string
     */
    protected $_identifier = '`';

    /**
     * @var int
     */
    protected $_fetchMode = PDO::FETCH_ASSOC;

    /**
     * 工廠方法
     *
     * 用法：
     *
     * <code>
     * $db = Goez_Db::factory('mysql', array(
     *     'username' => 'webuser',
     *     'password' => 'xxxxxxxx',
     *     'dbname' => 'test',
     *     'driver_options' => array(...),
     * ));
     * </code>
     *
     * 或是：
     * 
     * <code>
     * $db = Goez_Db::factory(array(
     *     'driver' => 'mysql',
     *     'params' => array(
     *         'username' => 'webuser',
     *         'password' => 'xxxxxxxx',
     *         'dbname' => 'test',
     *         'driver_options' => array(),
     * )));
     * </code>
     *
     * @param mixed $pdoType
     * @param array $config
     * @return Goez_Db
     */
    public static function factory($pdoType, $config = array())
    {
        // 處理第一個參數為陣列的狀況
        if (is_array($pdoType)) {
            if (isset($pdoType['params'])) {
                $config = $pdoType['params'];
            }
            if (isset($pdoType['driver'])) {
                $pdoType = (string) $pdoType['driver'];
            } else {
                $pdoType = null;
            }
        }

        if (!is_array($config)) {
            throw new Exception('Parameters must be in an array.');
        }

        if (!is_string($pdoType) || empty($pdoType)) {
            throw new Exception('Driver name must be specified in a string.');
        }

        $db = new self($config);

        /*
         * Verify that the object created is a descendent of the abstract driver type.
         */
        if (!$db instanceof Goez_Db) {
            throw new Exception("Driver '$pdoType' does not exist.");
        }

        return $db;
    }

    /**
     * 建構式
     *
     * @param array $options
     */
    public function __construct($pdoType, array $config)
    {
        $this->_pdoType = $pdoType;
        $this->_config = $config;
    }

    /**
     * 連接資料庫
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        $dsn = $this->_dsn();

        try {
            $this->_connection = new PDO(
                $dsn,
                $this->_config['username'],
                $this->_config['password'],
                $this->_config['driver_options']
            );
            $this->_connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * 建立 DSN
     *
     * @return string
     */
    protected function _dsn()
    {
        $dsn = $this->_config;

        // 移除 DSN 不需要的參數
        unset($dsn['username']);
        unset($dsn['password']);
        unset($dsn['options']);
        unset($dsn['charset']);
        unset($dsn['persistent']);
        unset($dsn['driver_options']);

        // 把剩下的設定建立為 DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return $this->_pdoType . ':' . implode(';', $dsn);
    }

    /**
     * 取得最後的自動編號
     *
     * @param string $tableName
     * @return int
     */
    public function lastInsertId($tableName = null)
    {
        $this->_connect();
        return $this->_connection->lastInsertId();
    }

    /**
     * 建立 SQL 查詢
     *
     * @param string $sql
     * @param array $bind
     * @return PDOStatement
     */
    public function query($sql, $bind = array())
    {
        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if (!is_int($name) && !preg_match('/^:/', $name)) {
                    $newName = ":$name";
                    unset($bind[$name]);
                    $bind[$newName] = $value;
                }
            }
        }

        try {
            $this->_connect();

            if (!is_array($bind)) {
                $bind = array($bind);
            }

            $stmt = $this->prepare($sql);
            $stmt->execute($bind);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            return $stmt;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * 將使用者輸入的值適當地加上引號
     *
     * 如果輸入的是陣列，那麼裡面的項目會在加上引號後，回傳為一個用逗號分隔的字串。
     *
     * @param mixed $value
     * @return mixed
     */
    public function quote($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val);
            }
            return implode(', ', $value);
        }
        return $this->_quote($value);
    }

    /**
     * 轉換條件式
     *
     * 利用一個問號做為佔位符，然後將使用者傳入的值做轉換，例如：
     *
     * <code>
     * $text = "WHERE date < ?";
     * $date = "2005-01-02";
     * $safe = $sql->quoteInto($text, $date);
     * // $safe = "WHERE date < '2005-01-02'"
     * </code>
     *
     * @param string  $text  帶有佔位符的字串
     * @param mixed   $value 需要適當用引號包含的值
     * @param integer $count (選用) 佔位符要取代的次數
     * @return string 取代後的字串
     */
    public function quoteInto($text, $value, $count = null)
    {
        if ($count === null) {
            return str_replace('?', $this->quote($value), $text);
        } else {
            while ($count > 0) {
                if (strpos($text, '?') != false) {
                    $text = substr_replace($text, $this->quote($value), strpos($text, '?'), 1);
                }
                --$count;
            }
            return $text;
        }
    }

    /**
     * 包裝識別符
     *
     * @param mixed $value
     * @return mixed
     */
    public function quoteIdentifier($value)
    {
        if (is_string($value)) {
            return $this->_identifier . $value . $this->_identifier;
        } elseif (is_array($value)) {
            $result = array();
            foreach ($value as $item) {
            	$result[] = $this->quoteIdentifier($item);
            }
            return $result;
        }
    }

    /**
     * 轉換條件式
     *
     * @param mixed $where
     * @return string
     */
    protected function _whereExpr($where)
    {
        if (empty($where)) {
            return $where;
        }
        if (!is_array($where)) {
            $where = array($where);
        }
        foreach ($where as $cond => &$term) {
            if (!is_int($cond)) {
                $term = $this->quoteInto($cond, $term);
            }
            $term = '(' . $term . ')';
        }

        $where = implode(' AND ', $where);
        return $where;
    }

    /**
     * 將值做適當地引號包含
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _quote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        $this->_connect();
        return $this->_connection->quote($value);
    }

    /**
     * @param int $mode
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case PDO::FETCH_LAZY:
            case PDO::FETCH_ASSOC:
            case PDO::FETCH_NUM:
            case PDO::FETCH_BOTH:
            case PDO::FETCH_NAMED:
            case PDO::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                throw new PDOException("Invalid fetch mode '$mode' specified");
                break;
        }
    }

    /**
     * @return Goez_Db
     */
    public function beginTransaction()
    {
        $this->_connect();
        $this->_connection->beginTransaction();
        return $this;
    }

    /**
     * @return Goez_Db
     */
    public function commit()
    {
        $this->_connect();
        $this->_connection->commit();
        return $this;
    }

    /**
     * @return Goez_Db
     */
    public function rollBack()
    {
        $this->_connect();
        $this->_connection->rollBack();
        return $this;
    }

//    /**
//     * Select 語法
//     *
//     * 利用 Fluent interface 來組合 Select 語法，目的是減少開發者手誤與避免 SQL Inject 。
//     *
//     * 用法：
//     *
//     * <code>
//     * $sql = Goez_Sql::select()
//     *                ->from('users');
//     * // $sql = "SELECT * FROM `users`"
//     *
//     * $sql = Goez_Sql::select(array('name', 'age'))
//     *                ->from('users');
//     * // $sql = "SELECT `name`, `age` FROM `users`"
//     *
//     * $sql = Goez_Sql::select(array('name' => 'userName', 'age' => 'userAge'))
//     *                ->from('users');
//     * // $sql = "SELECT name AS `userName`, age AS `userAge` FROM `users`"
//     *
//     * $sql = Goez_Sql::select()
//     *                ->distinct()
//     *                ->from('users');
//     * // $sql = "SELECT DISTINCT * FROM `users`"
//     *
//     * $sql = Goez_Sql::select()
//     *                ->from('users')
//     *                ->where('name = ?', 'John')
//     *                ->group('name')
//     *                ->order('age', 'DESC');
//     * // $sql = "SELECT * FROM `users` WHERE (name = 'John') GROUP BY `name` ORDER BY `age` DESC"
//     * </code>
//     *
//     * @param array $columns 要輸出的欄位，預設是 * (所有欄位)
//     * @return Goez_Sql_Select
//     */
//    public static function select($columns = '*')
//    {
//        return new Goez_Db_Query(array(
//            'COLUMN' => (array) $columns,
//        ));
//    }
//
//    /**
//     * Insert 語法
//     *
//     * 主要是為了避免開發者在撰寫 INSERT 語法時，常會出現的欄位與值無法區配的問題。
//     *
//     * 用法：
//     *
//     * <code>
//     * $sql = Goez_Sql::insert('users', array(
//     *     'name' => 'John',
//     *     'age' => 20,
//     * ));
//     * // $sql = "INSERT INTO `users` (`name`, `age`) VALUES ('John', 20)"
//     * </code>
//     *
//     * @param string $table
//     * @param array $data
//     * @return Goez_Sql_Insert
//     */
//    public static function insert($table, $data)
//    {
//        $sql = 'INSERT INTO %s (%s) VALUES (%s)';
//        $table = $this->quoteIdentifier($table);
//        $columns = join(', ', array_map(array($this, 'quoteIdentifier'), array_keys($data)));
//        $values = join(', ', array_map(array($this, 'quote'), array_values($data)));
//        return sprintf($sql, $table, $columns, $values);
//    }
//
//    /**
//     * Update 語法
//     *
//     * 主要是為了避免 SQL Injection 的問題。
//     *
//     * 用法：
//     *
//     * <code>
//     * $sql = Goez_Sql::update('users', array(
//     *     'name' => 'John',
//     *     'age' => 21,
//     * ), array(
//     *     'id = ?' => 1,
//     *     'age > 0',
//     * ));
//     * // $sql = "UPDATE `users` SET `name` = 'John', `age` = 21 WHERE (id = 1) AND (age > 0)"
//     * </code>
//     *
//     * 特別注意這裡第三個參數的用法，陣列的索引可以是帶有 ? 號的條件式。
//     *
//     * @param string $table
//     * @param array $data
//     * @param array $where
//     * @return Goez_Sql_Update
//     */
//    public static function update($table, $data, $where)
//    {
//        $table = $this->quoteIdentifier($table);
//        $set = array();
//        foreach ($data as $col => $value) {
//            $set[] = $this->quoteIdentifier($col) . ' = ' . $this->quote($value);
//        }
//        $set = join(', ', $set);
//
//        $sql = sprintf("UPDATE %s SET %s", $table, $set);
//        $where = $this->_whereExpr($where);
//        if (!empty($where)) {
//            $sql .= ' WHERE ' . $where;
//        }
//
//        return $sql;
//    }
//
//    /**
//     * Delete 語法
//     *
//     * 用法：
//     *
//     * <code>
//     * $sql = Goez_Sql::delete('users', array(
//     *     'id = ?' => 1,
//     * ));
//     * // $sql = "DELETE FROM `users` WHERE (id = 1)"
//     *
//     * $sql = Goez_Sql::delete('users', array(
//     *     'age > 20',
//     * ));
//     * // $sql = "DELETE FROM `users` WHERE (age > 20)"
//     * </code>
//     *
//     * @param string $table
//     * @param array $where
//     * @return Goez_Sql_Delete
//     */
//    public static function delete($table, $where)
//    {
//        $table = $this->quoteIdentifier($table);
//
//        $sql = sprintf("DELETE FROM %s", $table);
//        $where = $this->_whereExpr($where);
//        if (!empty($where)) {
//            $sql .= ' WHERE ' . $where;
//        }
//
//        return $sql;
//    }


}
