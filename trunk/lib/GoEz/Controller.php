<?php
/**
 * 抽象控制類別
 *
 */
class GoEz_Controller
{
    /**
     * View
     *
     * @var GoEz_View
     */
    protected $_view = null;

    /**
     * Base Url
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * 設定
     *
     * @var array
     */
    protected $_config = array(
        'view' => array(),
    );

    /**
     * 執行動作
     *
     */
    public final function run($env, $config)
    {
        $controller = new My_Controller($env, $config);
        $controller->{$controller->_action}();
    }

    /**
     * 不可初始化
     *
     */
    private function __construct($env, $config)
    {
        if (array_key_exists('config', $config)) {
            $this->_loadConfig($env, $config['config']);
        }

        if (array_key_exists('router', $config) && ($config['router'] instanceof GoEz_Router)) {
            $this->setRouter($config['router']);
        }

        $this->_baseUrl = rtrim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');

        $this->_initView();
    }

    /**
     * 載入網址設定檔
     *
     * @param string $configFile
     */
    protected function _loadConfig($env, $configFile)
    {
        $config = new GoEz_Config($configFile);
        $config = $config->toArray();
        $this->_config = $config[$env];
    }

    protected function _initView()
    {
        $this->_view = new GoEz_View($this->_config['view']);
        $this->_view->setFrontendVars('baseUrl', $this->_baseUrl);
    }

    /**
     * 取得設定
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 動作名稱
     *
     * @var string
     */
    protected $_action = '';

    /**
     * 預設動作
     *
     */
    protected function index() {}

    /**
     * 路由器
     *
     * @var Router
     */
    protected $router = NULL;

    /**
     * 設定路由器
     *
     * @param Router $router
     * @return GoEz_Controller
     */
    public function setRouter(GoEz_Router $router)
    {
        $action = $router->getAction();
        if (method_exists($this, $action)) {
            $this->_action = $action;
        } else {
            throw new Exception("Action \"$action\" does not exist");
        }
        return $this;
    }

    /**
     * 取得 BaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * 頁面重導向
     *
     * @param string $url
     */
    public function redirectTo($url)
    {
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = $this->_baseUrl . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
    }

    /**
     * 是否為 Post
     *
     * @return bool
     */
    public static function isPost()
    {
        return (bool) ('POST' == $_SERVER['REQUEST_METHOD']);
    }

    /**
     * 取得 POST 值
     *
     * @param string $key
     * @return string
     */
    public static function getPost($key, $stripTags = true)
    {
        return isset($_POST[$key]) ? ($stripTags ? strip_tags(trim($_POST[$key])) : trim($_POST[$key])) : null;
    }

    /**
     * 取得 GET 值
     *
     * @param string $key
     * @return string
     */
    public static function getQuery($key, $stripTags = true)
    {
        return isset($_GET[$key]) ? ($stripTags ? strip_tags(trim($_GET[$key])) : trim($_GET[$key])) : null;
    }
}