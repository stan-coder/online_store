<?php

class CustomFunctionModel extends modelManager
{
    public function getIp() {
        foreach (['X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR'] as $key) {
            if (isset($_SERVER[$key])) return $_SERVER[$key];
        }
        return 'Undefined IP';
    }

    public function getRandomString($length = 100) {
        $string = '';
        for ($c=0; $c<$length; $c++) {
            $string .= chr(mt_rand(35, 126));
        }
        return $string;
    }

    public function getHashChunkUpperCase($string, $length = 50) {
        return strtoupper(substr(hash('sha512', $string), 0, $length));
    }

    public function checkCorrectEmail($email) {
        return preg_match('~^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$~', $email);
    }

    public function checkExistingEmail($email) {
        return db::exec('select id from users where email = :email limit 1', [':email' => $email]);
    }

    public function getMultiplePasswordEncode($password, $salt) {
        $hash = '';
        for ($h=0; $h<50000; $h++) {
            $hash = hash('sha512', $hash.$password.$salt.Config::$secretKey);
        }
        return $hash;
    }

    public function checkCorrectHash($hash) {
        return count_chars(strtolower($hash), 3) === implode(array_merge(range(0, 9), range('a', 'f')));
    }

    public function getTypedField($nameField) {
        return (isset($_POST[$nameField])) ? htmlspecialchars(strip_tags($_POST[$nameField])) : '';
    }

    public function sendConfirmationLink($email, $hash) {
        return true;
    }

    public function getServerVariable($key) {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }
}