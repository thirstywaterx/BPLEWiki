<?php
header("Content-Type:application/json;charset=UTF-8");
$e=json_encode(array(
    "success"=>"false",
    "notice"=>"不存在的id",
    "user"=>"system"
),JSON_UNESCAPED_UNICODE);
if(isset($_REQUEST["id"])){
  
  include "../php/connect.php";
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
    $sql0 = "SELECT article.*,user.nickname FROM article 
    INNER JOIN user ON article.user = user.id
    WHERE article.id ='{$id}'";
    $result=$conn->query($sql0);
    $row=$result->fetch_assoc();
    if ($result->num_rows === 1) {
        $file = json_encode(array(
            "success"=>"true",
            "title"=>$row["title"],
            "content"=>$row["content"],
            "cover"=>$row["cover"],
            "user"=>$row["user"],
            "unickname"=>$row["nickname"],
            "type"=>$row["type"],
            "time"=>$row["reg_time"]
        ),JSON_UNESCAPED_UNICODE);
        if(isset($_REQUEST["preview"])){
            $file=json_decode($file,true);
            $file["content"]=substr(strip_tags($file["content"]),0,$_REQUEST["preview"])."…";
            $file=json_encode($file,JSON_UNESCAPED_UNICODE);
        }
        echo $file;
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



?>