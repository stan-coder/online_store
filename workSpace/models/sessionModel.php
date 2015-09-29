<?php

class SessionModel extends modelManager
{
    private static $started = false;

    private function start() {
        if (!self::$started) {
            $initialName = $this->model('customFunction')->getIp() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . Config::$secretKey;
            $name = strtoupper(substr(sha1($initialName), 0, 25));
            session_name($name);
            session_start();
            self::$started = true;
        }
    }

    public function get($name) {
        $this->start();
        return isset($_SESSION[$name])?$_SESSION[$name]:null;
    }

    public function set($name, $value) {
        $this->start();
        $_SESSION[$name] = $value;
    }
}