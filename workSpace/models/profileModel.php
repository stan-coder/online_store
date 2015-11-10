<?php

class ProfileModel extends modelManager
{
    public function checkFriendship($uId1, $uId2) {
        $sql = 'select exists(select entity_user_id1 from friends where (entity_user_id1 = :u1 and entity_user_id2 = :u2) or (entity_user_id1 = :u2 and entity_user_id2 = :u1) limit 1) as fExists';
        return (bool)array_filter(db::exec($sql, [':u1' => $uId1, ':u2' => $uId2]));
    }
}