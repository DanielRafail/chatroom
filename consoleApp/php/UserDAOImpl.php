<?php
require_once('../../public/User.php');
require_once('UserDAO.php');
require_once("vendor/autoload.php");

    /**
     * DAO class representing Users
     * 
     * @author Nick Trinh
     * @version 14/11/2018
     */
    class UserDAOImpl implements UserDAO {

        private $host = 'localhost';
        private $dbname = 'homestead';
        private $username = 'homestead';
        private $password = 'secret';
        private $pdo;

        function __construct() {
            $this->pdo = new PDO("pgsql:host=" . $this->host . "; port=5432; dbname=" . $this->dbname, $this->username, $this->password);
        }

        /**
         * Creates the User database
         * 
         * @author Nick Trinh
         * @version 14/11/2018
         */
        function createUserTable() {
            try {
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->exec("DROP TABLE IF EXISTS users;");
                $this->pdo->exec("CREATE TABLE users(
                  username varchar(20) primary key,
                  password varchar(255) not null,
                  attempt_counter numeric(5,0) default 0,
                  last_invalid_attempt timestamp default LOCALTIMESTAMP(0));"
                );
            } catch (PDOException $e) {
                echo $e->getMessage();
                exit;
            }
            return $this->pdo;

        }



        /**
         * Creates a new User record
         *
         * @param $user is the User object to be added
         */
        function createNewUser($user) {
            $date = date("Y-m-d H:i:s");
            try{
                $stmt = $this->pdo->prepare("INSERT INTO users (username, password, attempt_counter, last_invalid_attempt) VALUES (:nameVal, :pass, :counter, :attempt)");
                $stmt-> bindValue(':nameVal', $user->getUser());
                $stmt-> bindValue(':pass', $user->getPassword());
                $stmt-> bindValue(':counter', 0);
                $stmt-> bindValue(':attempt', $date);
                if ($stmt-> execute()) {
                    echo 'TRUE';
                    return TRUE;
                } else {
                    echo 'FALSE';
                    return FALSE;
                }

            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        function createFakeData() {
            $faker = Faker\Factory::create();
            for ($i = 0; $i < 3; $i++) {

                $user = new User();
                $user->setUser($faker->firstName);
                $user->setPassword($faker->firstName);
                $this->createNewUser($user);
                echo 'User #' . $i . ' created';

            }

        }

        /**
         * Returns a User object given a username
         *
         * @param $username is the username of the requested User
         */
        function findUserByUsername($username) {
            try{
                $stmt = $this->pdo->prepare("SELECT username, password, attempt_counter, last_invalid_attempt FROM users WHERE username = :nameVal");
                $stmt-> bindValue(':nameVal', $username);
                if ($stmt-> execute()) {
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $this->createUserObject($results);
                } else {
                    return FALSE;
                }

            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Returns a User object given an ID
         *
         * @param $userID is the ID of the requested User
         */
        function findUserByUserID($userID) {
            try{
                $stmt = $this->pdo->prepare("SELECT username, password, attempt_counter, last_invalid_attempt FROM users WHERE username = :id");
                $stmt-> bindValue(':id', $userID);
                if ($stmt-> execute()) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return $this->createUserObject($results);
                } else {
                    return FALSE;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Returns a User's last login attempt time
         *
         * @param $user is the User object
         */
        function findUserLastLoginAttempt($user) {
            try{
                $stmt = $this->pdo->prepare("SELECT last_invalid_attempt FROM users WHERE username = :nameVal");
                $stmt-> bindValue(':nameVal', $user->getUser());
                if ($stmt-> execute()) {
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $results;
                } else {
                    return FALSE;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Increments the invalid attempts counter and set the last invalid attempt time to now
         *
         * @param $user is the User object
         */
        function updateLastLoginAttemptByUser($user) {
            try{
                $stmt = $this->pdo->prepare("UPDATE users SET attempt_counter = :countVal, last_invalid_attempt = :dateVal WHERE username = :nameVal");
                $stmt-> bindValue(':nameVal', $user->getUser());
                $stmt-> bindValue(':countVal', $user->getInvalidLoginAttempts());
                $stmt-> bindValue(':dateVal', $user->getLastLoginAttemptTime());
                if ($stmt-> execute()) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Reset the invalid attempt counter and the last login time for a user
         *
         * @param $user is the User object
         */
        function resetLoginAttemptsForUser($user) {
            try{
                $stmt = $this->pdo->prepare("UPDATE users SET attempt_counter = 0, last_invalid_attempt = :dateVal WHERE username = :nameVal");
                $stmt-> bindValue(':nameVal', $user->getUser());
                $stmt-> bindValue(':dateVal', $user->getLastLoginAttemptTime());
                if ($stmt-> execute()) {
                    return TRUE;
                } else {
                    return FALSE;
                }

            }catch(PDOException $e){
                echo $e->getMessage();
                exit;
            }
        }

        /**
         * Helper method that creates a new User object
         *
         * @param $results is the Result set of a query
         */
        function createUserObject($results) {
            if (!isset($results['username'])) {
                return FALSE;
            }
            if (!isset($results['password'])) {
                return FALSE;
            }
            if (!isset($results['attempt_counter'])) {
                return FALSE;
            }
            if (!isset($results['last_invalid_attempt'])) {
                return FALSE;
            }
            $user = new User();

            $user->setUser($results['username']);
            $user->setPassword($results['password']);
            $user->setLastLoginAttemptTime($results['last_invalid_attempt']);
            $user->setInvalidLoginAttempts($results['attempt_counter']);
            return $user;
        }

    }
?>
