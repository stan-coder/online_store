<?php

class sheetController extends controllerManager
{
    public static $url = [
        'addPublication' => [
            'url' => '/sheet/ajax/addPublication',
            'ajax' => true
        ]];

    private $sheet = [],
            $comments = [];

    /**
     * Start action
     * @param $entity
     */
    public function start($entity) {
        $url = explode('/', implode($this->getMatchUrl()));
        if (!($_entity = $this->model('sheet')->checkExistingSheetEntity(end($url)))) {
            $this->errorLoading($entity);
            return;
        }
        if (($entities = $this->model('sheet')->getListEntities($_entity['entity_id'], $this->session()->get('userEntityId'))) === false) {
            $this->errorLoading($entity, 1);
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
        set(['sheetEntityId' => $_entity['entity_id'],
            'jsonSheet' => json_encode($this->sheet)]);
        $this->setTitle(ucfirst($entity).': '.$_entity['info']);
        return $_entity;
    }

    /**
     * Error loading
     * @param $entity
     * @param int $template
     */
    protected function errorLoading($entity, $template = 0) {
        $n = !$template ? [' not found', 'entityNotFound'] : [' cannot be loaded', 'crushWhileLoadingEntity'];
        set('entity', $entity);
        $this->setTitle(ucfirst($entity).$n[0]);
        $this->setView($n[1]);
    }

    /**
     * Set ajax token
     * @param $entityInfo
     */
    public function setAjaxToken($entityInfo) {
        set(['salt' => $salt = $this->model('customFunction')->getHashChunkUpperCase($this->model('customFunction')->getRandomString()),
            'token' => $this->model('customFunction')->getHashChunkUpperCase($this->session()->get('userSessionHash').$entityInfo.$salt)]);
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
     * Add publication
     * 0 - user sheet, 1 - group
     */
    public function addPublication() {
        /**
         * Добавить проверку на авторизацию
         */
        $uEntId = $this->session()->get('userEntityId');
        $this->validAjaxData(['content'], function($t){
            return empty($t->getData(0));
        });

        $sei = $this->data['sheetEntityInfo'];
        $errorMessage = 'You have not permission to add publication in this group';
        $notOwner = true;

        if ((int)$sei['e_type'] === 2) {
            if ($uEntId == $sei['entity_id']) $notOwner = false;
            elseif (!$this->model('profile')->checkFriendship($sei['entity_id'], $this->data['sheetEntityId'])) $this->getJson(0, 0, $errorMessage);
        } else {
            $res = $this->model('sheet')->checkPermissionForGroup($uEntId, $sei['entity_id']);
            if (!is_array($res) || count($res) < 1 || empty(array_filter($res))) $this->getJson(0, 0, $errorMessage);
            elseif (implode($res) == '10') $notOwner = false;
        }

        $res = $this->model('sheet')->addPublication($this->data['sheetEntityId'], htmlspecialchars(strip_tags($this->getData(0))), $notOwner?/*$uEntId*/1292:false);
        call_user_func_array([$this, 'getJson'],
            !is_numeric($res['pId']) || empty($res['created']) ? [0, 0, 'Publication not added'] :
            [1, $res, 0]);
    }
}