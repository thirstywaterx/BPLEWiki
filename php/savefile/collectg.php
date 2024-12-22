<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../usercollect.class.php");
$usersign=new userSign();
$output=new ioput();
$issignin=$usersign->issignin();
$output->jsonoutput("success");

if($issignin["success"]){
    $usercollect=new userCollect($usersign->uid);
    $result=$usercollect->get("workshop");
    if($result["success"]){
        $output->outputdata["workshop"]=$result["collects"];
        $output->success();
    }else{
        $output->addmessage($result["notice"]);
    }
}else{
    $output->addmessage($issignin["notice"]);
}

$output->output();