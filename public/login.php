<?php
    require_once('Authentication.php');
    // Check if username, password, and sign in method were sent in the XMLHttpRequest
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
	    if (isset($_POST['username'])
			&& isset($_POST['password'])) {
		// Check if username and password are of type string
		    if (is_string($_POST['username'])
				&& is_string($_POST['password'])) {
			    $xhttpRequestUser = $_POST['username'];
			    $xhttpRequestPass = $_POST['password'];
			    $auth = new Authentication();
                // Check which sign in method to use
                $validUser = FALSE;
			    if (isset($_POST['signUp']) && $_POST['signUp'] === 'on') {
                    $validUser = $auth->register($xhttpRequestUser, $xhttpRequestPass);
			    } else {
                    $validUser = $auth->login($xhttpRequestUser, $xhttpRequestPass);

                }

			    if ($validUser === TRUE) {
                    // Redirect to chatroom after successful login
                    header('Location: ./chatroom.php');
                    exit();
                }else{
                  header('Location: ./');
                  exit();
                }
            }
        }
    }
?>
