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
 * Dispatcher 類別
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Dispatcher
{
    public function dispatch(GoEz_Request $request, GoEz_Response $response)
    {
        
    }

    /**
     * 取得使用者定義的 Controller
     *
     * @return GoEz_Controller
     * @throws Excetion
     */
    protected function _getUserController(GoEz_Request $request)
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

}