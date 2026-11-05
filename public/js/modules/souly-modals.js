/**
 * Souly Modals & Scroll-Lock Engine
 * Battery-safe and prevents touch-screen background scrolling jumps
 */
export const SoulyModals = {
    lockScroll() {
        document.body.style.overflow = 'hidden';
        document.body.style.touchAction = 'none';
    },

    unlockScroll() {
        document.body.style.overflow = '';
        document.body.style.touchAction = '';
    },

    open(modalId) {
        const el = document.getElementById(modalId);
        if (!el) return;
        this.lockScroll();
        el.classList.remove('hidden');
        el.setAttribute('aria-hidden', 'false');
    },

    close(modalId) {
        const el = document.getElementById(modalId);
        if (!el) return;
        this.unlockScroll();
        el.classList.add('hidden');
        el.setAttribute('aria-hidden', 'true');
    }
};
