<?php
date_default_timezone_set("Asia/Shanghai");
session_start();
require_once("../php/connect.php");
$admin=true;
$state="sign";
$signin=false;
$htmlmsg="";
$passwordhash="$2y$10$"."uu85vjCBdefbIcXPTAifduFlmIVzaLX2GFJWvFPY0N18f9otKlJ0S";
$psw0="";
$psw1="";
function addmessage($msg){
    global $htmlmsg;
    $htmlmsg.="<p>{$msg}</p>";
}
if(empty($_GET["type"])){
    if(isset($_SESSION["isadmin"])&&$_SESSION["isadmin"]===true){
        $state="menu";
    }else{
        $state="sign";
    }
}elseif($_GET["type"]==="sign"){
    addmessage("signin：");
    if(isset($_POST["psw0"])){
        $psw0=$_POST["psw0"];
    }
    if(isset($_POST["psw1"])){
        $psw1=$_POST["psw1"];
    }
    if($psw0==date("d")*13 and (password_verify($psw1, $passwordhash))){
      //已登录
      $signin=true;
      $_SESSION["isadmin"]=true;
      addmessage("OK");
    }else{
      addmessage("fail");
    }
    $state="signin";
}elseif($_GET["type"]==="signout"){
    unset($_SESSION["isadmin"]);
    $state="control";
    addmessage("已退登");
}elseif(isset($_SESSION["isadmin"])&&$_SESSION["isadmin"]===true){
    $state="control";
    if($_GET["type"]==="sql"){
        if(empty($_POST["sql"])){
            $_POST["sql"]="";
        }
        $conn=connect();
        if ($conn->connect_error) {
            addmessage("连接失败: ".$conn->connect_error);
        }else{
            $sql=$_POST["sql"];
            if($conn->query($sql)===TRUE){
                addmessage("sql指令执行成功"); 
            }else{
                addmessage("sql指令执行发生错误: {$conn->error}");
            }
            $conn->close();
        }
    }elseif($_GET["type"]==="ip"){
        addmessage(ip());
    }elseif($_GET["type"]==="ban"&&isset($_POST["banuid"])){
        $bu=$_POST["banuid"];
        if(is_numeric($bu)){
            $conn=connect();
            if ($conn->connect_error) {
                die("连接失败: ".$conn->connect_error);
            }
            $ban=$_POST["banmodel"]==="deban"?0:1;
            $sql="UPDATE user 
            SET ban='{$ban}'
            WHERE id='{$bu}'";
            if($conn->query($sql)===TRUE){
                if($ban){
                    addmessage("封禁指令执行成功");
                }else{
                    addmessage("解封指令执行成功");
                }
            }else{
                addmessage("封禁指令执行发生错误: {$conn->error}");
            } 
            $conn->close();
        }else{
            addmessage("封禁指令执行发生错误: uid非数字或为空");
        }
    }elseif($_GET["type"]==="deletearticle"&&isset($_POST["aid"])){
        $aid=$_POST["aid"];
        if(is_numeric($aid)){
            $conn=connect();
            if ($conn->connect_error) {
                die("连接失败: ".$conn->connect_error);
            }
            $sql="DELETE FROM article 
            WHERE id='{$aid}'";
            if($conn->query($sql)===TRUE){
                addmessage("删除指令执行成功");
            }else{
                addmessage("删除指令执行发生错误: {$conn->error}");
            } 
            $conn->close();
        }else{
            addmessage("删除指令执行发生错误: aid非数字或为空");
        }
    }elseif($_GET["type"]==="r2dir"){
      if(is_array(json_decode($_POST["json"],true))){
        file_put_contents("../php/source/r2dir.json",$_POST["json"]);
        addmessage("目录列表修改执行成功");
      }else{
        addmessage("目录列表修改执行发生错误: 错误的json");
      }
    }
}

