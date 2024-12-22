<?php
require_once("token.class.php");
class userSign {
  private $ClassToken;
  public $uid=false;
  public $token;
  public $user;
  function __construct() {
    $this->ClassToken = new token();
  }
  function checktoken($token) {
    //已登录的操作
    $result=$this->ClassToken->decode($token);
    if($result["success"]){
        $this->uid=$result["payload"]->uid;
        $this->token=$token;
        return true;
    }else{
        return $result["error"];
    }
  }
  function issignin() {
    //是否登录
    if(empty($_COOKIE["token"])){
        return array("success"=>false,"notice"=>"无token");
    }else{
        $result=$this->checktoken($_COOKIE["token"]);
        if($result===true){
            return array("success"=>true,"uid"=>$this->uid);
        }else{
            return array("success"=>false,"notice"=>$result);
        }
    }
  }
  function userinfo(){
      if($this->uid===false){
        return array(
          "success"=>false,
          "notice"=>"还未验证token"
        );
      }
      require_once("connect.php");
    
      $conn = connect();
      if ($conn->connect_error) {
          return array(
            "success"=>false,
            "notice"=>"连接失败: " . $conn->connect_error
          );
      }
      $stmt=$conn->prepare("SELECT * FROM `user` WHERE `id` =?");
      $stmt->bind_param("i", $bp_id);
      $bp_id=(int)$this->uid;
      if(!$stmt->execute()){
          return array(
            "success"=>false,
            "notice"=>$stmt->error
          );
      }
      $result=$stmt->get_result();
      $row=$result->fetch_assoc();
      $stmt->close();
      $conn->close();
      if ($row > 0) {
          return array(
              "success"=>true,
              "user"=>array(
                  "id"=>$row["id"],
                  "username"=>$row["username"],
                  "password"=>$row["password"],
                  "email"=>$row["email"],
                  "emailbind"=>$row["emailbind"],
                  "nickname"=>$row["nickname"],
                  "avatar"=>$row["avatar"],
                  "ban"=>$row["ban"],
                  "token"=>$this->token
              )
          );
      }else{
          return array(
              "issigned"=>false,
              "notice"=>"查无此人"
          );
      }
  }
  function createuser($info){
      require_once("connect.php");
    
      $conn = connect();
      if ($conn->connect_error) {
          return (array(
            "success"=>false,
            "notice"=>"连接失败: " . $conn->connect_error
          ));
      }
      $stmt = $conn->prepare("INSERT INTO user (username, password, email, emailbind)
      VALUES (?, ?, ?, 1)");
      $stmt->bind_param("sss", $bp_un, $bp_pw, $bp_email);
      $bp_un=$info["username"];
      $bp_pw=$info["password"];
      $bp_email=$info["email"];
      if($stmt->execute()){
        return array(
            "success"=>true,
            "uid"=>$stmt->insert_id
        );
      }else{
          return array(
            "success"=>false,
            "notice"=>$stmt->error
        );
      }
      $stmt->close();
  }
  function changepassword($newpassword){
      require_once("connect.php");
      
      if(!$this->uid){
          return (array(
            "success"=>false,
            "notice"=>"uid未知"
          ));
      }
      
      $conn = connect();
      if ($conn->connect_error) {
          return (array(
            "success"=>false,
            "notice"=>"连接失败: " . $conn->connect_error
          ));
      }
      $stmt = $conn->prepare("UPDATE `user` 
      SET `password`=? 
      WHERE id={$this->uid}");
      $stmt->bind_param("s", $bp_pw);
      $bp_pw=$newpassword;
      if($stmt->execute()){
        return array(
            "success"=>true
        );
      }else{
          return array(
            "success"=>false,
            "notice"=>$stmt->error
        );
      }
      $stmt->close();
  }
  function login(){
    $this->ClassToken->create(array(
        "uid"=>$this->uid,
        "ip"=>self::ip(),
        "time"=>time()
    ));
    $this->token = $this->ClassToken->jwt;
    setcookie("token", $this->token, time()+3600*24*30, "/php/");
  }
  function checkpassword($username,$password){
    require_once("connect.php");
    $conn=connect();
    if ($conn->connect_error) {
      return array(
        "success"=>false,
        "notice"=>"连接失败: " . $conn->connect_error
      );
    }
    $sql = "SELECT `id`,`password` FROM `user` WHERE `username` =?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("s", $bp_un);
    $bp_un=$username;
    if($stmt->execute()){
        $result=$stmt->get_result();
        $row=$result->fetch_assoc();
    }else{
        return array(
            "success"=>false,
            "notice"=>$stmt->error
        );
    }
    $stmt->close();
    if($row > 0) {
        if(password_verify($password, $row["password"])){
            return array(
                "success"=>true,
                "uid"=>$row["id"]
            );
        }else{
            return array(
                "success"=>false,
                "notice"=>"密码错误"
            );
        }
    }else{
        return array(
            "success"=>false,
            "notice"=>"无效的用户名"
        );
    }
  }
  function checkunique($username,$email){
    //用户名和邮箱可用：true
    require_once("connect.php");
    $conn=connect();
    if ($conn->connect_error) {
        return "连接失败: " . $conn->connect_error;
    }
    $sql = "SELECT `username`,`email`,`emailbind` FROM `user` WHERE `username` =? OR `email` =?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ss", $bp_un, $bp_em);
    $bp_un=$username;
    $bp_em=$email;
    if(!$stmt->execute()){
        return $stmt->error;
    }
    $result=$stmt->get_result();
    $row=$result->fetch_assoc();
    if($row>0){
        if($row["username"]===$username){
            return "用户名已存在";
        }elseif($row["email"]===$email&&$row["emailbind"]==1){
            return "邮箱已被绑定";
        }else{
            return true;
        }
    }else{
        return true;
    }
  }
  public static function ip() {
    //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $res =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    return $res;
    //dump(phpinfo());//所有PHP配置信息
  }
}