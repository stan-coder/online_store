<?php

class ProfileModel extends modelManager
{
    public function checkFriendship($uId1, $uId2) {
        $sql = 'select exists(select entity_user_id1 from friends where entity_user_id1 = ? and entity_user_id2 = ? limit 1) as fExists';
        return (bool)array_filter($this->db()->selectOne($sql, [$uId1, $uId2]));
    }
}