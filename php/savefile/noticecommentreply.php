<?php
require_once("../usersign.class.php");
require_once("../connect.php");
require_once("../ioput.class.php");
require_once("../constants.class.php");
$usersign=new userSign();
$output=new ioput();
$signinfo=$usersign->issignin();
$output->jsonoutput("success");
if($signinfo["success"]){
  
  $conn = connect();
  $output->checknes(!$conn->connect_error,Constants::TEXT_CONNECTION_FAILED . $conn->connect_error,true);
    $uid=$signinfo["uid"];
    $sql0 = "SELECT workshopcomment.*, fileinfo.title, user.nickname
FROM workshopcomment
INNER JOIN fileinfo ON workshopcomment.fileid = fileinfo.id
INNER JOIN user ON workshopcomment.user = user.id
WHERE workshopcomment.tocomment IN (SELECT id FROM workshopcomment WHERE user = {$uid}) AND workshopcomment.user != {$uid} 
ORDER BY id DESC 
LIMIT 30";
//$sql0 = "SELECT * FROM articlecomment WHERE tocomment IN (SELECT id FROM articlecomment WHERE user = {$uid}) and user != {$uid} ORDER BY id DESC LIMIT 30";
    unset($uid);
    $result=$conn->query($sql0);
    $output->outputdata["comments"]=array();
    while($row=$result->fetch_assoc()){
        array_push($output->outputdata["comments"],array(
            "id"=>$row["id"],
            "user"=>$row["user"],
            "article"=>$row["fileid"],
            "articletitle"=>$row["title"],
            "unickname"=>$row["nickname"],
            "content"=>$row["content"],
            "time"=>$row["reg_time"]
        ));
    }
    $output->success();
    $conn->close();
}else{
    $output->addmessage(Constants::TEXT_HAS_NOT_SIGN_IN);
}
$output->output();