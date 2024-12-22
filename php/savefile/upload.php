<?php
require_once("../tebiBucket.class.php");
require_once("../ioput.class.php");
require_once("../usersign.class.php");
$usersign = new userSign();
$output = new ioput();

$tB = new tebiBucket();
$tB->createClient();

$output->jsonoutput("success");
try{
    $issignin = $usersign->issignin();
    if(!$issignin["success"]){
        throw new ProcessError($issignin["success"]);
    }
    $signinfo = $usersign->userinfo();
    if(!$signinfo["success"]){
        throw new ProcessError($signinfo["notice"]);
    }
    if($signinfo["user"]["ban"] != 0){
        throw new ProcessError("账号已被封禁");
    }
    ioput::postre(["title", "cover", "content"]);
    if(!($_POST["title"] && $_POST["cover"] && $_POST["content"])){
        throw new ProcessError("表单不完整");
    }
    if(!filter_var($_POST["cover"])){
        throw new ProcessError("封面是错误的url");
    }
    if (!isset($_POST["map"])) {
        $_POST["map"] = "";
    }
    if (empty($_POST["viewImg"])) {
        $_POST["viewImg"] = "";
    }
    $strtoken = strtok($_POST["viewImg"], ",");
    $imgs = array();
    while ($strtoken !== false) {
        if (filter_var($strtoken, FILTER_VALIDATE_URL)) {
            array_push($imgs, $strtoken);
        }
        if (count($imgs) > 20) {
            break;
        }
        $strtoken = strtok(",");
    }
    if (count($imgs) === 0) {
        $imgs = "cover";
    } else {
        $imgs = implode(",", $imgs);
    }
    if($_FILES["file"]["error"] > 0){
        throw new ProcessError("错误：" . $_FILES["file"]["error"]);
    }
    if($_FILES["file"]["size"] > 5242880){
        throw new ProcessError("文件过大");
    }
    if(!tebiBucket::checktype($_FILES["file"]["type"], $_FILES["file"]["name"])){
        throw new ProcessError("文件类型错误");
    }
    
    $conn = connect();
    if($conn->connect_error){
        throw new ProcessError("连接失败: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("INSERT INTO fileinfo (user, name, map, title, cover, content, viewImg) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $bp_uid, $bp_fn, $bp_map, $bp_title, $bp_cover, $bp_content, $bp_vi);

    $bp_uid = (int)$issignin["uid"];
    $bp_fn = ioput::test_input($_FILES["file"]["name"]);
    $bp_map = $_POST["map"];
    $bp_title = $_POST["title"];
    $bp_cover = $_POST["cover"];
    $bp_content = $_POST["content"];
    $bp_vi = $imgs;

    if(!$stmt->execute()){
        throw new ProcessError($stmt->error);
    }
    $state = $tB->upload("savefiles/" . ($stmt->insert_id), $_FILES["file"]["tmp_name"]);
    if($state != "200"){
        throw new ProcessError("上传失败：" . $state);
    }

    // 将 insert_id 添加到输出对象中
    $output->outputdata["id"] = $stmt->insert_id;

    $output->success("上传成功");
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();