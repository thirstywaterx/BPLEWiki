<?php
require_once("../constants.class.php");
require_once("../usersign.class.php");
require_once("../ioput.class.php");
$usersign=new userSign();
$output=new ioput();
$issignin=$usersign->issignin();
$output->jsonoutput("success");
if(!$issignin["success"]){
    $output->addmessage(Constants::TEXT_HAS_NOT_SIGN_IN);
}elseif(empty($_POST["title"]) or empty($_POST["content"]) or empty($_POST["cover"])){
    $output->addmessage(Constants::TEXT_FORM_INCOMPLETE);
}else{

    $title=ioput::test_input($_POST["title"]);
    $content=addslashes($_POST["content"]);
    $cover=addslashes($_POST["cover"]);
    
    $signinfo=$usersign->userinfo();
    if($signinfo["user"]["ban"]==0){
      $conn=connect();
      $output->checknes(!$conn->connect_error,Constants::TEXT_CONNECTION_FAILED . $conn->connect_error,true);
        $stmt = $conn->prepare("INSERT INTO article (title, content, user, cover, type)
        VALUES (?, ?, ?, ?, 'md')");
        $stmt->bind_param("ssis", $bp_title, $bp_content, $bp_user, $bp_cover);
        
        $bp_title = $title;
        $bp_content = $content;
        $bp_user = $signinfo['user']['id'];
        $bp_cover = $cover;
        $stmt->execute();
        
        $output->success(Constants::TEXT_CREATE_SUCCESS);
        $output->outputdata["id"]=$conn->insert_id;
      $stmt->close();
      $conn->close();
    }else{
        $output->addmessage(Constants::TEXT_ACCOUNT_BANNED);
    }
    
}
$output->output();