<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../connect.php");
require_once("../constants.class.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    if(empty($_POST["id"]) or !is_numeric($_POST["id"])){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_FORM_INCOMPLETE);
    }
    $usersign=new userSign();
    $issignin=$usersign->issignin();
    $acid=$_POST["id"];
    if(!$issignin["success"]){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_HAS_NOT_SIGN_IN);
    }
    $conn=connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED.$conn->connect_error);
    }
    $sql="SELECT user,tocomment FROM topiccomment 
    WHERE id='{$acid}'";
    $row=$conn->query($sql)->fetch_assoc();
    if((int)$row["user"]!==(int)$issignin['uid']){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD."："."无删除权限");
    }
    $dids=array($acid);
    //递归
    $queue = array($acid);
    while ($queue) {
        $current = array_pop($queue);
        if(!is_numeric($current)){
            continue;
        }
        $result = $conn->query("SELECT id FROM topiccomment WHERE tocomment='{$current}'");
        while ($row = $result->fetch_assoc()) {
            array_push($dids,$row["id"]);
            array_push($queue,$row["id"]);
        }
    }
    $idstr=implode(',',$dids);
    $sql="DELETE FROM topiccomment 
    WHERE id IN ({$idstr})";
    if(!$conn->query($sql)){
        throw new ProcessError(Constants::TEXT_DELETE_FAILD."：".$conn->error);
    }
    $output->success("删除成功");
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();