<?php

class SessionModel extends modelManager
{
    public static $name;
    private static $started = false;

    public function __construct() {
        if (!self::$started) {
            $cs = $this->model('customFunction');
            $initialName = $cs->getIp() . $cs->getServerVariable('HTTP_USER_AGENT') . $cs->getServerVariable('HTTP_ACCEPT_LANGUAGE') . $cs->getServerVariable('HTTP_HOST') . Config::$secretKey;
            $length = substr($strLen = (string)strlen($initialName), strlen((string)$strLen)-1, 1 );
            self::$name = $name = strtoupper(substr(sha1($initialName), 0, 20+intval($length)));
            session_name($name);
            session_start();
            self::$started = true;
        }
        return $this;
    }

    public function get($name) {
        return isset($_SESSION[$name])?$_SESSION[$name]:null;
    }

    public function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function remove($name) {
        unset($_SESSION[$name]);
    }

    public function getName() {
        return self::$name;
    }
}