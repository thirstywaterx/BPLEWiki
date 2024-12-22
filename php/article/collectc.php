<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../usercollect.class.php");
require_once("../constants.class.php");
$usersign=new userSign();
$output=new ioput();
$issignin=$usersign->issignin();
$output->jsonoutput("success");
try{
    if(empty($_POST["id"]) or !is_numeric($_POST["id"])){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    if(!$issignin["success"]){
        throw new ProcessError($signinfo["notice"]);
    }
    $signinfo=$usersign->userinfo();
    if($signinfo["user"]["ban"]!=0){
        throw new ProcessError(Constants::TEXT_ACCOUNT_BANNED);
    }
    $usercollect=new userCollect($usersign->uid);
    $result=$usercollect->set("article",(int)$_POST["id"]);
    
    if(!$result["success"]){
        throw new ProcessError($result["notice"]);
    }else{
        $output->success($result["notice"]);
    }
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();