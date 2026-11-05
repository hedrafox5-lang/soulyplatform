/**
 * Souly Touch Carousel Engine
 * Native swipe gestures without layout shift or vertical scroll blocking
 */
export const SoulyCarousel = {
    init(trackSelector) {
        const track = document.querySelector(trackSelector);
        if (!track) return;

        let isDown = false;
        let startX, scrollLeft;

        track.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - track.offsetLeft;
            scrollLeft = track.scrollLeft;
        }, { passive: true });

        track.addEventListener('touchend', () => { isDown = false; }, { passive: true });

        track.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - track.offsetLeft;
            const walk = (x - startX) * 1.5;
            track.scrollLeft = scrollLeft - walk;
        }, { passive: true });
    }
};
