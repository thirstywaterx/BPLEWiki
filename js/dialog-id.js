document.addEventListener('DOMContentLoaded', function() {
    var editButtons = document.querySelectorAll("[class^='open-'], [class*=' open-']");
    var closeButtons = document.querySelectorAll(".close-button");

    editButtons.forEach(button => {
        // 使用split和find来提取以'open-'开头的类名
        let className = button.className.split(' ').find(cls => cls.startsWith('open-'));
        let modalId = className.replace('open-', 'dialog-');
        let modal = document.getElementById(modalId);

        button.onclick = function() {
            if (modal) {
                modal.style.display = "block";
            }
        };
    });

    closeButtons.forEach(button => {
        button.onclick = function() {
            var modal = button.closest(".modal");
            if (modal) {
                modal.style.display = "none";
            }
        };
    });
});