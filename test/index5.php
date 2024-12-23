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
    $sql0 = "SELECT article.*,user.nickname FROM article 
    INNER JOIN user ON article.user = user.id
    WHERE article.id ='{$aid}'";
    $result=$conn->query($sql0);
    $row=$result->fetch_assoc();
    if ($result->num_rows === 1) {
        result($row["title"],$row["content"]);
        $file = array(
            "success"=>"true",
            "title"=>$row["title"],
            "user"=>$row["user"],
            "unickname"=>$row["nickname"],
            "type"=>$row["type"],
            "time"=>$row["reg_time"]
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
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.9.0/styles/default.min.css">
    <script src="https://cdn.bootcdn.net/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <link rel="stylesheet" href="/css/dropdown.css">
    <meta charset="UTF-8">
    <meta name="viewport" id="viewport" content="width=device-width, initial-scale=1">
    <meta name='description' content='<?php echo preview($acontent); ?>'>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/topbar.css" />
    <link rel="stylesheet" href="/css/dialog.css" />
    <link rel="stylesheet" href="/css/comment.css">
    <title><?php echo $atitle; ?>-猪吧维基BPLEWiki</title>
    <style>
        /*文章样式*/
         h1 {
            word-wrap:break-word;
         }

        p,
        h1,
        h2,
        h3:not(.title),
        h4,
        h5,
        h6 {
            font-family: sans-serif;
            word-wrap:break-word;         
        }

        .search {
            position: absolute;
            text-decoration: none;
            opacity: 0.9;
            top: 5px;
            right: 10px;
            width: 30px;
            height: 30px;
            color: white;
            background-color: #c2e7ff;
            font-size: 120%;
            border-radius: 5px;
            display: flex;
            align-items: center; /* 垂直居中 */
            justify-content: center; /* 水平居中 */
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

        .tool {
            background-color: white;
            float: right;
            margin-right: 10px;
            margin-top: 70px;
            margin-bottom: 50%;
            padding: 10px;
            box-shadow: 5px 5px 50px rgba(0, 0, 0, 0.5);
            width: 75%;
            height: 40px;
            border-radius: 30px;
            box-sizing: border-box;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .toolfont {
            margin: 0 auto;
            text-align: center;
            display: flex;
            align-items: center； justify-content: center;
            flex: 1;
        }

        .toolfont:active {
            color: green;
        }

        #content * {
            max-width: 100%;
        }

        /*评论样式*/

        #comment {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow：auto;
            z-index: 2;
            background-color: white;
            display: none;
        }

        #comment #areas {
            height: calc(100% - 100px);
            overflow: auto;
            margin-top: 70px;
        }

        #comment .block {
            position: relative;
            min-height: 140px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 5px 5px 30px rgba(0, 0, 0, 0.5);
            /* 初始阴影 */
            padding: 10px;
            margin-left: 10px;
            animation: blink 1.5s ease-out 3 forwards;
            animation-play-state: paused;
        }

        .toolfont {
            position: relative;
            display: inline-block;
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

        #comment .bottomBar {
            position: fixed;
            width: 100%;
            height: 40px;
            left: 0;
            margin: 0;
            bottom: 0px;
            background-color: white;
            box-shadow: 5px 5px 50px rgba(0, 0, 0, 0.5);
        }

        #comment input {
            position: relative;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            width: 80%;
            height: 25px;
            background-color: #e9eef6;
            border: none;
            border-radius: 30px;
        }

        #comment #send {
            position: relative;
            margin-left: 20px;
            top: 20%;
        }

        #comment #send:active {
            color: green;
        }

        #comment .space {
            height: 80px;
        }


        #comment .h2 {
            position: relative;
            left: 35px;
            top: -15px;
        }

        #comment .fa-angle-left {
            position: absolute;
            font-size: 170%;
            left: 10px;
            top: 5px;
        }

        #comment .fa-angle-left:active {
            color: green;
        }

        .head .index {
            margin-top: -25px;
            margin-left: 10px;
            width: 100%;
            height: 20px;
            left: 0;
        }

        .head {
            position: fixed;
            width: 100%;
            height: 70px;
            top: 0;
            left: 0;
            z-index: 1;
            background-color: white;
            box-shadow: 5px 5px 50px rgba(0, 0, 0, 0.5);
        }

        .index i {
            color: lightgreen;
        }

        #comment .more {
            position: absolute;
            right: 20px;
        }

        #comment .more i:active {
            color: green;
        }

        #comment .fa-comment-dots {
            position: absolute;
            right: 35px;
        }

        #comment .fa-comment-dots:active {
            color: green;
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
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
    </style>
