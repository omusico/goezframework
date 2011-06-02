<?php
/**
 * Goez
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 * @version    $Id$
 */

/**
 * 網址重寫 Router 類別
 *
 * @package    Goez
 * @copyright  Copyright (c) 2008-2012 Wabow Information Inc. (http://www.wabow.com)
 * @license    New BSD License
 */
class Goez_Router_Rewrite extends Goez_Router
{
    /**
     * 解析網址
     *
     * 解析下列格式網址：
     *
     * <code>
     * http://xxxxx/basedir/controller/action
     * http://xxxxx/basedir/controller/action/param1/value1/param2/value2...
     * </code>
     *
     */
    protected function _parseUrl()
    {
        $baseDir = basename(APP_ROOT_PATH);
        $currDir = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
        if (false !== strpos($currDir, '?')) {
            $currDir = str_replace(substr($currDir, strpos($currDir, '?')), '', $currDir);
        }

        $pattern = '/^\/' . preg_quote($baseDir, '/') . '\/*(.*)$/';
        preg_match($pattern, $currDir, $matches);
        if (empty($matches)) { // 如果是根目錄
            $matches = array('', ltrim($currDir, '/'));
        }
        $tickets = isset($matches[1]) ? explode('/', $matches[1]) : array ('', '');
        $this->_controller = ($tickets[0]) ? strtolower($tickets[0]) : 'index';
        $this->_action = (isset($tickets[1]) && $tickets[1]) ? strtolower($tickets[1]) : 'index';

        unset($tickets[0]);
        unset($tickets[1]);
        $tickets = array_values($tickets);

        for ($i = 0; $i < count($tickets); $i += 2) {
            $this->_request->setParam($tickets[$i], $tickets[$i + 1]);
        }
    }
}