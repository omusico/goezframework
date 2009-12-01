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
 * Insert
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Sql_Insert extends GoEz_Sql
{
    /**
     * 轉換成 SQL 語法
     *
     * @return string
     */
    public function __toString()
    {
        $sql = 'INSERT INTO %s (%s) VALUES (%s)';
        $table = $this->quoteIdentifier($this->_part['TABLE']);
        $columns = join(', ', array_map(array($this, 'quoteIdentifier'), array_keys($this->_part['DATA'])));
        $values = join(', ', array_map(array($this, 'quote'), array_values($this->_part['DATA'])));
        return sprintf($sql, $table, $columns, $values);
    }
}
