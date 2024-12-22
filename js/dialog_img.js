function openImgDialog() {
            var modal = document.getElementById("imgModal");

            // 获取弹窗图片
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");

            // 获取所有图片，为它们添加点击事件
            var imgs = document.querySelectorAll('.dialog-img');
            imgs.forEach(img => {
                img.onclick = function() {
                    modal.style.display = "block";
                    modalImg.src = this.src;
                }
            });

            // 获取 <span> 元素，设置关闭按钮的功能
            var span = document.getElementsByClassName("close")[0];

            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        document.addEventListener('DOMContentLoaded', openImgDialog);