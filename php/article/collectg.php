<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../usercollect.class.php");
$usersign=new userSign();
$output=new ioput();
$output->jsonoutput("success");
try{
    $issignin=$usersign->issignin();
    if(!$issignin["success"]){
        throw new ProcessError($issignin["notice"]);
    }
    $usercollect=new userCollect($usersign->uid);
    $result=$usercollect->get("article");
    if(!$result["success"]){
        throw new ProcessError($result["notice"]);
    }
    $output->outputdata["articles"]=$result["collects"];
    $output->success();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();