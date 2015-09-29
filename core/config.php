<?php

/**
 * Class Config
 */
class Config {

    /**
     * Options
     */
    public static
        $debug = true,
        $dbDriver = 'mysql',
        $dbHost = 'localhost',
        $dbUsername = 'root',
        $dbPassword = 'root',
        $dbName = 'online_store',
        $dbCharset = 'utf8',
        $secretKey = 'thisIsYourSecretKeyWhichDesirableBeingChanged';

    /**
     * List of files core
     * @var array
     */
    private static $filesCore = array(
        'functions',
        'logging',
        'baseManager',
        'controllerManager',
        'pdo',
        'cache',
        'routing',
        'render',
        'modelManager',
        'forms');

    /**
     * Included needed core files
     */
    public static function includingNeededCoreFiles() {
        foreach (self::$filesCore as $value) {
            require_once(CORE . $value . '.php');
        }
    }

    /**
     * Set up display errors config
     */
    public static function setUpDisplayErrorsConfiguration() {
        $displayErrors = array('Off', 0);

        /**
         * If debugging
         */
        if (self::$debug) {
            $displayErrors = array('On', E_ALL | E_STRICT);
            $cache = new Cache();
            $cache->startScan();
            set_error_handler(array('Config', 'setErrorHandlerFunctionForDebug'));
        }
        /**
         * Production mode
         */
        else {
            ini_set('log_errors', 'On');
            ini_set('error_log', DIR.'recording'.DS.'errors.log.sys.log');
            set_error_handler(array('Logging', 'setErrorHandlerFunctionForProduction'));
        }

        ini_set('display_startup_errors', $displayErrors[0]);
        ini_set('display_errors', $displayErrors[0]);
        ini_set('html_errors', $displayErrors[0]);
        ini_set('default_charset', 'UTF-8');
        @ini_set('magic_quotes_gpc', 0);
        @ini_set('register_globals', 0);
        error_reporting($displayErrors[1]);
    }

    /**
     * Display errors during debug
     *
     * @param $errorNo
     * @param $errorMessage
     * @param $fileName
     * @param $lineNumber
     */
    public static function setErrorHandlerFunctionForDebug($errorNo, $errorMessage, $fileName, $lineNumber) {
        $bResult = '<div id="head"><strong>' . (isset(Logging::$errorTypes[$errorNo]) ? Logging::$errorTypes[$errorNo] : 'Unknown error') .
            "</strong> : {$errorMessage}<br/><br/>File: {$fileName}<br/>Line: {$lineNumber}</div>";

        /**
         * Parsing backtrace data
         */
        $bData = debug_backtrace();
        for ($a = 0; $a < count($bData); $a++) {
            if ($a == 0) {
                $bResult .= '<table>';
            }
            $bResult .= '<tr><td class="number">' . ($a+1) . '. </td><td>' . (isset($bData[$a]['file']) ? $bData[$a]['file'] : '_') . (isset($bData[$a]['line']) ? ' line '.$bData[$a]['line'] : '_') . '</td></tr><tr><td></td><td class="secondRow">';

            if (isset($bData[$a]['class']) && isset($bData[$a]['function'])) {
                $bResult .= $bData[$a]['class'] . (isset($bData[$a]['type']) ? $bData[$a]['type'] : '__') . $bData[$a]['function'];
            } elseif (isset($bData[$a]['function'])) {
                $bResult .= 'func: "' . $bData[$a]['function'];
            } else {
                $bResult .= 'Unknown php-operator';
            }
            $bResult .= '</td></tr>';
            if ($a == (count($bData) - 1)) {
                $bResult .= '</table>';
            }
        }
        require_once(CORE . 'basicTemplates' . DS . 'debugError.php');
        exit;
    }
}
