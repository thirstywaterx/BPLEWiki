<?php
require_once("../ioput.class.php");
require_once("../constants.class.php");
require_once("../connect.php");
$output=new ioput(false);
$output->jsonoutput("success");
try{
    $conn = connect();
    if ($conn->connect_error) {
        throw new ProcessError(Constants::TEXT_CONNECTION_FAILED . $conn->connect_error);
    }
    $sql0 = "SELECT fileid, COUNT(*) AS count
    FROM workshopcomment
    WHERE reg_time >= NOW() - INTERVAL 7 DAY
    GROUP BY fileid
    ORDER BY count DESC
    LIMIT 5;";

    /*
    //收藏权重：
    SELECT ac.article, 
           COUNT(ac.article) AS comment_count, 
           COALESCE(c.collect_count, 0) AS collect_count,
           (COUNT(ac.article) + COALESCE(c.collect_count, 0)) AS total_count
    FROM articlecomment ac
    LEFT JOIN (
        SELECT itemid, COUNT(*) AS collect_count
        FROM collect
        WHERE type = 'article'
        GROUP BY itemid
    ) c ON ac.article = c.itemid
    GROUP BY ac.article
    ORDER BY total_count DESC
    LIMIT 1;
    */
    $result=$conn->query($sql0);
    $ids=array();
    if ($result->num_rows > 0) {
        while($row=$result->fetch_assoc()){
            array_push($ids,$row["fileid"]);
        }
    }else{
        array_push($ids,1);
    }
    $output->outputdata["results"]=$ids;
    $output->success();
    $conn->close();
}catch(ProcessError $e){
    $output->addmessage($e->getMessage());
}
$output->output();
