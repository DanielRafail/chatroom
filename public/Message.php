<?php
    class Message implements JsonSerializable {
        private $id;
        private $messageText;
        private $user;
        private $timeCreatedAt;
        private $tone;

        function __construct() {
        }

        function setMessageID($id) {
            $this->id = $id;
        }

        function getMessageID() {
            return $this->id;
        }

        function setMessage($message) {
            $this->messageText = $message;
        }

        function getMessage() {
            return $this->messageText;
        }

        function setUser($username) {
            $this->user = $username;
        }

        function getUser() {
            return $this->user;
        }

        function setTimeCreatedAt($datetime) {
            $this->timeCreatedAt = $datetime;
        }

        function getTimeCreatedAt() {
            return $this->timeCreatedAt;
        }

        function setTone($tone) {
            $this->tone = $tone;
        }

        function getTone() {
            return $this->tone;
        }

        function jsonSerialize() {
            return [ 
                'id' => $this->id,
                'message' => $this->messageText,
                'user' => $this->user,
                'timeCreatedAt' => $this->timeCreatedAt,
                'tone' => $this->tone
            ];

        }

    }

?>