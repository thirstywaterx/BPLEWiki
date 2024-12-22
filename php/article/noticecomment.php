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
    $sql0 = "SELECT articlecomment.*, article.title, user.nickname
FROM articlecomment
INNER JOIN article ON articlecomment.article = article.id
INNER JOIN user ON articlecomment.user = user.id
WHERE articlecomment.article IN (SELECT id FROM article WHERE article.user = {$uid}) AND articlecomment.user != {$uid} AND articlecomment.tocomment IS Null
ORDER BY id DESC 
LIMIT 30";

//"SELECT * FROM articlecomment WHERE article IN (SELECT id FROM article WHERE user = {$uid}) AND user != {$uid} ORDER BY id DESC LIMIT 30"
    $result=$conn->query($sql0);
    $output->outputdata["comments"]=array();
    while($row=$result->fetch_assoc()){
        array_push($output->outputdata["comments"],array(
            "id"=>$row["id"],
            "user"=>$row["user"],
            "article"=>$row["article"],
            "articletitle"=>$row["title"],
            "unickname"=>$row["nickname"],
            "content"=>$row["content"],
            "time"=>$row["reg_time"]
        ));
        unset($uid);
    }
    
    $output->success();
    $conn->close();
}else{
    $output->addmessage(Constants::TEXT_HAS_NOT_SIGN_IN);
}
$output->output();