<?php
// 引入connect.php
require_once '../connect.php';

try {
    // 创建数据库连接
    $conn = connect();

    // 查询帖子数据
    $sql = "SELECT * FROM topic";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("查询失败: " . $conn->error);
    }

    $topics = $result->fetch_all(MYSQLI_ASSOC);

    // 构造输出数据
    $data = [];
    foreach ($topics as $topic) {
        $avatar = mb_substr($topic['user'], 0, 1); // 用户名首字作为头像
        $avatarColors = ['#4299e1', '#48bb78', '#ed8936', '#9f7aea', '#f56565', '#38b2ac', '#667eea', '#f6ad55', '#fc8181', '#4fd1c5'];
        $avatarColor = $avatarColors[$topic['id'] % count($avatarColors)]; // 随机分配颜色

        // 计算该帖子下的评论数
        $stmtComments = $conn->prepare("SELECT COUNT(*) FROM topiccomment WHERE topic = ?");
        $stmtComments->bind_param("i", $topic['id']);
        $stmtComments->execute();
        $stmtComments->bind_result($replyCount);
        $stmtComments->fetch();
        $stmtComments->close();

        // 获取最后的回复时间
        $stmtLastReply = $conn->prepare("SELECT MAX(reg_time) FROM topiccomment WHERE topic = ?");
        $stmtLastReply->bind_param("i", $topic['id']);
        $stmtLastReply->execute();
        $stmtLastReply->bind_result($lastReply);
        $stmtLastReply->fetch();
        $stmtLastReply->close();

        $lastReply = $lastReply ? date("Y-m-d", strtotime($lastReply)) : null; // 如果没有评论，则为空

        $data[] = [
            'id' => (int)$topic['id'],
            'title' => $topic['title'],
            'category' => $topic['category'],
            'author' => $topic['user'],
            'avatar' => $avatar,
            'avatarColor' => $avatarColor,
            'replyCount' => $replyCount,
            'lastReply' => $lastReply
        ];
    }

    // 输出JSON数据
    echo json_encode([
        'success' => true,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // 输出错误信息
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>