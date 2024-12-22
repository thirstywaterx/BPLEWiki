<?php
session_start();
require_once("../usersign.class.php");
require_once("../vcode.class.php");
require_once("../ioput.class.php");
$usersign=new userSign();

ioput::getre(["step"]);
$output=new ioput();

if(!ioput::inputcheck("step","get","int")||!(0<=$_GET["step"]&&$_GET["step"]<=4)){
    $output->jsonoutput("success");
    $output->addmessage("无效访问");
}elseif($_GET["step"]===0){


    $output->jsonoutput("state");
    $result=$usersign->issignin();
    if($result["success"]){
        $output->setstate("signed");
        $output->addmessage("您已登录");
    }else{
        if(empty($_SESSION["signup"])){
            $output->setstate("nosession");
            $output->addmessage("会话不存在，从开始注册");
        }else{
            $output->setstate("signuping");
            $output->addmessage("正在验证");
        }
    }


}elseif($_GET["step"]===1){


    $output->jsonoutput("success");
    ioput::postre(["username","password","email"]);
    $output->checknes($_POST["username"]&&$_POST["password"]&&$_POST["email"],"表单不完整");
    if($output->allowcontinue()){
        $username=ioput::test_input($_POST["username"]);
        $email=ioput::test_input($_POST["email"]);
        $result=$usersign->checkunique($username,$email);
        if($result===true){
            $password=password_hash(substr(ioput::test_input($_POST["password"]),0,30), PASSWORD_DEFAULT);
            $signup=new vCode("signup");
            $signup->create();
            $signup->user["username"]=$username;
            $signup->user["password"]=$password;
            $signup->user["email"]=$email;
            $signup->setcode(rand(10000,99999));
            $signup->save();
            
            $output->success();
        }else{
            $output->addmessage($result["notice"]);
        }
    }


}elseif($_GET["step"]===2){

    $output->jsonoutput("success");
    $signup=new vCode("signup");
    if($signup->load()){
        if($signup->checkcooling(50)){
            //发送
            $signup->showname=mb_substr($signup->user["username"],0,-1)."*";
            $signup->subject="您的邮箱绑定验证码";
            $sended=$signup->send();
            if($sended){
                $signup->save();
                $output->success("验证码已发送");
            }else{
                $output->addmessage($sended);
            }
        }else{
            $output->addmessage("发送过快");
        }
    }else{
        $output->addmessage("会话为空");
    }


}elseif($_GET["step"]===3){


    $output->jsonoutput("success");
    $signup=new vCode("signup");
    if($signup->load() and isset($_POST["code"])){
        if(time()-$signup->reg<1200){
            if($signup->checkcode(ioput::test_input($_POST["code"]))){
                $result=$usersign->createuser(array(
                    "username"=>$signup->user["username"],
                    "password"=>$signup->user["password"],
                    "email"=>$signup->user["email"]
                ));
                if($result["success"]){
                    $usersign->uid=$result["uid"];
                    $usersign->login();
                    $output->success("注册成功");
                }else{
                    $output->addmessage("注册失败 ".$result["notice"]);
                }
                $signup->delete();
            }else{
                $output->addmessage("验证码错误");
            }
        }else{
            $signup->delete();
            $output->addmessage("超时，请重新注册");
        }
    }else{
        $output->addmessage("空的会话或无验证码");
    }


}elseif($_GET["step"]===4){


    $output->jsonoutput("success");
    if(isset($_SESSION["signup"])){
        unset($_SESSION["signup"]);
        $output->addmessage("取消成功");
    }else{
        $output->addmessage("空的会话");
    }


}
$output->output();