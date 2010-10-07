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
 * Model
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db_Table
{
    /**
     * @var Goez_Db
     */
    protected static $_defaultDb = null;

    /**
     * @param Goez_Db $db
     */
    public static function setDefaultAdapter(Goez_Db $db)
    {
        self::$_defaultDb = $db;
    }

    /**
     * @return Goez_Db
     */
    public static function getDefaultAdapter()
    {
        return self::$_defaultDb;
    }

    /**
     * @var Goez_Db
     */
    protected $_db = null;

    /**
     * @param Goez $db
     */
    public function setDb(Goez $db)
    {
        $this->_db = $db;
    }

    /**
     * @return Goez_Db
     */
    public function getDb()
    {
        return $this->_db;
    }

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
     * @return string
     */
    public function getPrimary()
    {
        return $this->_primary;
    }

    /**
     * @var array
     */
    protected $_cols = array();

    /**
     * 取得欄位定義
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_cols;
    }

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->setConfig($config);
        $this->_setup();
        $this->init();
    }

    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @param array $config
     */
    public function setConfig(array $config = array())
    {
        $this->_config = $config;
    }

    /**
     * 設定資料庫連線
     *
     */
    protected function _setup()
    {
        $this->_db = self::getDefaultAdapter();
    }

    /**
     * 初始化 (Hook)
     * 
     */
    public function init()
    {
    }

    public function fetchAll($select = null)
    {
        $select = (string) $select;
    }

    protected function _fetch(Goez_Db_Select $select)
    {
        $stmt = $this->getDb()->query($select);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * 新增資料列
     *
     * @param array $data
     * @return int 新增的自動編號
     */
    public function insert(array $data)
    {
        $this->getDb()->insert($this->_name, $data);
        return $this->getDb()->lastInsertId();
    }

    /**
     * 更新資料
     *
     * @param array $data
     * @param array $where
     * @return int 更新筆數
     */
    public function update(array $data, $where)
    {
        return $this->getDb()->update($this->_name, $data, $where);
    }

    /**
     * 刪除資料
     *
     * @param array $where
     * @return int 刪除筆數
     */
    public function delete($where)
    {
        return $this->_db->delete($this->_name, $where);
    }
}
