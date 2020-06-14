<?php
session_start();
require_once('../consoleApp/php/UserDAOImpl.php');
// require_once('User.php');
    class Authentication {

		private $dao;

		function __construct() {
			$this->dao = new UserDAOImpl();
		}

        /**
         * Creates a new User and add them to the database with the provided username and password
         *
         * @param $username is the User's username
         * @param $password is the User's password
         */
	    public function register($username, $password) {
			// User already exists
			$existingUser = $this->dao->findUserByUsername($username);
		    if ($existingUser === FALSE) {
				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
				$user = new User();
				$user->setUser($username);
				$user->setPassword($hashedPassword);
				$user->setLastLoginAttemptTime(date("Y-m-d H:i:s"));
				$user->setInvalidLoginAttempts(0);
				$createdUser = $this->dao->createNewUser($user);
				if ($createdUser === TRUE) {
					// Put username in session, valid registration should also log in user
					$this->saveInSession($username);
					return TRUE;
				}
            } else {
				$_POST['existing-user'] = TRUE;
				return FALSE;
			}
        }

        /**
         * Compares inputed username against the database to find a User
         * and checks if inputed password matches the one in the database.
         * Denies the user to log in if they have made more than 3 consecutive
         * attempts and if it has not been 5 minutes since the last lock out
         *
         * @param $username is the User's username
         * @param $password is the User's password
         */
	    public function login($username, $password) {
			$user = $this->dao->findUserByUsername($username);
	        if ($user === FALSE) {
				$_POST['error'] = TRUE;
				return FALSE;
			}
		    // Check if user made more than 3 consecutive invalid login attempts
		    if (($user->getInvalidLoginAttempts() > 3)
			    // Check if 5 minutes has passed since last attempt in case of user lock out
			    && (($user->getLastLoginAttemptTime() + 60*5) < date("Y-m-d H:i:s") )) {
					$_POST['locked'] = TRUE;
			    	return FALSE;
		    }

			// Check database for matching password
	        $hash = $user->getPassword();
	        if (password_verify($password, $hash) === FALSE) {
				// Failed authentication, increment attempts and update user's lastLoginAttemptTime
				$attempts = $user->getInvalidLoginAttempts();
				$user->setInvalidLoginAttempts($attempts+1);
                $user->setLastLoginAttemptTime(date("Y-m-d H:i:s"));
				$this->dao->updateLastLoginAttemptByUser($user);
				$_POST['error'] = TRUE;
		        return FALSE;
	        }

			// Successful authentication, set login attempts to 0
			$user->setInvalidLoginAttempts(0);
			$this->dao->resetLoginAttemptsForUser($user);
			$this->dao->updateLastLoginAttemptByUser($user);
		    //put $user name in session to check if user is logged in
		    $this->saveInSession($username);

			return TRUE;

		}
        /**
        * function to save username to session so that it can be used later
        */
		private function saveInSession($username) {
			$_SESSION['username'] = $username;
			session_regenerate_id();
		}
        /**
        * function to check if the user is logged in. If not, redirect to login page
        */
		public function checkLoggedIn() {
			if (isset($_SESSION['username'])) {
				// Get User object from the database
				$this->dao = new UserDAO();
				return $this->dao->getUser($_SESSION['username']);
			}
			//redirect to login page
			header('Location: ./');
		}

        /**
        * function to log out the user and destroy the session
        */
		public function logout() {
			// Destroy the cookie
			setcookie(session_name(),'', time() - 42000);
			// Unset session values
			$_SESSION = [];
			// Destroy session
			session_destroy();
			// Redirect
			header('Location: ./');
		}

    }


?>
