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
 * 一般 Router
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Router
{
    /**
     * 預設的 Controller
     *
     * @var string
     */
    protected $_controller = 'index';

    /**
     * 預設的動作
     *
     * @var string
     */
    protected $_action = 'index';

    /**
     * 在建構函式中解析 GET 變數
     *
     */
    public final function __construct()
    {
        $this->_parseUrl();
    }

    /**
     * 解析網址
     *
     * 主要用來取得目前網址上的 controller 和 action 。
     *
     * 使用者可以覆寫這個方法，用自己的方式取得 controller 和 action
     */
    protected function _parseUrl()
    {
        $this->_controller = $this->_getQuery('controller', 'index');
        $this->_action = $this->_getQuery('action', 'index');
    }

    /**
     * 取得 Query 參數值
     *
     * @param string $name
     * @param string $default
     * @return string
     */
    private function _getQuery($name, $default)
    {
        return isset($_GET[$name]) ? strtolower(trim(strip_tags($_GET[$name]))) : $default;
    }

    /**
     * 取得解析後的 Controller 名稱
     *
     * @return string 回傳解析後的 controller
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * 取得解析後的 Action 名稱
     *
     * @return string 回傳解析後的 action
     */
    public function getAction()
    {
        return $this->_action;
    }

}