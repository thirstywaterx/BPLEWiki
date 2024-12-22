<?php
session_start();
require '/www/wwwroot/www.bplewiki.top/php/vendor/autoload.php'; // 引入 Composer 自动加载文件

use OTPHP\TOTP;

// 检查是否已有 secret，否则生成一个新的
if (!isset($_SESSION['totp_secret'])) {
    $totp = TOTP::create();
    $_SESSION['totp_secret'] = $totp->getSecret();
    $totp->setLabel('MyWebsiteUser'); // 设置 label，确保为有效字符串
    echo "Secret Key (store it safely): " . $_SESSION['totp_secret'] . "<br>";
    $qrCodeUrl = $totp->getQrCodeUri('MyWebsite', 'MyWebsite'); // 使用 `MyWebsite` 作为标签
    echo "<img src='" . $qrCodeUrl . "' alt='QR Code'><br>";
} else {
    // 使用已有密钥创建 TOTP 对象
    $totp = TOTP::create($_SESSION['totp_secret']);
    $totp->setLabel('MyWebsiteUser'); // 确保 label 被设置
    $qrCodeUrl = $totp->getQrCodeUri('MyWebsite', 'MyWebsite');
    echo "<img src='" . $qrCodeUrl . "' alt='QR Code'><br>";
}
?>
<form action="verify.php" method="POST">
    <label for="totp">Enter TOTP Code:</label>
    <input type="text" name="totp" required>
    <button type="submit">Verify</button>
</form>