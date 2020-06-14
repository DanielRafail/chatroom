/* Called when DOM is loaded*/
"use strict";


var value = {
  "count": -1
};


U.ready(function() {
  sessionStorage.removeItem("error");
      U.$("text").addEventListener("keypress",function(e){
        if(e.keyCode==13){
        U.$("send").click();
        e.preventDefault();
        }
        return false;
      });
  /* Add event to %node%, event type is %event%, function when event type is called is %function% */
if ( typeof(EventSource) !== "undefined"){
	startSSE();
}else{
	startAjaxPolling();
}
  U.addHandler(U.$("send"), "click", onButtonClick);
});

/**
* Start a listener for server sided events
*/
function startSSE(){
var evtSource = new EventSource("../php/EventStream.php");
evtSource.onmessage = function(e) {
     var obj = JSON.parse(e.data);
     if(obj.id != value.count){
       var text = obj.message;
       var time = obj.timeCreatedAt.toLocaleString();
       var image = obj.tone;
       var user = obj.user;
       var id = obj.id;
       addToChatroom(text, user, time, image, id);
   }
  };
}

/**
* create an interval every 3 seconds to read if any messages were sent
*/
function startAjaxPolling(){
  setInterval(ajaxPolling, 3000);
}

/**
* Read any messages if they were sent
*/
function ajaxPolling(){
    var xhttp = new XMLHttpRequest();
    xhttp.open("GET", "../php/polling.php?id=" + value.count, true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(null);
    xhttp.onreadystatechange = function(e){
      if(xhttp.readyState == 4 && xhttp.status == 200){
        var obj = xhttp.responseText.replace(/\\/g, "").replace(/\\n/g, "").replace(/data:/g, "").split(";");
        for(var i = 0; i < obj.length - 1; i++){
          var response = JSON.parse(obj[i]);
          var text = response.message;
          var time = response.timeCreatedAt.toLocaleString();
          var image = response.tone;
          var user = response.user;
          var id = response.id;
          addToChatroom(text, user, time, image, id);
        }
    }
  };
}


/**
* Call an ajax request when the button is called
*/
function onButtonClick(){
    var text = U.$("text").value
    if(text != null && typeof text === "string" && text.length > 0){
      var xhttp = new XMLHttpRequest();
      xhttp.open("POST", "../php/chatroom.php", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("message=" + text);
      U.$("text").value = "";
    }
  }
/**
* Method used add a message to the chatroom
* @param text the text we get back from the php file (the text one of the users sent
* @param user the user who sent the message
* @param date the date the message was sent
* @param imageName the name of the image based on the message's emotions
* @param msgID the id of the message
*/
function addToChatroom(text, user, date, imageName, msgId){
  if(verifyResponse(text,user,date, msgId)){
    var parent = document.createElement("div");
    parent.classList.add("message");

    if(imageName != null && imageName.length > 0){
      var pic = document.createElement("div");
      pic.classList.add("pic");
      var image = document.createElement("img");
      image.src = "../images/" + imageName + ".png";
      image.alt = imageName;
      pic.appendChild(image);
      parent.appendChild(pic);
    }

    var userPara = document.createElement("p");
    var userNode = document.createTextNode(user);

    var messagePara = document.createElement("p");
    var messageNode = document.createTextNode(text);

    var datePara = document.createElement("p");
    var dateNode = document.createTextNode(date);

    userPara.classList.add("chat-message");
    messagePara.classList.add("chat-message");
    datePara.classList.add("chat-message");

    userPara.appendChild(userNode);
    messagePara.appendChild(messageNode);
    datePara.appendChild(dateNode);

    parent.appendChild(userPara);
    parent.appendChild(messagePara);
    parent.appendChild(datePara);


    value.count = msgId;

    U.$("chatlogs").appendChild(parent);
    }
}
/**
* Method used to verifiy if all the parameters are valid
* @param text the text we get back from the php file (the text one of the users sent
* @param user the user who sent the message
* @param date the date the message was sent
* @param imageName the name of the image based on the message's emotions
* @param msgId the id of the message
*/
function verifyResponse(text, user, date, msgId){
  if(text != null && typeof text === "string" && text.length > 0
    && user != null &&  typeof user === "string" && user .length > 0
    && date != null && typeof date === "string"
  && msgId != null && typeof msgId === "number"){
      return true;
    }
}
