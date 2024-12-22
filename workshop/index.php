<?php

$file=array("success"=>false);
if(isset($_GET["id"])){
    require_once("../php/connect.php");
  $conn = connect();
  if ($conn->connect_error) {
      result("错误",<<<EOF
<h1>错误</h1>
<p>连接失败：{$conn->connect_error}</p>
EOF
      );
  }else{
    $sql0 = "SELECT fileinfo.*,user.nickname FROM fileinfo 
    INNER JOIN user ON fileinfo.user = user.id
    WHERE fileinfo.id =?";
    $stmt=$conn->prepare($sql0);
    $stmt->bind_param("i", $bp_id);
    $bp_id=round($_GET["id"]);
    if($stmt->execute()){
        $result=$stmt->get_result();
        $row=$result->fetch_assoc();
        if ($result->num_rows === 1) {
            result($row["title"],$row["content"]);
            $file = array(
                "success"=>"true",
                "id"=>$row["id"],
                "title"=>$row["title"],
                "name"=>$row["name"],
                "cover"=>$row["cover"],
                "content"=>$row["content"],
                "viewImg"=>$row["viewImg"],
                "user"=>$row["user"],
                "unickname"=>$row["nickname"],
                "map"=>$row["map"],
                "time"=>$row["reg_time"]
            );
        }else{
            result("错误",<<<EOF
<h1>错误</h1>
<p>文章不存在</p>
EOF
            );
        }
    }else{
        result("错误",<<<EOF
<h1>错误</h1>
<p>{$stmt->error}</p>
EOF
        );
    }
  }
    $stmt->close();
    $conn->close();
}else{
    result("错误",<<<EOF
<h1>错误</h1>
<p>错误的文章id</p>
EOF
    );
}
function result($title,$content){
    global $wtitle,$wcontent;
    $wtitle=$title;
    $wcontent=$content;
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
    <meta name='description' content=''> <!-- 描述，用PHP打印，与文章的意义 不留空格，提高信息密度 -->
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/topbar.css" />
    <link rel="stylesheet" href="/css/carousel-pic.css">
    <link rel="stylesheet" href="/css/dialog_img.css">
    <link rel="stylesheet" href="/css/dialog.css">
    <link rel="stylesheet" href="/css/dropdown.css">
    <link rel="stylesheet" href="/css/comment.css">
    <link rel="stylesheet" href="/css/page.css" />
    <link rel="stylesheet" href="/workshop/index.css" />
    <link rel="stylesheet" type="text/css" href="/css/darkmode.css">
    <link rel="stylesheet" href="/css/highlightjs-copy.min.css" />
    <link rel="stylesheet" href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/highlight.js/11.4.0/styles/atom-one-dark.min.css">
    <title id="workshop-title"><?php echo $wtitle; ?>-猪吧维基BPLEWiki</title>
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

    <div id="imgModal" class="img-modal">
        <span class="close">&times;</span>
        <img class="modal-img-content" id="img01">
        <div id="caption"></div>
    </div>


    <div id="dialog-report-article" class="modal">
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
                    <button class="submit" onclick="javascript:submitReport('workshop')">提交</button>
                </center>
            </div>
        </div>
    </div>

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
                    <button class="submit" onclick="javascript:submitReport('workshopcomment')">提交</button>
                </center>
            </div>
        </div>
    </div>

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


    <!-- 轮播图用php打印即可，一个图片对应一个dot -->
    <div class="main-content">
<div class="slider">
    <div class="slides">
        <?php
    $viewImgArray = explode(",", $file["viewImg"]);

if($viewImgArray[0] == "cover") {
    $viewImgArray[0] = $file["cover"];
}
        function printImagesAndCount($array, &$count) {
            foreach ($array as $key => $value) {
                    echo '<img class="dialog-img" src="' . $value . '" alt="Image ' . ($count + 1) . '">' . "\n";
                    $count++;               
            }
        }

        // 初始化计数器
        $imgCount = 0;

        // 调用递归函数遍历整个 $file 数组并计算图片数量
        printImagesAndCount($viewImgArray, $imgCount);
        ?>
    </div>
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
    <div class="dots">
        <?php
        // 根据图片数量生成对应数量的点
        for ($i = 1; $i <= $imgCount; $i++) {
            echo '<span class="dot" onclick="currentSlide(' . $i . ')"></span>' . "\n";
        }
        ?>
    </div>
</div>

            <h1><?php echo $wtitle ?></h1>

            <div class="content" id="content"><?php echo $wcontent ?></div>

            <div class="bottom-container">
                <div class="open-download desktop-download"><i class="fa-solid fa-download"></i></div>
                <div class="tool-bar">
                    <div class="toolfont"><i class="fa-solid fa-pen-to-square" id="revise"></i></div>
                    <div class="toolfont" id="collect"><i class="fa-solid fa-star"></i></div>
                    <div class="toolfont"><i class="fa-solid fa-circle-info" id="info"></i></div>
                    <div class="toolfont moreOptions" id="moreOptions" data-type="article"><i class="fa-solid fa-ellipsis"></i>
                        <div class="dropdown" id="moreDropdown" style="display:none;">
                            <div class="dropdown-item open-report-article"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="dropdown-item open-delete-article"><i class="fa-solid fa-trash"></i></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <hr><br>


    <!-- 现有的评论代码开始 -->
<div class="comment" id="comment">
    <h2>评论</h2>
    <div class="send-comment">
        <div class="inputrow">
            <textarea placeholder=" 键入你欲评论的内容" maxlength="900" id="myInput"></textarea>
            <span class="openemoji" onclick="setemoji.tr()"><i class="fa-regular fa-smile"></i></span>
            <i class="fa-solid fa-arrow-rotate-right" id="toarticle"></i>
            <span id="send"><i class="fa-regular fa-paper-plane"></i></span>
        </div>
        <div class="emoji"></div>
    </div>
    <div class="comment-container">
        <div id="areas">
            <!-- JavaScript 动态生成的评论将插入到这里 -->
        </div>
    </div>
</div>



    <div class="bottom-bar">
        <div class="download open-download" id="download-btn">
            <i class="fa-solid fa-download"></i>
            <p>点击下载</p>
        </div>
        <!-- <i style="margin-left: 20px;" class="fa-solid fa-circle-question" onclick="javascript:window.open('https://www.bplewiki.top/doc/save.html')"></i> -->
    </div>

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
    <script>    
        document.getElementById('back').addEventListener('click', function() {
            history.back();
        }, true)

        //搜索按钮动画
        document.querySelector('.fa-magnifying-glass').addEventListener('click', function() {
            const icon = this;
            icon.classList.add('search-animate');
            icon.addEventListener('animationend', function() {
                icon.classList.remove('search-animate');
            });
        });
        
        

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
        var winform = {}; //article信息
        var signinfo; //用户登录信息
     
            //workshop载入
            let i = 
<?php
echo json_encode($file,JSON_UNESCAPED_UNICODE);
?>

        
        
        
        dqsa("#info", 1).onclick = function() {
                    alert(`工坊创建者：${i.user}(${i.unickname})，创建时间：${i.time}`)
                };
                
   dqsa("#revise", 1).onclick = function() {
          usersign.use().then(function(u){          
            if (u.issignin) {            
                if (u.uid == i.user) {
                    window.open("revise.html?id=" + GETs.id, "_self")
                } else {
                    alert("无编辑权限");
                }
            } else {
                alert("未登录");
            }
          });
        };
                
            if (i.success) {
                if (i.type === "md" || true) {
                    dqsa("#content", 1).innerHTML = marked.parse(dqsa("#content", 1).innerHTML);
                }
                hljs.highlightAll(); //代码块语法高亮
                for (let ii of dqsa("article-title")) {
                    ii.innerHTML = i.title;
                }
                               
                
                console.log(i)
                
                winform.id = i.id;
                winform.uid = i.user;
                winform.name = i.name;
                winform.map = i.map;
                winform.unickname = i.unickname;
                winform.time = i.time;
                
                signed();
            }
    </script>
    <script>
        Page.create(document.querySelector("#comment"));
    </script>
    <script>
        // 存档名称管理

        const saveData = {
            "originalName": <?php

// 检查是否通过 GET 请求接收到 id 参数
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
        echo "'" . $name . "'";
    } else {
        // 处理错误：没有找到对应的记录
        http_response_code(404);
        echo "错误: 未找到对应的记录";
    }

    // 关闭语句
    $stmt->close();
} else {
    // 未提供 id 参数
    http_response_code(400);
    echo "错误: 未提供 id 参数";
}

