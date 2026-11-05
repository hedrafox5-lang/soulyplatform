/**
 * Souly Core Micro-Controller
 * Lightweight & Battery-Safe for Mobile Devices
 */
document.addEventListener('DOMContentLoaded', () => {
    const isTouchDevice = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

    // Disable radial cursor tracking on touch screens to free RAM & CPU
    if (isTouchDevice) {
        const glowFollower = document.getElementById('cursor-glow');
        if (glowFollower) glowFollower.remove();
        document.body.classList.add('touch-optimized');
    }

    // Global Modal & Toast Bus
    window.SoulyUI = {
        toast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-6 left-1/2 -translate-x-1/2 px-4 py-2.5 rounded-2xl text-xs font-bold text-white z-50 transition-all duration-300 shadow-2xl ${
                type === 'error' ? 'bg-rose-600' : 'bg-cyan-600'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        },
        openWhatsAppModal(key, template, title) {
            window.dispatchEvent(new CustomEvent('open-whatsapp-gear', {
                detail: { key, template, title }
            }));
        }
    };
});
