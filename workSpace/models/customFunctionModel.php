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

    public function getRandomString($length) {
        $hash = '';
        for ($c=0; $c<$length; $c++) {
            $hash .= chr(mt_rand(35, 126));
        }
        return $hash;
    }

    public function checkCorrectEmail($email) {
        return preg_match('~^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$~', $email);
    }
}