// 关闭数据库连接
$conn->close();

?>,
            "mapName": ""
        };

const mapOptions = [{
                name: "不更换",
                saveName: saveData.originalName
            },
            {
                name: "脚踏实地",
                saveName: "Level_Sandbox_04"
            },
            {
                name: "异想天开之飞行小猪",
                saveName: "Level_Sandbox_03"
            },
            {
                name: "夜间飞行器",
                saveName: "Level_Sandbox_07"
            },
            {
                name: "起床战猪猪",
                saveName: "Level_Sandbox_09"
            },
            {
                name: "猪猪商城",
                saveName: "Episode_6_Tower Sandbox"
            },
            {
                name: "寻找头骨",
                saveName: "Level_Sandbox_01"
            },
            {
                name: "寻找雕像",
                saveName: "Episode_6_Dark Sandbox"
            },
            {
                name: "小猪大冒险",
                saveName: "MMSandbox"
            },
            {
                name: "梦幻之地",
                saveName: "Level_Sandbox_06"
            }
        ];

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

        var sel = document.getElementById('mapSelect');
        var sid = sel.selectedIndex;
        const selectedSaveName = sel[sid].value;
        document.querySelector(`#download-save`).download = `${selectedSaveName}`;


        let newSaveName = ''; // Move newSaveName to the global scope
