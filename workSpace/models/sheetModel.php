<?php

class SheetModel extends modelManager
{
    private $entitiesTypeList = ['publications', 'reposts'];

    public function getListEntities($entityId, $userEntityId){
        $sql = "select
          t1.e_id entity_id,
          t1.e_type entity_type,
          t1.created created,
          l2.likes_count likes,
          concat(u.first_name, ' ', u.surname) as u_initials,
          u.uid u_uid,
          e2.reposts_count reposts,
          re2.reviews_count reviews,
          e5.comments_count comments,
          e5.total_comments_count total_comments,
          if (l3.entity_id_user = :u_id, 1, null) liked_by_cur_user,
          e6.reposted_by_cur_user reposted_by_cur_user
        from (
          ((select esh.entity_id e_id, esh.type_entity_id e_type, esh.created from entities e1
            inner join entities_sheet esh on e1.id = esh.entity_id
            where e1.parent_id = :e_id)
              union
            (select owr.entity_repost_id e_id, 2 as e_type, owr.created from owners_reposts owr where owr.entity_owner_id = :e_id)) as t1
          )
          left join (select entity_user_id, entity_id from ignored_entities_by_users ign where ign.entity_user_id = :u_id) as t2 on t1.e_id = t2.entity_id
          left join (select l1.entity_id, count(l1.entity_id) likes_count from likes l1 group by l1.entity_id) l2 on t1.e_id = l2.entity_id
          left join not_owners_created_entities notown on t1.e_id = notown.entity_id
          left join users u on notown.entity_user_id = u.entity_id
          left join
          (select e3.parent_id, count(e3.id) as reposts_count from entities e3
            inner join reposts r1 on e3.id = r1.entity_sheet_id group by e3.parent_id) e2 on t1.e_id = e2.parent_id
          left join (select re1.entity_id, count(re1.entity_id) as reviews_count from reviews_entities re1 group by re1.entity_id) re2 on t1.e_id = re2.entity_id
          left join (select e4.parent_id, count(c.entity_id) as comments_count, count(c.entity_id)+sum(sc.children_count) as total_comments_count from comments c
            inner join entities e4 on c.entity_id = e4.id
            left join sub_comments_total_count sc on c.entity_id = sc.entity_parent_comment_id
          group by e4.parent_id) as e5 on e5.parent_id = t1.e_id
          left join likes l3 on t1.e_id = l3.entity_id and l3.entity_id_user = :u_id
          left join (select e7.parent_id, 1 as reposted_by_cur_user from entities e7
            inner join entities_sheet esh0 on e7.id = esh0.entity_id and esh0.type_entity_id = 2
            inner join owners_reposts owr5 on esh0.entity_id = owr5.entity_repost_id and entity_owner_id = :u_id) e6 on t1.e_id = e6.parent_id
        where t2.entity_id is null
        order by t1.created, t1.e_id desc limit 10";
        return $this->replaceKeys($this->db()->select($sql, [':e_id' => $entityId, ':u_id' => $userEntityId]), 'entity_id');
    }

    public function getEntitiesListByType($typeId, $listId, $qu) {
        $args = [$listId, $qu];
        if (count(func_get_args())===4) array_push($args, 1);
        return call_user_func_array([$this, 'get'.$this->getEntitiesByIndex($typeId-1)], $args);
    }

    public function getPublications($listId, $qu) {
        $sql[0] = "select entity_sheet_id, content from publications where entity_sheet_id in ({$qu})";
        $sql[1] = "select p.entity_sheet_id entity_sheet_id, p.content content, concat(u.first_name, ' ', u.surname) u_initials, u.uid u_uid from publications p
            left join not_owners_created_entities nown on p.entity_sheet_id = nown.entity_id
            left join users u on nown.entity_user_id = u.entity_id
            where entity_sheet_id in ({$qu})";
        $res = $this->replaceKeys($this->db()->select($sql[(count(func_get_args())===3?1:0)], $listId), 'entity_sheet_id');
        if (count($res)===1) {
            $key = current(array_keys($res));
            $res[$key] = array_filter($res[$key]);
        }
        return $res;
    }

