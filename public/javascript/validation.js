"use strict";

U.ready(function(){
    U.addHandler(U.$("login"), "click", onButtonClick);
});

function onButtonClick(e){
    var username = U.$("username").value;
    var password= U.$("password").value;
    var userRegex = /^[a-zA-Z0-9_-]+$/;
    var passRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{6,}$/;
  /* verify username and password are valid*/
    if(!userRegex.test(username) ||  !passRegex.test(password)){
        if(U.$("valid")){
      U.$("valid").id = "error";
    }
      U.$("username").class = "inputFalse";
      U.$("password").class = "inputFalse";
      e.preventDefault();
    }else{
     if(U.$("error")){
        U.$("error").id = "valid";
    }
      U.$("username").class = "input";
      U.$("password").class = "input";
  }
}
