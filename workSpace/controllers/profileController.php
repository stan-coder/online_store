<?php

class profileController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/profile/\d{14}$~m',
            'js' => ['jquery-1.11.3.min.js', 'sha512.min.js', '/public/js/mvc/views/baseView.js', '/public/js/mvc/origin.js']
        ]];

    /**
     * User profile sheet
     */
    public function sheet() {
        $shCnt = $this->getController('sheet');
        if (!($profile = $shCnt->start('profile'))) return;
        if (ctype_digit($ui = $this->session()->get('userEntityId')) && !empty($profile['entity_id']) &&
            ((int)$profile['entity_id'] === (int)$ui || $this->model('profile')->checkFriendship($ui, $profile['entity_id']))) {
            set('addRecord', 1);
        }
        $shCnt->setAjaxToken(implode($profile));
    }
}