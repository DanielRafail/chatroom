<?php
    class User implements JsonSerializable {
        private $username;
        private $password;
        private $lastLoginAttemptTime;
        private $numLoginAttempts;

        function __construct() {
        }

        function setUser($username) {
            $this->username = $username;
        }

        function getUser() {
            return $this->username;
        }

        function setPassword($password) {
            $this->password = $password;
        }

        function getPassword() {
            return $this->password;
        }

        function setLastLoginAttemptTime($dateTime) {
            $this->lastLoginAttemptTime = $dateTime;
        }

        function getLastLoginAttemptTime() {
            return $this->lastLoginAttemptTime;
        }

        function setInvalidLoginAttempts($numAttempts) {
            $this->numLoginAttempts = $numAttempts;
        }

        function getInvalidLoginAttempts() {
            return $this->numLoginAttempts;
        }

        function jsonSerialize() {
            return [ 'username' => $this->username,
                    'password' => $this->password,
                    'lastLoginAttemptTime' => $this->lastLoginAttemptTime,
                    'numLoginAttempts' => $this->numLoginAttempts              
            ];

        }

    }



?>