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
 * Delete
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Sql_Delete extends GoEz_Sql
{
    /**
     * 轉換成 SQL 語法
     *
     * @return string
     */
    public function __toString()
    {
        $table = $this->quoteIdentifier($this->_part['TABLE']);

        $sql = sprintf("DELETE FROM %s", $table);
        if ($where = $this->_whereExpr($this->_part['WHERE'])) {
            $sql .= ' WHERE ' . $where;
        }

        return $sql;
    }
}