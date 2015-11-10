<?php

class AuthModel extends modelManager
{
    public function authorization($userId, $expire, $hash) {
        $sql = "call insert_session_and_if_exists_remove_obsolete(?, ?, ?);";
        return db::exec($sql, [$userId, $hash, $expire]);
    }

    public function hasSessionUserData($userId, $hash) {
        return (is_numeric($userId) &&
            $userId > 0 &&
            !is_null($hash) &&
            strlen((string)$hash) === 128 &&
            $this->model('customFunction')->checkCorrectHash($hash));
    }

    public function checkCredential() {
        if (!$this->hasSessionUserData($ui = $this->model('session')->get('userId'), $h = $this->model('session')->get('userSessionHash'))) return 0;
        $sql = 'select UNIX_TIMESTAMP(expire) as expire from users_sessions where `user_id` = ? and `hash` = ?';
        if (empty($data = db::exec($sql, [$ui, $h])) || !isset($data['expire'])) return 0;
        if (($exp = (int)$data['expire']) !== 0 && $exp < time()) {
            $this->signOut($ui, $h);
        }
        return 1;
    }

    public function signOut($userId, $hash) {
        $sql = 'delete from `users_sessions` where `user_id` = ? and `hash` = ? limit 1';
        db::exec($sql, [$userId, $hash]);

        $this->model('session')->remove('userId');
        $this->model('session')->remove('userSessionHash');
        setcookie($this->model('session')->getName(), '', time() - 42000);
        session_destroy();

        header('Location: /');
        exit;
    }
}