<?php
    require_once('UserDAOImpl.php');
    require_once('MessagesDAOImpl.php');
    require_once("vendor/autoload.php"); 
    function setup(){
        $userDAO = new UserDAOImpl();
        $userDAO->createUserTable();
        // $userDAO->createFakeData();
        $messageDAO = new MessagesDAOImpl();
        $messageDAO->createMessagesTable();
        // $messageDAO->createFakeData();
        
    }
    setup();
?>
