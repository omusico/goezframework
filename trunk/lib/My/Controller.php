<?php
/**
 * 自訂程式
 *
 */
class My_Controller extends GoEz_Controller
{
    /**
     * 預設動作
     *
     */
    public function index()
    {
        $this->_view->renderTemplate('template.tpl.htm');
    }
}