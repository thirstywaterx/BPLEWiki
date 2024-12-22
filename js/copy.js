const copyBtn = document.getElementById("copy");
    const codeEle = document.querySelector(".content");

    copyBtn.addEventListener("click", () => {
        navigator.clipboard.writeText(codeEle.innerText).then(() => alert("复制成功"));
    });