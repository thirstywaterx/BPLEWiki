<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../usercollect.class.php");
require_once("../constants.class.php");
$usersign=new userSign();
$output=new ioput();
$issignin=$usersign->issignin();
$output->jsonoutput("success");

if(isset($_POST["id"]) and is_numeric($_POST["id"])){
    
    $aid=(int)$_POST["id"];
    $signinfo=$usersign->userinfo();
    if(!$issignin["success"]){
      $output->addmessage($signinfo["notice"]);
    }elseif($signinfo["user"]["ban"]!=0){
      $output->addmessage(Constants::TEXT_ACCOUNT_BANNED);
    }else{
        $usercollect=new userCollect($usersign->uid);
        $result=$usercollect->set("workshop",$aid);
        
        if(!$result["success"]){
            $output->addmessage($result["notice"]);
        }else{
            $output->success($result["notice"]);
        }
    }

}else{
    $output->addmessage(Constants::TEXT_FORM_INCOMPLETE);
}
$output->output();