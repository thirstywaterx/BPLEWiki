<?php
require_once("../usersign.class.php");
require_once("../ioput.class.php");
require_once("../constants.class.php");
$usersign=new userSign();
$output=new ioput();
$output->jsonoutput("success");
try{
    $issignin=$usersign->issignin();
    if(!$issignin["success"]){
        $output->outputdata["nosign"]=true;
        throw new ProcessError(Constants::TEXT_HAS_NOT_SIGN_IN);
    }
    $signinfo=$usersign->userinfo();
    $conn=connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    if(isset($_POST["keyword"])){
        //搜索检索
        $keyword=$_POST["keyword"];
        $stmt=$conn->prepare("SELECT id FROM fileinfo WHERE (title LIKE ? OR content LIKE ?) AND user = {$usersign->uid}");
        $stmt->bind_param("ss", $bp_kw, $bp_kw);
        $bp_kw="%".addcslashes($_POST["keyword"],"%_")."%";
        $stmt->execute();
        
        $result=$stmt->get_result();
        if ($result->num_rows === 0) {
            throw new ProcessError(Constants::TEXT_NO_RESULT);
        }
        // 输出数据
        $output->success();
        $output->outputdata["results"]=array();
        while($row = $result->fetch_assoc()) {
            array_push($output->outputdata["results"],$row["id"]);
        }
    }else{
        $result=$conn->query("SELECT id FROM fileinfo WHERE user = {$usersign->uid}");
        if ($result->num_rows === 0) {
            throw new ProcessError(Constants::TEXT_NO_RESULT);
        }
        // 输出数据
        $output->success();
        $output->outputdata["results"]=array();
        while($row = $result->fetch_assoc()) {
            array_push($output->outputdata["results"],$row["id"]);
        }
    }
    $conn->close();
    
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();

