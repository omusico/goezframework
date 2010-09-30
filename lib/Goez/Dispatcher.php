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
 * Dispatcher 類別
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Dispatcher
{
    protected $_config = array();

    protected $_userController = null;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function dispatch(Goez_Request $request, Goez_Response $response)
    {
        $this->_userController = $this->_getUserController();
        $this->_userController->setConfig($this->_config);
        $this->_userController->setRequest($this->_request);
        $this->_userController->setView($this->_view);
        $this->_userController->init();
        $this->_userController->beforeDispatch();
        $this->_userController->{$this->_getUserAction()}();
        $this->_userController->afterDispatch();
    }

    /**
     * 取得使用者定義的 Controller
     *
     * @return Goez_Controller
     * @throws Excetion
     */
    protected function _getUserController(Goez_Request $request)
    {
        $userNamespace = 'My_';
        if (array_key_exists('userNamespace', $this->_config['bootstrap'])) {
            $userNamespace = rtrim(ucfirst($this->_config['bootstrap']['userNamespace']), '_') . '_';
        }
        $controllerName = $userNamespace . ucfirst($this->_router->getController()) . 'Controller';
        try {
            return new $controllerName();
        } catch (Exception $e) {
            throw new Exception("Controller \"$controllerName\" 不存在。");
        }
    }

    /**
     * 取得使用者定義的動作
     *
     * @return string
     * @throws Excetion
     */
    protected function _getUserAction()
    {
        $action = $this->_router->getAction() . 'Action';
        if (method_exists($this->_userController, $action)) {
            return $action;
        } else {
            $controllerName = get_class($this->_userController);
            throw new Exception("Action \"$controllerName::$action\" 不存在。");
        }
    }
}