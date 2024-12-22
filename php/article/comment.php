<?php
require_once("../ioput.class.php");
require_once("../connect.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    if(empty($_REQUEST["id"]) or !is_numeric($_REQUEST["id"])){
        throw new ProcessError("表单不完整");
    }
    $conn = connect();
    if ($conn->connect_error) {
        throw new ProcessError("连接失败: " . $conn->connect_error);
    }
    $id=round($_REQUEST["id"]);
    $sql0 = "SELECT articlecomment.*,user.nickname, user.avatar FROM articlecomment 
    INNER JOIN user ON articlecomment.user = user.id 
    WHERE articlecomment.article ={$id}";
    $result=$conn->query($sql0);
    $output->outputdata["comments"]=array();
    while($row=$result->fetch_assoc()){
        $uid=$row["user"];
        array_push($output->outputdata["comments"],array(
            "id"=>$row["id"],
            "user"=>$uid,
            "unickname"=>$row["nickname"],
            "avatar"=>$row["avatar"],
            "content"=>$row["content"],
            "tocomment"=>is_null($row["tocomment"])?"null":$row["tocomment"],
            "time"=>$row["reg_time"]
        ));
        unset($uid);
    }
    $conn->close();
    $output->success();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();