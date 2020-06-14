<?php
require_once('../../public/User.php');
require_once('../../public/Message.php');  
require_once('MessagesDAO.php');


    /**
     * DAO class representing Messages
     * 
     * @author Nick Trinh
     * @version 14/11/2018
     */
    class MessagesDAOImpl implements MessagesDAO {
        private $host = 'localhost';
        private $dbname = 'homestead';
        private $username = 'homestead';
        private $password = 'secret';
        private $pdo;

        function __construct() {
            $this->pdo = new PDO("pgsql:host=" . $this->host . "; port=5432; dbname=" . $this->dbname, $this->username, $this->password);
        }

        /**
         * Helper method used to create fake data to populate the datbase
         * 
         */
        function createFakeData() {


            for ($i = 0; $i < 3; $i++) {
                $user = new User();
                $user->setUser("newuser");
                $user->setPassword("somepassword");
                $message = new Message();
                $message->setMessage("blah");
                $message->setTimeCreatedAt(date("Y-m-d H:i:s"));
                $this->createNewMessage($message, $user);
                echo 'Message #' . $i . ' created';

            }

        }

        /**
         * Creates the database that stores Messages
         * 
         */
        function createMessagesTable() {
            try {
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->exec("DROP TABLE IF EXISTS messages;");
                $this->pdo->exec("CREATE TABLE messages(
                  username varchar(1000),
                  message_text varchar(1000),
                  created_at timestamp,
                  tone varchar(50),
                  message_id serial primary key);"
                );
            } catch (PDOException $e) {
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Creates a new record in the Messages table
         * 
         * @param $message representing a Message object to be added to the database
         */
        function createNewMessage($message) {
            $date = date("Y-m-d H:i:s");
            try{
                $stmt = $this->pdo->prepare("INSERT INTO messages (username, message_text, created_at, tone) VALUES (:user, :text, :time, :tone)");
                $stmt-> bindValue(':user', $message->getUser());
                $stmt-> bindValue(':text', $message->getMessage());
                $stmt-> bindValue(':time', $message->getTimeCreatedAt());
                $stmt-> bindValue(':tone', $message->getTone());
                if ($stmt-> execute()) {
                    $message->setMessageID($this->pdo->lastInsertId());
                    return TRUE;
                } else {
                    return FALSE;
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Reads the Messages database and returns a Message object if found
         * 
         * @param $user representing a User object
         */
        function findMessagesByUser($user) {
            try{
                $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE username = :name");
                $stmt-> bindValue(':name', $user->getUser());
                if ($stmt-> execute()) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $this->createMessageObject($results);
                } else {
                    return FALSE;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Reads the Messages database and returns the last 8 records inserted into the database
         * 
         */
        function findLast8Messages() {
            try{
                $stmt = $this->pdo->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT 8");
                if ($stmt-> execute()) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($results as $row) {
                        $lastMessages[] = $this->createMessageObject($row);
                    }
                    return $lastMessages;
                } else {
                    return FALSE;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Reads the Messages database and returns the last record inserted into the database
         * based on a given ID
         * 
         * @param $last_event_id representing the MessageID that was added to the Messages table
         */
        function findLastMessage($last_event_id) {
            try{
                $stmt = $this->pdo->prepare("SELECT * FROM messages WHERE message_id = :id ORDER BY created_at DESC");
                $stmt-> bindValue(':id', $last_event_id + 1);
                if ($stmt-> execute()) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($results as $row) {
                        $lastMessages[] = $this->createMessageObject($row);
                    }
                    return $lastMessages;
                } else {
                    return FALSE;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

         /**
         * Helper method that creates a new Message object
         *
         * @param $results is the Result set of a query
         */
        function createMessageObject($results) {
            if (!isset($results['message_id'])) {
                return FALSE;
            }
            if (!isset($results['username'])) {
                return FALSE;
            }
            if (!isset($results['message_text'])) {
                return FALSE;
            }
            if (!isset($results['created_at'])) {
                return FALSE;
            }
            $message = new Message();
            $message->setMessageID($results['message_id']);
            $message->setUser($results['username']);
            $message->setMessage($results['message_text']);
            $message->setTimeCreatedAt($results['created_at']);
            $message->setTone($results['tone']);
            return $message;
        }
    }
?>
