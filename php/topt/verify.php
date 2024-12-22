# verify.php
<?php
session_start();
require '/www/wwwroot/www.bplewiki.top/php/vendor/autoload.php'; // 引入 Composer 自动加载文件

use OTPHP\TOTP;

if (!isset($_SESSION['totp_secret'])) {
    die('No TOTP secret found. Please generate a secret key first.');
}

$totpCode = isset($_POST['totp']) ? trim($_POST['totp']) : null;

if ($totpCode === null || $totpCode === '') {
    die('No TOTP code provided. Please enter a code.');
}

$totp = TOTP::create($_SESSION['totp_secret']);

// 验证 TOTP 代码
if ($totp->verify($totpCode)) {
    echo "TOTP Code is valid!";
} else {
    echo "Invalid TOTP Code. Please try again.";
}
?>