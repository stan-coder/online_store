<?php

class EntityModel extends modelManager
{
    public function checkExistingEntityAndLikeByUser($entityId, $entityUserId) {
        $sql = 'select e.id entity_id, l2.entity_id_user from entities e
            left join (select l1.entity_id, l1.entity_id_user from likes l1 where l1.entity_id = :entityId and l1.entity_id_user = :entityUserId) l2 on e.id = l2.entity_id
            where e.id = :entityId limit 1';
        return db::exec($sql, [':entityId' => $entityId, ':entityUserId' => $entityUserId]);
    }

    public function addLike($entityId, $entityUserId) {
        $sql = "insert into likes (`entity_id`, `entity_id_user`) value(?, ?)";
        return db::exec($sql, [$entityId, $entityUserId]);
    }

    public function removeLike($entityId, $entityUserId) {
        $sql = 'delete from likes where `entity_id` = ? and `entity_id_user` = ? limit 1';
        return db::exec($sql, [$entityId, $entityUserId]);
    }
}