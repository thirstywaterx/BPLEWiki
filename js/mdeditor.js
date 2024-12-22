var mdeditor={
    operationArea: undefined,
    textarea: undefined,
    data:[
        [
            "H1",
            "# 一级标题",
            `# `,
            "$front"
        ],
        [
            "H2",
            "## 二级标题",
            `## `,
            "$front"
        ],
        [
            "H3",
            "### 三级标题",
            `### `,
            "$front"
        ],
        [
            "H4",
            "#### 四级标题",
            `#### `,
            "$front"
        ],
        [
            "H5",
            "##### 五级标题",
            `##### `,
            "$front"
        ],
        [
            "H6",
            "###### 六级标题",
            `###### `,
            "$front"
        ],
        [
            "$br"
        ],
        [
            `<i class="fa-solid fa-bold"></i>`,
            "**加粗文本**",
            ["**","**"],
            "$inline"
        ],
        [
            `<i class="fa-solid fa-italic"></i>`,
            "*斜体文字*",
            ["*","*"],
            "$inline"
        ],
        [
            `<i class="fa-solid fa-minus"></i>`,
            "~~划线文字~~",
            ["~~","~~"],
            "$inline"
        ],
        [
            "$space"
        ],
        [
            `<i class="fa-solid fa-undo"></i>`,
            `undo`,
            `$control`
        ],
        [
            `<i class="fa-solid fa-redo"></i>`,
            `redo`,
            `$control`
        ],
        [
            "$br"
        ],
        [
            `<i class="fa-solid fa-link"></i>`,
            `[超链接显示名](超链接地址 "超链接标题")`,
            ["<",">"],
            "$inline"
        ],
        [
            `<i class="fa-regular fa-image"></i>`,
            `![图片显示名](图片链接 "图片标题")`
        ],
        [
            `<i class="fa-solid fa-code"></i>`,
            `\u0060\u0060\u0060 语言名称\n代码\n\u0060\u0060\u0060`
        ],
        [
            `<i class="fa-solid fa-quote-left"></i>`,
            `> 引用内容`,
            `> `,
            "$front"
        ],
        [
            `<i class="fa-solid fa-list-ul"></i>`,
            `* 行数1\n* 行数2`,
            `* `,
            "$front"
        ],
        [
            `<i class="fa-solid fa-grip-lines"></i>`,
            `---`
        ]
    ],
    undoStack:[],
    redoStack:[],
    create(oa,ta,defaultv,showlength=false){
        this.operationArea=oa;
        this.textarea=ta;
        this.textarea.value=defaultv;
        this.undoStack.push(this.textarea.value);
        this.textarea.addEventListener('input', () => {
          // 将新的文本内容添加到撤销堆栈中
          this.undoStack.push(this.textarea.value);
          // 清空重做堆栈
          this.redoStack.length = 0;
        });
        //显示字数
        if(showlength){
            this.eletextlength=this.textarea.parentNode.querySelector(".textarea-length");
            let eletextlength=this.eletextlength;
            this.eletextlength.innerHTML=this.textarea.value.length;
            this.textarea.addEventListener("input",function(){
                eletextlength.innerHTML=this.value.length;
                eletextlength.style.color=this.value.length<=eletextlength.dataset.max?"#fff":"red";
            },true);
        }
    },
    clearevent(ele){
        let f=(e)=>{
            e.preventDefault();
            return false;
        };
        ele.onmousedown=f;
        ele.ontouchdown=f;
    },
    loadbtn(){
        for(let i in this.data){
            let d=this.data[i];
            if(d.includes("$br")){
                this.operationArea.innerHTML += `<br/>`;
                continue;
            }
            if(d.includes("$space")){
                this.operationArea.innerHTML += ` | `;
                continue;
            }
            this.operationArea.innerHTML += `<span class="tool-font" data-sequence="${i}" onclick="mdeditor.addText(${i})">${d[0]}</span>`;
        }
    },
    map(f){
        for(let i of document.querySelectorAll("span.tool-font")){
            f(i);
        }
    },
    addText(sequence) {
        // 添加特定内容到textarea
        let textarea = this.textarea;
        let d=this.data[sequence];
        let setstr="";
        if(d.includes("$control")){
            // 操作
            let undoStack = this.undoStack;
            let redoStack = this.redoStack;
            //判断操作类型
            if(d[1]==="undo"){
              //撤销
              if (undoStack.length > 1) {
                let r=undoStack.pop()
                // 弹出最近的操作并将其添加到重做堆栈中
                redoStack.push(r);
                // 将Textarea的值设置为堆栈中的最后一个值
                textarea.value = undoStack[undoStack.length-1];
              }
            }else if(d[1]==="redo"){
              //重做
              if (redoStack.length > 0) {
                let r=redoStack.pop();
                // 将最近的操作从重做堆栈中弹出并将其添加到撤销堆栈中
                undoStack.push(r);
                // 将Textarea的值设置为堆栈中的最后一个值
                textarea.value = undoStack[undoStack.length-1];
                console.log(redoStack.length-1)
              }
            }
        }else if(document.activeElement===textarea){
            //已选定位置：在指定位置添加文本
            let start=textarea.selectionStart;
            let end=textarea.selectionEnd;
            let value=textarea.value
            function setselection(){
                textarea.setSelectionRange(start+setstr.length,start+setstr.length);
            }
            if(d.includes("$inline")){
                //行内则不自动换行
                if(start===end){
                    //光标型
                    setstr=d[1];
                    textarea.value=value.slice(0, start) + setstr + value.slice(start);
                    setselection();
                }else{
                    //已选中文本，在选中前后添加标签
                    let adds=d[2];
                    value=value.slice(0, start) + adds[0] + value.slice(start);
                    end+=adds[0].length;
                    textarea.value=value.slice(0, end) + adds[1] + value.slice(end);
                    textarea.setSelectionRange(start+adds[0].length, end);
                }
            }else{
                //块级
                if(d.includes("$front")&&start!==end){
                    //前加式
                    let p=value.lastIndexOf('\n', start)+1;
                    setstr=`${d[2]}`;
                    textarea.value=value.slice(0, p) + setstr + value.slice(p);
                }else{
                    setstr=`\n${d[1]}\n`;
                    textarea.value=value.slice(0, start) + setstr + value.slice(start);
                }
                setselection();
            }
            textarea.dispatchEvent(new Event("input"));
        }else{
            //未聚焦：在最后显示
            textarea.value += d.includes("$inline")?d[1]:`\n${d[1]}\n`;
            textarea.scrollTop = textarea.scrollHeight;
            textarea.dispatchEvent(new Event("input"));
        }
    }
};//0.0.3