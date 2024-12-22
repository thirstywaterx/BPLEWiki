<?php
require_once("../ioput.class.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    require_once("../connect.php");
    require_once("../constants.class.php");
    
    if(empty($_POST["keyword"]) or empty(ioput::test_input($_POST["keyword"]))){
        throw new ProcessError(Constants::TEXT_EMPTY_KEY);
    }
    
    $conn=connect();
    if ($conn->connect_error) {
      throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $stmt=$conn->prepare("SELECT id FROM fileinfo WHERE title LIKE ? OR content LIKE ?");
    $stmt->bind_param("ss", $bp_kw, $bp_kw);
    $bp_kw="%".ioput::test_input($_POST["keyword"])."%";
    $stmt->execute();
    
    $result=$stmt->get_result();
    if ($result->num_rows === 0) {
        throw new ProcessError(Constants::TEXT_NO_RESULT);
    }
    // 输出数据
    $output->outputdata["results"]=array();
    while($row = $result->fetch_assoc()) {
        array_push($output->outputdata["results"],$row["id"]);
    }
    $output->success();
    $conn->close();
    
    
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();