<?php
require_once("../ioput.class.php");
require_once("../constants.class.php");
require_once("../funcarticles.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    if(empty($_POST["ids"])){
        throw new ProcessErrors(Constants::TEXT_FORM_INCOMPLETE);
    }
    $strtoken=strtok($_POST["ids"],",");
    $ids=array();
    while($strtoken!=false){
        if(is_numeric($strtoken)){
            array_push($ids,$strtoken);
        }
        if(count($ids)>20){
            break;
        }
        $strtoken=strtok(",");
    }
    $preview=$_POST["preview"] ?? 30;
    if($preview>100){
        $preview=100;
    }
    $res=articles($ids,$preview);
    if($res["success"]!=="true"){
        throw new ProcessErrors($res["notice"]);
    }
    $output->success();
    $output->outputdata["articles"]=$res["articles"];
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();