function ip() {
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

// ### html
$html = array(
  "sign" => <<<EOF

<form method="POST" data-type="sign" onsubmit="return control(this)">
  <div class="mb-3 mt-3">
	<input type="password" class="form-control" id="password"  placeholder="公钥" name="psw0">
  </div>
  <div class="mb-3">
	<input type="password" class="form-control" id="content" placeholder="私钥" name="psw1">
  </div>
  <p><input type="submit" class="btn btn-primary" value="登录"></p>
</form>

EOF
,
  "menu" => <<<EOF

<form method="POST" data-type="sql" onsubmit="return control(this)">
  <div class="mb-3 mt-3">
	<textarea class="form-control" id="sql" placeholder="sql指令" name="sql" ></textarea>
  </div>
  <p><input type="submit" class="btn btn-primary" value="提交"></p>
</form>

<!-- <form> -->
  <div class="mb-3 mt-3">
	<textarea class="form-control" id="jseval"  placeholder="JavaScript eval"></textarea>
  </div>
  <button class="btn btn-primary" onclick="eval(document.getElementById('jseval').value)">执行</button>
<!-- </form> -->

<form method="POST" data-type="ban" onsubmit="return control(this)">
  <div class="mb-3 mt-3">
    <p><input type="number" class="form-control" name="banuid" value="ban" min="1" step="0" placeholder="封禁用户id"></p>
    <p><input type="radio" class="form-check-input" name="banmodel" value="ban">封禁</p>
    <p><input type="radio" class="form-check-input" name="banmodel" value="deban">解封</p>
    <p><input type="submit" class="btn btn-primary" value="提交"></p>
  </div>
</form>

<form method="POST" data-type="deletearticle" onsubmit="return control(this)">
  <div class="mb-3 mt-3">
    <p><input type="number" class="form-control" name="aid" min="1" step="1" placeholder="删除文章id"></p>
    <p><input type="submit" class="btn btn-primary" value="提交"></p>
  </div>
</form>

<form method="POST" data-type="posttest" onsubmit="return false">
  <div class="mb-3 mt-3">
    <p><input type="url" class="form-control" name="url" placeholder="测试url"></p>
    <p><input type="text" class="form-control" name="send" placeholder="发送内容"></p>
    <p><input type="submit" class="btn btn-primary" value="提交" onclick="posttest(dqsa(`form[data-type='posttest'] input[type='url']`,1).value,dqsa(`form[data-type='posttest'] input[type='text']`,1).value)"></p>
  </div>
</form>
<script>
(function(){
    =()=>{
        ajax(
            success(r){
                
            }
        );
        return false;
    };
})();
</script>

<form method="POST" data-type="ip" onsubmit="return control(this)">
  <div class="mb-3 mt-3">
    <p><input type="radio" class="form-check-input" name="showmyip" value="show">显示自己的ip（不会保存）</p>
    <p><input type="submit" class="btn btn-primary" value="显示"></p>
  </div>
</form>

<div id="r2" class="mb-3 mt-3">
    r2：<button class="btn btn-primary" onclick="queryr2dir()">获取列表</button>
    <div id="r2dirlist"></div>
    <div><button onclick="addr2input()">+</button></div>
    <div><button onclick="r2send()">上传</button></div>
</div>

<form method="POST" data-type="signout" onsubmit="return control(this,true)">
  <div class="mb-3 mt-3">
    <p><input type="submit" class="btn btn-primary" value="退出登录"></p>
  </div>
</form>

EOF
);// ### html
header("Content-Type:application/json;charset=UTF-8");
if($state==="sign"){
  echo json_encode(array(
    "title"=>"管理员登录",
    "content"=>$html["sign"]
  ),JSON_UNESCAPED_UNICODE);
}elseif($state==="menu"){
  echo json_encode(array(
    "title"=>"管理员界面",
    "content"=>$html["menu"]
  ),JSON_UNESCAPED_UNICODE);
}elseif($state==="control"){
  echo json_encode(array(
    "notice"=>$htmlmsg
  ),JSON_UNESCAPED_UNICODE);
}elseif($state==="signin"){
  echo json_encode(array(
    "reload"=>$signin,
    "notice"=>$htmlmsg
  ),JSON_UNESCAPED_UNICODE);
}
?>