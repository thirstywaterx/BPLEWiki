<?php
require_once("../connect.php");
require_once("../usersign.class.php");
require_once("../ioput.class.php");
$output=new ioput();
$output->jsonoutput("success");
$usersign=new userSign();
try{
    $issignin=$usersign->issignin();
    if(!$issignin["success"]){
        throw new ProcessError("未登录");
    }
    $conn = connect();
    if($conn->connect_error){
        throw new ProcessError("连接失败: ".$conn->connect_error);
    }
    if(empty($_POST["newPassword"])){
        throw new ProcessError("表单不完整");
    }
    $oldPassword=$_POST["oldPassword"] ?? '';
    $newPassword=$_POST["newPassword"] ?? '';
    
    $stmt=$conn->prepare("SELECT `password` FROM `user` WHERE `id` =?");
    $stmt->bind_param("i", $bp_id);
    $bp_id=(int)$issignin["uid"];
    if(!$stmt->execute()){
        throw new ProcessError($stmt->error);
    }
    $result=$stmt->get_result();
    if(!$result->num_rows>0){
        throw new ProcessError("登录状态错误");
    }
    $row=$result->fetch_assoc();
    if(!password_verify($oldPassword, $row["password"])){
        throw new ProcessError("旧密码错误");
    }
    $result=$usersign->changepassword(password_hash($newPassword, PASSWORD_DEFAULT));
    if(!$result["success"]){
        throw new ProcessError($result["notice"]);
    }
    $output->success("密码更新成功！");
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();

