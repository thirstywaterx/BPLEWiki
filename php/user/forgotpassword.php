<?php
session_start();
require_once("../connect.php");      // 你的 connect.php 返回 MySQLi 连接
require_once("../vcode.class.php");
require_once("../ioput.class.php");

ioput::getre(["step"]);
$output = new ioput();

// step 必须在 [1,3]
if (!ioput::inputcheck("step","get","int") || !($_GET["step"] >= 1 && $_GET["step"] <= 3)) {
    $output->jsonoutput("success");
    $output->addmessage("无效访问");
    $output->output();
    exit;
}

// 同你的前端结构：step=1 => sendCode, step=2 => verifyCode, step=3 => resetPassword

// -------------------------------------------------
// Step 1 (sendCode): 用户提交用户名 => 查表获取邮箱 => 生成验证码写进 vCode("forgot")
// -------------------------------------------------
// -------------------------------------------------
// Step 1 (sendCode): 用户提交用户名 => 获取邮箱 => 立即发送验证码
// -------------------------------------------------
if ($_GET["step"] == 1) {
    $output->jsonoutput("success");

    ioput::postre(["action","username"]);
    if ($_POST["action"] !== "sendCode") {
        $output->addmessage("动作不匹配，需 sendCode");
        $output->output();
        exit;
    }

    $output->checknes($_POST["username"], "用户名不能为空");
    if (!$output->allowcontinue()) {
        $output->output();
        exit;
    }

    $username = ioput::test_input($_POST["username"]);
    $conn = connect();
    $stmt = $conn->prepare("SELECT email FROM user WHERE username=? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($email);
        if ($stmt->fetch()) {
            if (empty($email)) {
                $output->addmessage("该用户未绑定邮箱");
            } else {
                // 创建并保存验证码到会话
                $forgot = new vCode("forgot");
                $forgot->create();
                $forgot->user["username"] = $username;
                $forgot->user["email"]    = $email;
                $forgot->setcode(rand(10000,99999));
                // 这里立刻发送邮件
                $forgot->showname = $username;
                $forgot->subject  = "您的忘记密码验证码";

                $sended = $forgot->send();  // 发邮件
                if ($sended) {
                    $forgot->save();
                    $output->success("验证码已发送"); // 后端真正发出邮件
                } else {
                    $output->addmessage("验证码发送失败");
                }
            }
        } else {
            $output->addmessage("用户不存在");
        }
        $stmt->close();
    } else {
        $output->addmessage("数据库查询失败");
    }

    $output->output();
    exit;
}

// -------------------------------------------------
// Step 2 (verifyCode or resendCode):
//   1) 如果 action=resendCode, 则重复发送同一个验证码
//   2) 如果 action=verifyCode, 则校验输入的 code
// -------------------------------------------------
if ($_GET["step"] == 2) {
    $output->jsonoutput("success");

    ioput::postre(["action","username"]);
    // 这里要区分 action 是 "verifyCode" 还是 "resendCode"
    $action = $_POST["action"];

    $username = ioput::test_input($_POST["username"]);

    $forgot = new vCode("forgot");
    if (!$forgot->load()) {
        $output->addmessage("忘记密码会话不存在，请先 Step 1");
        $output->output();
        exit;
    }

    // 核对一下会话里的 username 是否与传入相符
    if ($forgot->user["username"] !== $username) {
        $output->addmessage("用户名与会话不匹配");
        $output->output();
        exit;
    }

    if ($action === "resendCode") {
        // 前端点击「重新发送」 => 再发一次验证码
        // 注意: vCode->checkcooling(60) => 60秒内限制重复发送
        if ($forgot->checkcooling(60)) {
            $forgot->showname = $username;
            $forgot->subject  = "您的忘记密码验证码(重发)";
            $sended = $forgot->send();  // 发送邮件到 forgot->user["email"]
            if ($sended) {
                $forgot->save();
                $output->success("验证码已重新发送");
            } else {
                $output->addmessage("邮件发送失败");
            }
        } else {
            $output->addmessage("发送过快，请稍后再试");
        }

    } elseif ($action === "verifyCode") {
        // 前端输入验证码 => 校验
        ioput::postre(["code"]);
        $output->checknes($_POST["code"], "验证码不能为空");
        if (!$output->allowcontinue()) {
            $output->output();
            exit;
        }

        $inputCode = ioput::test_input($_POST["code"]);
        if ($forgot->checkcode($inputCode)) {
            // 验证码正确 => 后端不改密码, 只是返回 success
            $output->success("验证码正确");
        } else {
            $output->addmessage("验证码错误");
        }
    } else {
        $output->addmessage("无效action: " . $action);
    }

    $output->output();
    exit;
}

// -------------------------------------------------
// Step 3 (resetPassword): 验证码已校验，前端传入新密码 => UPDATE user.password
// -------------------------------------------------
if ($_GET["step"] == 3) {
    $output->jsonoutput("success");

    ioput::postre(["action","username","newPassword"]);
    // 前端约定 action=resetPassword
    if ($_POST["action"] !== "resetPassword") {
        $output->addmessage("动作不匹配，需 resetPassword");
        $output->output();
        exit;
    }

    $username   = ioput::test_input($_POST["username"]);
    $newPassRaw = ioput::test_input($_POST["newPassword"]);

    $output->checknes($username && $newPassRaw, "表单不完整");
    if (!$output->allowcontinue()) {
        $output->output();
        exit;
    }

    // 加载 forgot 会话
    $forgot = new vCode("forgot");
    if (!$forgot->load()) {
        $output->addmessage("忘记密码会话不存在，请先 Step 1");
        $output->output();
        exit;
    }
    // 若需要再次确认验证码或时间，这里也可以做
    // 例如 if(time()-$forgot->reg>1200){...} 过期判断
    // 但前端逻辑是 Step 2 已校验验证码，这里只管改密码

    // 防止篡改username
    if ($forgot->user["username"] !== $username) {
        $output->addmessage("用户名与会话不匹配");
        $output->output();
        exit;
    }

    // 更新数据库密码
    $conn = connect();
    $hashedPass = password_hash(substr($newPassRaw,0,30), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE user SET password=? WHERE username=?");
    if ($stmt) {
        $stmt->bind_param("ss", $hashedPass, $username);
        if ($stmt->execute()) {
            // 成功
            $forgot->delete(); // 清空会话
            $output->success("密码重置成功");
        } else {
            $output->addmessage("数据库更新失败");
        }
        $stmt->close();
    } else {
        $output->addmessage("数据库操作失败");
    }

    $output->output();
    exit;
}