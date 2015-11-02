<?php

class groupsController extends controllerManager
{
    public static $url = [
        'sheet' => [
            'url' => '~^/group/\d{14}$~m',
            //'js' => ['jquery-1.11.3.min.js', 'control.js', 'groupTabs.js', 'sheet.js', 'sha512.min.js', 'entity.js', 'eventManager.js']
            'js' => ['jquery-1.11.3.min.js', 'sha512.min.js', '/public/js/mvc/views/baseView.js', '/public/js/mvc/origin.js']
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
    private $validData = [],
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
        $this->prepareComments($li);
        foreach ($types as $key => $type) {
            $lId = $allEntId[$key];
            $qu = implode(',', array_fill(1, count($lId), '?'));
            if (!($data = $this->model('sheet')->getEntitiesListByType($type, $lId, $qu))) {exit('Here show error');}
            $typeEntity = $this->model('sheet')->getEntitiesByIndex($type-1);
            foreach ($lId as $enId) {
                call_user_func_array([$this, 'prepare'.ucfirst($typeEntity)], [$enId, $data, $entities]);
            }
        }
        krsort($this->sheet);
        $this->sheet = array_values($this->sheet);
        $this->bindEntitiesAndComments();
        array_walk_recursive($this->sheet, function(&$el, $key){
            if (substr_count($key, 'created')) $el = date('d F Y h:i', strtotime($el));
        });
        set(['groupId' => $group['entity_id'],
            'hash' => $hash = strtoupper(substr(hash('sha512', $this->model('customFunction')->getRandomString().$this->session()->get('userSessionHash')), 0, 100)),
            'jsonSheet' => json_encode($this->sheet)]);
        $this->setTitle($group['title']);
        $this->session()->set('ajaxHash', $hash);
    }

    /**
     * Binding entities and comments
     */
    private function bindEntitiesAndComments() {
        $cm = $this->comments;
        $this->sheet = array_map(function($el) use($cm){
            $entId = (is_numeric($minKey = min(array_keys($el))) ? $el[$minKey]['entity_id'] : $el['entity_id']);
            if (isset($cm[$entId])) {
                $el['commentsArray'] = $cm[$entId];
            }
            return $el;
        }, $this->sheet);
    }

    /**
     * Prepare comments to array representation to include in rendered entity
     * @param $li
     * @return array
     */
    private function prepareComments($li) {
        $plH = [];
        $plS = '';
        foreach ($li as $key => $l) {
            $plH[':e'.$key] = $l;
            $plS .= ':e'.$key.',';
        }
        $plH['enUserId'] = $this->session()->get('userEntityId');
        if (!is_array($comments = $this->model('sheet')->getCommentsByEntitiesList(substr($plS, 0, -1), $plH)) && count($comments) > 0) {
            return [];
        }
        foreach (array_unique(array_column($comments, 'parent_id')) as $parentId) {
            $tmEl = array_filter($comments, function($el) use($parentId){
                return $el['parent_id'] == $parentId;
            });
            usort($tmEl, function ($a, $b) {
                if (($c1 = $a['created']) == ($c2 = $b['created'])) return 0;
                return ($c1 > $c2) ? -1 : 1;
            });
            $this->comments[$parentId] = array_values(array_map(function($el){
                unset($el['parent_id']);
                return array_filter($el);
            }, $tmEl));
        }
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
        }), ['content' => $data[$enId]['content']]);
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
            'content' => $fr['descr'],
            'entity_id' => $fr['ent_sheet_id'],
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
            if (!($origEnt = $this->model('sheet')->getEntitiesListByType($preOrigEnt['original_entity_sheet_type'], [$preOrigEnt['original_parent_en_id']], '?', 1))) {
                exit('Here show error');
            }
            $origEnt = end($origEnt);
            $de = ['original_entity_sheet_type', 'original_entity_created', 'source_entity_type', 'source_uid', 'source_info', 'original_parent_en_id'];
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
            $de = ['source_info', 'source_uid', 'source_entity_type', 'original_entity_created_single', 'original_entity_sheet_type_single', 'original_parent_en_id_single'];
            foreach ($de as $el) {
                $origEnt[$el] = $fr[$el];
                unset($fr[$el]);
            }
            unset($origEnt['original_parent_en_id_single']);
            $this->sheet[$fr['created']] = [current($rp), $origEnt];
        }
    }

    /**
     * Get info about group
     */
    public function getInfo() {
        $this->validAjaxData(['groupId'], function($t){
            return !ctype_digit($t->getData(0)) || (int)$t->getData(0) < 1;
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
}