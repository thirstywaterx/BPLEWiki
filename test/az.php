<?php
$url = "http://ddd69cdb-b7cb-49a0-9367-c06978358c7e.westus2.azurecontainer.io/score"; // 使用HTTPS
$token = "Bearer HE22q6hJtzNoCMYbASJaCyeAHq2YOYVF"; // 确保Bearer令牌格式正确

$data = [
    "Inputs" => [
        "data" => [
            [
                "Pregnancies" => 0,
                "Glucose" => 00,
                "BloodPressure" => 00,
                "SkinThickness" => 30,
                "Insulin" => 20,
                "BMI" => 28,
                "DiabetesPedigreeFunction" => 47,
                "Age" => 45
            ]
        ]
    ],
    "GlobalParameters" => [
        "method" => "predict"
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// 禁用SSL验证（调试时使用）
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// 启用调试输出
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "预测结果: " . $response;
} else {
    echo "请求失败，状态码: " . $httpCode . "，响应: " . $response;
}
?>