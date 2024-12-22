document.addEventListener('DOMContentLoaded', function() {
            let currentPage = getCurrentPageFromQueryString();
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('currentPageInput').value = currentPage;

            // 调用loadPageData加载当前页数据
            loadPageData(currentPage);

            // 更新翻页按钮的事件处理器
            document.querySelector('.prevPage').addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    changePage(currentPage);
                }
            });

            document.querySelector('.nextPage').addEventListener('click', function() {
                if (currentPage < totalPages) {
                    currentPage++;
                    changePage(currentPage);
                }
            });

            document.getElementById('currentPageInput').addEventListener('change', function() {
                let inputPage = parseInt(this.value, 10);
                if (inputPage >= 1 && inputPage <= totalPages) {
                    currentPage = inputPage; // 更新currentPage
                    changePage(inputPage);
                } else {
                    alert("请输入有效的页码");
                    this.value = currentPage; // Reset to the current valid page number
                }
            });
        });

        function getCurrentPageFromQueryString() {
            const urlParams = new URLSearchParams(window.location.search);
            return parseInt(urlParams.get('page') || '1', 10);
        }

        function changePage(page) {
            // Update the current URL with the new page number and reload
            window.location.href = `${window.location.pathname}?page=${page}`;
        }