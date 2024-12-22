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
            "success"=>true,
            "title"=>$row["title"],
            "content"=>$row["content"],
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
    <link href="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/font-awesome/6.0.0/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/styles/atom-one-dark.min.css">
    <!--link href="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/styles/github.min.css" rel="stylesheet"-->
    <link rel="stylesheet" href="/css/dropdown.css">
    <meta charset="UTF-8">
    <meta name="viewport" id="viewport" content="width=device-width, initial-scale=1">
    <meta name='description' content='<?php echo preview($acontent); ?>'>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/topbar.css" />
    <link rel="stylesheet" href="/css/dialog.css" />
    <link rel="stylesheet" href="/css/comment.css">
    <link rel="stylesheet" href="/css/highlightjs-copy.min.css" />
    <link rel="stylesheet" type="text/css" href="/css/darkmode.css">
    <link rel="stylesheet" type="text/css" href="index.css?v=0.1.0"><!-- 本页专用css-->
    <title><?php echo $atitle; ?>-猪吧维基BPLEWiki</title>
    <style>        

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
    <noscript>
        <p>javascript未运行</p>
    </noscript>
    <div id="content">
<?php
    echo "<h1>".$atitle."</h1>";
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

    <div id="reportCommentModal" class="modal">
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
            <span id="back"><i class="fa-solid fa-angle-down close-comment"></i></span>
            <p class="h2">评论</p>
            <p class="index"><b>文章标题：</b><i><article-title>标题</article-title></i><i id="toarticle" class="fa-solid fa-comment-dots">重置回复</i></p>
        </div>
        <div id="areas">
            <!-- 评论 -->
        </div>
        <div class="bottomBar">
            <div class="inputrow">
                <span class="openemoji" onclick="setemoji.tr()"><i class="fa-solid fa-smile"></i></span>
                <textarea placeholder=" 键入你欲评论的内容" maxlength="900" id="myInput"></textarea>
                <span id="send"><i class="fa-solid fa-paper-plane"></i></span>
            </div>
            <div class="emoji"></div>
        </div>
    </div>
</body>

<script src="/js/darkmode.js"></script>
<script src="/js/ajax.js"></script>
<script src="/js/dialog.js"></script>
<script src="/js/dropdown.js"></script>
<script src="/js/usersign.js?v=0.1.0"></script>

