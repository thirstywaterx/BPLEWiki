<?php
// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'bple_sql');
define('DB_USER', 'bple_sql');
define('DB_PASS', 'bple_server');

// 初始化变量
$file = [
    "success" => false,
    "id" => null,
    "title" => "错误",
    "name" => "",
    "cover" => "",
    "content" => "<h1>错误</h1><p>发生未知错误</p>",
    "viewImg" => "",
    "user" => null,
    "unickname" => "",
    "map" => "",
    "time" => ""
];
$wtitle = "错误";
$wcontent = "<h1>错误</h1><p>发生未知错误</p>";

// 函数定义
function result($title, $content) {
    global $wtitle, $wcontent;
    $wtitle = $title;
    $wcontent = $content;
}

function preview($str){
    $str = preg_replace("/[#*\n']/u", "", $str);
    $str = preg_replace("/\s{2,}/u", " ", $str);
    return mb_substr(strip_tags(trim($str)), 0, 150) . "…";
}

// 处理GET参数
if(isset($_GET["id"])){
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if($id === false || $id <= 0){
        result("错误", "<h1>错误</h1><p>无效的文章ID</p>");
    } else {
        // 创建数据库连接
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            result("错误", "<h1>错误</h1><p>连接失败：" . htmlspecialchars($conn->connect_error) . "</p>");
        } else {
            // 准备SQL语句
            $sql = "SELECT f.*, u.nickname FROM fileinfo f INNER JOIN user u ON f.user = u.id WHERE f.id = ?";
            $stmt = $conn->prepare($sql);
            if($stmt){
                $stmt->bind_param("i", $id);
                if($stmt->execute()){
                    $result = $stmt->get_result();
                    if($result->num_rows === 1){
                        $row = $result->fetch_assoc();
                        // 设置结果
                        $file = [
                            "success" => true,
                            "id" => $row["id"],
                            "title" => htmlspecialchars($row["title"]),
                            "name" => htmlspecialchars($row["name"]),
                            "cover" => htmlspecialchars($row["cover"]),
                            "content" => $row["content"], // 保留HTML内容
                            "viewImg" => htmlspecialchars($row["viewImg"]),
                            "user" => $row["user"],
                            "unickname" => htmlspecialchars($row["nickname"]),
                            "map" => htmlspecialchars($row["map"]),
                            "time" => htmlspecialchars($row["reg_time"])
                        ];
                        result($file["title"], $file["content"]);
                    } else {
                        result("错误", "<h1>错误</h1><p>文章不存在</p>");
                    }
                } else {
                    result("错误", "<h1>错误</h1><p>执行错误：" . htmlspecialchars($stmt->error) . "</p>");
                }
                $stmt->close();
            } else {
                result("错误", "<h1>错误</h1><p>准备语句失败：" . htmlspecialchars($conn->error) . "</p>");
            }
            $conn->close();
        }
    }
} else {
    result("错误", "<h1>错误</h1><p>缺少文章ID</p>");
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name='description' content='<?= htmlspecialchars($wtitle) ?> - 猪吧维基BPLEWiki'>
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.css" rel="stylesheet">
    <!-- 保留所有CSS引用 -->
    <link rel="stylesheet" href="/css/topbar.css" />
    <link rel="stylesheet" href="/css/carousel-pic.css">
    <link rel="stylesheet" href="/css/dialog_img.css">
    <link rel="stylesheet" href="/css/dialog.css">
    <link rel="stylesheet" href="/css/dropdown.css">
    <link rel="stylesheet" href="/css/comment.css">
    <link rel="stylesheet" href="/css/page.css" />
    <link rel="stylesheet" type="text/css" href="/css/darkmode.css">
    <link rel="stylesheet" href="/css/highlightjs-copy.min.css" />
    <link rel="stylesheet" href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/styles/atom-one-dark.min.css">
    <title id="workshop-title"><?= htmlspecialchars($wtitle) ?>-猪吧维基BPLEWiki</title>
    <style>
        /* 保留所有原有的CSS */
        p,
        h1,
        h2,
        h3:not(.title),
        h4,
        h5,
        h6 {
            font-family: sans-serif;
            word-wrap: break-word;
        }
        
        h1:first-of-type {    
            font-weight: 400;    /* 设置字体粗细 */    
        }

        a {
            text-decoration: none;
            color: lightgreen;
        }

        body {
            margin-top: 60px;
            display: flex;
            flex-wrap: wrap;
            max-width: 100%;
            padding-bottom: 1000px;
        }
        
        #content * {
            max-width: 100%;
        }

        .delete {
            width: 50px;
            height: 25px;
            background-color: red;
            color: white;
            font-size: 15px;
            border: none;
            border-radius: 3px;
        }

        .delete:active {
            background-color: #CC0000;
        }

        .img-modal img {
            border-radius: 5px;
        }

        .search {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            background-color: #c2e7ff;
            top: 10px;
            right: 10px;
            display: flex;
            align-items: center;
            /* 垂直居中 */
            justify-content: center;
            opacity: 0.9;
            color: #abb2ba;
        }

        .fa-magnifying-glass {
            color: white;
        }

        .fa-magnifying-glass:active {
            animation: tilt-hit 0.5s ease forwards;
        }


        @keyframes tilt-hit {
            0% {
                transform: rotate(0deg);
            }

            50% {
                transform: rotate(-40deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .search-animate {
            animation: tilt-hit 0.5s ease forwards;
        }


        .slider {
            border-radius: 4px;
        }

        .author {
            width: max-content;
            height: 40px;
            background-color: #f6f6f6;
            margin-top: 20px;
            box-shadow: 5px 5px 50px rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            padding-right: 10px;
        }

        .avatar {
            position: relative;
            width: 30px;
            height: 30px;
            border: 1px solid green;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 5px;
            float: left;
        }

        .author p {
            position: relative;
            margin-left: 45px;
            top: 50%;
            transform: translateY(-60%);
        }

        .download .fa-download {
            margin-right: 5px;
        }

        .bottom-bar {
            position: fixed;
            display: flex;
            flex-direction: row;
            bottom: 0;
            left: 0;
            background-color: #f6f6f6;
            width: 100%;
            height: 45px;
            box-shadow: 5px 5px 50px rgba(0, 0, 0, 0.5);
        }

        .download {
            position: relative;
            background-color: lightgreen;
            width: 240px;
            height: 35px;
            border-radius: 5px;
            color: white;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: box-shadow 0.1s ease, transform 0.1s ease;
        }

        .download:active {
            background-color: #98fb98;
        }


        .fa-circle-question {
            position: relative;
            left: 40px;
            top: 50%;
            margin-right: 10px;
            transform: translateY(-25%);
            font-size: 140%;
            color: lightgrey;
        }

        .main-content {
            width: 600px;
            min-height: 500px;
        }

        .comment {
            width: 320px;
            height: 400px;
        }


        hr {
            margin-top: 50px;
            border: none;
            width: 95%;
            height: 1px;
            background-color: grey;
        }


        .prev,
        .next {
            z-index: 0;
        }

        .desktop-download {
            display: none;
        }


        .tool-bar {
            position: relative;
            background-color: white;
            float: right;
            margin-right: 10px;
            margin-top: 70px;
            padding: 10px;
            box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.4);
            width: 75%;
            height: 40px;
            border-radius: 30px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            justify-content: space-evenly;
        }

        .tool-bar .fa-solid {
            margin: 0 auto;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        .moreOptions {
            position: relative;
        }

        .tool-bar .fa-solid:active {
            color: green;
        }

        .save-info {
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }

        .save-info p {
            margin: 5px 0;
            color: #555;
        }

        .save-info p strong {
            font-weight: bold;
            color: #333;
        }

        #dialog-download label {
            display: block;
            margin: 20px 0 10px;
            color: #333;
        }

        #dialog-download select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        #dialog-download button {
            position: relative;
            width: 60px;
            height: 30px;
            background-color: lightgreen;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 10px;
            left: 50%;
            transform: translate(-50%);
            margin-top: 35px;
        }

        #dialog-download button:active {
            background-color: #32CD32;
        }

        #numberInput {
            width: 100px;
            height: 25px;
            border: none;
            border-radius: 2px;
            background-color: #e9eef6;
        }

        #numberInput:focus {
            outline: 1px solid lightgreen;
        }

        .submit {
            width: 50px;
            height: 25px;
            background-color: lightgreen;
            color: white;
            font-size: 15px;
            border: none;
            border-radius: 3px;
        }

        .submit:active {
            background-color: #80DD80;
        }

        .argument {
            border: none;
            width: 220px;
            max-width: 220px;
            min-height: 200px;
            border-radius: 2px;
            background-color: #e9eef6;
            padding: 10px;
            margin-bottom: 20px;
            margin-top: 30px;
        }


        .report-option {
            padding: 5px 10px;
            margin-right: 5px;
            background-color: #f1f1f1;
            cursor: pointer;
            border-radius: 5px;
            user-select: none;
            display: inline-block;
            margin-bottom: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
            /* 添加背景和文字颜色的过渡效果 */
        }

        .report-option.selected {
            background-color: #4CAF50;
            color: white;
        }

        .comment-container {
            width: 100%;
            height: 1000px;
            /* 调整为您需要的高度 */
            overflow-y: auto;
            border: 1px solid #ccc;
            /* 可选：用于区分评论框 */
            border-radius: 5px;
            padding: 10px;
            background-color: white;
            padding-top: 20px;
        }

        .comment-block {
            margin-left: 0px;
            width: 90%;
        }

        #comment .send-comment {
            width: 106%;
            min-height: 60px;
            background-color: white;
            margin-bottom: 30px;
            border-radius: 5px;
            box-shadow: 5px 5px 20px rgba(0, 0, 0, 0.5);
        }
        
        #comment .send-comment .fa-regular:active{
            color:green;
        }

        #comment .send-comment .fa-solid:active{
            color:green;
        }

        #comment .send-comment .inputrow {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 5px;
        }

        #comment textarea {
            position: relative;
            left: 15px;
            width: 70%;
            min-height: 50px;
            max-height: 130px;
            background-color: #e9eef6;
            border: none;
            border-radius: 8px;
            resize: vertical;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        #comment #send,
        #comment .openemoji {
            margin-top: 0;
            margin-left: 0px;
            font-size: 18px;
        }

        #comment .openemoji {
            margin-left: 20px;
        }

        #comment #send:active {
            color: green;
        }

        #comment div.emoji {
            overflow: auto;
            display: none;
            background-color: #DDDDE2;
            width: 100%;
            box-sizing: border-box;
            height: 140px;
            padding-left: 4px;
            padding-right: 2px;
        }

        #comment span.emoji {
            display: inline-block;
            margin: 12px;
            transform: scale(150%);
        }

        #comment span.emoji:active {
            background-color: #f9f9fa;
            border-radius: 2px;
        }


        @media (min-width: 768px) {
            body {
                flex-direction: row;
                /* 横向布局 */
                justify-content: space-between;
                /* 分散对齐 */
                padding: 50px;
            }

            .info {
                width: 70%;
                margin: 0 auto;
            }

            .main-content {
                width: calc(65% - 20px);
                margin: 10px;
                padding: 0px;
            }

            .comment {
                width: calc(35% - 20px);
                margin: 10px;
            }

            h2 {
                margin-top: 0px;
            }

            hr {
                display: none;
            }

            .bottom-bar {
                display: none;
            }


            .bottom-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
            }

            .desktop-download {
                position: relative;
                top: 35px;
                display: block;
                width: 50px;
                height: 40px;
                background-color: white;
                box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4);
                border-radius: 5px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .desktop-download:active {
                color: green;
            }

        }
        
        .hljs {
            background-color: #282C34;
            color: #ABB2BF;
            border-radius: 5px;
        }

        /* for block of numbers */
        .hljs-ln-numbers {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

            text-align: center;
            color: #ccc;
            border-right: 1px solid #CCC;
            vertical-align: top;
            padding-right: 5px;
        }

        .hljs-ln-code {
            position:relative;
            left:6px;
        }
        
        .hljs-ln-n {
            margin-right:6px;
        }
    </style>
