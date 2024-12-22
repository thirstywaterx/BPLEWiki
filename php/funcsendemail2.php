<?php
require_once('email.class.php');
//阿里云

function sendemail2($mailsubject,$mailbody,$mailto,$from=""){
    
    $smtpserver     = "smtpdm.aliyun.com";
    $smtpserverport = 80;
    // $smtpserver= "ssl://smtpdm.aliyun.com";
    // $smtpserverport = 465;
    $smtpusermail   = "";
    
    // 发件人的账号，填写控制台配置的发信地址,比如test***@@example.net
    $smtpuser       = "";
    // 访问SMTP服务时需要提供的密码（在控制台选择发信地址进行设置）
    $smtppass       = "";
    
    $mailsubject    = "=?UTF-8?B?" . base64_encode($mailsubject) . "?=";
    $mailtype       = "HTML";
    
    //可选，设置回信地址
    $smtpreplyto    = "***";
    
    $smtp           = new smtp($smtpserver, $smtpserverport, true, $smtpuser, $smtppass);
    $smtp->debug    = false;
    $cc   ="";
    $bcc  = "";
    $additional_headers = "";
    //设置发件人名称，名称用户可以自定义填写。
    $sender  = "猪吧维基";
    return $smtp->sendmail($mailto,$smtpusermail, $mailsubject, $mailbody, $mailtype, $cc, $bcc, $additional_headers, $sender, $smtpreplyto);
}
?>