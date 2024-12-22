<?php
header("Content-Type:application/json;charset=UTF-8");
header('Access-Control-Allow-Origin: ');
echo file_get_contents("source/r2dir.json");