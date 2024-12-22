var Page = (function() {
    let Page = {};
    let autoid = 0;
    Page.v = [0, 0, 4];
    Page.checkv = function(v) {
        if (typeof Page.v === 'undefined') {
            return false;
        } else if (Page.v[0] < v[0]) {
            return false;
        } else if (Page.v[1] < v[1]) {
            return false;
        } else if (Page.v[2] < v[2]) {
            return false;
        }
        return true;
    };
    Page.data = {};

    //添加翻页元素。ele：对应父元素
    Page.create = function(ele, id, hasoutput = true, clear = false) {
        // 检查是否已经存在翻页框
        if (ele.querySelector(".pagination")) {
            return;
        }

        autoid++;
        if (clear) {
            ele.innerHTML = "";
        }
        if (hasoutput) {
            ele.innerHTML += `<div class="output"></div>`;
        }
        id = id || ("pagination_" + autoid);
        ele.innerHTML += `
<div class="pagination" id=${id}>
    <button class="prevPage"><i class="fa-solid fa-angle-left"></i></button>
    第 <input type="number" class="currentPage" value="1" min="1" style="width: 50px;"> 页 /
    共 <span class="totalPages">?</span> 页
    <button class="nextPage"><i class="fa-solid fa-angle-right"></i></button>
</div>
        `;
    };

    //获取总页数。totality：number 元素数量
    Page.getPage = function(totality) {
        let totalPages = 1;
        if (totality % 10 === 0) {
            totalPages = totality / 10;
        } else if (totality / 10 > 1) {
            totalPages = Math.ceil(totality / 10);
        }
        return totalPages;
    };

    Page.getTenItemsAsString = function(arr, pageNum) {
        const startIndex = (pageNum - 1) * 10;
        const slice = arr.slice(startIndex, startIndex + 10);
        return slice.join(',');
    };

    //翻页功能。currentPage：number 默认页码；prevbutton：element 上一页按钮；nextbutton：element 下一页按钮；cpInput：element 显示、改变页数的input；theTotalPages：number 总页数；theids：array 一些id。outputEle：element 输出内容元素。funcsendid：function 发送ids与进行相关操作的函数
    Page.addPageTurning = function(currentPage, prevbutton, nextbutton, cpInput, eleTotal, theTotalPages, theids, outputEle, funcsendid) {
        prevbutton.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                changePage(currentPage);
            }
        });

        nextbutton.addEventListener('click', function() {
            if (currentPage < theTotalPages) {
                currentPage++;
                changePage(currentPage);
            }
        });

        cpInput.value = currentPage;
        cpInput.addEventListener('change', function() {
            let inputPage = parseInt(this.value, 10);
            if (inputPage >= 1 && inputPage <= theTotalPages) {
                currentPage = inputPage; // 更新currentPage
                changePage(inputPage);
            } else {
                alert("请输入有效的页码");
                this.value = currentPage; // Reset to the current valid page number
            }
        });
        eleTotal.innerHTML = theTotalPages;

        function changePage(page) {
            cpInput.value = page;
            document.body.scrollIntoView(true)
            funcsendid(Page.getTenItemsAsString(theids, page), outputEle);
        }

        changePage(currentPage);
    };

    //用ids获取articles，载入到element中
    Page.getarticles = function(idsString, element) {
        if (typeof idsString !== "string") {
            idsString = Page.getTenItemsAsString(idsString);
        }
        ajax({
            url: '/php/savefile/workshop.php',
            method: "POST",
            send: `ids=${idsString}`,
            success: function(rjson) {
                let r = JSON.parse(rjson);
                if (r.success !== "true") {
                    alert(r.notice);
                    return;
                }
                element.innerHTML = "";
                for (let id of idsString.split(",")) {
                    for (let i of r.articles) {
                        if (i.id != id) {
                            continue;
                        }
                        element.innerHTML += Page.resulthtml(i);
                    }
                }
            }
        });
    };

    Page.resulthtml = function(i, isblank = false) {
        let target = isblank ? "_blank" : "_self";
        return `
            <a href="../workshop/?id=${i.id}" target="${target}">
            <div class="${Page.data.resultblock ? Page.data.resultblock : "contentBlock"}">
              <img src="${i.cover}" alt="预览图片" class="previewimg">
            <i class="fa-solid fa-wrench" style="float:left"></i><h3 class="article_title">
              ${i.title}
              </h3>
              <p>${i.content}</p>
            </div></a>
        `;
    };

    //articles的简单载入
    Page.aptEasy1 = function(pe, theids) {
        Page.addPageTurning(1, pe.querySelector(".prevPage"), pe.querySelector(".nextPage"), pe.querySelector("input.currentPage"), pe.querySelector("span.totalPages"), Page.getPage(theids.length), theids, pe.querySelector(".output"), Page.getarticles);
    }
    return Page;
})();