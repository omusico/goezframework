<?php
/**
 * 預設的路由器
 *
 */
class GoEz_Router
{
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
    public function __construct()
    {
        $this->_action = isset($_GET['act']) ? strtolower(trim(strip_tags($_GET['act']))) : 'index';
    }

    /**
     * 取得解析後的動作名稱
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

}