<?php
class vCode {
  public $code;
  public $lastsend;
  public $reg;
  public $lastcontrol;
  public $savename;
  public $sendcooling;
  public $user;
  public $subject;
  function __construct($savename) {
    $this->savename = $savename;
  }
  function create() {
    //载入变量
    $this->code=rand(100000,999999);
    $this->lastsend=0;
    $this->reg=time();
    $this->user=array();
  }
  function setcode($code){
    $this->code=$code;
  }
  function load(){
    //从session载入
    if(isset($_SESSION[$this->savename])){
        $vcode=unserialize($_SESSION[$this->savename]);
        $this->code=$vcode["code"];
        $this->lastsend=$vcode["lastsend"];
        $this->lastcontrol=$vcode["lastcontrol"];
        $this->reg=$vcode["reg"];
        $this->user=$vcode["user"];
        return true;
    }else{
        return false;
    }    
  }
  function checkcode ($code) {
    if(strval($this->code) === $code){
        return true;
    }else{
        return false;
    }
  }
  function checkovertime($overtime){
    if(time()-$this->lastcontrol<$overtime){
        return true;
    }else{
        return false;//超时
    }
  }
  function checkcooling($sendcooling) {
    if(time()-$this->lastsend>$sendcooling){
        return true;
    }else{
        return false;//发送过快
    }
  }
  function send () {
    $this->lastsend=time();
    //require_once("funcsendemail.php");
    require_once("funcsendemail2.php");
    $sendcontent=file_get_contents("source/bindemailstyle.html",true);
    foreach(array(
      "{username}"=>$this->showname,
      "{code}"=>$this->code
    ) as $before=>$after){
        $sendcontent=str_replace($before,$after,$sendcontent);
    }
    return sendemail2($this->subject,$sendcontent,$this->user["email"]);
    //sendemail
  }
  function save () {
    $save=array(
        "user"=>$this->user,
        "code"=>$this->code,
        "lastsend"=>$this->lastsend,
        "lastcontrol"=>time(),
        "reg"=>$this->reg,
    );
    $_SESSION[$this->savename]=serialize($save);
  }
  function delete() {
    unset($_SESSION[$this->savename]);
  }
}