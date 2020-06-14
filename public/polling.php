<?php
require_once('../consoleApp/php/MessagesDAOImpl.php');
require_once('Message.php');

if (!isset($_SESSION['username'])) {
    exit();
}

$dao = new MessagesDAOImpl();
$last_event_id = $_GET['id'];
if ($last_event_id == -1) { ////first time
  //query database of last 8 messages
  $results = $dao->findLast8Messages();
} else {
  $results = $dao->findLastMessage($last_event_id);
}

//for each message, create an SSE message. e.g.
$results = array_reverse($results);
foreach($results as $record){
  echo "data:" . json_encode($record) . ";";
  ob_flush();
  flush();
}


?>
