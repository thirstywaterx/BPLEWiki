<?php
$file=array("success"=>false);
if(isset($_GET["id"]) and is_numeric($_GET["id"])){
    $aid=round($_GET["id"]);
    include "../php/connect.php";
  $conn = connect();
  if ($conn->connect_error) {
      result("错误",<<<EOF
<h1>错误</h1>
<p>连接失败：{$conn->connect_error}</p>
EOF
      );
  }else{
    //$sql = "SELECT username, password FROM user";
    $sql0 = "SELECT topic.*,user.nickname,user.avatar FROM topic 
    INNER JOIN user ON topic.user = user.id
    WHERE topic.id ='{$aid}'";
    $result=$conn->query($sql0);
    $row=$result->fetch_assoc();
    if ($result->num_rows === 1) {
        result($row["title"],$row["content"]);
        $file = array(
            "success"=>true,
            "title"=>$row["title"],
            "content"=>$row["content"],
            "user"=>$row["user"],
            "unickname"=>$row["nickname"],
            "avatar"=>$row["avatar"],
            "type"=>$row["type"],
            "time"=>$row["reg_time"],
            "category"=>$row["category"]
        );
    }else{
        result("错误",<<<EOF
<h1>错误</h1>
<p>文章不存在</p>
EOF
        );
    }
  }
    $conn->close();
}else{
    result("错误",<<<EOF
<h1>错误</h1>
<p>错误的文章id</p>
EOF
    );
}
function result($title,$content){
    global $atitle,$acontent;
    $atitle=$title;
    $acontent=$content;
}
function preview($str){
    $str=str_replace("#","",$str);
    $str=str_replace("*","",$str);
    $str=str_replace("'","",$str);
    $str=str_replace("\n"," ",$str);
    $str=str_replace("  "," ",$str);
    return mb_substr(strip_tags(trim($str)),0,150)."…";
}
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name='description' content='<?php echo preview($acontent); ?>'>
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/topbar.css" />
    <link rel="stylesheet" href="/css/comment.css" />
    <link rel="stylesheet" type="text/css" href="/css/darkmode.css">
    <link rel="stylesheet" href="/css/dropdown.css">
    <link rel="stylesheet" href="/css/dialog.css">
    <link rel="stylesheet" href="/css/highlightjs-copy.min.css" />
    <link rel="stylesheet" href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/styles/atom-one-dark.min.css">
    <title><?php echo $atitle; ?>-猪吧维基BPLEWiki</title>
    <style>
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
            font-weight: 400;
            /* 设置字体粗细 */            
        }
        
        body{
        margin-top:65px;
        }

        #content * {
            max-width: 100%;
        }

        p {
            margin-top: 2px;
            margin-bottom: 2px;
            word-wrap: break-word;
        }

        .profile {
            width: 40px;
            height:40px;
            border: 1px solid black;
            border-radius: 5000px;
            margin-bottom: 8px;
            overflow:hidden;            
        }

        .author-information {
            display: flex;
            align-items: center;
        }

        .author-information #username {
            margin-left: 10px;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        #date p {
            margin: 0;
            font-size: 15px;
            color: #667D99;
            margin-top: 20px;
        }

        .actions {
            display: flex;
            align-items: center;
        }

        .reply-button {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #667D99;
        }

        .reply-button:hover {
            color: #333;
            /* 鼠标悬停效果 */
        }

        .moreOptions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .category {
            width: 60px;
            height: 18px;
            background-color: #37AFE1;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            border-radius: 3px;
            font-size: 80%;
        }

        .post-function {
            display: flex;
            position: relative;
            float: right;
            right: 1px;
            margin-top: 20px;
            margin-right: 10px;
            margin-bottom: 5px;
            align-items: center;
            color: #667D99;
        }

        .post-function:active {
            color: #708090;
        }

        .post-function p {
            margin-left: 3px;
        }

        hr {
            border: none;
            width: 100%;
            height: 1px;
            background-color: lightgrey;
            margin: 0;
            margin-bottom: 20px;
            text-align: left;
        }

        .comment-block {
            box-shadow: 0px 0px 0px rgba(0, 0, 0, 0) !important;
            margin-left: 0 !important;
            margin-top: 0px !important;
            padding-left: 0;
        }

        .mainBlock {
            min-width: 97% !important;
        }

        .subBlock {
            max-width: 80% !important;
        }

        .sub {
            width: 80%;
        }



        #comment .dropdown {
            left: 40px;
        }

        #dialog-replycomment .operation {
            text-align: center;
            padding: 20px;
        }

        #myInput {
            width: 95%;
            height: 160px;
            margin: 0 auto;
            margin-bottom: 10px;
            resize: vertical;
            font-size: 16px;
            border: none;
            outline: none;
            padding-left: 6px;
        }

        #myInput::placeholder {
            position：relative;
            transform: translateX(-8px);
        }

        .button-group {
            position: relative;
            width: 70px;
            float: right;
            right: 2px;
            top: 50%;
            transform: translateY(-50%);
        }

        #dialog-replycomment #send,
        #dialog-replycomment .openemoji {
            margin-top: 0;
            margin-left: 5px;
            font-size: 18px;
            color: #667D99;
        }

        #dialog-replycomment #send:active {
            color: grey !important;
        }

        #dialog-replycomment .openemoji:active {
            color: grey !important;
        }

        #dialog-replycomment #send:active {
            color: green;
        }

        #dialog-replycomment div.emoji {
            position: fixed;
            overflow: auto;
            display: none;
            background-color: #DDDDE2;
            width: 400%;
            box-sizing: border-box;
            height: 140px;
            margin-top: 10px;
            margin-left: -250px;
            padding-left: 4px;
            padding-right: 2px;
            border-radius: 5px;
        }

        #dialog-replycomment span.emoji {
            display: inline-block;
            margin: 12px;
            transform: scale(150%);
        }

        #dialog-replycomment span.emoji:active {
            background-color: #f9f9fa;
            border-radius: 2px;
        }

        #dialog-replycomment .modal-content {
            position: relative;
            z-index: 1000;
            /* 创建相对定位的上下文 */
            background-color: #fefefe;
            margin: 0 auto;
            padding: 0px;
            padding-top: 3px;
            border-radius: 5px;
            width: 100%;
        }

        #dialog-replycomment .modal-content {
            border: 0px solid #888;
        }

        #dialog-replycomment .operation {
            padding-left: 0;
            padding-right: 0;
        }

        #dialog-replycomment hr {
            position: relative;
            width: 100%;
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
            position: relative;
            left: 6px;
        }

        .hljs-ln-n {
            margin-right: 6px;
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

        .report-option.selected {
            background-color: #4CAF50;
            /* 选中后的背景色 */
            color: white;
        }

        a {
            color: lightgreen;
            text-decoration: none;
        }

        .delete {
            width: 50px;
            height: 25px;
            background-color: red;
            color: white;
            font-size: 15px;
            border: none;
            border-radius: 3px;
            margin-top: 15px;
        }

        .delete:active {
            background-color: #CC0000;
        }

        @media (min-width: 768px) {
            #dialog-replycomment .modal-content {
                position: relative;
                z-index: 1000;
                /* 创建相对定位的上下文 */
                background-color: #fefefe;
                margin: 0 auto;
                padding: 0px;
                padding-top: 3px;
                border-radius: 5px;
                max-width: 450px;
            }
        }

        .hidden-reply {
            display: none;
            /* 默认隐藏超过4条的子评论 */
        }

        .show-more-replies {
            margin-top: 10px;
            text-align: left;
            margin-bottom: 15px;
        }

        .show-more-btn {
            cursor: pointer;
            color: #667D99;
        }

        .show-more-btn:hover {
            color: #667D99;
        }
        
        @keyframes blink {

    0%,
    25%,
    100% {
background-color:none;
        /* 初始阴影 */
    }

    50% {
        background-color:#C2E7FF;
    }
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
    </div>

    <div id="dialog-report-topic" class="modal">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>举报帖子</h2>
            <div class="operation">
                <center>
                    <p>如果你认为此文章有背于文章守则，您可以举报它。</p>
                    <div id="report-options">
                        <span class="report-option" data-value="violence">暴力恐怖</span>
                        <span class="report-option" data-value="inappropriate-speech">不当言论</span>
                        <span class="report-option" data-value="obscenity">淫秽内容</span>
                        <span class="report-option" data-value="baiting">引战钓鱼</span>
                        <span class="report-option" data-value="irrelevant">无关新创</span>
                        <span class="report-option" data-value="other">其它</span>
                    </div>
                    <textarea class="argument" placeholder="在此键入举报理由"></textarea><br>
                    <button class="submit" onclick="javascript:submitReport('topic')">提交</button>
                </center>
            </div>
        </div>
    </div>

    <div id="dialog-delete-topic" class="modal">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>删除帖子</h2>
            <div class="operation">
                <center>
                    <p>你确定要删除该文章吗，一经删除将会<b>永久</b>无法恢复</p>
                    <button class="delete" onclick="deleteArticle()">删除</button>
                </center>
            </div>
        </div>
    </div>

    <div id="dialog-reportcomment" class="modal" style="z-index:300 !important;">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>举报评论</h2>
            <div class="operation">
                <center>
                    <p>如果你认为此评论有背于社区守则，您可以举报它。</p>
                    <div id="report-options">
                        <span class="report-option" data-value="violence">暴力恐怖</span>
                        <span class="report-option" data-value="inappropriate-speech">不当言论</span>
                        <span class="report-option" data-value="obscenity">淫秽内容</span>
                        <span class="report-option" data-value="baiting">引战钓鱼</span>
                        <span class="report-option" data-value="other">其它</span>
                    </div>
                    <textarea class="argument" placeholder="在此键入举报理由"></textarea><br>
                    <button class="submit" onclick="javascript:submitReport('topiccomment')">提交</button>
                </center>
            </div>
        </div>
    </div>

    <div id="dialog-replycomment" class="modal" style="display: none;">
        <div class="modal-content modal-content-creative">
            <span class="close-button">&times;</span>
            <h2>回复</h2>
            <div class="operation">
                <textarea id="myInput" maxlength="900" placeholder="键入你欲评论的内容"></textarea>
                <hr>
                <div class="button-group">
                    <span class="openemoji" onclick="setemoji.tr()"><i class="fa-solid fa-smile"></i></span>
                    <div class="emoji"></div>
                    <span id="send"><i class="fa-solid fa-paper-plane"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="author-information">
        <img class="profile" src="<?php echo htmlspecialchars($file['avatar']); ?>" alt="用户头像">
        <p id="username">用户名</p>
        <div class="category">
            <p>技术讨论</p>
        </div>
    </div>

    <div class="content" id="content">
    </div>
    <div class="post-header">
        <div id="date">
            <p>2024/11/17</p>
        </div>
        <div class="actions">
            <div class="post-function reply-button" onclick="javascript:comment.cto('none')">
                <i class="fa-solid fa-reply"></i>
                <p>回复</p>
            </div>
            <div class="moreOptions post-function" data-type="comment">
                <i class="fa-solid fa-ellipsis font"></i>
                <div class="dropdown" style="display:none;z-index:3;">
                    <div class="dropdown-item open-report-topic" onclick="comment.report(${options.id})"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="dropdown-item open-delete-topic" onclick="comment.delete(${options.id},${options.uid})"><i class="fa-solid fa-trash"></i></div>
                </div>
                <p>更多</p>
            </div>
        </div>
    </div>

    <hr style="background-color: black;">

    <div id="comment">

        <div class="comment-container">
            <div id="areas">

            </div>
        </div>

    </div>


    <script src="/js/darkmode.js"></script>
    <script src="/js/dialog-id.js"></script>
    <script src="/js/ajax.js"></script>
    <script src="/js/dropdown.js"></script>
    <script src="https://s4.zstatic.net/ajax/libs/dompurify/3.1.7/purify.min.js"></script>
    <script src="/js/usersign.js?v=0.1.0"></script>
    <script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/marked/4.0.2/marked.min.js" type="application/javascript"></script>
    <script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/highlight.min.js"></script>
    <script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js" type="application/javascript"></script>
    <script src="/js/highlightjs-copy.min.js"></script>
<script>
document.getElementById('back').addEventListener('click', function() {
            history.back();
        }, true)
</script>
    <script>        
        function dqsa(qsqs, first) {
            if (first) {
                return document.querySelector(qsqs);
            } else {
                return document.querySelectorAll(qsqs);
            }
        }

        var GETs = (function() {
            var url = window.document.location.href.toString();
            var u = url.split("?");
            if (typeof(u[1]) == "string") {
                u = u[1].split("&");
                var get = {};
                for (var i in u) {
                    var j = u[i].split("=");
                    get[j[0]] = j[1];
                }
                return get;
            } else {
                return {};
            }
        })(); //get对象
        //url:"article.php?id="+GETs.id,
        document.querySelectorAll('.report-option').forEach(function(element) {
            element.addEventListener('click', function() {
                // 清除所有选项的选中状态
                document.querySelectorAll('.report-option').forEach(function(el) {
                    el.classList.remove('selected');
                });
                // 为当前点击的选项添加选中状态
                this.classList.add('selected');
            });
        });


        var ainform = {}; //article信息
        //var signinfo; //用户登录信息改为usersign
        if ((GETs.id)) {
            //article载入
                    let i = JSON.parse(`<?php
    echo addcslashes(json_encode($file,JSON_UNESCAPED_UNICODE),"\\`");
?>`);

            if (i.success) {
                let filterconfig = {
                    CUSTOM_ELEMENT_HANDLING: {
                        tagNameCheck: /^article-/, // 允许article-开头的元素
                        attributeNameCheck: null, // 使用默认/标准属性允许列表
                        allowCustomizedBuiltInElements: true, // 不允许自定义内置元素
                    },
                };
                if (i.type === "md") {
                    dqsa("#content", 1).innerHTML = DOMPurify.sanitize(marked.parse(i.content), filterconfig);
                } else {
                    dqsa("#content", 1).innerHTML = DOMPurify.sanitize(i.content, filterconfig);
                }
                for (let ii of dqsa("article-title")) {
                    ii.innerHTML = i.title;
                }

                // 英文类别到中文类别的映射
const categoryMapping = {
    technical: "技术探讨",
    sharing: "作品分享",
    solving: "疑难解答",
    discussion: "综合讨论",
    worldbuilding: "世界观",
    beginner: "新手交流",
    feedback: "反馈与建议"
};

// 获取类别值并转换为中文
const categoryValue = i.category;
const categoryChinese = categoryMapping[categoryValue] || "未知类别";

// 转换函数
function formatDate(input) {
  // 拆分日期和时间部分
  const [datePart] = input.split(" ");
  // 用"-"分割日期部分
  const [year, month, day] = datePart.split("-");
  // 返回格式化后的日期
  return `${year}/${month}/${day}`;
}

// 调用函数并输出结果
document.querySelector("#date").innerHTML = `<p>${formatDate(i.time)}</p>`;


// 将转换后的类别显示到页面
dqsa(".category", 1).innerHTML = categoryChinese;
document.querySelector("#username").innerText = i.unickname;

                hljs.highlightAll(); //代码块语法高亮

                ainform.uid = i.user;
                ainform.unickname = i.unickname;
                ainform.time = i.time;
            }
        }

        function deleteArticle() {
            usersign.use().then(function(u) {
                if (!u.issignin) {
                    alert(`未登录，无法删除帖子`);
                } else if (ainform.uid == u.uid) {
                    ajax({
                        url: "/php/forum/remove.php",
                        method: "POST",
                        send: `id=${GETs.id}`,
                        success: function(response) {
                            alert(`帖子已删除`)
                            location.reload();
                        }
                    });
                } else {
                    alert(`无删除权限`);
                }
            });
        }

        function submitReport(type) {
            let modalPrefix = type === 'topic' ? 'dialog-report-topic' : 'dialog-reportcomment';
            let modal = document.getElementById(modalPrefix);
            let selectedElement = modal.querySelector('.report-option.selected');
            let reasonTextarea = modal.querySelector('.argument');

            if (selectedElement) {
                var selectedOption = selectedElement.dataset.value;
                var reason = reasonTextarea.value;
                var reportContent = {
                    reason: reason,
                    option: selectedOption,
                    id: GETs.id
                };

                ajax({
                    url: "/php/report.php",
                    method: "POST",
                    send: (`type=${type}&id=${GETs.id}&reason=${selectedOption}` + (reason.length > 0 ? `&content=${reason}` : "")),
                    success: function(response) {
                        alert(response)
                        response = JSON.parse(response);
                        if (response.success) {
                            alert(`举报成功`);
                        } else {
                            alert(response.notice);
                        }
                        modal.style.display = `none`;
                    }
                });
            } else {
                alert('请选择一个举报原因');
            }
        }

        var comment = {
            to: "none",
            inputelement: dqsa("#myInput", 1),
            sendbutton: dqsa("#send", 1)
        };
        comment.get = function(aftersuccess) {
            let srbt = "<button onclick='comment.get()'>刷新评论</button>";
            let areas = dqsa("#areas", 1);
            if (!GETs.id) {
                areas.innerHTML = "<p>无文章id，无法获取评论</p>";
                return;
            }
            areas.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
            let successdo = function(r) {
                let i = JSON.parse(r);
                if (i.success !== true) {
                    areas.innerHTML = `<p>发生错误：${i.notice}。${srbt}</p>`;
                } else {
                    let empty = function(sth) {
                        return typeof sth === "undefined" || sth === "";
                    };
                    let allcomm = [];
                    let mains = [];
                    let getcommentbyid = function(cid) {
                        for (let comm of allcomm) {
                            if (comm.id === cid) {
                                return comm;
                            }
                        }
                        let emptycomm = {
                            id: cid,
                            showname: "System",
                            uid: -1,
                            content: "该评论已被删除",
                            tocomment: "null",
                            subs: [],
                            parent: "page",
                            time: 0,
                        };
                        console.log("引用到空评论：" + cid);
                        return emptycomm;
                    };
                    let findmain = function(c) {
                        let p = getcommentbyid(c.tocomment);
                        if (p.parent === "page") {
                            return p;
                        } else {
                            return findmain(p);
                        }
                    };
                    for (let c of i.comments) {
                        allcomm.push({
                            id: c.id,
                            showname: empty(c.unickname) ? c.user : c.unickname,
                            uid: c.user,
                            avatar: c.avatar,
                            content: c.content,
                            tocomment: c.tocomment,
                            subs: c.tocomment === "null" ? [] : "none",
                            parent: c.tocomment === "null" ? "page" : "waitfind",
                            time: c.time,
                        });
                    }
                    for (let c of allcomm) {
                        if (c.parent === "page") {
                            // 主评论
                            mains.push(c);
                        } else {
                            // 评论的回复
                            c.parent = findmain(c);
                            c.parent.subs.push(c);
                            c.toname = getcommentbyid(c.tocomment).showname;
                        }
c.content = c.content.replaceAll(/\[:([0-9]+)\]/g, '&#$1;');//表情替换
                    c.content = c.content.replaceAll("\n","<br/>");//换行替换
                    }
                    if (allcomm.length === 0) {
                        areas.innerHTML = `暂无评论。${srbt}`;
                    } else {
                        areas.innerHTML = "";
                    }
                    for (let m of mains) {
                        let sub = "";
                        let ht = function(options) {
                            return `
<div class="${options.type}Block comment-block" id="comments_${options.id}">
    <img src="${options.imgurl}" loading="lazy">
    <span class="uname pos"><b>${options.showname}</b></span>
    <span class="more">
        <i class="fa-solid fa-comment-dots font" data-commentid="${options.id}" onclick="comment.cto(this.dataset.commentid)"></i>
        <span class="moreOptions" data-type="comment">
            <i class="fa-solid fa-ellipsis font"></i>
            <div class="dropdown" style="display:none;z-index:3;">
                <div class="dropdown-item" onclick="comment.report(${options.id})"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="dropdown-item" onclick="comment.delete(${options.id},${options.uid})"><i class="fa-solid fa-trash"></i></div>
            </div>
        </span>
    </span>
    <p class="mainP">${options.content}</p>
</div>
<hr class="${options.type}">
                        `;
                        };
                        for (let s of m.subs) {
                            let tcm = `<span class="reply" onclick="comment.toreply(${s.tocomment})">@${s.toname} </span>`;
                            let htload = {
                                type: "sub",
                                id: s.id,
                                imgurl: s.avatar,
                                showname: s.showname,
                                uid: s.uid,
                                content: (s.tocomment === m.id ? "" : tcm) + s.content,
                            };

                            // 设置超过4条子回复时的显示状态
                            let hiddenClass = m.subs.indexOf(s) >= 4 ? "hidden-reply" : "";
                            sub += `
                        <div class="${hiddenClass}">
                            ${ht(htload)}
                        </div>
                    `;
                        }

                        // 添加“显示更多”按钮
                        if (m.subs.length > 4) {
                            sub += `
                        <div class="show-more-replies">
                            <span class="show-more-btn" onclick="toggleReplies(this)">显示更多回复</span>
                        </div>
                    `;
                        }

                        let htload = {
                            type: "main",
                            id: m.id,
                            imgurl: m.avatar,
                            showname: m.showname,
                            uid: m.uid,
                            content: m.content,
                        };
                        areas.innerHTML += `
                        <div class="area">
                          ${ht(htload)}
                          <p class="subP">${sub}</p>
                        </div>
                    `;
                    }
                    areas.innerHTML += `<div class="space"></div>`;
                    newdropdown(areas.querySelectorAll(".moreOptions"));
                    if (aftersuccess) {
                        aftersuccess();
                    }
                }
            };
            ajax({
                url: "https://www.bplewiki.top/php/forum/comment.php?id=" + GETs.id,
                method: "POST",
                success: successdo,
            });
        };

        // 控制“显示更多回复”按钮的逻辑
        function toggleReplies(button) {
            const parent = button.closest(".area"); // 主评论块
            const hiddenReplies = parent.querySelectorAll(".hidden-reply");

            if (button.textContent === "显示更多回复") {
                // 展开：显示所有隐藏的子回复
                hiddenReplies.forEach((reply) => (reply.style.display = "block"));
                button.textContent = "收起回复";
            } else {
                // 收起：隐藏多余的子回复
                hiddenReplies.forEach((reply) => (reply.style.display = "none"));
                button.textContent = "显示更多回复";
            }
        }

        comment.get()


        comment.send = function() {
            usersign.use().then(function(u) {
                if (!u.issignin) {
                    alert("请先登录");
                } else if (dqsa("#myInput", 1).value.length === 0) {
                    alert("请先输入评论");
                } else {
                    comment.sendbutton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
                    ajax({
                        url: "/php/forum/newcomment.php",
                        method: "POST",
                        send: `topic=${GETs.id}&tocomment=${comment.to}&content=${comment.inputelement.value}`,
                        success: function(r) {
                            let i = JSON.parse(r);
                            comment.sendbutton.innerHTML = `<i class="fa-solid fa-paper-plane"></i>`;
                            if (i.success === true) {
                                alert("发送成功");
                                comment.get(() => {
                                    comment.toreply(i.id)
                                });
                                comment.inputelement.value = "";
                            } else {
                                alert("发送失败：" + i.notice);
                            }
                        }
                    });
                }
            });
        };
        comment.sendbutton.addEventListener('click', comment.send, true);
        comment.cto = function(to) {
            document.querySelectorAll(".modal")[3].style.display = "block"; // 显示举报弹窗
            comment.to = to;
            comment.inputelement.value = "";
            comment.inputelement.placeholder = " 键入你欲评论的内容 " + (to === "none" ? "" : `（回复#${to}）`)
        };
        comment.toreply = function(pid) {
            if (typeof pid === "undefined") {
                return;
            }
            let reply = dqsa("#comments_" + pid, 1);
            reply.scrollIntoView(true);
            reply.getAnimations()[0].play();
            console.log(reply.getAnimations()[0])
        };
        comment.report = function(cid) {
            document.querySelectorAll(".modal")[2].style.display = "block"; // 显示举报弹窗
        }

        comment.delete = function(cid, uid) {
            if (!window.confirm("确认删除评论？")) {
                return;
            }
            usersign.use().then(function(u) {
                if (!u.issignin) {
                    alert(`未登录，无法删除评论`)
                } else if (uid != u.uid) {
                    alert(`无删除权限`)
                } else {
                    ajax({
                        url: "/php/forum/removecomment.php",
                        method: "POST",
                        send: `id=${cid}`,
                        success: function(response) {
                            alert(response)
                            response = JSON.parse(response);
                            alert(response.notice);
                            if (response.success) {
                                comment.get();
                            }
                        }
                    });
                }
            });
        };

        if (GETs.comment) {
    comment.get(() => {
        comment.toreply(GETs.comment); // 跳转到指定评论
    });
}

    </script>
    <script>
        const inputElement = document.getElementById('myInput');
        const chineseCountElement = document.getElementById('chineseCount');
        const englishCountElement = document.getElementById('englishCount');

        inputElement.addEventListener('input', function() {
            const inputValue = inputElement.value;
            let chineseCount = 0;
            let englishCount = 0;


            //监测英文字符和中文字符的个数
            for (let i = 0; i < inputValue.length; i++) {
                const char = inputValue[i];
                if (char.match(/[\u4e00-\u9fff]/)) {
                    chineseCount++;
                } else if (char.match(/[a-zA-Z0-9]|[^\u4e00-\u9fff\s\w]/)) {
                    englishCount++;
                }
            }

            //console.log(chineseCount)
            //console.log(englishCount)

            //判断
            if (chineseCount + englishCount / 3 > 300) {
                document.getElementById('myInput').maxLength = 1;
            } else {
                document.getElementById('myInput').maxLength = 900;
            }
        });
    </script>
    <script>
        var setemoji = {
            emojidiv: undefined,
            input: undefined,
            tr() { //切换打开与收起
                this.emojidiv.style.display = this.emojidiv.style.display === "none" ? "block" : "none";
            },
            emojiarr: (function() {
                let arr = [];
                for (let i = 128512; i <= 128591; i++) {
                    arr.push(i);
                }
                return arr;
            })(),
            clearevent(ele) {
                let f = (e) => {
                    e.preventDefault();
                    return false;
                };
                ele.onmousedown = f;
                ele.ontouchdown = f;
            },
            create(element, input) {
                this.emojidiv = element;
                element.style.display = "none";
                this.input = input;
                element.innerHTML += this.emojiarr.map((code) => {
                    return `<span class="emoji" data-id="${code}">&#${code}</span>`;
                }).join("");
                let onclick = function() {
                    setemoji.addemoji(this.dataset.id);
                };
                for (let i of document.querySelectorAll("div.emoji span.emoji")) {
                    i.addEventListener("click", onclick, true);
                    this.clearevent(i);
                }
            },
            addemoji(code) {
                let emoji = `[:${code}]`; //`&#${code}`;//String.fromCharCode(code);
                let output = this.input;
                if (document.activeElement === output) {
                    let value = output.value;
                    let start = output.selectionStart;
                    output.value = value.slice(0, start) + emoji + value.slice(start);
                    textarea.setSelectionRange(start + 1, start + 1);
                } else {
                    output.value += emoji;
                }
            }
        };

        setemoji.create(dqsa("div.emoji")[0], dqsa("#dialog-replycomment textarea")[0]);

        dqsa(".openemoji")[0].onclick = () => {
            setemoji.tr()
        }

        hljs.addPlugin(
            new CopyButtonPlugin({
                lang: "zh", // The copy button now says "Copiado!" when selected.
            })
        );

        hljs.highlightAll();

        hljs.initLineNumbersOnLoad();
    </script>
</body>

</html>