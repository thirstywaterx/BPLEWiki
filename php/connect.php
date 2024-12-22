<?php
function connect($db=""){
    static $conn = null;

    // 如果连接已经存在并且没有关闭，直接返回这个连接
    if ($conn && $conn->ping()) {
        return $conn;
    }
    
    // 如果连接不存在或者已经关闭，创建新的连接
    return new mysqli("localhost", "", "", $db);
}

?>