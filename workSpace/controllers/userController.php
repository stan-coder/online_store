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

    }

    public function signIn() {

    }
}