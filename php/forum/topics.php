<?php
require_once("../constants.class.php");
require_once("../functopics.php");
require_once("../vendor/autoload.php");
require_once("../ioput.class.php");
$output=new ioput();
$output->jsonoutput("success");
/*
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
// 创建日志服务
$logger = new Logger('debug_logger');
// 添加一些处理器
$logger->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG));
$logger->pushHandler(new FirePHPHandler());
// 现在你就可以用日志服务了
$logger->info('///savefile');
*/
try{
    if(empty($_POST["ids"])){
        throw new ProcessError(Constants::TEXT_FORM_INCOMPLETE);
    }
    $strtoken=strtok($_POST["ids"],",");
    $ids=array();
    while($strtoken!=false){
        if(is_numeric($strtoken)){
            array_push($ids,$strtoken);
        }
        
        if(count($ids)>20){
            break;
        }
        $strtoken=strtok(",");
    }
    $res=savefiles($ids);
    if(!$res["success"]){
        throw new ProcessError($res["notice"]);
    }
    $output->success();
    $output->outputdata["results"]=$res["results"];
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();