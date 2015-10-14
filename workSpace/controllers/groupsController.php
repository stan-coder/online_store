<?php

class groupsController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m',
            'js' => ['jquery-1.11.3.min.js', 'groupTabs.js']
        ],
        'getInfo' =>[
            'url' => '/groups/ajax/getInfo',
            'ajax' => true
        ]
    ];
    private $validData = [0, 1];

    /**
     * Group sheet
     */
    public function sheet() {
        /*$url = explode('/', implode($this->getMatchUrl()));
        $group = $this->model('groups')->getInitialInfo(end($url), $this->session()->get('userEntityId'));
        if (empty($group)) {
            $this->setView('groupNotFound');
            return;
        }*/
        set('groupId', 1);
        set('hash', 'OWIMDSDKJ');
    }

    /**
     * Get info about group
     */
    public function getInfo() {
        $this->validAjaxData(['groupId', 'name'], function($t){
            return strlen($t->getData(1)) < 3 || !ctype_digit($t->getData(0));
        });
        $group = $this->model('groups')->getInitialInfo($_POST['groupId'], $this->session()->get('userEntityId'));
        print_r($group);
    }

    /**
     * Validating data that was transferred via ajax
     * @param $data
     * @param $validate
     */
    private function validAjaxData($data, $validate) {
        function get($message) {
            exit(json_encode(['success', false, 'message' => $message]));
        }
        if (!is_array($this->validData = $data) || !is_callable($validate)) {
            get(Config::$debug ? 'The validating data contains incorrect value' : 'Unknown error');
        }
        if ((new ReflectionFunction($validate))->invoke($this)) {
            get('Incorrect data');
        }
    }

    protected function getJson($success, $data) {
        echo json_encode(['success' => true, 'data' => $data]);
    }

    /**
     * Get ajax data that will be checked
     * @param $num
     * @return null
     */
    protected function getData($num) {
        return isset($this->validData[$num]) && isset($_POST[$this->validData[$num]]) ? $_POST[$this->validData[$num]] : null;
    }
}