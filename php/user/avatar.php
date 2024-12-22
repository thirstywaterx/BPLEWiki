<?php
require_once '../connect.php';

// 获取 POST 数据（如果是 JSON 格式）
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$id = isset($data['id']) ? intval($data['id']) : null; // 用户 ID
$avatar = isset($data['avatar']) ? $data['avatar'] : null; // 头像链接

// 验证输入
if ($id === null || $avatar === null) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid input. 'id' and 'avatar' are required."
    ]);
    exit;
}

// 验证头像链接的合法性
if (!filter_var($avatar, FILTER_VALIDATE_URL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid avatar URL."
    ]);
    exit;
}

// 数据库连接
$conn = connect();
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

// 更新头像字段
$stmt = $conn->prepare("UPDATE user SET avatar = ? WHERE id = ?");
$stmt->bind_param("si", $avatar, $id);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Avatar updated successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to update avatar: " . $stmt->error
    ]);
}

// 清理资源
$stmt->close();
$conn->close();
?>