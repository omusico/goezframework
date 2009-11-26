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
 * 抽象 Controller 類別
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
abstract class GoEz_Controller
{
    /**
     * Config
     *
     * @var array
     */
    protected $_config = array();

    /**
     * 指定 Config
     *
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * 取得 Config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Request
     *
     * @var GoEz_Request
     */
    protected $_request = null;

    /**
     * 指定 Request
     *
     * @param GoEz_Request $request
     */
    public function setRequest(GoEz_Request $request)
    {
        $this->_request = $request;
    }

    /**
     * 取得 Request
     *
     * @return GoEz_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * View
     *
     * @var GoEz_View
     */
    protected $_view = null;

    /**
     * 指定 View
     *
     * @param GoEz_View $view
     */
    public function setView(GoEz_View $view)
    {
        $this->_view = $view;
    }

    /**
     * 取得 View
     *
     * @return GoEz_View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Controller 初始化
     *
     */
    public function init() {}

    /**
     * Dispatch 之前
     *
     */
    public function beforeDispatch() {}

    /**
     * Dispatch 之後
     *
     */
    public function afterDispatch() {}

    /**
     * 預設動作
     *
     */
    public function indexAction() {}

    /**
     * 頁面重導向
     *
     * 如果代入的 url 不是一般的 http:// 網址，
     * 那麼就會在前面加上 BaseUrl 後再做導向的動作
     *
     * @param string $url
     */
    public function redirect($url)
    {
        if (!$this->_request->isAjax()) {
            if (!preg_match('/^[a-z]+?:\/\//i', $url)) {
                $url = $this->_request->getBaseUrl() . '/' . ltrim($url, '/');
            }
            header('Location: ' . $url);
            exit;
        }
    }
}