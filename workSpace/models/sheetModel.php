<?php

class SheetModel extends modelManager
{
    private $typeList = ['publications', 'reposts'];

    public function getListEntities($groupEntityId, $userEntityId){
        $sql = 'select t1.e_id entity_id, t1.e_type entity_type, t1.created created, l2.likes_count likes, notown.entity_user_id not_owner_entity_user_id, e2.reposts_count reposts,
          re2.reviews_count reviews, e5.comments_count comments, e5.total_comments_count total_comments
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
        return $this->db()->select($sql, [':g_id' => $groupEntityId, ':u_id' => $userEntityId]);
    }

    public function getEntitiesListByType($typeId, $listId, $qu) {
        $rm = new ReflectionMethod('SheetModel', 'get'.$this->typeList[$typeId-1]);
        return $rm->invoke(new SheetModel(), $listId, $qu);
    }

    public function getPublications($listId, $qu) {
        $sql = "select content from publications where entity_sheet_id in ({$qu})";
        return $this->db()->select($sql, $listId);
    }

    public function getReposts($listId, $qu) {

    }
}