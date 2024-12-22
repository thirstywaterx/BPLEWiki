<?php
require_once("../ioput.class.php");
require_once("../constants.class.php");
require_once("../connect.php");
$output=new ioput();
$output->jsonoutput("");
try{
    if(!isset($_REQUEST["do"])){
        throw new ProcessError();
    }
    $conn = connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $result=$conn->query("SELECT id FROM article ORDER BY id DESC");
    $output->outputdata["ids"]=array();
    while($row=$result->fetch_assoc()){
        array_push($output->outputdata["ids"],$row["id"]);
    }
    $conn->close();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();
