<?php
require_once("../ioput.class.php");
require_once("../usersign.class.php");
require_once("../constants.class.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    if(empty($_POST["username"])||empty($_POST["password"])){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    $usersign = new userSign();
      
    $result=$usersign->checkpassword(
        ioput::test_input($_POST["username"]),
        ioput::test_input($_POST["password"])
    );
    if ($result["success"]) {
        $usersign->uid=$result["uid"];
        $usersign->login();
        $output->success(Constants::TEXT_SIGNIN_SUCCESS);
    }else{
        throw new ProcessError($result["notice"]);
    }
}catch(ProcessError $e){
    $output->addmessage($e);
}
$output->output();