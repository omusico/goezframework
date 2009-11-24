<?php
/**
 * 網址重寫路由器
 *
 */
class GoEz_RewriteRouter extends GoEz_Router
{
    /**
     * 可以解析 http://xxxxx/basedir/action
     *
     */
	public function __construct()
	{
        $baseDir = basename(APP_ROOT_PATH);
        $currDir = str_replace('index.php', '', $_SERVER["REQUEST_URI"]);
        $pattern = '/^\/' . $baseDir . '\/*(.*)$/';
        preg_match($pattern, $currDir, $matches);
        $tickets = isset($matches[1]) ? explode('/', $matches[1]) : array ('');
        $this->_action = ($tickets[0]) ? strtolower($tickets[0]) : 'index';
    }
}