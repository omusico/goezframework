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
 * 抽象 Controller 類別
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
abstract class Goez_Controller
{
    /**
     * @var array
     */
    protected $_config = array();

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * @return array
     */
    public function getConfig($name = null)
    {
        if (null === $name) {
            return $this->_config;
        } elseif (isset($this->_config[$name])) {
            return $this->_config[$name];
        }
        return array();
    }

    /**
     * @var Goez_Request
     */
    protected $_request = null;

    /**
     * @param Goez_Request $request
     */
    public function setRequest(Goez_Request $request)
    {
        $this->_request = $request;
    }

    /**
     * @return Goez_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @var Goez_Response
     */
    protected $_response = null;

    /**
     * @param Goez_Response $response
     */
    public function setResponse(Goez_Response $response)
    {
        $this->_response = $response;
    }

    /**
     * @return Goez_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * View
     *
     * @var Goez_View
     */
    protected $_view = null;

    /**
     * @param Goez_View $view
     */
    public function setView(Goez_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Goez_View
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
     *
     *
     */
    public function autoRender()
    {

    }

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
            $this->_response->setRedirect($url);
        }
    }

    /**
     * 設定讓瀏覽器下載檔案的標頭
     *
     * @param string $fileName
     * @param int $fileSize
     */
    public function setDownloadHeader($fileName = 'unnamed', $fileSize = null)
    {
        $this->_response->setHeader('Pragma', 'public')
                        ->setHeader('Expires', 0)
                        ->setHeader('Last-Modified', gmdate('D, d M Y H:i ') . ' GMT')
                        ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->setHeader('Cache-Control', 'private', false)
                        ->setHeader('Content-Type', 'application/octet-stream')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '";')
                        ->setHeader('Content-Transfer-Encoding', 'binary');
        if ($fileSize) {
            $this->_response->setHeader('Content-Length', $fileSize);
        }
    }
}