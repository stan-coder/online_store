<?php

class groupsController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m',
            'js' => ['jquery-1.11.3.min.js', 'groupTabs.js', 'sha512.min.js']
        ],
        'getUsers' => [
            'url' => '/groups/ajax/getUsers',
            'ajax' => true
        ],
        'getInfo' =>[
            'url' => '/groups/ajax/getInfo',
            'ajax' => true
        ]
    ];
    private $validData = [0, 1],
            $sheet = [],
            $comments = [];

    /**
     * Group sheet
     */
    public function sheet() {
        $url = explode('/', implode($this->getMatchUrl()));
        if (!($group = $this->model('groups')->checkExistingGroup($groupId = end($url)))) {
            $this->setView('groupNotFound');
            return;
        }
        if (($entities = $this->model('sheet')->getListEntities($group['entity_id'], $this->session()->get('userEntityId'))) === false) {
            $this->setView('crushWhileLoadingGroup');
            return;
        }
        $types = array_unique(array_column($entities, 'entity_type'));
        $separated = array_map(function($el0) use($entities){
            return array_filter($entities, function($el1) use($el0) {
                return $el1['entity_type'] == $el0;
            });
        }, $types);
        $combined = array_combine($types, $separated);
        $allEntId = array_map(function($el) use($combined){
            return array_column($combined[$el], 'entity_id');
        }, $types);
        $li = [];
        array_walk_recursive($allEntId, function($el) use(&$li){
            $li[] = $el;
        });
        $plH = [];
        $plS = '';
        foreach ($li as $key => $l) {
            $plH[':e'.$key] = $l;
            $plS .= ':e'.$key.',';
        }
        $plH['enUserId'] = $this->session()->get('userEntityId');
        if (is_array($comments = $this->model('sheet')->getCommentsByEntitiesList(substr($plS, 0, -1), $plH)) && count($comments) > 0) {
            $this->prepareComments($comments);
        }
        foreach ($types as $key => $type) {
            $lId = $allEntId[$key];
            $qu = implode(',', array_fill(1, count($lId), '?'));
            if (!($data = $this->model('sheet')->getEntitiesListByType($type, $lId, $qu))) {exit('Here show error');}
            $typeEntity = $this->model('sheet')->getEntitiesByIndex($type-1);
            foreach ($lId as $enId) {
                call_user_func_array([$this, 'prepare'.ucfirst($typeEntity)], [$enId, $data, $entities]);
            }
        }
        ksort($this->sheet);
        $this->sheet = array_values($this->sheet);
        set(['groupId' => $group['entity_id'],
            'hash' => $hash = strtoupper(substr(hash('sha512', $this->model('customFunction')->getRandomString()), 0, 100))]);
        $this->setTitle($group['title']);
        $this->session()->set('ajaxHash', $hash);
    }

    /**
     * Prepare comments to array representation to include in rendered entity
     * @param $comArray
     */
    private function prepareComments($comArray) {
        // array_unique(array_column($comArray, 'parent_id'))
        $this->comments = [];
    }

    /**
     * Prepare publications to render on sheet
     * @param $enId
     * @param $data
     * @param $entities
     */
    private function preparePublications($enId, $data, $entities) {
        $tm = array_merge(array_filter($entities[$enId], function($el){
            return !is_null($el);
        }), ['content' => nl2br($data[$enId]['content'])]);
        $this->sheet[$tm['created']] = $tm;
    }

    /**
     * Prepare rePosts to render on sheet
     * @param $enId
     * @param $data
     * @param $entities
     */
    private function prepareReposts($enId, $data, $entities) {
        foreach($entities[$enId] as $key => $el) {
            if (in_array($key, ['entity_id', 'entity_type', 'created'])) {
                $entities[$enId][$key] = null;
            }
        }
        $extraInfo = array_filter($entities[$enId]);
        $filtered =  array_filter($data, function($el) use($enId){
            return $el['ent_sheet_id'] == $enId;
        });
        reset($filtered);
        $fr = current($filtered);
        $rp = [];
        $rp[] = [
            'description' => $fr['descr'],
            'entity_post_id' => $fr['ent_sheet_id'],
            'created' => $fr['created']];
        /**
         * Nested rePost
         */
        if (is_numeric($fr['en_sheet_id_sub_rp'])) {
            foreach ($filtered as $subRePost) {
                $tm = array_filter($subRePost, function($el) {
                    return !is_null($el);
                });
                array_walk($tm, function(&$el, $index){
                    $el = (in_array($index, ['ent_sheet_id', 'descr', 'created']) ? false : $el);
                });
                $rp[] = array_filter($tm);
            }
            /**
             * Retrieve original parent entity_id
             */
            $preOrigEnt = array_filter($rp, function($el){
                return isset($el['original_parent_en_id']);
            });
            $preOrigEnt = array_shift($preOrigEnt);
            if (!($origEnt = $this->model('sheet')->getEntitiesListByType($preOrigEnt['original_entity_sheet_type'], [$preOrigEnt['original_parent_en_id']], '?'))) {
                exit('Here show error');
            }
            $origEnt = end($origEnt);
            $de = ['original_entity_sheet_type', 'source_entity_id', 'source_uid', 'source_info', 'original_parent_en_id'];
            array_walk($rp, function(&$el) use($de, &$origEnt){
                if (isset($el[$de[0]])) {
                    foreach ($de as $e) {
                        $origEnt[$e] = $el[$e];
                        unset($el[$e]);
                    }
                }
            });
            array_pop($origEnt);
            array_push($rp, $origEnt);
            $rp[$mn = min(array_keys($rp))] = array_merge($rp[$mn], $extraInfo);
            $this->sheet[$fr['created']] = $rp;
        }
        /**
         * Simple rePost
         */
        else {
            $fr = array_filter($fr);
            if (!($origEnt = $this->model('sheet')->getEntitiesListByType($fr['original_entity_sheet_type_single'], [$fr['original_parent_en_id_single']], '?'))) {
                exit('Here show error');
            }
            $origEnt = end($origEnt);
            $de = ['source_info', 'source_uid', 'source_entity_id', 'original_entity_sheet_type_single', 'original_parent_en_id_single'];
            foreach ($de as $el) {
                $origEnt[$el] = $fr[$el];
                unset($fr[$el]);
            }
            $this->sheet[$fr['created']] = [current($rp), $origEnt];
        }
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
     * Get list of users belongs to group
     */
    public function getUsers() {
        $this->getJson(1, ['name', 'root']);
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