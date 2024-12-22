<?php
require_once("../constants.class.php");
require_once("../usersign.class.php");
require_once("../ioput.class.php");

$usersign=new userSign();
$output=new ioput();
$output->jsonoutput("success");

try {
    $issignin=$usersign->issignin();
    if(!$issignin["success"]){
        throw new ProcessError($issignin["notice"]);
    }
    $signinfo=$usersign->userinfo();
    if($signinfo["user"]["ban"]!==0){
        throw new ProcessError(Constants::TEXT_ACCOUNT_BANNED);
    }
    if(!(isset($_POST["content"]) and isset($_POST["tocomment"]) and isset($_POST["article"]))){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    $conn=connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $sql="INSERT INTO articlecomment (user, content, article, tocomment)
    VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $bp_user, $bp_content, $bp_article, $bp_tocomment);
    $bp_user=(int)$signinfo["user"]["id"];
    $bp_content=ioput::test_input($_POST["content"]);
    $bp_article=(int)ioput::test_input($_POST["article"]);
    $bp_tocomment=is_numeric($_POST["tocomment"])?(int)$_POST["tocomment"]:NULL;
    if(!$stmt->execute()){
        throw new ProcessError($conn->close());
    }
    $output->success(Constants::TEXT_SEND_SUCCESS);
    $output->outputdata["id"]=$stmt->insert_id;
} catch (ProcessError $e) {
    $output->addmessage($e->getMessage());
}
$output->output();