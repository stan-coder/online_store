<?php

class groupsController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m',
            'js' => ['jquery-1.11.3.min.js', 'groupTabs.js', 'sha512.min.js']
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
        $url = explode('/', implode($this->getMatchUrl()));
        if (!($group = $this->model('groups')->checkExistingGroup(end($url)))) {
            $this->setView('groupNotFound');
            return;
        }
        set('groupId', $group['entity_id']);
        set('hash', $hash = strtoupper(substr(hash('sha512', $this->model('customFunction')->getRandomString()), 0, 100)));
        $this->session()->set('ajaxHash', $hash);
    }

    /**
     * Get info about group
     */
    public function getInfo() {
        $this->validAjaxData(['groupId', 'name'], function($t){
            return strlen($t->getData(1)) < 3 || !ctype_digit($t->getData(0));
        });
        $groupInfo = $this->model('groups')->getInitialInfo($_POST['groupId'], $this->session()->get('userEntityId'));
        $rt = !$groupInfo ? [0, 0, 'Group not found'] : [1, $groupInfo, 0];
        $this->getJson($rt[0], $rt[1], $rt[2]);
    }

    /**
     * Validating data that was transferred via ajax
     * @param $data
     * @param $validate
     */
    private function validAjaxData($data, $validate) {
        if (count($hs = array_keys(array_filter(array_flip($_POST), function($e){
            return in_array($e, ['hash', 'salt']);
        }))) !== 2 || substr(hash('sha512', $this->session()->get('ajaxHash') . $hs[1]), 0, 50) !== (string)$hs[0] ) {
            $this->getJson(0, 0, 'Incorrect request');
        }
        if (!is_array($this->validData = $data) || !is_callable($validate)) {
            $this->getJson(0, 0, Config::$debug ? 'The validating data contains incorrect value' : 'Unknown error');
        }
        if ((new ReflectionFunction($validate))->invoke($this)) {
            $this->getJson(0, 0, 'Incorrect data');
        }
    }

    /**
     * Return encoded json result
     * @param $success
     * @param $data
     * @param null $message
     */
    protected function getJson($success, $data, $message = null) {
        exit(json_encode(array_merge(['success' => (bool)$success], is_array($data) ? ['data' => $data] : [], is_string($message) ? ['message' => $message] : [])));
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