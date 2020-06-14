<?php
session_start();
//verify if user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: ./');
    exit();
}
?>
<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/chatroom.css">
    <Title>Chat Room</Title>
    <script src="./javascript/utilities.js"></script>
    <script src="./javascript/init.js"></script>
    <script src="./javascript/chatroom.js"></script></head>
</head>
<body>
  <div class = "chatbox" id = "chatbox">
    <div class = "chatlogs" id = "chatlogs">
      <!-- <div class = "message"> -->
        <!-- <div class = "pic"> -->
          <!-- <img src = "item"/> -->
        <!-- </div> -->
          <!-- <p class = "chat-message">msg</p> -->
      <!-- </div> -->

    </div>
  <div class = "chat-form">
    <textarea id="text"></textarea>
    <button id = "send">Send</button>
  </div>
</div>
<footer>
    <p>Nick & Daniel ©</p>
</footer>
</body>
</html>
<?php
    require_once('../consoleApp/php/UserDAOImpl.php');
    require_once('User.php');
    require_once('../consoleApp/php/MessagesDAOImpl.php');
    require_once('Message.php');
    if(!isset($_SESSION['username'])) {
      // not logged in
      header('Location: ./');
      exit();
    }
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['message'])){
        $messageDAO = new MessagesDAOImpl();
        $message = new Message();
        $date = date("Y-m-d H:i:s");
        $message->setMessage($_POST['message']);
        $message->setTimeCreatedAt($date);
        $message->setUser($_SESSION['username']);
				$object = getCurlResult("https://gateway.watsonplatform.net/tone-analyzer/api/v3/tone?version=2017-09-21&sentences=false&text="
				. urlencode($_POST['message']));
        if($object->document_tone->tones == null){
          $message->setTone("");
        }else{
          $imageName = current($object->document_tone->tones)->tone_name;
          $message->setTone($imageName);
        }
        $messageDAO->createNewMessage($message);

      }
    }

    /**
    * function to get the curl result from the web api
    */
    function getCurlResult($url){
        $username = "c8fb096f-0616-4acf-aeac-b9804af7a60c";
        $password = "nXdp57gDD4Xd";
        if(!(is_null($url)) && strlen($url) > 0 && is_string($url)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //url has https and you don't want to verify source certificate
            $response = curl_exec($ch);
            $messageObject = json_decode($response);
            curl_close($ch);

            return $messageObject;
        }
      }
?>
