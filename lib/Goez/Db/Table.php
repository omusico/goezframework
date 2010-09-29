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
 * Table 抽象物件
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db_Table
{
//    /**
//     * @var string
//     */
//    protected $_schema = '_schema_';
//
//    /**
//     * @var string
//     */
//    protected $_name = '_table_';
//
//    /**
//     * @var PDO
//     */
//    protected static $_defaultDb = null;
//
//    /**
//     * @var PDO
//     */
//    protected $_db = null;
//
//    /**
//     * @param array $config
//     */
//    public function __construct($config = array())
//    {
//        if ($config) {
//            $this->setOptions($config);
//        }
//
//        $this->_setup();
//        $this->init();
//    }
//
//    /**
//     * @param array $options
//     * @return Goez_Db_Table
//     */
//    public function setOptions(array $options)
//    {
//        foreach ($options as $key => $value) {
//            switch ($key) {
//                case 'db':
//                    $this->_setAdapter($value);
//                    break;
//                case 'schema':
//                    $this->_schema = (string) $value;
//                    break;
//                case 'name':
//                    $this->_name = (string) $value;
//                    break;
//                default:
//                    break;
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @param PDO $db
//     */
//    public static function setDefaultDb(PDO $db)
//    {
//        self::$_defaultDb = $db;
//    }
//
//    /**
//     * @return PDO
//     */
//    public static function getDefaultAdapter()
//    {
//        return self::$_defaultDb;
//    }
//
//    /**
//     * @param PDO $db
//     * @return Goez_Db_Table
//     */
//    protected function _setAdapter(PDO $db)
//    {
//        $this->_db = $db;
//        return $this;
//    }
//
//    /**
//     * @return void
//     */
//    protected function _setup()
//    {
//        $this->_setupDatabaseAdapter();
//        $this->_setupTableName();
//    }
//
//    /**
//     * @return void
//     */
//    public function init()
//    {
//    }
//
//    /**
//     * @return void
//     */
//    protected function _setupDatabaseAdapter()
//    {
//        if (! $this->_db) {
//            $this->_db = self::getDefaultAdapter();
//            if (!$this->_db instanceof Goez_Db_Table) {
//                throw new Exception('No adapter found for ' . get_class($this));
//            }
//        }
//    }
//
//    /**
//     * @return void
//     */
//    protected function _setupTableName()
//    {
//        if (!$this->_name) {
//            $this->_name = get_class($this);
//        } elseif (strpos($this->_name, '.')) {
//            list($this->_schema, $this->_name) = explode('.', $this->_name);
//        }
//    }

    /**
     * 新增紀錄
     * 
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        $this->_db->insert($tableSpec, $data);
        return $this->_db->lastInsertId();
    }

    /**
     * 修改紀錄
     *
     * @param array $data
     * @param mixed $where
     * @return int
     */
    public function update(array $data, $where)
    {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        return $this->_db->update($tableSpec, $data, $where);
    }

    /**
     * 刪除紀錄
     *
     * @param mixed $where
     * @return int
     */
    public function delete($where)
    {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        return $this->_db->delete($tableSpec, $where);
    }
}