function changeDownloadName() {
            var sel = document.getElementById('mapSelect');
            var sid = sel.selectedIndex;
            var selectedOption = sel.options[sid];
            var selectedSaveName = selectedOption.text.match(/\(([^)]+)\)/)[1];

            var numberInput = document.getElementById('numberInput');
            var inputNumber = parseInt(numberInput.value, 10);

            if (inputNumber < 1) {
                alert("数字不能小于1");
                return;
            }

            var numberSuffix = inputNumber > 1 ? `_${inputNumber - 1}` : '';

            // Update global newSaveName
            newSaveName = `${selectedSaveName}${numberSuffix}`;
            console.log(newSaveName);

            // Update the download attribute
            document.querySelector(`#download-save`).download = newSaveName;
        }


changeDownloadName()


        document.getElementById('download-save').addEventListener('click', function() {
            download(<?php
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('file_url' => $file_url))); // 将 file_url 作为 POST 数据
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
?>, newSaveName)
        }, true)


        function download(url, filename) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.responseType = 'blob';
            xhr.onload = function() {
                if (xhr.status !== 200) {
                    alert('下载异常！');
                    return;
                }
                var encodedFilename = encodeURIComponent(filename); // 对文件名进行编码
                if (window.navigator.msSaveOrOpenBlob) {
                    navigator.msSaveBlob(xhr.response, filename);
                } else {
                    var newUrl = window.URL.createObjectURL(xhr.response);
                    var a = document.createElement('a');
                    a.setAttribute('href', newUrl);
                    a.setAttribute('target', '_blank');
                    a.setAttribute('download', encodedFilename); // 使用编码后的文件名
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            };
            xhr.send();
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

        let workShopID = GETs.id

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
                    id: workShopID
                };

                ajax({
                    url: "/php/report.php",
                    method: "POST",
                    send: (`type=${type}&id=${id}&reason=${selectedOption}` + (reason.length > 0 ? `&content=${reason}` : "")),
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
        
    let id = Number(GETs.id);    

const collect = {
        data: (function(co) {
            if (typeof co !== "undefined") {
                setTimeout(() => {
                    collect.setstarcolor()
                }, 100);
                co = JSON.parse(co);
                if (typeof co.workshop === "undefined") {
                    co.workshop = [];
                }
                return co;
            } else {
                setTimeout(() => {
                    checkAndSetStarColor()
                }, 100);
                return {
                    workshop: [],
                    gettime: 0
                };
            }
        })(localStorage.collect),
        hasarticle(id) {
            console.log(this.data.workshop)
            console.log(Number(id))
            return this.data.workshop.includes(Number(id));
        },
        updateA(ids) {
            if (typeof ids === "undefined") {
                ids = [];
            }
            this.data.workshop = ids;
            this.save();
        },
        updateA2() {
            let a = this.data.workshop;
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
            url: "/php/savefile/collectg.php",
            method: "POST",
            success: function(r) {
                r = JSON.parse(r);
                collect.updateA(r.workshop);
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
                        url: "/php/savefile/collectc.php",
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
    
    function deleteArticle() {
      usersign.use().then(function(u){
        if (!u.issignin) {
            alert(`未登录，无法删除文章`);
        }else if(i.user == u.uid) {
            ajax({
                url: "/php/savefile/remove.php",
                method: "POST",
                send: `id=${id}`,
                success: function(response) {
                    alert(`物品已删除`)
                    location.reload();
                }
            });
        } else {
            alert(`无删除权限`);
        }
      });
    }
    
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
        let successdo = function(r) {
            let i = JSON.parse(r);
            if (i.success !== true) {
                areas.innerHTML = `<p>发生错误：${i.notice}。${srbt}</p>`;
            } else {
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
                    c.content = c.content.replaceAll(/\[:([0-9]+)\]/g, '&#$1;');
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
            }
        };
        ajax({
            url: "/workshop/comment.php?id=" + GETs.id,
            method: "POST",
            success: successdo
        });
    };
    
 comment.get()
    
    comment.send = function() {
      usersign.use().then(function(u){
        if (!u.issignin) {
            alert("请先登录");
        } else if (dqsa("#myInput", 1).value.length === 0) {
            alert("请先输入评论");
        } else {
            comment.sendbutton.innerHTML = `<i class="fa fa-refresh fa-spin"></i>`;
            ajax({
                url: "/php/savefile/newcomment.php",
                method: "POST",
                send: `fileid=${GETs.id}&tocomment=${comment.to}&content=${comment.inputelement.value}`,
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
        usersign.use().then(function(u){
            if (!u.issignin) {
                alert(`未登录，无法删除评论`)
            } else if (uid != u.uid) {
                alert(`无删除权限`)
            } else {
                ajax({
                    url: "/php/savefile/removecomment.php",
                    method: "POST",
                    send: `id=${cid}`,
                    success: function(response) {alert(response)
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






    function dqsa(qsqs, first) {
        if (first) {
            return document.querySelector(qsqs);
        } else {
            return document.querySelectorAll(qsqs);
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