<script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/highlight.min.js"></script>
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js" type="application/javascript"></script>
<script src="/js/highlightjs-copy.min.js"></script>
<script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/marked/4.0.2/marked.min.js" type="application/javascript"></script>
<script src="https://s4.zstatic.net/ajax/libs/dompurify/3.1.7/purify.min.js"></script>
<script>
//https://cdn.bootcdn.net/ajax/libs/dompurify/3.1.6/purify.min.js
    //载入内容等
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
    //var signinfo; //用户登录信息改为usersign
    if ((GETs.id)) {
        //article载入
        let i = JSON.parse(`<?php
    echo addcslashes(json_encode($file,JSON_UNESCAPED_UNICODE),"\\`");
?>`);
        if (i.success) {
            let filterconfig={
                CUSTOM_ELEMENT_HANDLING: {
                    tagNameCheck: /^article-/, // 允许article-开头的元素
                    attributeNameCheck: null, // 使用默认/标准属性允许列表
                    allowCustomizedBuiltInElements: true, // 不允许自定义内置元素
                },
            };
            if (i.type === "md") {
                dqsa("#content", 1).innerHTML = DOMPurify.sanitize(marked.parse(i.content),filterconfig);
            } else {
                dqsa("#content", 1).innerHTML = DOMPurify.sanitize(i.content,filterconfig);
            }
            for (let ii of dqsa("article-title")) {
                ii.innerHTML = i.title;
            }
            
            hljs.highlightAll(); //代码块语法高亮
            
            ainform.uid = i.user;
            ainform.unickname = i.unickname;
            ainform.time = i.time;
            dqsa("#info", 1).onclick = function() {
                alert(`文章创建者：${ainform.uid}(${ainform.unickname})，创建时间：${ainform.time}`)
            };
        }
        dqsa("#revise", 1).onclick = function() {
          usersign.use().then(function(u){
            if (u.issignin) {
                if (u.uid == ainform.uid) {
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

    //根据屏幕尺寸更改关闭评论界面的按钮的类型
    let width = window.innerWidth
    if (width >= 768) {
        const closeCommentBtn = document.querySelector(`.close-comment`)
        closeCommentBtn.classList.remove(`fa-angle-down`)
        closeCommentBtn.classList.add(`fa-angle-right`)
    }


    const commentPanel = document.getElementById("comment");

    commentPanel.addEventListener('transitionend', function() {
        if (commentPanel.classList.contains('out')) {
            commentPanel.style.display = "none";
            commentPanel.classList.remove('out'); // 确保在隐藏后移除 out 类
        }
    });

    function toggleCommentPanel(show) {
        const commentPanel = document.getElementById("comment");
        if (show) {
            commentPanel.classList.remove('out');
            setTimeout(() => {
                commentPanel.classList.add('show');
            }, );
            // 强制浏览器重绘元素
            commentPanel.style.display = "block";
            comment.get(() => {
                if (GETs.comment) {
                    comment.toreply(GETs.comment);
                }
            });
        } else {
            commentPanel.classList.remove('show');
            commentPanel.classList.add('out');
            setTimeout(() => {
                commentPanel.style.display = "none";
            }, 200); // 假设过渡动画时间是300毫秒
        }
    }

    document.getElementById('commentBtn').addEventListener('click', function() {
        toggleCommentPanel(true);
    }, true);

    document.getElementById('back').addEventListener('click', function() {
        toggleCommentPanel(false);
    }, true);


    function deleteArticle() {
      usersign.use().then(function(u){
        if (!u.issignin) {
            alert(`未登录，无法删除文章`);
        }else if(ainform.uid == u.uid) {
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
            alert(`无删除权限`);
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
            areas.innerHTML = "<p>无文章id，无法获取评论</p>";
            return;
        }
        areas.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
        fetch("/php/article/comment.php?id=" + GETs.id, {
            method: "POST"
        })
            .then((r)=>r.json())
            .then((i)=>{
                if (i.success !== true) {
                    throw i.notice;
                } else {
                    return i.comments;
                }
            })
            .then((comments)=>{
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
                    let emptycomm={id:cid,showname:"System",uid:-1,content:"该评论已被删除",tocomment:"null",subs:[],parent:"page",time:0};
                    console.log("引用到空评论："+cid)
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
                for (let c of comments) {
                    allcomm.push({
                        id: c.id,
                        showname: empty(c.unickname) ? c.user : c.unickname,
                        uid: c.user,
                        avatar: c.avatar,
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
                        `;
                    }
                    for (let s of m.subs) {
                        let tcm = `<span class="reply" onclick="comment.toreply(${s.tocomment})">@${s.toname} </span>`;
                        let htload = {
                            type: "sub",
                            id: s.id,
                            imgurl: s.avatar,
                            showname: s.showname,
                            uid: s.uid,
                            content: (s.tocomment === m.id ? "" : tcm) + s.content
                        };
                        sub += ht(htload);
                    }
                    let htload = {
                        type: "main",
                        id: m.id,
                        imgurl: m.avatar,
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
            
            
            })
            .catch((e)=>{
                areas.innerHTML = `<p>发生错误：${e}。${srbt}</p>`;
            });
    };
    comment.send = function() {
      usersign.use().then(function(u){
        if (!u.issignin) {
            alert("请先登录");
        } else if (dqsa("#myInput", 1).value.length === 0) {
            alert("请先输入评论");
        } else {
            comment.sendbutton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
            fetch("/php/article/newcomment.php",{
                method: "POST",
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify({
                    article: GETs.id,
                    tocomment: comment.to,
                    content: comment.inputelement.value.replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{1F700}-\u{1F77F}\u{1F780}-\u{1F7FF}\u{1F800}-\u{1F8FF}\u{1F900}-\u{1F9FF}\u{1FA00}-\u{1FA6F}\u{1FA70}-\u{1FAFF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{2300}-\u{23FF}]/gu, (match) => {return '[:' + match.codePointAt(0).toString(10).padStart(4, '0') + ']';})
                })//`article=${GETs.id}&tocomment=${comment.to}&content=${comment.inputelement.value}`,
            })
            .then((r)=>r.json())
            .then((i)=>{
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
            }).catch(e=>alert(e));
        }
      });
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
            throw null;
        }
        usersign.use()
        .then((u)=>{
            if (!u.issignin) {
                throw (`未登录，无法删除评论`);
            } else if (uid != u.uid) {
                throw (`无删除权限`);
            } else {
                return fetch("/php/article/removecomment.php",{
                    method: "POST",
                    body: (()=>{let fd=new FormData();fd.append("id",cid);return fd;})()
                });
            }
        })
        .then((r)=>r.json())
        .then((r)=>{
            alert(r.notice);
            if (r.success) {
                comment.get();
            }
        })
        .catch((e)=>{
            if(typeof e!=="undefined"){
                alert(e);
            }
        });
    };

    if (GETs.comment) {
        document.getElementById('commentBtn').click();
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
                    articles: [],
                    gettime: 0
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
            document.querySelector(`.fa-star`).style.setProperty('color', this.hasarticle(id) ? 'green' : 'black', 'important');
        },
        save() {
            localStorage.collect = JSON.stringify(this.data);
        }
    };

    function checkAndSetStarColor() { //avascript:checkAndSetStarColor()
        ajax({
            url: "/php/article/collectg.php",
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
            url: "/php/user/issignin.php",
            method: "POST",
            success: function(r) {
                r = JSON.parse(r);
                if (r.success) {
                    ajax({
                        url: "/php/article/collectc.php",
                        method: "POST",
                        send: `id=${id}`,
                        success: function(response) {
                            if (response.indexOf("<b>") !== -1) {
                                alert(response);
                                return;
                            }
                            let r = JSON.parse(response);
                            alert(r.notice);
                            if(!r.success){
                                return;
                            }
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
                send: (`type=${type}&id=${id}&reason=${selectedOption}` + (reason.length > 0 ? `&content=${reason}` : "")),
                success: function(response) {
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

    setemoji.create(dqsa("div.emoji")[0], dqsa("#comment textarea")[0]);

    dqsa(".openemoji")[0].onclick = () => {
        setemoji.tr()
    }
</script>
<script>
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