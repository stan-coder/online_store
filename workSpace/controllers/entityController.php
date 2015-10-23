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
        $this->validAjaxData(['entityId'], function($t){
            $entityId = $t->getData(0);
            if (!ctype_digit($entityId) || (int)$entityId < 1) return true;
            if (empty($res = $t->model('entity')->checkExistingEntityAndLikeByUser($entityId, $t->userEntityId)) || empty($res['entity_id'])) $t->getJson(0, 0, 'Unknown entity');
            if (is_numeric($res['entity_id']) && is_numeric($res['entity_id_user'])) $t->getJson(0, 0, 'Already marked');
            return false;
        });
        $res = ($this->model('entity')->addNewLike($this->getData(0), $this->userEntityId) === 1 ? [1, 0, 'Like added'] : [0, 0, 'Unknown error. Liked not added']);
        call_user_func_array([$this, 'getJson'], $res);
    }
}