<?php
require_once('Authentication.php');
// Check if username, password, and sign in method were sent in the XMLHttpRequest
/**
* function to log in the user and redirect to the chatroom
*/
function login(){
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
        }
      }
    }
  }
}
?>

<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/login.css">
    <Title>Login</Title>
    <script src="./javascript/utilities.js"></script>
    <script src="./javascript/init.js"></script>
    <script src="./javascript/validation.js"></script></head>
<body>
    <form action="" method="post">
<div id="main_body">
        <h1 id="page_title">Login</h1>
        <div class="container">
            <p>Username</p>
            <input type="text" placeholder="Enter Username: " name="username" id = "username" class = "input" required>

            <p>Password</p>
            <input type="password" placeholder="Enter Password: " name="password" id = "password" class="input" required>
          </br>
            <button type="submit" name="login" id ="login">Login</button>
          </br>
                <input type="checkbox" name="signUp" id="signUp">
                <label>Sign up</label>
                <?php
                login();
                if(isset($_POST['existing-user'])){
                    echo '<p id ="error">Username already exists</p>';
                    unset($_POST['existing-user']);
                }else if(isset($_POST['error'])){
                    echo '<p id ="error">Invalid username or password</p>';
                    unset($_POST['error']);
                }else if(isset($_POST['locked'])){
                    echo '<p id ="error">You have been locked for 5 minutes</p>';
                    unset($_POST['locked']);
                }
                ?>
        </div>
    </form>
</div>
<footer>
  <p>Nick & Daniel ©</p>
</footer>
</body>
</html>
