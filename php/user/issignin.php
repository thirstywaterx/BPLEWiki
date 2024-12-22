<?php
header("Content-Type:application/json;charset=UTF-8");
require_once("../usersign.class.php");
$usersign=new userSign();
$output=$usersign->issignin();
if($output["success"] and isset($_GET["getinfo"])){
    $info=$usersign->userinfo();
    if($info["success"]){
        $un=$info["user"]["username"];
        $output["user"]=array(
            "id"=>$info["user"]["id"],
            "username"=>mb_substr($un,0,1).str_repeat('*', mb_strlen($un) - 1),
            "nickname"=>$info["user"]["nickname"],
            "avatar"=>$info["user"]["avatar"],
            "email"=>$info["user"]["email"],
            "ban"=>$info["user"]["ban"]
        );
    }else{
        $output["user"]=array(
            "notice"=>$info["notice"]
        );
    }
}
echo json_encode($output,JSON_UNESCAPED_UNICODE);