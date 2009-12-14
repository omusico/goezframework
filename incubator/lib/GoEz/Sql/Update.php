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
 * Update
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Sql_Update extends GoEz_Sql
{
    /**
     * 轉換成 SQL 語法
     *
     * @return string
     */
    public function __toString()
    {
        $table = $this->quoteIdentifier($this->_part['TABLE']);
        $set = array();
        foreach ($this->_part['DATA'] as $col => $value) {
            $set[] = $this->quoteIdentifier($col) . ' = ' . $this->quote($value);
        }
        $set = join(', ', $set);

        $sql = sprintf("UPDATE %s SET %s", $table, $set);
        if ($where = $this->_whereExpr($this->_part['WHERE'])) {
            $sql .= ' WHERE ' . $where;
        }

        return $sql;
    }
}
