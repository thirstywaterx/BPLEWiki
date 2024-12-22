<?php
header("Content-Type:application/json;charset=UTF-8");
$e=json_encode(array(
    "success"=>"false",
    "notice"=>"不存在的id",
    "user"=>"system"
),JSON_UNESCAPED_UNICODE);
if(isset($_REQUEST["id"])){
  
  require_once("../php/connect.php");
  $conn = connect();
  if ($conn->connect_error) {
      die(json_encode(array(
        "success"=>"false",
        "notice"=>"连接失败: " . $conn->connect_error,
        "user"=>"system"
      ),JSON_UNESCAPED_UNICODE));
  }
    //开始验证登录
    if(is_numeric($_REQUEST["id"])){
        $id=round($_REQUEST["id"]);
    }else{
        die(json_encode(array(
            "success"=>"false",
            "notice"=>"错误的文章id",
            "user"=>"system"
        ),JSON_UNESCAPED_UNICODE));
    }
    //$sql = "SELECT username, password FROM user";
    $sql0 = "SELECT fileinfo.*,user.nickname FROM fileinfo 
    INNER JOIN user ON fileinfo.user = user.id
    WHERE fileinfo.id ='{$id}'";
    $result=$conn->query($sql0);
    $row=$result->fetch_assoc();
    if ($result->num_rows === 1) {
        $file = array(
            "success"=>"true",
            "title"=>$row["title"],
            "cover"=>$row["cover"],
            "content"=>$row["content"],
            "viewImg"=>$row["viewImg"],
            "user"=>$row["user"],
            "unickname"=>$row["nickname"],
            "map"=>$row["map"],
            "time"=>$row["reg_time"]
        );
        if(isset($_REQUEST["preview"])){
            $file["content"]=substr(strip_tags($file["content"]),0,$_REQUEST["preview"])."…";
        }
        echo json_encode($file,JSON_UNESCAPED_UNICODE);
    }else{
        echo $e;
    }
    $conn->close();
}else{
    echo $e;
}

function test_input($data)//表单过滤
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}