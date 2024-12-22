<?php
header("Content-Type:application/json;charset=UTF-8");
if(isset($_REQUEST["id"])){
  
  require_once("../php/connect.php");
  $conn = connect();
  if ($conn->connect_error) {
      die(fail_json("连接失败: " . $conn->connect_error));
  }
    //
    if(is_numeric($_REQUEST["id"])){
        $id=round($_REQUEST["id"]);
    }else{
        die(json_encode(array(
            "success"=>false,
            "notice"=>"错误的文章id",
            "user"=>"system"
        ),JSON_UNESCAPED_UNICODE));
    }
    //$sql = "SELECT username, password FROM user";
    $sql0 = "SELECT workshopcomment.*,user.nickname,user.avatar FROM workshopcomment 
    INNER JOIN user ON workshopcomment.user = user.id 
    WHERE workshopcomment.fileid ={$id}";
    $result=$conn->query($sql0);
    $file = array(
        "success"=>true,
        "comments"=>array()
    );
    while($row=$result->fetch_assoc()){
        $uid=$row["user"];
        array_push($file["comments"],array(
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
    echo json_encode($file,JSON_UNESCAPED_UNICODE);
    $conn->close();
}else{
    echo fail_json("不存在的id");
}

function test_input($data)//表单过滤
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

function fail_json($notice){
    return json_encode(array(
        "success"=>false,
        "notice"=>$notice,
        "user"=>"system"
    ),JSON_UNESCAPED_UNICODE);
}

?>