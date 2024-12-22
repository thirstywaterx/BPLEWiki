if(window.localStorage.getItem('dark-mode') == "enabled"){
let body = document.querySelector('body')
body.style.backgroundColor = "#1c1c1c"
}

document.querySelector('body').insertAdjacentHTML(
            "beforeend",
            `
    <span id="dark-mode-status" style="display: none;"></span>
    <div id="dark-mode-toggle" class="toggle-container" style="display: none !important;"></div>
    `
        );

        function rgbToHsl(r, g, b) {
            r /= 255, g /= 255, b /= 255;
            let max = Math.max(r, g, b),
                min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0; // achromatic
            } else {
                let d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r:
                        h = (g - b) / d + (g < b ? 6 : 0);
                        break;
                    case g:
                        h = (b - r) / d + 2;
                        break;
                    case b:
                        h = (r - g) / d + 4;
                        break;
                }
                h /= 6;
            }

            return [h * 360, s * 100, l * 100];
        }

        function hslToRgb(h, s, l) {
            h /= 360, s /= 100, l /= 100;
            let r, g, b;

            if (s === 0) {
                r = g = b = l; // achromatic
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                }

                let q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                let p = 2 * l - q;
                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }

            return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
        }

        function adjustColorForDarkMode(bgColor) {
            const rgb = bgColor.match(/\d+/g).map(Number);
            const [h, s, l] = rgbToHsl(rgb[0], rgb[1], rgb[2]);

            if (s >= 0 && s <= 70 && l >= 70 && l <= 100) {
                let l2;

                if (l >= 90) {
                    l2 = l - 80;
                } else if (l >= 80) {
                    l2 = l - 60;
                } else {
                    l2 = l - 50;
                }

                if (l2 < 10) l2 = 10;
                return `hsl(${h}, ${s}%, ${l2}%)`;
            }

            if (h >= 180 && h <= 240) {
                let adjustedL = Math.max(l * 0.8, 20);
                return `hsl(${h}, ${s}%, ${adjustedL}%)`;
            }

            if (h >= 0 && h <= 60 && s > 70) {
                let adjustedL = Math.max(l * 0.6, 30);
                let adjustedS = Math.min(s * 1.2, 100);
                const [r, g, b] = hslToRgb(h, adjustedS, adjustedL);
                return `rgb(${r}, ${g}, ${b})`;
            }

            let adjustedL = Math.max(l * 0.5, 15);
            let adjustedS = Math.min(s * 1.1, 100);
            const [r, g, b] = hslToRgb(h, adjustedS, adjustedL);
            return `rgb(${r}, ${g}, ${b})`;
        }

        function createCSSClass(className, styles) {
            let styleElement = document.getElementById('dynamic-styles');
            if (!styleElement) {
                styleElement = document.createElement('style');
                styleElement.id = 'dynamic-styles';
                document.head.appendChild(styleElement);
            }
            styleElement.appendChild(document.createTextNode(`.${className} { ${styles} }`));
            return className;
        }

        function applyDarkModeToElement(element, isDarkMode) {
            const originalBgColor = element.dataset.originalBgColor || window.getComputedStyle(element).backgroundColor;

            if (isDarkMode) {
                if (!element.dataset.originalBgColor) {
                    element.dataset.originalBgColor = originalBgColor;
                }
                const darkModeColor = adjustColorForDarkMode(originalBgColor);
                const darkModeClass = createCSSClass(`dark-mode-bg-${Math.random().toString(36).substr(2, 9)}`, `background-color: ${darkModeColor}`);
                element.classList.add(darkModeClass);
                element.dataset.darkModeClass = darkModeClass;
            } else {
                if (element.dataset.darkModeClass) {
                    element.classList.remove(element.dataset.darkModeClass);
                    delete element.dataset.darkModeClass;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const toggleContainer = document.getElementById('dark-mode-toggle');
            const darkModeStatus = document.getElementById('dark-mode-status');
            const bodyInChange = Array.from(document.querySelectorAll('*')).filter(element => {
                if (element.tagName.toLowerCase() === 'body' || element.tagName.toLowerCase() === 'html') {
                    return false;
                }

                const bgColor = window.getComputedStyle(element).backgroundColor;
                const rgb = bgColor.match(/\d+/g);
                if (rgb) {
                    const [r, g, b] = rgb.map(Number);
                    return r > 240 && g > 240 && b > 240;
                }
                return false;
            });

            const toggleDarkMode = () => {
                const isDarkMode = document.body.classList.toggle('dark-mode');
                toggleContainer.classList.toggle('dark-mode');
                if (isDarkMode === false) {
                    document.querySelector(`.toggle-button`).onlick = change(`light`)
                } else {
                    document.querySelector(`.toggle-button`).onlick = change(`dark`)
                }
                bodyInChange.forEach(element => {
                    applyDarkModeToElement(element, isDarkMode);
                });

                localStorage.setItem('dark-mode', isDarkMode ? 'enabled' : 'disabled');
            };

            const currentMode = localStorage.getItem('dark-mode');
            const isDarkMode = currentMode === 'enabled';
            if (isDarkMode) {
                document.body.classList.add('dark-mode');
                toggleContainer.classList.add('dark-mode');
                bodyInChange.forEach(element => {
                    applyDarkModeToElement(element, isDarkMode);
                });
            }

            if (isDarkMode == false) {
                change(`light`)
            }

            toggleContainer.addEventListener('click', toggleDarkMode);
        });