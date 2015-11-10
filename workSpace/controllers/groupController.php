<?php

class groupController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m',
            'js' => ['jquery-1.11.3.min.js', 'sha512.min.js', '/public/js/mvc/views/baseView.js', '/public/js/mvc/origin.js']
        ],
        'getUsers' => [
            'url' => '/group/ajax/getUsers',
            'ajax' => true
        ],
        'getInfo' =>[
            'url' => '/group/ajax/getInfo',
            'ajax' => true
        ]
    ];

    /**
     * Group sheet
     */
    public function sheet() {
        $shCnt = $this->getController('sheet');
        if (empty($entity = $shCnt->start('group'))) return;
        if (is_numeric($gi = $entity['entity_id']) && is_numeric($ui = $this->session()->get('userEntityId'))) {
            $permission = $this->model('sheet')->checkPermissionForGroup($ui, $gi);
            if (strpos(implode('', array_values($permission)), '1') !== false) set('addRecord', true);
        }
        $shCnt->setAjaxToken(implode($entity));
    }

    /**
     * Get info about group
     */
    public function getInfo() {
        $this->validAjaxData(['groupId'], function($t){
            return !ctype_digit($t->getData(0)) || (int)$t->getData(0) < 1;
        });
        $groupInfo = $this->model('group')->getInitialInfo($_POST['groupId'], $this->session()->get('userEntityId'));
        $rt = !$groupInfo ? [0, 0, 'Group not found'] : [1, $groupInfo, 0];
        $this->getJson($rt[0], $rt[1], $rt[2]);
    }

    /**
     * Get list of users belongs to group
     */
    public function getUsers() {
        $this->getJson(1, ['name', 'root']);
    }
}