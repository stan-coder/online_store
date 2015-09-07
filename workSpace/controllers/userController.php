<?php

class UserController extends controllerManager
{
    public static $url = [
        'registration' => [
            'url' => '/registration',
            'title' => 'Registration new user'],
        'login' => [
            'url' => '/login',
            'title' => 'Login in system',
            'css' => ['login']],
    ];

    public function registration() {

    }

    public function login()
    {

    }
}