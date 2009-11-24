<?php
defined('APP_ENV') || define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));
defined('APP_ROOT_PATH') || define('APP_ROOT_PATH', realpath(dirname(__FILE__)));
define('APP_LIB_PATH', APP_ROOT_PATH . '/lib');
define('APP_ETC_PATH', APP_ROOT_PATH . '/etc');

$includePath = array(APP_LIB_PATH, '.');
set_include_path(join(PATH_SEPARATOR, $includePath));

function __autoload($className)
{
    $className = str_replace('_', '/', $className);
    require_once "$className.php";
}

GoEz_Controller::run(APP_ENV, array(
    'config' => APP_ETC_PATH . '/config.ini',
    'router' => new GoEz_RewriteRouter(),
));