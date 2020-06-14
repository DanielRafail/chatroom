<?php
require_once('../consoleApp/php/MessagesDAOImpl.php');
require_once('Message.php');
    header("Content-Type: text/event-stream");
    header("Cache-Control: no-cache"); //to prevent caching of event data
    header("Connection: keep-alive");

    session_start();
    session_write_close();


    if (!isset($_SESSION['username'])) {
        exit();
    }
    $dao = new MessagesDAOImpl();
    $last_event_id = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : -1);
    if ($last_event_id == -1) { ////first time
	    //query database of last 8 messages
	    $results = $dao->findLast8Messages();
    } else {
      $results = $dao->findLastMessage($last_event_id);
    }

    //for each message, create an SSE message. e.g.
    $results = array_reverse($results);
    foreach($results as $record){
      echo "id: " . $record->getMessageId().PHP_EOL;
      echo "data:" . json_encode($record).PHP_EOL;
      echo PHP_EOL;
      ob_flush();
      flush();
    //remember the last id sent
    $last_event_id = $record->getMessageID();
    }


?>
