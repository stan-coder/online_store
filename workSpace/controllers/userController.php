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

            $this->requireFields(['email', 'password', 'repeat_password']);
            extract($_POST);

            if ($this->formIsValid()) {
                echo 'All valid';
            }
            /*if (1!=1) {
                extract($_POST);

                echo 'aaaa';
            } else {
                echo 'Could';
            }*/
        }
    }

    private function requireFields($array)
    {
        $initialValues = array_map(function(){
            return ['message' => '', 'valid' => true];
        }, range(0, count($array)-1));
        self::$fieldError = array_combine($array, $initialValues);

        if (!empty($absence = array_diff($array, array_keys($_POST)))) {
            array_walk($absence, function($item){
                $this->setFieldError($item, 'The field "'.ucfirst($item).'" is required');
            }, $this);
        }
    }

    public function signIn() {

    }
}