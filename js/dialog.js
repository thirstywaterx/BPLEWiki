document.addEventListener('DOMContentLoaded', function() {
    var modal = document.querySelectorAll(".modal");
    var editButton = document.querySelectorAll('.edit-button');
    var closeButton = document.querySelectorAll(".close-button");
    //console.log(modal)
    console.log(editButton)
    // 当点击编辑按钮时显示弹窗
    for (let i = 0; i < modal.length; i++) {
        editButton[i].onclick = function() {
            modal[i].style.display = "block";
        }

        // 当点击关闭按钮时隐藏弹窗
        closeButton[i].onclick = function() {
            modal[i].style.display = "none";
        }
    }

});