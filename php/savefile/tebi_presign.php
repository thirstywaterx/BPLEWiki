<?php

// 生成预签名URL的函数
function generate_presigned_url($access_key, $secret_key, $bucket, $object_key, $expires_in) {
    $host = "s3.tebi.io";
    $resource = "/{$bucket}/{$object_key}";
    $expires = time() + $expires_in;

    // 生成要签名的字符串
    $string_to_sign = "GET\n\n\n{$expires}\n{$resource}";

    // 使用密钥生成签名
    $signature = urlencode(base64_encode(hash_hmac('sha1', $string_to_sign, $secret_key, true)));

    // 构建最终的预签名URL
    $url = "https://{$host}{$resource}?AWSAccessKeyId={$access_key}&Signature={$signature}&Expires={$expires}";

    return $url;
}

// 检查是否通过 POST 请求接收到 file_url 参数
if (isset($_POST['file_url'])) {
    $file_url = $_POST['file_url'];

    // 解析 file_url，假设 file_url 的格式是 https://s3.tebi.io/{bucket}/{object_key}
    $parsed_url = parse_url($file_url);
    $path_parts = explode('/', ltrim($parsed_url['path'], '/'));
    
    // 检查路径是否包含 bucket 和 object_key
    if (count($path_parts) >= 2) {
        $bucket = $path_parts[0]; // 第一个部分是 bucket
        $object_key = implode('/', array_slice($path_parts, 1)); // 余下部分为 object_key

        // 配置 S3 的访问密钥、私密密钥和有效期
        $access_key = 'X9u6MC7vmdL9ytj1'; // 替换为你的访问密钥
        $secret_key = 'PYQaY8lQFYqIq4ZTxbyMs2NMn5F4TIYRFBpfWjsV'; // 替换为你的私密密钥
        $expires_in = 180; // 3分钟

        // 生成预签名的 URL
        $presigned_url = generate_presigned_url($access_key, $secret_key, $bucket, $object_key, $expires_in);

        // 输出预签名URL，去掉前面的文字和冒号，并用单引号包裹
        echo "'" . $presigned_url . "'";
    } else {
        // 处理错误：路径解析失败
        http_response_code(400);
        echo "错误: 无法解析 file_url";
    }
} else {
    // 未提供 file_url 参数
    http_response_code(400);
    echo "错误: 未提供 file_url 参数";
}

?>