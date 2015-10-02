<?php

class SessionModel extends modelManager
{
    public $name;
    private static $started = false;

    public function start() {
        if (!self::$started) {
            $initialName = $this->model('customFunction')->getIp() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $_SERVER['HTTP_HOST'] . Config::$secretKey;
            $length = substr($strLen = (string)strlen($initialName), strlen((string)$strLen)-1, 1 );
            $this->name = $name = strtoupper(substr(sha1($initialName), 0, 20+intval($length)));
            session_name($name);
            session_start();
            self::$started = true;
        }
        return $this;
    }

    public function get($name) {
        $this->start();
        return isset($_SESSION[$name])?$_SESSION[$name]:null;
    }

    public function set($name, $value) {
        $this->start();
        $_SESSION[$name] = $value;
    }

    public function remove($name) {
        $this->start();
        unset($_SESSION[$name]);
    }
}