<?php

class CustomFunctionModel extends modelManager
{
    public function getIp() {
        foreach (['X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR'] as $key) {
            if (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getRandomString($length = 100) {
        $string = '';
        for ($c=0; $c<$length; $c++) {
            $string .= chr(mt_rand(35, 126));
        }
        return $string;
    }

    public function checkCorrectEmail($email) {
        return preg_match('~^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$~', $email);
    }

    public function checkExistingEmail($email) {
        return (bool)$this->db()->selectOne('select id from users where email = :email limit 1', [':email' => $email]);
    }

    public function getMultiplePasswordEncode() {

    }
}