<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../constants.class.php");

$usersign=new userSign();
$output=new ioput();

$output->jsonoutput("success");

try {
    if(empty($_POST["title"]) or empty($_POST["content"]) or empty($_POST["cover"]) or empty($_POST["aid"])){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    $title=ioput::test_input($_POST["title"]);
    $content=$_POST["content"];
    $cover=$_POST["cover"];
    $articleid=(int)$_POST["aid"];
    if(!is_numeric($articleid)){
        throw new ProcessError(Constants::TEXT_TYPE_ERROR.": articleid: not number");
    }
    
    $issignin=$usersign->issignin();
    if(!$issignin["success"]){
        throw new ProcessError(Constants::TEXT_HAS_NOT_SIGN_IN);
    }
    $signinfo=$usersign->userinfo();
    if($signinfo["user"]["ban"]!=0){
        throw new ProcessError(Constants::TEXT_ACCOUNT_BANNED);
    }
    $conn=connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $sql0 = "SELECT * FROM article WHERE id ='{$articleid}'";
    $result=$conn->query($sql0);
    $row=$result->fetch_assoc();
    if ($row == 0) {
        throw new ProcessError(Constants::TEXT_ARTICLE_NOT_EXIST);
    }
    if($row["user"]!=$signinfo["user"]["id"]){
        throw new ProcessError(Constants::TEXT_UNAUTHORIZED);
    }
    
    //文章属于该用户
    $stmt = $conn->prepare("UPDATE article
    SET `title` = ?, `content` = ?, `cover` = ?
    WHERE `id` = ?;");
    $stmt->bind_param("sssi", $bp_title, $bp_content, $bp_cover, $bp_id);
    $bp_title = $title;
    $bp_content = $content;
    $bp_cover = $cover;
    $bp_id = $articleid;
    $stmt->execute();
    $output->success(Constants::TEXT_REVISE_SUCCESS);
    $stmt->close();
      
} catch (ProcessError $e) {
    $output->addmessage($e->getMessage());
} finally {
    $output->output();
}
