function newdropdown(elements) {
            for (let moreOptions of elements) {

                let dropdown = moreOptions.querySelector('.dropdown');
                moreOptions.addEventListener('click', function(event) {
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                    event.stopPropagation(); // 阻止事件冒泡到 window 或其他元素
                });
                moreOptions.querySelectorAll('.dropdown-item').forEach(function(item) {
                    item.addEventListener('click', function(event) {
                        // 调用具体功能，并关闭下拉菜单                    
                        dropdown.style.display = 'none'; // 关闭下拉菜单
                        event.stopPropagation(); // 阻止事件冒泡
                    });
                });

            }
        }
        newdropdown(document.querySelectorAll('.moreOptions'));

        // 关闭下拉框
        window.onclick = function(event) {
            if (!event.target.matches('.fa-ellipsis')) {
                var dropdowns = document.getElementsByClassName("dropdown");
                for (let openDropdown of dropdowns) {
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
            }
        };