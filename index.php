<?php
/**
 * @author Stnislav Zavalishin
 * @email stanislav.web.developer@gmail.com
 */
//echo chr(2480);

// ВНИМАНИЕ, КОГДА добавляется значение в поле users_sessions.agent, то необходимо проверять на длину значения
// потому что если больше 100, то будет выдавать ошибку, поэтому надо обрезать, если больше

ini_set('display_errors', 1);

define('DS', DIRECTORY_SEPARATOR);
define('DIR', dirname(__FILE__).DS); // var/www/english/
define('CORE', dirname(__FILE__).DS.'core'.DS); // var/www/english/core/


if (isset($_SERVER['XDEBUG_CONFIG'])) {
    define('HOST', 'http://work');
    define('URI', '/'); // /open/solve/12?root=10
} else {
    define('HOST', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST']);
    define('URI', $_SERVER['REQUEST_URI']); // /open/solve/12?root=10
}


define('ROUTER_CACHE_FILE', DIR . 'recording' . DS . 'router.php');
define('WORK_SPACE_FOLDER_PATH', DIR . 'workSpace' . DS);

require_once(CORE.'config.php');

Config::includingNeededCoreFiles();
Config::setUpDisplayErrorsConfiguration();
Routing::run();
