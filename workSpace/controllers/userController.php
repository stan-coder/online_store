<?php

class UserController extends controllerManager
{
    public static $url = [
        'registration' => [
            'url' => '/registration',
            'title' => 'Registration new user'],
        'signIn' => [
            'url' => '/sign_in',
            'title' => 'Login in system',
            'css' => ['login']],
    ];

    public function registration() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty(array_diff(['email', 'password', 'repeat_password'], array_keys($_POST)))) {
                extract($_POST);

                echo 'aaaa';
            } else {
                echo 'Could';
            }
        }
    }

    public function signIn() {

    }
}