</head>

<body>

    <div class="topblk" id="topblk">
        <div class="back" id="btn">
            <i class="fa-solid fa-angle-left"></i>
        </div>
        <a href="../index.html">
            <div class="logo">
                <img src="../img/favicon.ico" alt="logo">
                <h3 class="title">猪吧维基</h3>
            </div>
        </a>
        <a class="search" href="../search.html"><i class="fa-solid fa-magnifying-glass"></i></a>
    </div>
            <noscript><p>javascript未运行</p></noscript>
    <div id="content">
<?php
    echo $acontent;
?>

    </div>
        <div id="reportArticleModal" class="modal">
            <div class="modal-content modal-content-creative">
                <span class="close-button">&times;</span>
                <h2>举报文章</h2>
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
                        <button class="submit" onclick="javascript:submitReport('article')">提交</button>
                    </center>
                </div>
            </div>
        </div>

        <div class="modal">
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

        <div id="reportCommentModal" class="modal" style="z-index:3 !important;">
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
                        <button class="submit" onclick="javascript:submitReport('articlecomment')">提交</button>
                    </center>
                </div>
            </div>
        </div>


    </div>
    <div class="tool">
        <div class="toolfont"><i class="fa-solid fa-pen-to-square" id="revise"></i></div>
        <div class="toolfont" id="collect"><i class="fa-solid fa-star"></i></div>
        <div class="toolfont" id="commentBtn"><i class="fa-solid fa-comment"></i></div>
        <div class="toolfont"><i class="fa-solid fa-circle-info" id="info"></i></div>
        <div class="toolfont moreOptions" id="moreOptions" data-type="article"><i class="fa-solid fa-ellipsis"></i>
            <div class="dropdown" id="moreDropdown" style="display:none;">
                <div class="dropdown-item edit-button"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="dropdown-item edit-button"><i class="fa-solid fa-trash"></i></div>
            </div>
        </div>
    </div>


    <div id="comment">
        <div class="head">
            <span id="back"><i class="fa-solid fa-angle-left"></i></span>
            <p class="h2">评论</p>
            <p class="index"><b>文章标题：</b><i><article-title>标题</article-title></i><i id="toarticle" class="fa-solid fa-comment-dots">重置回复</i></p>
        </div>
        <div id="areas">
            <!-- 评论 -->
        </div>
        <div class="bottomBar">
            <input placeholder=" 键入你欲评论的内容" maxlength="900" id="myInput">
            <span id="send"><i class="fa-solid fa-paper-plane"></i></span>
        </div>
    </div>
