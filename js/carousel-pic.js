        let currentIndex = 0;
        let interval = 3000; // 定时切换的间隔时间
        let autoSlideTimer; // 用于存放定时器
        let touchStartX = 0; // 触摸开始的X坐标
        let touchEndX = 0; // 触摸结束的X坐标
        let translateX = 0; // 当前滑动的距离

        const slides = document.querySelector('.slides');
        const dots = document.querySelectorAll('.dot');
        const slideWidth = slides.clientWidth; // 获取轮播图的宽度

        function resetTimer() {
            clearInterval(autoSlideTimer); // 清除当前的定时器
            autoSlideTimer = setInterval(nextSlide, interval); // 重新设置定时器
        }

        function updateSlidePosition() {
            slides.style.transition = 'none'; // 取消动画效果，使得滑动更流畅
            slides.style.transform = `translateX(${translateX - currentIndex * slideWidth}px)`;
        }

        function finalizeSlidePosition() {
            slides.style.transition = 'transform 0.5s ease'; // 恢复动画效果
            if (Math.abs(touchStartX - touchEndX) > slideWidth / 24) { // 判断滑动距离是否超过轮播图宽度的八分之一
                if (touchStartX > touchEndX) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            } else {
                showSlides(currentIndex); // 滑动距离小，则返回原位置
            }
        }

        function showSlides(n) {
            if (n >= slides.children.length) currentIndex = 0;
            if (n < 0) currentIndex = slides.children.length - 1;
            slides.style.transform = `translateX(-${currentIndex * 100}%)`;
            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentIndex].classList.add('active');
            resetTimer(); // 每次手动切换后重置定时器
        }

        function currentSlide(n) {
            currentIndex = n - 1;
            showSlides(currentIndex);
        }

        function nextSlide() {
            currentIndex++;
            showSlides(currentIndex);
        }

        function prevSlide() {
            currentIndex--;
            showSlides(currentIndex);
        }

        function handleTouchStart(evt) {
            touchStartX = evt.touches[0].clientX;
            clearInterval(autoSlideTimer); // 在手动滑动时停止自动轮播
        }

        function handleTouchMove(evt) {
            touchEndX = evt.touches[0].clientX;
            translateX = touchEndX - touchStartX;
            updateSlidePosition();
        }

        function handleTouchEnd() {
            finalizeSlidePosition();
            resetTimer(); // 手动滑动结束后重新开始自动轮播
        }

        document.querySelector('.prev').addEventListener('click', prevSlide);
        document.querySelector('.next').addEventListener('click', nextSlide);

        slides.addEventListener('touchstart', handleTouchStart);
        slides.addEventListener('touchmove', handleTouchMove);
        slides.addEventListener('touchend', handleTouchEnd);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => currentSlide(index + 1));
        });

        resetTimer(); // 初始化定时器
        showSlides(currentIndex); // 初始化时设置第一个小圆点样式