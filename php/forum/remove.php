<?php
header("Content-Type:application/json;charset=UTF-8");
require_once("../constants.class.php");
if(isset($_POST["id"]) and is_numeric($_POST["id"])){
    require_once("../usersign.class.php");
    require_once("../ioput.class.php");
    require_once("../connect.php");
    $usersign=new userSign();
    $signinfo=$usersign->issignin();
    $aid=$_POST["id"];
    if($signinfo["success"]){
        $conn=connect();
        if ($conn->connect_error) {
            die(Constants::TEXT_CONNECTION_FAILED.$conn->connect_error);
        }
        $sql="DELETE FROM topic 
        WHERE id='{$aid}' AND user = {$signinfo['uid']}";
        if($conn->query($sql)===TRUE){
            message(Constants::TEXT_DELETE_SUCCESS,true);
        }else{
            message(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_ERROR.": {$conn->error}");
        } 
        $conn->close();
    }else{
        message(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_HAS_NOT_SIGN_IN);
    }
}else{
    message(Constants::TEXT_DELETE_FAILD.": ".Constants::TEXT_FORM_INCOMPLETE);
}
function message($str,$success=false){
    echo json_encode(array(
        "success"=>$success,
        "notice"=>$str
    ),JSON_UNESCAPED_UNICODE);
}