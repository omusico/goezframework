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
 * Select
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Sql_Select extends GoEz_Sql
{
    /**
     * 設定 DISTINCT
     *
     * @param bool $flag
     * @return GoEz_Sql_Select
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
     * @return GoEz_Sql_Select
     */
    public function from($table)
    {
        $this->_part['TABLE'] = $table;
        return $this;
    }

    /**
     * 設定條件式
     *
     * @param string $condi
     * @param mixed $value
     * @return GoEz_Sql_Select
     */
    public function where($condi, $value)
    {
        $this->_part['WHERE'][] = $this->quoteInto($condi, $value);
        return $this;
    }

    /**
     * 設定 Group
     *
     * @param string $column
     * @return GoEz_Sql_Select
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
     * @return GoEz_Sql_Select
     */
    public function order($column, $dir = 'ASC')
    {
        $dir = strtoupper($dir);
        $dir = in_array($dir, array('ASC', 'DESC')) ? $dir : 'ASC';
        $this->_part['ORDER'][(string) $column] = $dir;
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
            $columns = array_map(array($this, 'quoteIdentifier'), $this->_part['COLUMN']);
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
        return $sql;
    }
}
