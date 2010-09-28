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
 * Select
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db_Query
{

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
        'LIMIT_COUNT' => null,
        'LIMIT_OFFSET' => null,
    );

    /**
     * 設定 DISTINCT
     *
     * @param bool $flag
     * @return Goez_Sql_Select
     */
    public function distinct($flag = true)
    {
        $this->_part['DISTINCT'] = (bool) $flag;
        return $this;
    }

    /**
     * 設定資料表
     *
     * @param string $table
     * @return Goez_Sql_Select
     */
    public function from($table)
    {
        $this->_part['TABLE'] = $table;
        return $this;
    }

    /**
     * 設定條件式
     *
     * @param string $cond
     * @param mixed $value
     * @return Goez_Sql_Select
     */
    public function where($cond, $value)
    {
        $this->_part['WHERE'][] = $this->_db->quoteInto($cond, $value);
        return $this;
    }

    /**
     * 設定 HAVING 條件式
     *
     * @param mixed $cond
     * @return Goez_Db_Select
     */
    public function having($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_db->quoteInto($cond, $val);
        }

        if ($this->_parts['HAVING']) {
            $this->_parts['HAVING'][] = 'AND' . " ($cond)";
        } else {
            $this->_parts['HAVING'][] = "($cond)";
        }

        return $this;
    }

    /**
     * 設定 Group
     *
     * @param string $column
     * @return Goez_Sql_Select
     */
    public function group($column)
    {
        $this->_part['GROUP'][] = (string) $column;
        return $this;
    }

    /**
     * 設定 Order
     *
     * @param string $column
     * @return Goez_Sql_Select
     */
    public function order($column, $dir = 'ASC')
    {
        $dir = strtoupper($dir);
        $dir = in_array($dir, array('ASC', 'DESC')) ? $dir : 'ASC';
        $this->_part['ORDER'][(string) $column] = $dir;
        return $this;
    }

    /**
     * 設定 LIMIT
     *
     * @param int $count
     * @param int $offset
     * @return Goez_Sql_Select
     */
    public function limit($count = null, $offset = null)
    {
        $this->_part['LIMIT_COUNT']  = (int) $count;
        $this->_part['LIMIT_OFFSET'] = (int) $offset;
        return $this;
    }

    /**
     * 轉換成 SQL 語法
     *
     * @return string
     */
    public function __toString()
    {
        // COLUMN
        $sql = 'SELECT';
        if ($this->_part['DISTINCT']) {
            $sql .= ' DISTINCT';
        }

        if ((1 === count($this->_part['COLUMN'])) && ('*' === $this->_part['COLUMN'][0])) {
            $columns = $this->_part['COLUMN'];
        } else {
            $columns = array();
            foreach ($this->_part['COLUMN'] as $col => $alias) {
                if (is_int($col)) {
                    $columns[] = $this->quoteIdentifier($alias);
                } else {
                    $columns[] = $col . ' AS ' . $this->quoteIdentifier($alias);
                }
            }
        }
        $sql .= ' ' . join(', ', $columns);

        // TABLE
        $sql .= ' FROM';
        $sql .= ' ' . $this->quoteIdentifier($this->_part['TABLE']);

        // WHERE
        if (!empty($this->_part['WHERE'])) {
            $sql .= ' WHERE ';
            $sql .= $this->_whereExpr($this->_part['WHERE']);
        }

        // GROUP BY
        if (!empty($this->_part['GROUP'])) {
            $sql .= ' GROUP BY ';
            $sql .= join(', ', array_map(array($this, 'quoteIdentifier'), $this->_part['GROUP']));
        }

        // ORDER BY
        if (!empty($this->_part['ORDER'])) {
            $sql .= ' ORDER BY ';
            $orders = array();
            foreach ($this->_part['ORDER'] as $column => $dir) {
            	$orders[] = $this->quoteIdentifier($column) . ' ' . $dir;
            }
            $sql .= join(', ', $orders);
        }

        // LIMIT
        if (!empty($this->_part['LIMIT_COUNT']) && empty($this->_part['LIMIT_OFFSET'])) {
            $sql .= ' LIMIT ' . (int) $this->_part['LIMIT_COUNT'];
        } elseif (!empty($this->_part['LIMIT_COUNT'])) {
            $sql .= ' LIMIT ' . (int) $this->_part['LIMIT_OFFSET'] . ', ' . (int) $this->_part['LIMIT_COUNT'];
        }

        return $sql;
    }
}
