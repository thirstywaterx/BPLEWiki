<?php
header("Content-Type:application/json;charset=UTF-8");
require_once("../funworkshop.php");
$output=array();
if(isset($_POST["ids"])){
    $strtoken=strtok($_POST["ids"],",");
    $ids=array();
    while($strtoken!=false){
        if(is_numeric($strtoken)){
            array_push($ids,$strtoken);
        }
        if(count($ids)>20){
            break;
        }
        $strtoken=strtok(",");
    }
    $res=articles($ids);
    if($res["success"]==="true"){
        $output+=array(
            "success"=>"true",
            "articles"=>$res["articles"]
        );
    }else{
        $output+=array(
            "success"=>"false",
            "notice"=>$res["notice"]
        );
    }
}else{
    $output+=array(
        "success"=>"false",
        "notice"=>"empty ids"
    );
}
echo json_encode($output,JSON_UNESCAPED_UNICODE);
function test_input($data)//表单过滤
{
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}



?>