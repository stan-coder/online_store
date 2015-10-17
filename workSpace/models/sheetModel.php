<?php

class SheetModel extends modelManager
{
    private $entitiesTypeList = ['publications', 'reposts'];

    public function getListEntities($groupEntityId, $userEntityId){
        $sql = 'select
          t1.e_id entity_id,
          t1.e_type entity_type,
          t1.created created,
          l2.likes_count likes,
          notown.entity_user_id not_owner_entity_user_id,
          u.first_name u_f_name,
          u.surname u_surname,
          u.uid u_uid,
          e2.reposts_count reposts,
          re2.reviews_count reviews,
          e5.comments_count comments,
          e5.total_comments_count total_comments
        from (
          ((select esh.entity_id e_id, esh.type_entity_id e_type, esh.created from groups g
            left join entities e1 on g.entity_id = e1.parent_id
            left join entities_sheet esh on e1.id = esh.entity_id
          where g.entity_id = :g_id)
            union
            (select owr.entity_repost_id e_id, 2 as e_type, owr.created from groups g
            left join owners_reposts owr on g.entity_id = owr.entity_owner_id
            where g.entity_id = :g_id)) as t1
          )
          left join (select entity_user_id, entity_id from ignored_entities_by_users ign where ign.entity_user_id = :u_id) as t2 on t1.e_id = t2.entity_id
          left join (select l1.entity_id, count(l1.entity_id) likes_count from likes l1 group by l1.entity_id) l2 on t1.e_id = l2.entity_id
          left join not_owners_created_entities notown on t1.e_id = notown.entity_id
          left join users u on notown.entity_user_id = u.entity_id
          left join (
            select e3.parent_id, count(e3.id) as reposts_count from entities e3
            left join reposts r1 on e3.id = r1.entity_sheet_id where r1.entity_sheet_id is not null group by e3.parent_id) e2 on t1.e_id = e2.parent_id
          left join (select re1.entity_id, count(re1.entity_id) as reviews_count from reviews_entities re1 group by re1.entity_id) re2 on t1.e_id = re2.entity_id
          left join (select e4.parent_id, count(c.entity_id) as comments_count, count(c.entity_id)+sum(sc.children_count) as total_comments_count from comments c
            left join entities e4 on c.entity_id = e4.id
            left join sub_comments_total_count sc on c.entity_id = sc.entity_parent_comment_id
          group by e4.parent_id) as e5 on e5.parent_id = t1.e_id
          where t2.entity_id is null
          order by t1.created, t1.e_id limit 8';
        return $this->replaceKeys($this->db()->select($sql, [':g_id' => $groupEntityId, ':u_id' => $userEntityId]), 'entity_id');
    }

    public function getEntitiesListByType($typeId, $listId, $qu) {
        return call_user_func_array([$this, 'get'.$this->getEntitiesByIndex($typeId-1)], [$listId, $qu]);
    }

    public function getPublications($listId, $qu) {
        $sql = "select entity_sheet_id, content from publications where entity_sheet_id in ({$qu})";
        return $this->replaceKeys($this->db()->select($sql, $listId), 'entity_sheet_id');
    }

    public function getReposts($listId, $qu) {
        $sql = "select
          r1.entity_sheet_id ent_sheet_id,
          r1.description descr,
          r1.created created,
          r2.entity_sheet_id en_sheet_id_sub_rp,
          r2.description descr_sub_rp,
          owr.entity_owner_id en_owner_id_sub_rp,
          t3.uid owner_uid,
          t3.info owner_info,
          t3.e_type owner_type,
          t1.parent_id original_parent_en_id_single,
          t1.type_entity_id original_entity_sheet_type_single, -- from entities_sheet.type_entity_id
          e4.parent_id original_parent_en_id, -- first publication or other entity
          esh1.type_entity_id original_entity_sheet_type, --  from entities_sheet.type_entity_id
          t4.entity_id source_entity_id,
          t4.uid source_uid,
          t4.info source_info
        from reposts r1
          left join reposts_parents_trees rpt1 on r1.entity_sheet_id = rpt1.entity_repost_id
          left join reposts r2 on rpt1.entity_parent_id = r2.entity_sheet_id
          left join owners_reposts owr on owr.entity_repost_id = r2.entity_sheet_id
          left join
            (select e2.id id, e2.parent_id parent_id, esh.type_entity_id from entities e2 left join entities_sheet esh on e2.parent_id = esh.entity_id) t1 on
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
        order by r1.created asc";
        return $this->db()->select($sql, $listId); // {$qu}
    }

    public function getEntitiesByIndex($index) {
        return isset($this->entitiesTypeList[$index])?$this->entitiesTypeList[$index]:null;
    }

    private function replaceKeys($array, $field) {
        return is_array($array) && count($array) > 0 ? array_combine(array_column($array, $field), $array) : [];
    }
}