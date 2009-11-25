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
 * 網址重寫 Router 類別
 *
 * @package    GoEz
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class GoEz_Router_Rewrite extends GoEz_Router
{
    /**
     * 解析網址
     *
     * 解析下列格式網址：
     *
     * <code>
     * http://xxxxx/basedir/controller/action
     * </code>
     *
     */
	protected function _parseUrl()
	{
        $baseDir = basename(APP_ROOT_PATH);
        $currDir = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
        $pattern = '/^\/' . $baseDir . '\/*(.*)$/';
        preg_match($pattern, $currDir, $matches);
        $tickets = isset($matches[1]) ? explode('/', $matches[1]) : array ('', '');
        $this->_controller = ($tickets[0]) ? strtolower($tickets[0]) : 'index';
        $this->_action = isset($tickets[1]) ? strtolower($tickets[1]) : 'index';
    }
}