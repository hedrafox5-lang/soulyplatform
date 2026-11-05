/**
 * Souly Modular Loader Engine
 * Lightweight, zero-blocking cinematic starter for mobile devices
 */
export const SoulyLoader = {
    init(duration = 2500) {
        const loader = document.getElementById('souly-loader');
        if (!loader) return;

        // Skip animation if visitor already saw it in current session
        if (sessionStorage.getItem('souly_loader_seen') === 'true') {
            loader.style.display = 'none';
            return;
        }

        const bar = document.getElementById('loader-progress-bar');
        const count = document.getElementById('loader-progress-counter');
        const start = performance.now();

        const update = (now) => {
            const elapsed = now - start;
            const progress = Math.min(Math.round((elapsed / duration) * 100), 100);

            if (bar) bar.style.width = `${progress}%`;
            if (count) count.textContent = `${progress}%`;

            if (progress < 100) {
                requestAnimationFrame(update);
            } else {
                sessionStorage.setItem('souly_loader_seen', 'true');
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 400);
            }
        };

        requestAnimationFrame(update);
    }
};
