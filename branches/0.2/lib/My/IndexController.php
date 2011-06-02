<?php
/**
 * 自訂程式
 *
 */
class My_IndexController extends Goez_Controller
{
    /**
     * 預設動作
     *
     */
    public function indexAction()
    {
        $this->getView()->renderTemplate('index.tpl.htm');
    }

    /**
     * Cron
     *
     */
    public function cronAction()
    {
        var_dump($this->_request->getParams());
    }
}