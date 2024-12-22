<?php
session_start();
setcookie("token", "", time());
setcookie("token", "", time(),"/");
setcookie("token", "", time(),"/php/");
unset($_SESSION["cemail"]);
?>