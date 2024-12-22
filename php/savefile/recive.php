<?php
// 引入必要的类
require_once("/www/wwwroot/www.bplewiki.top/php/connect.php");
require_once("../tebiBucket.class.php");
require_once("../ioput.class.php");
require_once("../usersign.class.php");

$output = new ioput();
$usersign = new userSign();
$issignin = $usersign->issignin();
$signinfo = $usersign->userinfo();

$conn = connect();
$tebi = new tebiBucket();
$tebi->createClient();  // 初始化Tebi客户端

// 接收表单数据
$id = $_POST['id'] ?? null;
$title = $_POST['title'] ?? '';
$cover = $_POST['cover'] ?? '';
$content = $_POST['content'] ?? '';
$imageLinks = $_POST['viewImg'] ?? '';
$isFileChanged = isset($_POST['isFileChanged']) ? filter_var($_POST['isFileChanged'], FILTER_VALIDATE_BOOLEAN) : false;

$response = [
    "success" => false,
    "notice" => ""
];

if (!$id || !$title || !$cover || !$content) {
    $response['notice'] = '所有必填字段必须填写。';
    echo json_encode($response);
    exit();
}

$output->checknes($issignin["success"], "未登录");
$output->checknes($signinfo["user"]["ban"] == 0, "banned");

// 如果文件已更改，进行文件验证
if ($isFileChanged) {
    $output->checknes(!$_FILES["file"]["error"] > 0, "错误：" . $_FILES["file"]["error"]);
    $output->checknes($_FILES["file"]["size"] < 5242880, "文件过大");
    $output->checknes(tebiBucket::checktype($_FILES["file"]["type"], $_FILES["file"]["name"]), "文件类型错误");

    if ($output->allowcontinue()) {
        // 删除 Tebi 存储桶中的旧文件
        $deleteStatus = $tebi->deleteFile($id);

        if ($deleteStatus == 204) { // HTTP 204 表示成功删除
            if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
                $fileName = $id;  // 使用 $id 作为新文件名
                $filePath = $_FILES["file"]["tmp_name"];

                // 上传新文件到 Tebi 存储桶
                $statusCode = $tebi->upload("savefiles/" . $fileName, $filePath);

                if ($statusCode == 200) {
                    // 获取原始文件名
                    $originalFileName = $_FILES["file"]["name"];
                    $response['notice'] = '文件上传到 Tebi 成功。';
                } else {
                    $response['notice'] = '文件上传到 Tebi 失败。';
                    echo json_encode($response);
                    exit();
                }
            } else {
                $response['notice'] = '文件上传失败或未选择文件。';
                echo json_encode($response);
                exit();
            }
        } else {
            $response['notice'] = '旧文件删除失败。';
            echo json_encode($response);
            exit();
        }
    } else {
        $response['notice'] = '上传失败。';
        echo json_encode($response);
        exit();
    }
}

// 更新数据库记录
if ($isFileChanged) {
    $sql = "UPDATE fileinfo SET title=?, cover=?, content=?, viewImg=?, name=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $cover, $content, $imageLinks, $originalFileName, $id);
} else {
    $sql = "UPDATE fileinfo SET title=?, cover=?, content=?, viewImg=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $cover, $content, $imageLinks, $id);
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['notice'] = '数据更新成功。';
} else {
    $response['notice'] = '数据更新失败：' . $stmt->error;
}

$stmt->close();
$conn->close();

// 输出响应
echo json_encode($response);
?>