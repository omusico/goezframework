<?php
/**
 * GoEz
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * SQL 語法產生器
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Sql
{
    /**
     * 語法類型
     *
     * @var array
     */
    protected static $_sqlTypeList = array('SELECT', 'INSERT', 'UPDATE', 'DELETE');

    /**
     * 語法類型
     *
     * @var array
     */
    protected $_sqlType = '';

    /**
     * 識別符
     *
     * @var string
     */
    protected $_identifier = '`';

    /**
     * SQL Parts
     *
     * @var array
     */
    protected $_part = array(
        'DISTINCT' => false,
        'COLUMN' => array('*'),
        'TABLE' => 'TABLE',
        'DATA' => array(),
        'WHERE' => array(),
        'GROUP' => array(),
        'HAVING' => array(),
        'ORDER' => array(),
    );

    /**
     * 建構式
     *
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($this->_part as $key => $value) {
            if (array_key_exists($key, $options)) {
                $this->_part[$key] = $options[$key];
            }
        }
    }

    /**
     * 建立語法
     *
     * @param string $sqlType
     * @return GoEz_Sql
     */
    protected static function _create($sqlType, $options = array())
    {
        $sqlType = strtoupper($sqlType);
        if (!in_array($sqlType, self::$_sqlTypeList)) {
            return null;
        }
        $className = 'GoEz_Sql_' . ucfirst(strtolower($sqlType));
        return new $className($options);
    }

    /**
     * Select 語法
     *
     * @param array $columns
     * @return GoEz_Sql_Select
     */
    public static function select($columns = '*')
    {
        return self::_create('select', array(
            'COLUMN' => (array) $columns,
        ));
    }

    /**
     * Insert 語法
     *
     * @return GoEz_Sql_Insert
     */
    /**
     * Insert 語法
     *
     * @param string $table
     * @param array $data
     * @return GoEz_Sql_Insert
     */
    public static function insert($table, $data)
    {
        return self::_create('insert', array(
            'TABLE' => (string) $table,
            'DATA' => (array) $data,
        ));
    }

    /**
     * Update 語法
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @return GoEz_Sql_Update
     */
    public static function update($table, $data, $where)
    {
        return self::_create('update', array(
            'TABLE' => (string) $table,
            'DATA' => (array) $data,
            'WHERE' => (array) $where,
        ));
    }

    /**
     * Delete 語法
     *
     * @param string $table
     * @param array $where
     * @return GoEz_Sql_Delete
     */
    public static function delete($table, $where)
    {
        return self::_create('delete', array(
            'TABLE' => (string) $table,
            'WHERE' => (array) $where,
        ));
    }

    /**
     * 將使用者輸入的值適當地加上引號
     *
     * 如果輸入的是陣列，那麼裡面的項目會在加上引號後，回傳為一個用逗號分隔的字串
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
            // is $cond an int? (i.e. Not a condition)
            if (!is_int($cond)) {
                // $cond is the condition with placeholder,
                // and $term is quoted into the condition
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
        if (is_int($value)) {
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        }
        return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
    }
}
