<?php
class userCollect{
  public $uid=false;
  public function __construct($uid){
      $this->uid=$uid;
  }
  private function falsetype($type){
    return !in_array($type,array("article","workshop"));
  }
  public function get($type){
      $uid=$this->uid;
      if($this->falsetype($type)){
          return (array(
            "success"=>false,
            "notice"=>"type不正确"
          ));
      }
      require_once("connect.php");
      $conn = connect();
      if ($conn->connect_error) {
          return (array(
            "success"=>false,
            "notice"=>"连接失败: " . $conn->connect_error
          ));
      }
      $result = $conn->query("SELECT `itemid` FROM `collect` WHERE `uid` = {$uid} and `type` = '{$type}'");
      $collects=array();
      if($result->num_rows > 0){
          while ($row = $result->fetch_assoc()) {
              $collects[]=$row['itemid'];
          }
      }
      $conn->close();
      return (array(
        "success"=>true,
        "collects"=>$collects
      ));
  }
  public function set($type,$itemid){
      $uid=$this->uid;
      if($this->falsetype($type)){
          return (array(
            "success"=>false,
            "notice"=>"type不正确"
          ));
      }
      if(!is_numeric($itemid)){
          return (array(
            "success"=>false,
            "notice"=>"itemid不正确"
          ));
      }
      require_once("connect.php");
      $conn = connect();
      if ($conn->connect_error) {
          return (array(
            "success"=>false,
            "notice"=>"连接失败: " . $conn->connect_error
          ));
      }
      $result = $conn->query("SELECT `itemid` FROM `collect` WHERE `uid` = {$uid} AND `type` = '{$type}' AND `itemid` = {$itemid}");
      $sql="";
      $control="";
      if($result->num_rows > 0){
          $control="取消收藏";
          $sql="DELETE FROM `collect` WHERE `uid` = {$uid} AND `type` = '{$type}' AND `itemid` = {$itemid}";
      }else{
          $control="收藏";
          $sql="INSERT INTO `collect` (`uid`, `itemid`, `type`) VALUES ({$this->uid}, {$itemid}, '{$type}')";
      }
      if($conn->query($sql)===true){
          return (array(
            "success"=>true,
            "notice"=>$control."成功"
          ));
      }else{
          return (array(
            "success"=>false,
            "notice"=>$control."sql err:".$conn->error
          ));
      }
  }
}