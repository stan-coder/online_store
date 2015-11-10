<?php

class UserModel extends modelManager
{
    public function createNewUser($arguments) {
        db::executeWithinTransaction(function() use($arguments){
            db::exec('insert into `entities` (parent_id) value (null)');
            array_push($arguments, db::getInsertedId());
            $sql = "insert into `users` (`email`, `password`, `salt`, `routine_hash_code`, `routine_hash_code_expired`, `created`, `entity_id`) values (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 3 DAY), NOW(), ?)";
            db::exec($sql, $arguments);
        });
    }

    public function getInitialInfoByEmail($email) {
        $sql = "select `id`, `password`, `salt`, `is_active`, `is_confirmed`, `entity_id`, concat(`first_name`, ' ', `surname`) as initials from `users` where `email` = ? limit 1";
        return db::exec($sql, $email);
    }
}