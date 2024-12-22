<?php
class ioput{
    //input
    public function __construct($jsoncheck=true) {
        if($jsoncheck){
            //json数据合并进_post中
            $headers = getallheaders();
            if (isset($headers['Content-Type']) && strpos($headers['Content-Type'],'application/json')!==false) {
                // 获取请求的原始数据
                $json = file_get_contents('php://input');
                
                // 将 JSON 数据解码为 PHP 数组
                $data = json_decode($json, true);
                
                // 检查解码是否成功
                if (json_last_error() === JSON_ERROR_NONE) {
                    $_POST=array_merge($_POST,$data);
                }
            }
        }
    }
    public static function postre($arr){
        foreach($arr as $key){
            if(!isset($_POST[$key])){
                $_POST[$key]=false;
            }
        }
    }
    public static function getre($arr){
        foreach($arr as $key){
            if(!isset($_GET[$key])){
                $_GET[$key]=false;
            }
        }
    }
    public static function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    public static function inputcheck($key,$method,$datatype){
        if($method==="post"){
            if(isset($_POST[$key])){
                if($datatype==="int"){
                    if(is_numeric($_POST[$key])){
                        $_POST[$key]=(int)round($_POST[$key]);
                        return true;
                    }else{
                        return false;
                    }
                }elseif($datatype==="float"){
                    if(is_numeric($_POST[$key])){
                        $_POST[$key]=(float)$_POST[$key];
                        return true;
                    }else{
                        return false;
                    }
                }else{//string
                    if($_POST[$key]===""){
                        return false;
                    }else{
                        return true;
                    }
                }
            }else{
                return false;
            }
        }else{
            if(isset($_GET[$key])){
                if($datatype==="int"){
                    if(is_numeric($_GET[$key])){
                        $_GET[$key]=(int)round($_GET[$key]);
                        return true;
                    }else{
                        return false;
                    }
                }elseif($datatype==="float"){
                    if(is_numeric($_GET[$key])){
                        $_GET[$key]=(float)$_GET[$key];
                        return true;
                    }else{
                        return false;
                    }
                }else{//string
                    if($_GET[$key]===""){
                        return false;
                    }else{
                        return true;
                    }
                }
            }else{
                return false;
            }
        }
    }
    function checknes($bool,$msg,$die=false){
        if(!$bool){
            $this->allowcontinue=false;
            $this->addmessage($msg);
            if($die){
                die(json_encode($this->outputdata,JSON_UNESCAPED_UNICODE));
            }
        }
    }
    private $allowcontinue=true;
    function allowcontinue(){
        return $this->allowcontinue;
    }
    //output
    public $outputdata;
    public function jsonoutput($state,$hasnotice=true){
        header("Content-Type:application/json;charset=UTF-8");
        
        //output逻辑
        $this->outputdata=array();
        if($hasnotice){
            $this->outputdata["notice"]="";
        }
        if($state==="success"){
            $this->outputdata["success"]=false;
        }elseif($state==="state"){
            $this->outputdata["state"]="";
        }
        
        //monolog相关
        require_once("vendor/autoload.php");
        // 创建日志通道
        $log = new Monolog\Logger('running_warning');
        $log->pushHandler(new Monolog\Handler\StreamHandler(__DIR__.'/source/log/warning.log', Monolog\Logger::WARNING));
        $thisArg=$this;
        // 自定义错误处理
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($log,$thisArg) {
            $now=time();
            $log->error("Error: [$errno] $errstr in $errfile on line $errline",array($now));
            $thisArg->outputdata["notice"].="错误: [$now][$errno].请联系管理员";
            return true;
        });
        // 自定义异常处理
        set_exception_handler(function ($exception) use ($log,$thisArg) {
            $now=time();
            $log->critical("Uncaught exception: " . $exception->getMessage(),array($now));
            $thisArg->outputdata["notice"].="未知异常 [$now].请联系管理员";
            $thisArg->output();
        });
        
    }
    public function addmessage($str){
        $this->outputdata["notice"].=$str." ";
    }
    public function success($str=""){
        $this->outputdata["success"]=true;
        if($str!==""){
            $this->addmessage($str);
        }
    }
    public function setstate($str){
        $this->outputdata["state"]=$str;
    }
    public function output(){
        echo json_encode($this->outputdata,JSON_UNESCAPED_UNICODE);
    }
}
class ProcessError extends Exception{}