<?php
    require_once('../../public/Message.php');  

    interface MessagesDAO {
        public function createMessagesTable();
        public function createNewMessage($message);
        public function findMessagesByUser($user);
    }
?>