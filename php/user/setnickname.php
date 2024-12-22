<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../constants.class.php");
$usersign=new userSign();
$output=new ioput();
$output->jsonoutput("success");
try{

    $signinfo=$usersign->issignin();
    if(!$signinfo["success"]){
        throw new ProcessError(Constants::TEXT_HAS_NOT_SIGN_IN);
    }
    $userinfo=$usersign->userinfo();
    if($userinfo["user"]["nickname"]===$_POST["nickname"]){
        throw new ProcessError("已是该昵称");
    }
    if(empty(ioput::test_input($_POST["nickname"]))){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    $conn=connect();
    if($conn->connect_error){
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED.$conn->connect_error);
    }
    $nickname=ioput::test_input($_POST["nickname"]);
    $sql="UPDATE user 
    SET nickname=?
    WHERE id=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("si", $bp_nn, $bp_id);
    $bp_nn=$nickname;
    $bp_id=(int)$userinfo["user"]["id"];
    if(!$stmt->execute()){
        throw new ProcessError("修改失败：".$stmt->error);
    }
    $output->outputdata["newnickname"]=$nickname;
    $output->success(Constants::TEXT_REVISE_SUCCESS);
    $stmt->close();
    $conn->close();
    
}catch(ProcessError $e){
    $output->addmessage($e);
}
$output->output();