</body>
<script src="/js/ajax.js"></script>
<script src="https://bplewiki.top/js/dialog.js"></script>
<script src="/js/dropdown.js"></script>
<script src="marked.min.js"></script>
<script>
    document.getElementById('btn').addEventListener('click', function() {
        history.back();
    }, true);
    
    document.querySelector('.fa-magnifying-glass').addEventListener('click', function() {
    const icon = this;
    icon.classList.add('search-animate');
    icon.addEventListener('animationend', function() {
        icon.classList.remove('search-animate');
    });
});

    document.getElementById('commentBtn').addEventListener('click', function() {
        document.getElementById("topblk").style.display = "none";
        document.getElementById("comment").style.display = "block";
        comment.get(() => {
            if (GETs.comment) {
                comment.toreply(GETs.comment);
            }
        });
    }, true)
    document.getElementById('back').addEventListener('click', function() {
        document.getElementById("comment").style.display = "none";

        document.getElementById("topblk").style.display = "block";
    }, true)


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
    var ainform = {}; //article信息
    var signinfo; //用户登录信息
    if ((GETs.id)) {
        //article载入
        let i = JSON.parse(`
<?php
echo json_encode($file,JSON_UNESCAPED_UNICODE);
?>
        `);
        if (i.success) {
            if(i.type==="md"){
                dqsa("#content", 1).innerHTML = marked.parse(dqsa("#content", 1).innerHTML);
            }
            hljs.highlightAll(); //代码块语法高亮
            for (let ii of dqsa("article-title")) {
                ii.innerHTML = i.title;
            }
            ainform.uid = i.user;
            ainform.unickname = i.unickname;
            ainform.time = i.time;
            dqsa("#info", 1).onclick = function() {
                alert(`文章创建者：${ainform.uid}(${ainform.unickname})，创建时间：${ainform.time}`)
            };
            signed();
        }
        dqsa("#revise", 1).onclick = function() {
            signed(function() {
                if (signinfo.success === "true") {
                    if (signinfo.user.id == ainform.uid) {
                        window.open("revise.html?id=" + GETs.id, "_self")
                    } else {
                        alert("无编辑权限");
                    }
                } else {
                    alert("未登录");
                }
            });
        };
    }

    function deleteArticle() {
        signed(function() {
            if (signinfo.success === "true") {
                if (ainform.uid == signinfo.user.id) {
                    ajax({
                        url: "/php/article/remove.php",
                        method: "POST",
                        send: `id=${id}`,
                        success: function(response) {
                            alert(`文章已删除`)
                            location.reload();
                        }
                    });
                } else {
                    alert(`无删除权限`)
                }
            } else {
                alert(`未登录，无法删除文章`)
            }
        });
    }

    //评论
    var comment = {
        to: "none",
        inputelement: dqsa("#myInput", 1),
        sendbutton: dqsa("#send", 1),
        toarticlebutton: dqsa("#toarticle", 1)
    };
    comment.get = function(aftersuccess) {
        let srbt = "<button onclick='comment.get()'>刷新评论</button>";
        let areas = dqsa("#areas", 1);
        if (!GETs.id) {
            areas.innerHTML = "<p>无文章id，无法获取评论</p>"
        } else {
            areas.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
            let successdo = function(r) {
                let i = JSON.parse(r);
                if (i.success === "true") {
                    let empty = function(sth) {
                        if (typeof sth === "undefined" || sth === "") {
                            return true;
                        } else {
                            return false;
                        }
                    };
                    let allcomm = [];
                    let mains = [];
                    let getcommentbyid = function(cid) {
                        for (let comm of allcomm) {
                            if (comm.id === cid) {
                                return comm;
                            }
                        }
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
                            content: c.content,
                            tocomment: c.tocomment,
                            subs: c.tocomment === "null" ? [] : "none",
                            parent: c.tocomment === "null" ? "page" : "waitfind",
                            time: c.time
                        });
                    }
                    for (let c of allcomm) {
                        if (c.parent === "page") {
                            //主评论
                            mains.push(c);
                        } else {
                            //评论的回复
                            c.parent = findmain(c);
                            c.parent.subs.push(c);
                            c.toname = getcommentbyid(c.tocomment).showname;
                        }
                    }
                    if (allcomm.length === 0) {
                        areas.innerHTML = "暂无评论";
                    } else {
                        areas.innerHTML = "";
                    }
                    for (let m of mains) {
                        let sub = "";
                        let ht = function(options) {
                            return `
       <div class="${options.type}Block comment-block" id="comments_${options.id}">
            <img src="${options.imgurl}">
            <span class="uname pos"><b>${options.showname}</b></span>
            <span class="more">
                <i class="fa-solid fa-comment-dots font" data-commentid="${options.id}" onclick="comment.cto(this.dataset.commentid)"></i>
                <span class="moreOptions" data-type="comment">
                    <i class="fa-solid fa-ellipsis font"></i>
                    <div class="dropdown" style="display:none;z-index:3;">
                        <div class="dropdown-item"><i class="fa-solid fa-triangle-exclamation" onclick="comment.report(${options.id})"></i></div>
                        <div class="dropdown-item"><i class="fa-solid fa-trash" onclick="comment.delete(${options.id},${options.uid})"></i></div>
                    </div>
                </span>
            </span>
            <p class="mainP">${options.content}</p>
        </div>
                            `;
                        }
                        for (let s of m.subs) {
                            let tcm = `<span class="reply" onclick="comment.toreply(${s.tocomment})">@${s.toname} </span>`;
                            let htload = {
                                type: "sub",
                                id: s.id,
                                imgurl: "favicon.png",
                                showname: s.showname,
                                uid: s.uid,
                                content: (s.tocomment === m.id ? "" : tcm) + s.content
                            };
                            sub += ht(htload);
                        }
                        let htload = {
                            type: "main",
                            id: m.id,
                            imgurl: "favicon.png",
                            showname: m.showname,
                            uid: m.uid,
                            content: m.content
                        };
                        areas.innerHTML += `
                            <div class="area">
                              ${ht(htload)}
                              <p class="subP">${sub}</p>
                            </div>
                        `;
                    }
                    areas.innerHTML += `<div class="space"></div>`;
                    newdropdown(areas.querySelectorAll('.moreOptions'));
                    if (aftersuccess) {
                        aftersuccess();
                    }
                } else {
                    areas.innerHTML = `<p>发生错误：${i.notice}。${srbt}</p>`;
                }
            };
            ajax({
                url: "comment.php?id=" + GETs.id,
                method: "POST",
                success: successdo
            });
        }
    };
    comment.send = function() {
        if (signinfo.success !== "true") {
            alert("请先登录");
        } else if (dqsa("#myInput", 1).value.length === 0) {
            alert("请先输入评论");
        } else {
            comment.sendbutton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
            ajax({
                url: "/php/article/newcomment.php",
                method: "POST",
                send: `article=${GETs.id}&tocomment=${comment.to}&content=${comment.inputelement.value}`,
                success: function(r) {
                    let i = JSON.parse(r);
                    comment.sendbutton.innerHTML = `<i class="fa-solid fa-paper-plane"></i>`;
                    if (i.success === "true") {
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
    };
    comment.sendbutton.addEventListener('click', comment.send, true);
    comment.cto = function(to) {
        comment.to = to;
        comment.inputelement.value = "";
        comment.inputelement.placeholder = " 键入你欲评论的内容 " + (to === "none" ? "" : `（回复#${to}）`)
    };
    comment.toarticlebutton.addEventListener('click', () => {
        comment.cto("none")
    }, true);
    comment.toreply = function(pid) {
        if (typeof pid === "undefined") {
            return;
        }
        let reply = dqsa("#comments_" + pid, 1);
        reply.scrollIntoView(true);
        reply.getAnimations()[0].play();
    };
    comment.report = function(cid) {
        document.querySelectorAll(".modal")[2].style.display = "block"; // 显示举报弹窗
    }

    comment.delete = function(cid, uid) {
        if (!window.confirm("确认删除评论？")) {
            return;
        }
        signed(function() {
            if (signinfo.success !== "true") {
                alert(`未登录，无法删除评论`)
            } else if (uid != signinfo.user.id) {
                alert(`无删除权限`)
            } else {
                ajax({
                    url: "/php/article/removecomment.php",
                    method: "POST",
                    send: `id=${cid}`,
                    success: function(response) {
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
        document.getElementById('commentBtn').click();
    }

    function signed(thendo) {
        ajax({
            url: "/php/issignin.php",
            method: "POST",
            success: function(r) {
                signinfo = JSON.parse(r);
                if (thendo) {
                    thendo();
                }
            }
        });
        return true;
    }


    function dqsa(qsqs, first) {
        if (first) {
            return document.querySelector(qsqs);
        } else {
            return document.querySelectorAll(qsqs);
        }
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
    let id = Number(GETs.id);

    const collect = {
        data: (function(co) {
            if (typeof co !== "undefined") {
                setTimeout(() => {
                    collect.setstarcolor()
                }, 100);
                co = JSON.parse(co);
                if (typeof co.articles === "undefined") {
                    co.articles = [];
                }
                return co;
            } else {
                setTimeout(() => {
                    checkAndSetStarColor()
                }, 100);
                return {
                    articles: []
                };
            }
        })(localStorage.collect),
        hasarticle(id) {
            console.log(this.data.articles)
            console.log(Number(id))
            return this.data.articles.includes(Number(id));
        },
        updateA(ids) {
            if (typeof ids === "undefined") {
                ids = [];
            }
            this.data.articles = ids;
            this.save();
        },
        updateA2() {
            let a = this.data.articles;
            let io = a.indexOf(id);
            if (io === -1) {
                a.push(Number(id));
            } else {
                a.splice(io, 1);
            }
            this.save();
        },
        setstarcolor() {
            document.querySelector(`.fa-star`).style.color = this.hasarticle(id) ? "green" : "black";
        },
        save() {
            localStorage.collect = JSON.stringify(this.data);
        }
    };

    function checkAndSetStarColor() { //avascript:checkAndSetStarColor()
        ajax({
            url: "/php/collectg.php",
            method: "POST",
            success: function(r) {
                r = JSON.parse(r);
                collect.updateA(r.articles);
                collect.setstarcolor();
            }
        });
    }

    document.getElementById('collect').addEventListener('click', function() {
        ajax({
            url: "/php/issignin.php",
            method: "POST",
            success: function(r) {
                r = JSON.parse(r);
                if (r.success === "true") {
                    ajax({
                        url: "/php/collectc.php",
                        method: "POST",
                        send: `id=${id}`,
                        success: function(response) {
                            if (response.indexOf("<b>") !== -1) {
                                alert(response);
                                return;
                            }
                            let r = JSON.parse(response);
                            alert(r.notice);
                            // 在每次操作后设置收藏状态
                            if (Math.random() < 0.9) {
                                collect.updateA2();
                                collect.setstarcolor();
                            } else {
                                checkAndSetStarColor();
                            }
                        }
                    });
                } else {
                    alert(`请登录`)
                }
            }
        }); //这段代码一运行就会发送请求

    }, true);
</script>
<script>
    function saveHistory() {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');

        if (id) {
            let history = localStorage.getItem('articleHistory');
            history = history ? JSON.parse(history) : [];
            history.push(id); // 直接添加id，不检查是否重复
            localStorage.setItem('articleHistory', JSON.stringify(history));
        }
    }

    window.addEventListener("load", saveHistory, true);
</script>
<script>
    document.querySelectorAll('.close-button')[2].addEventListener('click', function() {
        document.querySelectorAll(".modal")[2].style.display = "none"
    }, true);

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




    function submitReport(type) {
        let modalPrefix = type === 'article' ? 'reportArticleModal' : 'reportCommentModal';
        let modal = document.getElementById(modalPrefix);
        let selectedElement = modal.querySelector('.report-option.selected');
        let reasonTextarea = modal.querySelector('.argument');

        if (selectedElement) {
            var selectedOption = selectedElement.dataset.value;
            var reason = reasonTextarea.value;
            var reportContent = {
                reason: reason,
                option: selectedOption,
                id: id
            };

            ajax({
                url: "/php/report.php",
                method: "POST",
                send: (`type=${type}&id=${id}&reason=${selectedOption}`+(reason.length>0?`&content=${reason}`:"")),
                success: function(response) {alert(response)
                    response=JSON.parse(response);
                    if(response.success){
                        alert(`举报成功`);
                    }else{
                        alert(response.notice);
                    }
                    modal.style.display = `none`;
                }
            });
        } else {
            alert('请选择一个举报原因');
        }
    }
</script>
</body>

</html>