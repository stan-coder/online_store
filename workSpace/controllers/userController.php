<?php

class UserController extends controllerManager
{
    public static $url = [
        'registration' => [
            'url' => '/registration',
            'title' => 'Registration new user',
            'js' => ['sha512.min.js', 'jquery-1.11.3.min.js', 'encodePassword.js']],
        'signIn' => [
            'url' => '/sign_in',
            'title' => 'Login in system',
            'css' => ['login']],
    ];

    public function registration() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $prepPsw = null;

            $this->formInit(['email', 'password', 'repeat_password'])
                 ->isEmpty()
                 ->valid(0, strlen($this->post('email')) > 50, 'Email must be less then or equal 50 symbols')
                 ->valid(0, !$this->model('customFunction')->checkCorrectEmail($this->post('email')), 'Email is not correct')
                 ->valid(0, $this->model('customFunction')->checkExistingEmail($this->post('email')), 'Email exists. Please, specify another')
                 ->valid(1, function($t) use(&$prepPsw) {
                     if (substr_count($password = $t->post('password'), '|') !== 1) return 1;
                     if (count($prepPsw = explode('|', $password)) !== 2) return 1;
                     if (strlen($prepPsw[0]) !== 128 || strlen($prepPsw[1]) !== 50) return 1;
                     return 0;
                 }, 'Password has incorrect value');

            if ($this->formIsValid()) {
                $salt = hash('sha512', $this->model('customFunction')->getRandomString());
                echo $this->post('password');
            }
        }
    }

    private function formInit($fields)
    {
        self::$formFields = $fields;
        $initialValues = array_map(function($element) {
            return ['message' => '', 'valid' => true, 'name' => $element];
        }, $fields);
        self::$formFieldsError = array_combine($fields, $initialValues);

        if (!empty($absence = array_diff($fields, array_keys($_POST)))) {
            array_walk($absence, function($item){
                $this->setFieldError($item, 'The field "'.ucfirst($item).'" is required');
            }, $this);
        }
        $this->post = array_map(function($element){
            return trim($element);
        }, $_POST);
        return $this;
    }

    private function valid($field, $condition, $message) {
        $field = current($this->getFieldByIndex($field));
        if ($field['valid']) {
            $condition = (is_callable($condition)) ? (new ReflectionFunction($condition))->invoke($this) : $condition;
            if ($condition) {
                $this->setFieldError($field['name'], $message);
            }
        }
        return $this;
    }

    private function isEmpty() {
        $fields = (count($fields = func_get_args()) > 0 ? $fields : range(0, count(self::$formFieldsError)-1));
        foreach ($fields as $value) {
            $key = key($this->getFieldByIndex($value));
            if (empty($this->post($key))) {
                $this->setFieldError($key, "The field \"{$key}\" cannot be empty");
            }
        }
        return $this;
    }

    public function signIn() {}
}