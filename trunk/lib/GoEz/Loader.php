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
 * 自動載入類別
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Loader
{
    /**
     * 私有初始化
     *
     */
    private function __construct() {}

    /**
     * 自動載入
     *
     * 自動載入類別檔案
     */
    public function autoload()
    {
        $loader = new self();
        spl_autoload_register(array($loader, 'loadClass'));
    }

    /**
     * 載入類別
     *
     * 將類別名稱 Xx_Yyy_Zzz 轉為 Xx/Yyy/Zzz.php 後載入
     *
     * @param string $className
     * @throws Excetion
     */
    public function loadClass($className)
    {
        $fileName = str_replace('_', '/', $className) . '.php';
        @include_once $fileName;
        if (!class_exists($className, false) && !interface_exists($className, true)) {
            eval('class ' . $className . ' {}');
            throw new Exception("類別 \"$className\" 不存在");
        }
    }
}