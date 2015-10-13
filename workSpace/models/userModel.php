<?php

class UserModel extends modelManager
{
    public function createNewUser(Array $arguments) {
        $this->db(1)->exec('insert into `entities` (parent_id) value (null)');
        if (!($entityUserId = db::get()->lastInsertId())) {
            return false;
        }
        array_push($arguments, $entityUserId);
        $sql = "insert into `users` (`email`, `password`, `salt`, `routine_hash_code`, `routine_hash_code_expired`, `created`, `entity_id`) values (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), ?)";
        return (bool)$this->db()->exec($sql, $arguments);
    }

    public function getInitialInfoByEmail($email) {
        $sql = 'select `id`, `password`, `salt`, `is_active`, `is_confirmed`, `entity_id` from `users` where `email` = ? limit 1';
        return $this->db()->selectOne($sql, [$email]);
    }
}