<?php

class UserModel extends modelManager
{
    public function createNewUser(Array $arguments) {
        $sql = "insert into `users` (`email`, `password`, `salt`, `routine_hash_code`, `routine_hash_code_expired`, `created`) values (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 3 DAY), NOW())";
        return (bool)$this->db()->exec($sql, $arguments);
    }

    public function getInitialInfoByEmail($email) {
        $sql = 'select `id`, `password`, `salt`, `is_active`, `is_confirmed` from `users` where `email` = ? limit 1';
        return $this->db()->selectOne($sql, [$email]);
    }
}