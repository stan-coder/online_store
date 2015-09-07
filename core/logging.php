<?php

class Logging
{
    /**
     * Default value of folder recording
     *
     * @var bool
     */
    public static $recordingWritable = false;

    /**
     * Sensitive data keys so they will not be logged into error log
     *
     * @var array
     */
    private static $sensitiveDataKeys = array('password', 'username', 'email');

    /**
     * Error code to readable string mappings
     *
     * @var array
     */
    public static $errorTypes = array(
        E_PARSE => 'Parsing Error',
        E_ALL => 'All errors occured at once',
        E_WARNING => 'Warning',
        E_CORE_WARNING => 'Core Warning',
        E_COMPILE_WARNING => 'Compile Warning',
        E_USER_WARNING => 'User Warning',
        E_ERROR => 'Error',
        E_CORE_ERROR => 'Core Error',
        E_COMPILE_ERROR => 'Compile Error',
        E_USER_ERROR => 'User Error',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_NOTICE => 'Notice',
        E_USER_NOTICE => 'User Notice',
        E_STRICT => 'Strict Error',
        E_DEPRECATED  => 'Deprecated'
    );

    /**
     * Check recording folder on writable
     */
    public static function checkRecordingFolderOnWritable() {
        if (self::$recordingWritable) {
            return;
        }
        if (!is_writeable(DIR . 'recording')) {
            crash('The folder "recording" is closed for writing');
        }
        self::$recordingWritable = true;
    }

    /**
     * Saving report about revealed errors
     *
     * @param $errorNo
     * @param $errorMessage
     * @param $fileName
     * @param $lineNumber
     */
    public static function setErrorHandlerFunctionForProduction($errorNo, $errorMessage, $fileName, $lineNumber) {
        $textError = date('Y-m-d H:i:s') . ' | ip=' . $_SERVER['REMOTE_ADDR'] . ' | phpvers=' . phpversion() . "\r\n" .
        (isset(self::$errorTypes[$errorNo]) ? self::$errorTypes[$errorNo] : 'Unkown error code ' . $errorNo ) . ': ' . $errorMessage . "\r\n" .
        $fileName . ' line=' . $lineNumber . "\r\n" . 'REQUEST_METHOD=' . $_SERVER['REQUEST_METHOD'] . ' | ' .
        (isset($_SERVER['REQUEST_URI']) ? 'REQUEST_URI='.$_SERVER['REQUEST_URI'] : '') .
        (isset($_SERVER['HTTP_REFERER']) ? ' | REFERER='.$_SERVER['HTTP_REFERER'] : '') . "\r\n" .
        (isset($_GET) && count($_GET) > 0 ? 'GET='.self::convertArrayToString($_GET) . "\r\n" : '') .
        (isset($_POST) && count($_POST) > 0 ? 'POST='.self::convertArrayToString($_POST) . "\r\n" : '') .
        (isset($_FILES) && count($_FILES) > 0 ? 'POST='.self::convertArrayToString($_FILES) . "\r\n" : '')."\r\n\r\n";

        error_log($textError, 3, DIR.'recording'.DS.'errors.log');
        require_once(CORE . 'basicTemplates' . DS . 'productionCrashMessage.php');
        exit();
    }

    /**
     * Converting array into string
     *
     * @param $array
     * @param bool $setKey
     * @return string
     */
    private static function convertArrayToString($array, $setKey = false){
        $result = '';
        $comma = ', ';
        $compare = array_diff(range(0, count($array)-1), array_keys($array));

        if (empty($compare)) {
            $setKey = true;
            $comma = ',';
        }
        foreach ($array as $key => $value) {

            $result .= (!$setKey ? "[{$key}] => " : '');
            if (in_array((string)$key, self::$sensitiveDataKeys)) {
                continue;
            }
            if (is_array($value) && !empty($value)) {
                $result .= "array(" . self::convertArrayToString($value) . "){$comma}";
            } elseif (is_array($value) && empty($value)) {
                $result .= "array(){$comma}";
            } else {
                $result .= self::convertTypeToString($value).$comma;
            }
        }
        return preg_replace('~,\s?$~im', '', $result);
    }

    /**
     * Converting various type into string
     *
     * @param $value
     * @return string
     */
    private static function convertTypeToString($value){
        switch (gettype($value)) {
            case 'string' : {
                return $value;
                break;
            }
            case 'integer' : {
                return (string) $value;
                break;
            }
            case 'boolean' : {
                return (!$value ? 'false' : 'true');
                break;
            }
            default : {
                return gettype($value);
            }
        }
    }
}