    public function getReposts($listId, $qu) {
        $sql = "select
          r1.entity_sheet_id ent_sheet_id,
          r1.description descr,
          r1.created created,
          r2.entity_sheet_id en_sheet_id_sub_rp,
          r2.created en_created_sub_rp,
          r2.description descr_sub_rp,
          owr.entity_owner_id en_owner_id_sub_rp,
          t3.uid owner_uid,
          t3.info owner_info,
          t3.e_type owner_type,
          t1.parent_id original_parent_en_id_single,
          t1.type_entity_id original_entity_sheet_type_single, -- from entities_sheet.type_entity_id
          t1.created original_entity_created_single,
          e4.parent_id original_parent_en_id, -- first publication or other entity
          esh1.type_entity_id original_entity_sheet_type, --  from entities_sheet.type_entity_id
          esh1.created original_entity_created,
          t4.e_type source_entity_type,
          t4.uid source_uid,
          t4.info source_info
        from reposts r1
          left join reposts_parents_trees rpt1 on r1.entity_sheet_id = rpt1.entity_repost_id
          left join reposts r2 on rpt1.entity_parent_id = r2.entity_sheet_id
          left join owners_reposts owr on owr.entity_repost_id = r2.entity_sheet_id
          left join
            (select e2.id id, e2.parent_id parent_id, esh.type_entity_id, esh.created from entities e2 inner join entities_sheet esh on e2.parent_id = esh.entity_id) t1 on
              t1.id = r1.entity_sheet_id and rpt1.entity_repost_id is null
          left join packed_general_entities t3 on owr.entity_owner_id = t3.entity_id
          left join
            (select rpt3.entity_repost_id, min(rpt3.entity_parent_id) min_parent from reposts_parents_trees rpt3 group by rpt3.entity_repost_id) t2 on
              rpt1.entity_parent_id = t2.min_parent and r1.entity_sheet_id = t2.entity_repost_id
          left join entities e4 on t2.min_parent = e4.id
          left join entities e5 on e4.parent_id = e5.id or t1.parent_id = e5.id
          left join packed_general_entities t4 on t4.entity_id = e5.parent_id
          left join entities_sheet esh1 on e4.parent_id = esh1.entity_id
        where r1.entity_sheet_id in ({$qu})
        order by r1.created desc";
        return $this->db()->select($sql, $listId);
    }

    public function getEntitiesByIndex($index) {
        return isset($this->entitiesTypeList[$index])?$this->entitiesTypeList[$index]:null;
    }

    private function replaceKeys($array, $field) {
        return is_array($array) && count($array) > 0 ? array_combine(array_column($array, $field), $array) : [];
    }

    public function getCommentsByEntitiesList($placeholdersId, $data) {
        $sql = "select
          e1.parent_id,
          c1.entity_id en_id,
          c1.entity_user_id user_owner_en_id,
          c1.content content,
          c1.created created,
          count(c1.entity_id) as count_of_child_comments,
          l2.cn count_of_likes,
          if (l3.entity_id_user = :enUserId, 1, null) as is_liked_by_current_user,
          if (nvw.entity_user_id = :enUserId, 1, null) as not_viewed_by_user,
          concat(u.first_name, ' ', u.surname) userInit
        from comments c1
        inner join entities e1 on c1.entity_id = e1.id
        left join entities e2 on c1.entity_id = e2.parent_id
        left join (select l1.entity_id, count(l1.entity_id) as cn from likes l1 group by l1.entity_id) l2 on c1.entity_id = l2.entity_id
        left join likes l3 on c1.entity_id = l3.entity_id and l3.entity_id_user = :enUserId
        left join ignored_entities_by_users ign on c1.entity_id = ign.entity_id and ign.entity_user_id = :enUserId
        left join not_viewed_new_comments_by_users nvw on c1.entity_id = nvw.entity_comment_id and nvw.entity_user_id = :enUserId
        left join users u on c1.entity_user_id = u.entity_id
        where e1.parent_id in ({$placeholdersId}) and ign.entity_id is null
        group by c1.entity_id
        order by c1.created desc";
        return $this->db()->select($sql, $data);
    }

    public function addPublication($sheetEntityId, $content, $notOwner) {
        try {
            db::get()->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            db::get()->beginTransaction();

            $sql = 'insert into entities (`parent_id`) value (?)';
            $this->db()->exec($sql, [$sheetEntityId]);
            $publicationId = db::get()->lastInsertId();
            $created = $this->addEntityToEntitiesSheet($publicationId, 1);
            $result = ['pId' => $publicationId, 'created' => date('d F Y h:i', strtotime($created))];
            $sql = 'insert into publications (`entity_sheet_id`, `content`) value (?, ?)';
            $this->db()->exec($sql, [$publicationId, $content]);

            if ($notOwner) {
                $sql = 'insert into not_owners_created_entities (`entity_user_id`, `entity_id`) value (?, ?)';
                $this->db()->exec($sql, [$notOwner, $publicationId], true);
                $result['suggestedBy'] = $this->model('session')->get('userInitials');
            }
        } catch (Exception $ex) {
            db::get()->rollBack();
            return false;
        }
        db::get()->commit();
        return $result;
    }

    private function addEntityToEntitiesSheet($entityId, $typeEntityId) {
        $sql = 'insert into entities_sheet (`entity_id`, `type_entity_id`, `created`) value (?, ?, ?)';
        if (!((bool)$this->db()->exec($sql, [$entityId, $typeEntityId, $created = date('Y-m-d h:i:s')]))) {
            return false;
        }
        return $created;
    }

    public function checkPermissionForGroup($userEntityId, $groupEntityId) {
        $sql = 'select
          (exists(select * from groups_admins ga where ga.entity_user_id = :u_id and ga.entity_group_id = :g_id))
            is_admin,
          (exists(select * from groups_users gu where gu.entity_user_id = :u_id and gu.entity_group_id = :g_id))
            is_user';
        return $this->db()->selectOne($sql, [':u_id' => $userEntityId, ':g_id' => $groupEntityId]);
    }

    public function checkExistingSheetEntity($searchCriteria, $findBy = 0) {
        $sql = 'select pge.entity_id, pge.uid, pge.info, pge.e_type from packed_general_entities pge where pge.'.(!$findBy?'uid':'entity_id').' = ? limit 1';
        return $this->db()->selectOne($sql, [$searchCriteria]);
    }
}