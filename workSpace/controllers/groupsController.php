<?php

class groupsController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m'
        ]
    ];

    /**
     * Group sheet
     */
    public function sheet() {
        $url = explode('/', implode($this->getMatchUrl()));
        $group = $this->model('groups')->getInitialInfo(end($url), $this->session()->get('userEntityId'));
        if (empty($group)) {
            $this->setView('groupNotFound');
            return;
        }

    }
}