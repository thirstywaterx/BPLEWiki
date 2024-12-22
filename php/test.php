<?php
if ($_FILES["file"]["error"] > 0){
    echo "错误：" . $_FILES["file"]["error"] . "<br>";
}elseif($_FILES["file"]["size"]<5242880){
    //5mb
    //application/octet-stream
    echo "文件类型: " . $_FILES["file"]["type"] . "<br>";
    echo "文件大小: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "获取后缀: " .pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION) ;
}