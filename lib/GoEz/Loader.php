<?php
/**
 * 自動載入類別
 *
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
     */
    public function autoload()
    {
        $loader = new self();
        spl_autoload_register(array($loader, 'loadClass'));
    }

    /**
     * 載入類別
     *
     * @param string $className
     * @throws Excetion
     */
    public function loadClass($className)
    {
        $fileName = str_replace('_', '/', $className) . '.php';
        @include_once $fileName;
        if (!class_exists($className, false)) {
            eval('class ' . $className . ' {}');
            throw new Exception("Class \"$className\" does not exist.");
        }
    }
}