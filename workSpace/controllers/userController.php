<?php

class UserController extends controllerManager
{
    public static $url = [
        'registration' => [
            'url' => '/registration',
            'title' => 'Registration new user',
            'js' => ['sha512.min.js', 'jquery-1.11.3.min.js', 'encodePasswordRegistration.js']],
        'signIn' => [
            'url' => '/sign_in',
            'title' => 'Login in system',
            'css' => ['login.css'],
            'js' => ['sha512.min.js', 'jquery-1.11.3.min.js', 'encodePasswordSignIn.js']],
        'signOut' => [
            'url' => '/sign_out'
        ],
        'profile' => [
            'url' => '/profile'
        ]
    ],
    $noView = ['signOut'];

    /**
     * Registration new user
     */
    public function registration() {
        if ($this->isPost()) {
            $psw = null;

            $this->formInit(['email', 'password', 'repeat_password'])
                 ->isEmpty()
                 ->valid(0, strlen($this->post('email')) > 50, 'Email must be less then or equal 50 symbols')
                 ->valid(0, !$this->model('customFunction')->checkCorrectEmail($this->post('email')), 'Email is not correct')
                 ->valid(0, $this->model('customFunction')->checkExistingEmail($this->post('email')), 'Email exists. Please, specify another')
                 ->valid(1, function($t) use(&$psw) {
                     if (substr_count($password = $t->post('password'), '|') !== 1) return 1;
                     if (count($psw = explode('|', $password)) !== 2) return 1;
                     if (strlen($psw[0]) !== 128 || strlen($psw[1]) !== 50) return 1;
                     if (!$t->model('customFunction')->checkCorrectHash(implode($psw))) return 1;
                     return 0;
                 }, 'Password has incorrect value');

            if ($this->formIsValid()) {
                $salt = hash('sha512', $this->model('customFunction')->getRandomString());
                $preparedPassword = $this->model('customFunction')->getMultiplePasswordEncode($psw[0], $salt).$psw[1];
                $confirmCode = hash('sha512', $this->model('customFunction')->getRandomString());
                $isCreated = $this->model('user')->createNewUser([
                    $this->post('email'),
                    $preparedPassword,
                    $salt,
                    $confirmCode,
                ]);
                if (!$isCreated) {
                    $this->setView('failRegistration');
                    return;
                } else {
                    $this->model('customFunction')->sendConfirmationLink($this->post('email'), $confirmCode);
                    $this->session()->set('registerComplete', true);
                    $this->redirect('/sign_in');
                }
            }
        }
        set('typedEmail', $this->model('customFunction')->getTypedField('email'));
    }

    /**
     * Sign In
     */
    public function signIn() {
        if ($this->isPost()) {

            $this->formInit(['email', 'password'])
                 ->isEmpty()
                 ->valid(0, strlen($this->post('email')) > 50, 'Email must be less then or equal 50 symbols')
                 ->valid(0, !$this->model('customFunction')->checkCorrectEmail($this->post('email')), 'Email is not correct')
                 ->valid(1, strlen($psw = $this->post('password')) !== 128 || !$this->model('customFunction')->checkCorrectHash($psw), 'Password has incorrect value');

                $valid = [
                ['Wrong email or password', empty($init = $this->model('user')->getInitialInfoByEmail($this->post('email')))],
                ['Your account is blocked', function() use($init){return !$init['is_active'];}],
                ['Your did not confirm your email.<br/>If you did not receive message pass this <a href="#">link</a>', function() use($init){return !$init['is_confirmed'];}],
                ['Wrong email or password', function($t) use($init){
                    $hash = $this->model('customFunction')->getMultiplePasswordEncode(hash('sha512', $t->post('password').substr($init['password'], 128)), $init['salt']);
                    if ($hash !== substr($init['password'], 0, 128)) return 1;
                    return 0;
                }],
                ['There is a unknown reason that do not allow you sign in. Try later, please.', function($t) use($init){
                    if (!$t->model('auth')->authorization($init['id'], $init['entity_id'], $init['initials'], $t->post('email'), (int)$t->post('rememberMe'))) return 1;
                    $this->redirect('/');
                }]
            ];
            foreach ($valid as $el) {
                if ( is_callable($el[1])?$el[1]($this):$el[1] ) {
                    $em = $el[0];
                    break;
                }
            }
        }
        $rc = false;
        if ($this->session()->get('registerComplete')) {
            $this->session()->remove('registerComplete');
            $rc = true;
        }
        set('registerComplete', $rc);
        set('errorMessage', (isset($em)?$em:''));
        set('typedEmail', $this->model('customFunction')->getTypedField('email'));
    }

    /**
     * Sign out
     */
    public function signOut() {
        if ($this->model('auth')->hasSessionUserData($ui = $this->session()->get('userId'), $h = $this->session()->get('userSessionHash'))) {
            $this->model('auth')->signOut($ui, $h);
        }
    }

    /**
     * User profile
     */
    public function profile() {

    }
}