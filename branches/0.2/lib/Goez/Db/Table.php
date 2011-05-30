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
 * Database Table
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Db_Table extends Goez_Db_Common
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
}
