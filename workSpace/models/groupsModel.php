<?php

class GroupsModel extends modelManager
{
    public function getInitialInfo($groupId, $userEntityId) {
        $sql = '
        select g.description descr, g.created created, gu2.users_count users_count, ga2.admins_count admins_count,
          count(t2.pr_id) as entities_count from groups g
        left join (select gu1.entity_group_id, count(gu1.entity_user_id) as users_count from groups_users gu1 group by gu1.entity_group_id) gu2 on g.entity_id = gu2.entity_group_id
        left join (
            select t1.pr_id pr_id from (
              (select e1.id e_id, e1.parent_id pr_id from entities e1)
                union all
              (select owr.entity_repost_id e_id, owr.entity_owner_id pr_id from owners_reposts owr)
            ) t1
            left join (select * from ignored_entities_by_users ie1 where ie1.entity_user_id = :userEntityId) ie2 on t1.e_id = ie2.entity_id
            where ie2.entity_id is null
          ) t2 on g.entity_id = t2.pr_id
        left join (select ga1.entity_group_id, count(ga1.entity_group_id) admins_count from groups_admins ga1 group by ga1.entity_group_id) ga2 on g.entity_id = ga2.entity_group_id
        where g.entity_id = :groupId
        group by g.entity_id
        limit 1';

        return $this->db()->selectOne($sql, [':groupId' => $groupId, ':userEntityId' => $userEntityId]);
    }

    public function checkExistingGroup($uid) {
        $sql = 'select entity_id from groups where uid = ? limit 1';
        return $this->db()->selectOne($sql, [$uid]);
    }
}