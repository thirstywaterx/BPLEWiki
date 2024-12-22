<?php
session_start();
require_once("../usersign.class.php");
require_once("../vcode.class.php");
require_once("../connect.php");
require_once("../ioput.class.php");
$usersign=new userSign();
$cemail=new vCode("cemail");
$output=new ioput();
try{
    if($_GET["step"]==="0"){
      $output->jsonoutput("state");
      $issignin=$usersign->issignin();
      if(!$issignin["success"]){
          $output->setstate("unsigned");
          throw new ProcessError("未登录");
      }
      if(empty($_SESSION["cemail"])){
          $output->setstate("nosession");
          throw new ProcessError("会话不存在，从开始绑定");
      }
      $cemail->load();
      if($issignin["uid"]!==$cemail->user["id"]){
          $cemail->delete();
          $output->setstate("differentid");
          throw new ProcessError("登录状态错误");
      }
      if(time()-$cemail->lastcontrol>600){
          $cemail->delete();
          $output->setstate("overtime");
          throw new ProcessError("长时间未操作");
      }
      if($cemail->user["email"]===""){
          $output->setstate("inputemail");
          throw new ProcessError("正在输入邮箱");
      }
      $output->setstate("cemailing");
      throw new ProcessError("正在验证");
    }elseif($_GET["step"]==="1"){
      $output->jsonoutput("success");
      $issignin=$usersign->issignin();
      if(!$issignin["success"]){
          throw new ProcessError($issignin["notice"]);
      }
      $signinfo=$usersign->userinfo();
      if(!password_verify($_POST["password"], $signinfo["user"]["password"])){
          throw new ProcessError("密码错误");
      }
      $cemail->create();
      $cemail->user["id"]=$issignin["uid"];
      $cemail->user["showname"]=mb_substr($signinfo["user"]["username"],0,1)."****";
      $cemail->user["email"]="";
      $cemail->save();
      $output->success();
    }elseif($_GET["step"]==="2"){
      if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
          throw new ProcessError("邮箱格式错误");
      }
      if(!$cemail->load()){
          throw new ProcessError("会话为空");
      }
      $conn=connect();
      if ($conn->connect_error) {
        throw new ProcessError("连接失败: " . $conn->connect_error);
      }
      $stmt=$conn->prepare("select `id`,`email`,`emailbind` from `user` where `email` =?");
      $stmt->bind_param("s", $email);
      $email=ioput::test_input($_POST["email"]);
      if(!$stmt->execute()){
          throw new ProcessError($stmt->error);
      }
      $result=$stmt->get_result();
      $row=$result->fetch_assoc();
      if($row>0 and $row["emailbind"]==1){
          if($row["id"]===$cemail->user["id"]){
            throw new ProcessError("您已绑定该邮箱");
          }else{
            throw new ProcessError("该邮箱已被绑定");
          }
      }
      //保存
      $cemail->user["email"]=ioput::test_input($_POST["email"]);
      $cemail->save();
      $output->success();
    }elseif($_GET["step"]==="3"){
        $output->jsonoutput("success");
        $issignin=$usersign->issignin();
        if(!$cemail->load()){
            throw new ProcessError("会话为空");
        }
        if(!$issignin["success"]){
            $cemail->delete();
            throw new ProcessError("未登录");
        }
        if($issignin["uid"]!==$cemail->user["id"]){
            $cemail->delete();
            throw new ProcessError("登录状态错误");
        }
        if(!$cemail->checkcooling(57)){
            throw new ProcessError("发送过快");
        }
        if(!$cemail->checkovertime(600)){
            $cemail->delete();
            throw new ProcessError("长时间未操作，请刷新");
        }
        if($cemail->user["email"]===""){
            throw new ProcessError("请先输入邮箱");
        }
        $cemail->subject="您的邮箱绑定验证码";
        $sended=$cemail->send();
        if(!$sended){
            throw new ProcessError($sended);
        }
        $cemail->save();
        $output->success("验证码已发送");
    }elseif($_GET["step"]==="4"){
      $output->jsonoutput("success");
      if(empty($_SESSION["cemail"])){
          throw new ProcessError("空的会话");
      }
      if(empty($_POST["code"])){
          throw new ProcessError("表单不完整");
      }
      $issignin=$usersign->issignin();
      $cemail->load();
      if(!$issignin["success"]){
          throw new ProcessError("未登录");
      }
      if($issignin["uid"]!==$cemail->user["id"]){
          $cemail->delete();
          throw new ProcessError("登录状态错误");
      }
      if(!$cemail->checkovertime(600)){
          $cemail->delete();
          throw new ProcessError("长时间未操作，请刷新");
      }
      if(!$cemail->checkcode(ioput::test_input($_POST["code"]))){
          $cemail->save();
          throw new ProcessError("验证码错误");
      }
      require_once("../connect.php");
      $conn=connect();
      // 检测连接
      if ($conn->connect_error) {
          throw new ProcessError("连接失败: ".$conn->connect_error);
      }
      $stmt = $conn->prepare("UPDATE `user` 
      SET `email`=?,`emailbind`=1 
      WHERE `id`=?");
      $stmt->bind_param("si", $bp_email, $bp_id);
      $bp_email = $cemail->user["email"];
      $bp_id = (int)$cemail->user["id"];
      if(!$stmt->execute()){
          throw new ProcessError($stmt->error);
      }
      $output->success("绑定成功");
      $stmt->close();
      $conn->close();
      $cemail->delete();
      
    }elseif($_GET["step"]==="5"){
      $output->jsonoutput("success");
      $cemail->delete();
      $output->success();
    }
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();
