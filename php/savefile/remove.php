<?php
require_once("../constants.class.php");
require_once("../ioput.class.php");
require_once("../usersign.class.php");
require_once("../connect.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    if(empty($_POST["id"]) or !is_numeric($_POST["id"])){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_FORM_INCOMPLETE);
    }
    $usersign=new userSign();
    $issignin=$usersign->issignin();
    $aid=$_POST["id"];
    if(!$issignin["success"]){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_HAS_NOT_SIGN_IN);
    }
    $conn=connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED.': '.$conn->connect_error);
    }
    $sql="DELETE FROM fileinfo 
    WHERE id='{$aid}' AND user = {$signinfo['uid']}";
    if($conn->query($sql)!==TRUE){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_ERROR.": {$conn->error}");
    }
    $output->success(Constants::TEXT_DELETE_SUCCESS);
    $conn->close();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();