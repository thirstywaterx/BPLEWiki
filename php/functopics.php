<?php
require_once("connect.php");
require_once("constants.class.php");
function savefiles($ids,$preview=30){
    
    $output=array();
      $conn = connect();
      if ($conn->connect_error) {
          return array(
            "success"=>false,
            "notice"=>Constants::TEXT_CONNECTION_FAILED . $conn->connect_error
          );
      }
      $arrstr=implode(", ",$ids);
      $result=$conn->query("SELECT id,title,content FROM topic WHERE id IN ({$arrstr})");
      if(!$result){
        return array(
            "success"=>false,
            "notice"=>$conn->error
        );
      }
      if($result->num_rows===0){
        return array(
            "success"=>false,
            "notice"=>"result: 0"
        );
      }
      while($row=$result->fetch_assoc()){
        $row["content"]=mb_substr(strip_tags($row["content"]),0,$preview);
        array_push($output,$row);
      }
    return array(
        "success"=>true,
        "results"=>$output
    );
    
}
?>