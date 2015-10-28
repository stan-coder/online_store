<?php

class EntityController extends controllerManager
{
    public static $url = [
        'like' =>[
            'url' => '/entity/ajax/like',
            'ajax' => true
        ]
    ],
    $isAjax = true;
    private $userEntityId = null;

    public function preController() {
        if (empty($this->userEntityId = $this->session()->get('userEntityId'))) {
            $this->getJson(0, 0, 'There is no authorized user to perform such action');
        }
    }

    /**
     * Add like
     */
    public function like() {
        //sleep(2);
        $this->validAjaxData(['entityId', 'reject'], function($t){
            $entityId = $t->getData(0);
            if (!ctype_digit($entityId) || (int)$entityId < 1) return true;
            if (empty($res = $t->model('entity')->checkExistingEntityAndLikeByUser($entityId, $t->userEntityId)) || empty($res['entity_id'])) $t->getJson(0, 0, 'Unknown entity');
            if (is_numeric($res['entity_id']) && is_numeric($res['entity_id_user']) && !$t->getData(1)) $t->getJson(0, 0, 'Already marked');
            return false;
        });
        $args = ($this->getData(1) ? ['removeLike', 'removed'] : ['addLike', 'added']);
        $res = (call_user_func_array([$this->model('entity'), $args[0]], [$this->getData(0), $this->userEntityId]) === 1
            ? [1, 0, 'Like have been '.$args[1]]
            : [0, 0, 'Unknown error. Liked have not been '.$args[1]]);
        call_user_func_array([$this, 'getJson'], $res);
    }
}