<?php
require_once("ioput.class.php");
require_once("usersign.class.php");
require_once("connect.php");
$output=new ioput();
ioput::postre(["reason","type","content","id"]);
$output->jsonoutput("success");
$usersign=new userSign();
if($_POST["reason"] and $_POST["type"] and ioput::inputcheck("id","post","int")){
    $signinfo=$usersign->issignin();
    if($signinfo["success"]){
        $conn=connect();
        if($conn->connect_error){
            $output->addmessage("连接失败".connect_error);
        }else{
            if(!$_POST["content"]){
                $stmt=$conn->prepare("INSERT INTO report (reason, type, user, tid)
                VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssii", $bp_reason, $bp_type, $bp_user, $bp_tid);
            }else{
                $stmt=$conn->prepare("INSERT INTO report (content, reason, type, user, tid)
                VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssii", $bp_content, $bp_reason, $bp_type, $bp_user, $bp_tid);
            }
            $bp_reason=$_POST["reason"];
            $bp_type=$_POST["type"];
            $bp_content=$_POST["content"];
            $bp_user=(int)$signinfo["uid"];
            $bp_tid=$_POST["id"];
            $stmt->execute();
            $output->success();
            $output->addmessage("举报成功");
        }
    }else{
        $output->addmessage("未登录");
    }
}else{
    $output->addmessage("表单不完整 ");
    $output->addmessage($_POST["reason"].", ");
    $output->addmessage($_POST["type"]);
}
$output->output();