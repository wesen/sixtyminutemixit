<?php

include("mixitlib.php");

doLogin();

mixitHead("sixtyminutemixit");
mixitHeader();

mixitLoginPage();

mixitFooter();

function doLogin() {
  if ($_POST["action"] && ($_POST["action"] == "login")) {
    $login = strip_tags($_POST["loginname"]);
    $password = strip_tags($_POST["password"]);

    if (checkLogin($login, $password)) {
      setcookie("loginname", $login, time() + 3600 * 6);
      setcookie("password", $password, time() + 3600 * 6);
    }
  }
}

function mixitLoginPage() {
  global $mixit_logins;

  if (!$_POST["action"]) {
    $_POST["action"] = "default";
  }
  
  if ($_POST["action"] && ($_POST["action"] == "login")) {
    $login = strip_tags($_POST["loginname"]);
    $password = strip_tags($_POST["password"]);
    
    if (preg_match("/\s/", $login) || preg_match("/\s/", $password)) {
      mixitFrontBox("Login Error", "login.php", 0, "Login and Password can't contain whitespace.");
    } else {

   if (loginExists($login)) {
     if (checkLogin($login, $password)) {
       mixitFrontBox("Login successful", "login.php", 0, "Your login was correct!");
     } else {
       mixitFrontBox("Login incorrect", "login.php", 0, "Your login was not correct, please try again");
     }
   } else {
      $mixit_logins[$login] = $password;
      writeLogins();
      mixitFrontBox("Login $login created", "login.php", 0, "The login $login was successfully created");
   }
   }
    }

  if (!loggedIn()) {
    mixitFrontBoxHeader("Login check", "login.php", 0);
?>
    To create a login just enter a username and a password and if the username is not taken already a new account will have been created.
<form action="login.php" method="POST">
   <input type="hidden" name="action" value="login"/>
     <table>
     <tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
     <tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
     <tr><td cols="2"><input type="submit" value="Login!"></td></tr>
     </table>
</form>
     <?php
   mixitFrontBoxFooter();
   }
}
?>
