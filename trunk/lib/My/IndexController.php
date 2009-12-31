<?php
/**
 * 自訂程式
 *
 */
class My_IndexController extends GoEz_Controller
{
    /**
     * 預設動作
     *
     */
    public function indexAction()
    {
        $test = new Test();
        $this->_view->renderTemplate('index.tpl.htm');
    }

    public function testAction()
    {
        echo 'test';
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