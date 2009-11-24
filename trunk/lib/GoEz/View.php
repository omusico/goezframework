<?php
/**
 * 抽象視圖類別
 *
 */
class GoEz_View
{
    /**
     * 設定
     *
     * @var array
     */
    protected $_config = array();

    /**
     * 初始化
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_config = (array) $config;
    }

    /**
     * 樣版變數
     *
     * @var array
     */
    protected $_vars = array();

    /**
     * 設定樣版變數
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value = null)
    {
        $this->_setVars($this->_vars, $name, $value);
    }

    /**
     * HTML 前端樣版變數
     *
     * @var array
     */
    protected $_frontendVars = array();

    /**
     * 設定前端樣版變數
     *
     */
    public function setFrontendVars($name, $value = null)
    {
        $this->_setVars($this->_frontendVars, $name, $value);
    }

    /**
     * 設定變數
     *
     * @param array $vars
     * @param string $name
     * @param mixed $value
     */
    protected function _setVars(&$vars, $name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                if ($key != '') {
                    $vars[$key] = $val;
                }
            }
        } else {
            if ($name != '') {
                $vars[$name] = $value;
            }
        }
    }

    /**
     * 自動取得對應的樣版變數
     *
     * @param string $name
     * @return mixed
     */
    private function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : NULL;
    }

    /**
     * 取得 View Engine
     *
     * @return Smarty
     */
    public function getViewEngine()
    {
        static $engine = null;
        if (null === $engine) {
            require_once 'Smarty/Smarty.class.php';
            $engine = new Smarty();
            foreach ($this->_config as $attr => $value) {
            	if (property_exists($engine, $attr)) {
            	    if (is_string($value)) {
                        $engine->$attr = $value;
            	    } elseif (is_array($value)) {
            	        $engine->$attr = array_merge($engine->$attr, $value);
            	    }
            	}
            }
        }
        return $engine;
    }

    /**
     * 取得 Render 結果
     *
     * @param string $file
     * @return string
     */
    public function fetchTemplate($file)
    {
        $engine = $this->getViewEngine();
        $engine->assign($this->_vars);
        $engine->assign('frontendVars', $this->_frontendVars);
        return $engine->fetch($file);
    }

    /**
     * 輸出 Render 結果
     *
     * @param string $file
     * @return string
     */
    public function renderTemplate($file)
    {
        echo $this->fetchTemplate($file);
    }

    /**
     * 產生 JSON
     *
     * @return string
     */
    public function fetchJson()
    {
        return json_encode($this->_vars);
    }

    /**
     * 顯示 JSON
     *
     * @return string
     */
    public function renderJson()
    {
        echo $this->fetchJson();
    }
}