<?php
/**
 * 定義常數
 *
 */
defined('APP_ROOT_PATH') || define('APP_ROOT_PATH', realpath(dirname(dirname(__FILE__))));
define('APP_LIB_PATH', APP_ROOT_PATH . '/lib');
define('APP_ETC_PATH', APP_ROOT_PATH . '/etc');

/**
 * 設定載入路徑
 *
 */
$includePath = array(APP_LIB_PATH, '.');
set_include_path(join(PATH_SEPARATOR, $includePath));

/**
 * Auto load
 *
 * @see GoEz_Loader
 */
require_once 'GoEz/Loader.php';
GoEz_Loader::autoload();

/**
 * Start
 */
$options = GoEz_Cli::parseArgs($argv);
