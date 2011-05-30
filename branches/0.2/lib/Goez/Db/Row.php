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
 * Database Table Row
 *
 * @package    Goez
 * @subpackage Goez_Db
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db_Row extends Goez_Db_Common
{
    /**
     * 資料表名稱
     *
     * @var string
     */
    protected $_name = null;

    /**
     * 主索引鍵名稱
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
    }

    /**
     * @var Goez_Db_Table
     */
    protected $_table = null;

    /**
     * @param Goez_Db_Table $db
     */
    public function setTable(Goez_Db $table)
    {
        $this->_table = $table;
    }

    /**
     * @return Goez_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * 資料列內容
     *
     * @var array
     */
    protected $_data = array();

    /**
     *
     * @var array
     */
    protected $_cleanData = array();

    /**
     * 有修改過的欄位
     *
     * @var array
     */
    protected $_modifiedFields = array();

    /**
     * @return array
     */
    public function toArray()
    {
        return (array) $this->_data;
    }

    /**
     * 從陣列設定資料
     *
     * @param  array $data
     * @return Goez_Db_Row
     */
    public function setFromArray(array $data)
    {
        $data = array_intersect_key($data, $this->_data);

        foreach ($data as $columnName => $value) {
            $this->__set($columnName, $value);
        }

        return $this;
    }

    /**
     * @param string $columnName
     * @param string $value
     */
    public function __set($columnName, $value)
    {
        $columnName = (string) $columnName;
        if (!array_key_exists($columnName, $this->_data)) {
            throw new Exception("欄位 \"$columnName\" 不存在");
        }
        $this->_data[$columnName] = $value;
        $this->_modifiedFields[$columnName] = true;
    }

    /**
     * @param string $columnName
     * @return string
     */
    public function __get($columnName)
    {
        $columnName = (string) $columnName;
        if (!array_key_exists($columnName, $this->_data)) {
            throw new Exception("欄位 \"$columnName\" 不存在");
        }
        return $this->_data[$columnName];
    }

    /**
     * 刪除資料列
     *
     * @return int 被刪除的筆數
     */
    public function delete()
    {
        $where = $this->_getWhereQuery();
        $this->_beforeDelete();
        $result = $this->getTable()->delete($where);
        $this->_afterDelete();
        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );
        return $result;
    }

    /**
     * @return void
     */
    protected function _beforeDelete()
    {
    }

    /**
     * @return void
     */
    protected function _afterDelete()
    {
    }

    /**
     * 儲存紀錄
     *
     * @return int 主索引欄位
     */
    public function save()
    {
        if (empty($this->_cleanData)) {
            return $this->_doInsert();
        } else {
            return $this->_doUpdate();
        }
    }

    /**
     * @return int 主索引鍵
     */
    protected function _doInsert()
    {
        $this->_beforeInsert();
        $primaryKey = $this->_insert();
        $this->_afterInsert();
        $this->_refresh();
        return $primaryKey;
    }

    protected function _insert()
    {
        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        $primaryKey = $this->getTable()->insert($data);
        $newPrimaryKey = array($this->_primary => $primaryKey);
        $this->_data = array_merge($this->_data, $newPrimaryKey);
        return $primaryKey;
    }

    /**
     * @return void
     */
    protected function _beforeInsert()
    {
    }

    /**
     * @return void
     */
    protected function _afterInsert()
    {
    }

    /**
     * @return int 主索引鍵
     */
    protected function _doUpdate()
    {
        $where = $this->_getWhereQuery(false);
        $this->_beforeUpdate();
        $this->_update();
        $this->_postUpdate();
        $this->_refresh();
        return $this->_getPrimaryKey();
    }

    /**
     * @return void
     */
    protected function _update()
    {
        $diffData = array_intersect_key($this->_data, $this->_modifiedFields);
        if (count($diffData) > 0) {
            $this->getTable()->update($diffData, $where);
        }
    }

    /**
     * @return void
     */
    protected function _beforeUpdate()
    {
    }

    /**
     * @return void
     */
    protected function _afterUpdate()
    {
    }

    /**
     * 從資料庫中重新讀取所有欄位值
     *
     * @return void
     */
    protected function _refresh()
    {
        $where = $this->_getWhereQuery();
        $row = $this->getTable()->fetchRow($where);

        if (null === $row) {
            throw new Exception('無法更新資料列');
        }

        $this->_data = $row->toArray();
        $this->_cleanData = $this->_data;
        $this->_modifiedFields = array();
    }

    /**
     * 取得主索引鍵值
     *
     * @return int
     */
    protected function _getPrimaryKey()
    {
        return $this->_data[$this->_primary];
    }

    /**
     * 取得主索引條件式
     *
     * @return string
     */
    protected function _getWhereQuery()
    {
        return $this->getTable()
                    ->getDb()
                    ->quoteInto($this->_primary . ' = ?', $this->_getPrimaryKey());
    }
}
