<?php
require_once("../ioput.class.php");
require_once("../constants.class.php");
require_once("../connect.php");
$output=new ioput();
$output->jsonoutput("success");
try{
    $conn = connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $sql0 = "SELECT id FROM article 
    ORDER BY id DESC 
    LIMIT 5";
    $result=$conn->query($sql0);
    $ids=array();
    if ($result->num_rows > 0) {
        while($row=$result->fetch_assoc()){
            array_push($ids,$row["id"]);
        }
    }
    $output->outputdata["results"]=$ids;
    $output->success();
    $conn->close();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();