<?php
require_once("connect.php");
function articles($ids,$preview=30){
    
    $output=array();
      $conn = connect();
      if ($conn->connect_error) {
          return array(
            "success"=>"false",
            "notice"=>"连接失败: " . $conn->connect_error
          );
      }
      $arrstr=implode(", ",$ids);
      $result=$conn->query("SELECT id,title,content,cover FROM article WHERE id IN ({$arrstr})");
      while($row=$result->fetch_assoc()){
        $row["content"]=mb_substr(strip_tags($row["content"]),0,$preview);
        array_push($output,$row);
      }
    return array(
        "success"=>"true",
        "articles"=>$output
    );
    
}
?>