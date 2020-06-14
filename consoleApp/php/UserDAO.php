<?php
require_once('../../public/User.php'); 

    interface UserDAO {
        
        public function createUserTable();
        public function createNewUser($user);
        public function findUserByUsername($username);
        public function findUserByUserID($userID);
        public function findUserLastLoginAttempt($user);
        public function updateLastLoginAttemptByUser($user);
        public function resetLoginAttemptsForUser($user);
    }
?>