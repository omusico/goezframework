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
        $this->_view->renderTemplate('template.tpl.htm');
    }
}