</head>

<body>
    <div class="topblk">
        <div class="back" id="back">
            <i class="fa-solid fa-angle-left"></i>
        </div>
        <a href="../index.html">
            <div class="logo">
                <img src="/img/favicon.ico" alt="logo">
                <h3>猪吧维基</h3>
            </div>
        </a>
        <a href="/search.html">
            <div class="search"><i class="fa-solid fa-magnifying-glass"></i></div>
        </a>
    </div>

    <!-- 图片模态框 -->
    <div id="imgModal" class="img-modal">
        <span class="close">&times;</span>
        <img class="modal-img-content" id="img01">
        <div id="caption"></div>
    </div>

    <!-- 举报文章模态框 -->
    <div id="dialog-report-article" class="modal">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>举报文章</h2>
            <div class="operation">
                <center>
                    <p>如果你认为此文章有背于文章守则，您可以举报它。</p>
                    <div id="report-options-article">
                        <span class="report-option" data-value="violence">暴力恐怖</span>
                        <span class="report-option" data-value="inappropriate-speech">不当言论</span>
                        <span class="report-option" data-value="obscenity">淫秽内容</span>
                        <span class="report-option" data-value="baiting">引战钓鱼</span>
                        <span class="report-option" data-value="irrelevant">无关新创</span>
                        <span class="report-option" data-value="other">其它</span>
                    </div>
                    <textarea class="argument" placeholder="在此键入举报理由"></textarea><br>
                    <button class="submit" onclick="submitReport('workshop')">提交</button>
                </center>
            </div>
        </div>
    </div>

    <!-- 删除文章模态框 -->
    <div id="dialog-delete-article" class="modal">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>删除文章</h2>
            <div class="operation">
                <center>
                    <p>你确定要删除该文章吗，一经删除将会<b>永久</b>无法恢复</p>
                    <button class="delete" onclick="deleteArticle()">删除</button>
                </center>
            </div>
        </div>
    </div>

    <!-- 举报评论模态框 -->
    <div id="dialog-reportcomment" class="modal" style="z-index:300 !important;">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>举报评论</h2>
            <div class="operation">
                <center>
                    <p>如果你认为此评论有背于社区守则，您可以举报它。</p>
                    <div id="report-options-comment">
                        <span class="report-option" data-value="violence">暴力恐怖</span>
                        <span class="report-option" data-value="inappropriate-speech">不当言论</span>
                        <span class="report-option" data-value="obscenity">淫秽内容</span>
                        <span class="report-option" data-value="baiting">引战钓鱼</span>
                        <span class="report-option" data-value="other">其它</span>
                    </div>
                    <textarea class="argument" placeholder="在此键入举报理由"></textarea><br>
                    <button class="submit" onclick="submitReport('workshopcomment')">提交</button>
                </center>
            </div>
        </div>
    </div>

    <!-- 下载存档模态框 -->
    <div id="dialog-download" class="modal">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>下载存档</h2>
            <div class="operation">
                <p>您可以配置可选选项然后再进行下载。</p>
                <h3>自定义存档名称</h3>
                <p>存档作者在制作存档时用的地图可能不是您想要的目标地图，您可以通过以下设置更改存档内容所属的地图。（部分浏览器可能不支持该功能 <a href="/doc/filename">手动更名</a>）</p>
                <p>请注意：由于存档内容并非是基于您要改变到的地图制作的，所以存档迁移之后可能会出现载具位置找不到，卡入墙里等问题。</p>
                <div class="save-info">
                    <p><strong>原存档名称:</strong> <span id="originalName"></span></p>
                    <p><strong>对应地图:</strong> <span id="mapName"></span></p>
                </div>
                <label for="mapSelect">选择要更换到的地图:</label>
                <select id="mapSelect" onchange="changeDownloadName()">
                    <!-- 动态生成选项 -->
                </select>
                <label for="numberInput" class="field" style="margin-top: 0;">请输入栏位:</label>
                <input type="number" id="numberInput" value="1" onchange="changeDownloadName()"><br>
                <a id="download-save"><button id="downloadBtn">下载存档</button></a>
            </div>
        </div>
    </div>

    <!-- 轮播图和主要内容 -->
    <div class="main-content">
        <div class="slider">
            <div class="slides">
                <?php
                $viewImgArray = explode(",", $file["viewImg"]);
                if($viewImgArray[0] === "cover" && !empty($file["cover"])) {
                    $viewImgArray[0] = $file["cover"];
                }
                foreach($viewImgArray as $index => $imgSrc){
                    echo '<img class="dialog-img" src="' . htmlspecialchars($imgSrc) . '" alt="Image ' . ($index + 1) . '">' . "\n";
                }
                ?>
            </div>
            <button class="prev">&#10094;</button>
            <button class="next">&#10095;</button>
            <div class="dots">
                <?php
                foreach($viewImgArray as $i => $img){
                    echo '<span class="dot" onclick="currentSlide(' . ($i + 1) . ')"></span>' . "\n";
                }
                ?>
            </div>
        </div>

        <h1><?= htmlspecialchars($wtitle) ?></h1>
        <div class="content" id="content"><?= $wcontent ?></div>

        <div class="bottom-container">
            <div class="open-download desktop-download"><i class="fa-solid fa-download"></i></div>
            <div class="tool-bar">
                <div class="toolfont"><i class="fa-solid fa-pen-to-square" id="revise"></i></div>
                <div class="toolfont" id="collect"><i class="fa-solid fa-star"></i></div>
                <div class="toolfont"><i class="fa-solid fa-circle-info" id="info"></i></div>
                <div class="toolfont moreOptions" id="moreOptions" data-type="article">
                    <i class="fa-solid fa-ellipsis"></i>
                    <div class="dropdown" id="moreDropdown" style="display:none;">
                        <div class="dropdown-item open-report-article"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="dropdown-item open-delete-article"><i class="fa-solid fa-trash"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr><br>

    <!-- 评论区 -->
    <div class="comment" id="comment">
        <h2>评论</h2>
        <div class="send-comment">
            <div class="inputrow">
                <textarea placeholder=" 键入你欲评论的内容" maxlength="900" id="myInput"></textarea>
                <span class="openemoji" onclick="setemoji.toggle()"><i class="fa-regular fa-smile"></i></span>
                <i class="fa-solid fa-arrow-rotate-right" id="toarticle"></i>
                <span id="send"><i class="fa-regular fa-paper-plane"></i></span>
            </div>
            <div class="emoji"></div>
        </div>
        <div class="comment-container" id="areas">
            <!-- 动态生成的评论将插入到这里 -->
        </div>
    </div>

    <div class="bottom-bar">
        <div class="download open-download" id="download-btn">
            <i class="fa-solid fa-download"></i>
            <p>点击下载</p>
        </div>
    </div>

    <!-- 保留所有外部引用的JavaScript -->
    <script src="/js/ajax.js"></script>
    <script src="/js/darkmode.js"></script>
    <script src="/js/carousel-pic.js"></script>
    <script src="/js/dialog_img.js"></script>
    <script src="/js/dropdown.js"></script>
    <script src="/js/dialog-id.js"></script>
    <script src="/js/usersign.js?v=0.1.0"></script>
    <script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/marked/4.0.2/marked.min.js" type="application/javascript"></script>
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/highlight.min.js"></script>
    <script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js" type="application/javascript"></script>
    <script src="/js/highlightjs-copy.min.js"></script>
    
    <!-- 内嵌JavaScript -->
    <script>
        // 工具函数
        const dqsa = (selector, first = true) => first ? document.querySelector(selector) : document.querySelectorAll(selector);

        // 后退按钮
        dqsa('#back').addEventListener('click', () => history.back());

        // 搜索按钮动画
        dqsa('.fa-magnifying-glass').addEventListener('click', function() {
            this.classList.add('search-animate');
            this.addEventListener('animationend', () => this.classList.remove('search-animate'), { once: true });
        });

        // 获取URL参数
        const GETs = (() => {
            const params = new URLSearchParams(window.location.search);
            let get = {};
            for(let [key, value] of params.entries()){
                get[key] = value;
            }
            return get;
        })();

        // 初始化数据
        const workshopData = <?= json_encode($file, JSON_UNESCAPED_UNICODE) ?>;

        // 设置标题和内容
        document.title = `${workshopData.title}-猪吧维基BPLEWiki`;
        document.querySelector("meta[name='description']").setAttribute('content', `${workshopData.title} - ${preview(workshopData.content)}`);

        if(workshopData.success){
            // 处理Markdown内容
            dqsa("#content").innerHTML = marked.parse(dqsa("#content").innerHTML);
            hljs.highlightAll();
            // 设置收藏状态
            collect.updateUI();
            console.log(workshopData);
        }

        // 下载功能
        document.getElementById('download-btn').addEventListener('click', () => {
            const id = <?= intval($_GET['id'] ?? 0) ?>;
            if(id <= 0){
                alert("无效的下载ID");
                return;
            }

            // 发送请求获取预签名URL
            fetch(`https://www.bplewiki.top/php/savefile/tebi_presign.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({file_url: `https://s3.tebi.io/s3.bplewiki.top/savefiles/${id}`})
            })
            .then(response => response.text())
            .then(url => download(url, workshopData.name))
            .catch(error => alert("下载异常：" + error));
        });

        function download(url, filename){
            fetch(url)
                .then(response => {
                    if(!response.ok) throw new Error('网络响应失败');
                    return response.blob();
                })
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                })
                .catch(() => alert('下载异常！'));
        }

        // 收藏功能
        const collect = {
            data: JSON.parse(localStorage.getItem('collect') || '{"workshop":[]}'),
            hasArticle(id){
                return this.data.workshop.includes(id);
            },
            toggle(id){
                if(this.hasArticle(id)){
                    this.data.workshop = this.data.workshop.filter(item => item !== id);
                } else {
                    this.data.workshop.push(id);
                }
                this.save();
                this.updateUI();
            },
            save(){
                localStorage.setItem('collect', JSON.stringify(this.data));
            },
            updateUI(){
                const starIcon = dqsa(".fa-star", true);
                if(starIcon){
                    starIcon.style.color = this.hasArticle(workshopData.id) ? 'green' : 'black';
                }
            }
        };

        // 初始化收藏状态
        collect.updateUI();

        // 收藏按钮事件
        dqsa("#collect").addEventListener('click', () => {
            if(!usersign.isSignedIn()){
                alert("请登录后收藏");
                return;
            }
            collect.toggle(workshopData.id);
            // 发送收藏状态到服务器
            fetch("/php/savefile/collectc.php", {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({id: workshopData.id})
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    alert("收藏状态已更新");
                } else {
                    alert("更新失败：" + data.notice);
                }
            })
            .catch(() => alert("网络错误"));
        });

        // 删除文章
        function deleteArticle(){
            if(!usersign.isSignedIn()){
                alert("请先登录");
                return;
            }
            if(workshopData.user !== usersign.userId()){
                alert("无删除权限");
                return;
            }
            if(!confirm("确认删除文章？")) return;
            fetch("/php/savefile/remove.php", {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({id: workshopData.id})
            })
            .then(response => response.json())
            .then(data => {
                alert(data.notice);
                if(data.success){
                    window.location.href = "../index.html";
                }
            })
            .catch(() => alert("删除失败"));
        }

        // 更多选项事件绑定
        document.querySelectorAll(".open-report-article").forEach(elem => {
            elem.addEventListener('click', () => {
                document.getElementById('dialog-report-article').style.display = "block";
            });
        });

        document.querySelectorAll(".open-delete-article").forEach(elem => {
            elem.addEventListener('click', () => {
                document.getElementById('dialog-delete-article').style.display = "block";
            });
        });

        // 举报功能
        function submitReport(type) {
            let modalPrefix = type === 'workshop' ? 'dialog-report-article' : 'dialog-reportcomment';
            let modal = document.getElementById(modalPrefix);
            let selectedElement = modal.querySelector('.report-option.selected');
            let reasonTextarea = modal.querySelector('.argument');

            if (selectedElement) {
                var selectedOption = selectedElement.dataset.value;
                var reason = reasonTextarea.value;
                var reportContent = {
                    reason: reason,
                    option: selectedOption,
                    id: workshopData.id
                };

                let formData = new URLSearchParams({
                    type: type,
                    id: workshopData.id,
                    reason: selectedOption,
                    content: reason
                });

                fetch("/php/report.php", {
                    method: "POST",
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: formData
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        alert("举报成功");
                    } else {
                        alert(response.notice);
                    }
                    modal.style.display = `none`;
                })
                .catch(() => alert("举报失败"));
            } else {
                alert('请选择一个举报原因');
            }
        }

        // 删除按钮关闭模态框
        document.querySelectorAll(".close-button").forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.modal').style.display = "none";
            });
        });

        // 点击模态框外部关闭
        window.addEventListener('click', (event) => {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        });

        // 评论功能
        const comment = {
            to: "none",
            input: dqsa("#myInput"),
            sendButton: dqsa("#send"),
            toArticleButton: dqsa("#toarticle"),
            container: dqsa("#areas"),
            fetchComments: function(){
                if(!GETs.id){
                    this.container.innerHTML = "<p>无文章ID，无法获取评论</p>";
                    return;
                }
                this.container.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
                fetch(`/workshop/comment.php?id=${GETs.id}`, {method: 'POST'})
                    .then(response => response.json())
                    .then(data => {
                        if(!data.success){
                            this.container.innerHTML = `<p>发生错误：${data.notice}。<button onclick="comment.fetchComments()">刷新评论</button></p>`;
                            return;
                        }
                        this.renderComments(data.comments);
                    })
                    .catch(() => {
                        this.container.innerHTML = `<p>无法获取评论。<button onclick="comment.fetchComments()">刷新评论</button></p>`;
                    });
            },
            renderComments: function(comments){
                if(comments.length === 0){
                    this.container.innerHTML = `暂无评论。`;
                    return;
                }
                // 简化评论渲染逻辑
                let html = '';
                comments.forEach(c => {
                    html += `
                        <div class="comment-block" id="comments_${c.id}">
                            <img src="${c.avatar}" alt="Avatar" loading="lazy">
                            <span class="uname"><b>${c.unickname || c.user}</b></span>
                            <span class="more">
                                <i class="fa-solid fa-comment-dots" onclick="comment.reply(${c.id})"></i>
                                <i class="fa-solid fa-ellipsis" onclick="comment.toggleDropdown(this)"></i>
                                <div class="dropdown" style="display:none;">
                                    <div class="dropdown-item" onclick="comment.report(${c.id})"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                    <div class="dropdown-item" onclick="comment.delete(${c.id}, ${c.uid})"><i class="fa-solid fa-trash"></i></div>
                                </div>
                            </span>
                            <p>${c.content}</p>
                        </div>
                    `;
                });
                this.container.innerHTML = html;
                newdropdown(this.container.querySelectorAll('.moreOptions'));
            },
            send: function(){
                if(!usersign.isSignedIn()){
                    alert("请先登录");
                    return;
                }
                if(this.input.value.trim() === ""){
                    alert("请先输入评论");
                    return;
                }
                this.sendButton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
                fetch(`/php/savefile/newcomment.php`, {
                    method: "POST",
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        fileid: GETs.id,
                        tocomment: this.to,
                        content: this.input.value.trim()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.sendButton.innerHTML = `<i class="fa-regular fa-paper-plane"></i>`;
                    if(data.success){
                        alert("发送成功");
                        this.input.value = "";
                        this.fetchComments();
                    } else {
                        alert("发送失败：" + data.notice);
                    }
                })
                .catch(() => {
                    this.sendButton.innerHTML = `<i class="fa-regular fa-paper-plane"></i>`;
                    alert("发送失败");
                });
            },
            reply: function(id){
                this.to = id;
                this.input.placeholder = `回复#${id}`;
                this.input.focus();
            },
            toggleDropdown: function(element){
                const dropdown = element.nextElementSibling;
                dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
            },
            report: function(id){
                document.getElementById('dialog-reportcomment').style.display = "block";
                // 可以在这里设置举报的评论ID
            },
            delete: function(id, uid){
                if(!usersign.isSignedIn()){
                    alert("请先登录");
                    return;
                }
                if(usersign.userId() !== uid){
                    alert("无删除权限");
                    return;
                }
                if(!confirm("确认删除评论？")) return;
                fetch("/php/savefile/removecomment.php", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({id})
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.notice);
                    if(data.success){
                        this.fetchComments();
                    }
                })
                .catch(() => alert("删除失败"));
            }
        };

        // 发送评论事件
        comment.sendButton.addEventListener('click', () => comment.send());

        // 初始化评论
        comment.fetchComments();

        // Emoji功能
        const setemoji = {
            emojidiv: dqsa(".emoji", false)[0],
            input: dqsa("#myInput"),
            toggle: function(){
                this.emojidiv.style.display = this.emojidiv.style.display === "none" ? "block" : "none";
            },
            add: function(code){
                const emoji = `[:${code}]`;
                const start = this.input.selectionStart;
                const end = this.input.selectionEnd;
                const text = this.input.value;
                this.input.value = text.slice(0, start) + emoji + text.slice(end);
                this.input.selectionStart = this.input.selectionEnd = start + emoji.length;
                this.input.focus();
            },
            init: function(){
                const emojis = Array.from({length: 80}, (_, i) => 128512 + i).map(code => String.fromCodePoint(code));
                this.emojidiv.innerHTML = emojis.map(e => `<span class="emoji" onclick="setemoji.add(${e.codePointAt(0)})">${e}</span>`).join('');
            }
        };

        setemoji.init();

        // 监听地图选择和栏位输入变化
        function updateMapName(data, options) {
            const match = options.find(option => 
                (data.originalName.includes(option.saveName) || data.originalName.includes(`${option.saveName}.bak`)) 
                && option.name !== "不更换"
            );
            
            if (match) {
                data.mapName = match.name;
            } else {
                data.mapName = "未找到匹配地图";
            }
        }

        // 处理存档名称管理
        const saveData = {
            "originalName": '<?php
                // 获取查询字符串中的 ID 参数
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']); // 将 ID 转换为整数

                    // 从数据库中查询 name
                    $sql = "SELECT name FROM fileinfo WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id); // 绑定参数
                    $stmt->execute();
                    $stmt->bind_result($name);
                    
                    if ($stmt->fetch()) {
                        // 查询成功，返回 name
                        echo addslashes($name);
                    } else {
                        // 处理错误：没有找到对应的记录
                        echo "未找到对应的记录";
                    }

                    // 关闭语句
                    $stmt->close();
                } else {
                    echo "未提供 ID 参数";
                }

                // 关闭数据库连接
                $conn->close();
            ?>',
            "mapName": ""
        };

        const mapOptions = [
            { name: "不更换", saveName: saveData.originalName },
            { name: "脚踏实地", saveName: "Level_Sandbox_04" },
            { name: "异想天开之飞行小猪", saveName: "Level_Sandbox_03" },
            { name: "夜间飞行器", saveName: "Level_Sandbox_07" },
            { name: "起床战猪猪", saveName: "Level_Sandbox_09" },
            { name: "猪猪商城", saveName: "Episode_6_Tower Sandbox" },
            { name: "寻找头骨", saveName: "Level_Sandbox_01" },
            { name: "寻找雕像", saveName: "Episode_6_Dark Sandbox" },
            { name: "小猪大冒险", saveName: "MMSandbox" },
            { name: "梦幻之地", saveName: "Level_Sandbox_06" }
        ];

        updateMapName(saveData, mapOptions);

        // 显示存档信息
        document.getElementById('originalName').innerText = saveData.originalName;
        document.getElementById('mapName').innerText = saveData.mapName;

        // 动态生成选项
        const select = document.getElementById('mapSelect');
        select.innerHTML = ''; // 清空现有选项
        mapOptions.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.name;
            opt.innerText = `${option.name} (${option.saveName})`;
            select.appendChild(opt);
        });

        function changeDownloadName() {
            const selectedOption = select.options[select.selectedIndex];
            const selectedSaveName = selectedOption.text.match(/([^)]+)/)[1];
            const inputNumber = parseInt(document.getElementById('numberInput').value, 10);

            if (inputNumber < 1) {
                alert("数字不能小于1");
                return;
            }

            const numberSuffix = inputNumber > 1 ? `_${inputNumber - 1}` : '';
            const newSaveName = `${selectedSaveName}${numberSuffix}`;
            document.querySelector(`#download-save`).download = newSaveName;
        }

        changeDownloadName();

        // 下载按钮事件
        document.getElementById('download-save').addEventListener('click', function() {
            const presignedUrl = '<?php
                // 获取查询字符串中的 ID 参数
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']); // 将 ID 转换为整数

                    // 构建需要传递的 URL
                    $file_url = "https://s3.tebi.io/s3.bplewiki.top/savefiles/" . $id;

                    // 发送 POST 请求到 tebi_presign.php 以获取预签名的 URL
                    $presign_url = "https://www.bplewiki.top/php/savefile/tebi_presign.php";

                    // 使用 cURL 发送请求
                    $ch = curl_init();

                    // 设置 cURL 选项
                    curl_setopt($ch, CURLOPT_URL, $presign_url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['file_url' => $file_url])); // 将 file_url 作为 POST 数据
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // 执行请求并获取响应
                    $response = curl_exec($ch);

                    // 错误处理
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    } else {
                        // 输出预签名的 URL
                        echo $response;
                    }

                    // 关闭 cURL
                    curl_close($ch);
                } else {
                    echo "未提供 ID 参数";
                }
            ?>';
            const filename = document.querySelector('#download-save').download;
            if(presignedUrl.startsWith('Error')){
                alert('下载异常！');
                return;
            }
            download(presignedUrl, filename);
        }, true);

        // 统一下载函数
        function download(url, filename) {
            fetch(url)
                .then(response => {
                    if(!response.ok) throw new Error('网络响应失败');
                    return response.blob();
                })
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                })
                .catch(() => alert('下载异常！'));
        }

        // 评论相关功能封装
        const commentModule = {
            to: "none",
            input: dqsa("#myInput"),
            sendButton: dqsa("#send"),
            toArticleButton: dqsa("#toarticle"),
            container: dqsa("#areas"),
            fetchComments: function(){
                if(!GETs.id){
                    this.container.innerHTML = "<p>无文章ID，无法获取评论</p>";
                    return;
                }
                this.container.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
                fetch(`/workshop/comment.php?id=${GETs.id}`, {method: 'POST'})
                    .then(response => response.json())
                    .then(data => {
                        if(!data.success){
                            this.container.innerHTML = `<p>发生错误：${data.notice}。<button onclick="commentModule.fetchComments()">刷新评论</button></p>`;
                            return;
                        }
                        this.renderComments(data.comments);
                    })
                    .catch(() => {
                        this.container.innerHTML = `<p>无法获取评论。<button onclick="commentModule.fetchComments()">刷新评论</button></p>`;
                    });
            },
            renderComments: function(comments){
                if(comments.length === 0){
                    this.container.innerHTML = `暂无评论。`;
                    return;
                }
                let html = '';
                comments.forEach(c => {
                    html += `
                        <div class="comment-block" id="comments_${c.id}">
                            <img src="${c.avatar}" alt="Avatar" loading="lazy">
                            <span class="uname"><b>${c.unickname || c.user}</b></span>
                            <span class="more">
                                <i class="fa-solid fa-comment-dots" onclick="commentModule.reply(${c.id})"></i>
                                <i class="fa-solid fa-ellipsis" onclick="commentModule.toggleDropdown(this)"></i>
                                <div class="dropdown" style="display:none;">
                                    <div class="dropdown-item" onclick="commentModule.report(${c.id})"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                    <div class="dropdown-item" onclick="commentModule.delete(${c.id}, ${c.uid})"><i class="fa-solid fa-trash"></i></div>
                                </div>
                            </span>
                            <p>${c.content}</p>
                        </div>
                    `;
                });
                this.container.innerHTML = html;
                newdropdown(this.container.querySelectorAll('.moreOptions'));
            },
            send: function(){
                if(!usersign.isSignedIn()){
                    alert("请先登录");
                    return;
                }
                if(this.input.value.trim() === ""){
                    alert("请先输入评论");
                    return;
                }
                this.sendButton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
                fetch(`/php/savefile/newcomment.php`, {
                    method: "POST",
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({
                        fileid: GETs.id,
                        tocomment: this.to,
                        content: this.input.value.trim()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.sendButton.innerHTML = `<i class="fa-regular fa-paper-plane"></i>`;
                    if(data.success){
                        alert("发送成功");
                        this.input.value = "";
                        this.fetchComments();
                    } else {
                        alert("发送失败：" + data.notice);
                    }
                })
                .catch(() => {
                    this.sendButton.innerHTML = `<i class="fa-regular fa-paper-plane"></i>`;
                    alert("发送失败");
                });
            },
            reply: function(id){
                this.to = id;
                this.input.placeholder = `回复#${id}`;
                this.input.focus();
            },
            toggleDropdown: function(element){
                const dropdown = element.nextElementSibling;
                dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
            },
            report: function(id){
                document.getElementById('dialog-reportcomment').style.display = "block";
                // 可以在这里设置举报的评论ID
            },
            delete: function(id, uid){
                if(!usersign.isSignedIn()){
                    alert("请先登录");
                    return;
                }
                if(usersign.userId() !== uid){
                    alert("无删除权限");
                    return;
                }
                if(!confirm("确认删除评论？")) return;
                fetch("/php/savefile/removecomment.php", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: new URLSearchParams({id})
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.notice);
                    if(data.success){
                        this.fetchComments();
                    }
                })
                .catch(() => alert("删除失败"));
            }
        };

        // 发送评论事件
        commentModule.sendButton.addEventListener('click', () => commentModule.send());

        // 初始化评论
        commentModule.fetchComments();

        // Emoji功能
        const setemoji = {
            emojidiv: dqsa(".emoji", false)[0],
            input: dqsa("#myInput"),
            toggle: function(){
                this.emojidiv.style.display = this.emojidiv.style.display === "none" ? "block" : "none";
            },
            add: function(code){
                const emoji = `[:${code}]`;
                const start = this.input.selectionStart;
                const end = this.input.selectionEnd;
                const text = this.input.value;
                this.input.value = text.slice(0, start) + emoji + text.slice(end);
                this.input.selectionStart = this.input.selectionEnd = start + emoji.length;
                this.input.focus();
            },
            init: function(){
                const emojis = Array.from({length: 80}, (_, i) => 128512 + i).map(code => String.fromCodePoint(code));
                this.emojidiv.innerHTML = emojis.map(e => `<span class="emoji" onclick="setemoji.add(${e.codePointAt(0)})">${e}</span>`).join('');
            }
        };

        setemoji.init();

        // 收藏按钮点击事件
        dqsa("#info").onclick = function() {
            alert(`工坊创建者：${workshopData.user}(${workshopData.unickname})，创建时间：${workshopData.time}`);
        };
        
        dqsa("#revise").onclick = function() {
            usersign.use().then(function(u){          
                if (u.issignin) {            
                    if (u.uid == workshopData.user) {
                        window.open("revise.html?id=" + GETs.id, "_self");
                    } else {
                        alert("无编辑权限");
                    }
                } else {
                    alert("未登录");
                }
            });
        };

        // Highlight.js插件初始化
        hljs.addPlugin(
            new CopyButtonPlugin({
                lang: "zh", // The copy button now says "Copiado!" when selected.
            })
        );

        hljs.highlightAll();
        hljs.initLineNumbersOnLoad();
    </script>
    <script>
        // 页面加载完成后初始化下拉菜单
        document.addEventListener('DOMContentLoaded', () => {
            // 初始化下拉菜单的事件
            function newdropdown(elements){
                elements.forEach(element => {
                    element.addEventListener('click', () => {
                        const dropdown = element.querySelector('.dropdown');
                        dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
                    });
                });
            }
            newdropdown(document.querySelectorAll('.moreOptions'));
        });
    </script>
</body>
</html>