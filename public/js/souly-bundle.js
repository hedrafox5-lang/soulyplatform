/**
 * Souly Master Bundle
 * Bundles lightweight micro-modules into a single ultra-fast entry point
 */
import { SoulyLoader } from './modules/souly-loader.js';
import { SoulyStorage } from './modules/souly-storage.js';
import { SoulyModals } from './modules/souly-modals.js';
import { SoulyCarousel } from './modules/souly-carousel.js';

window.Souly = {
    loader: SoulyLoader,
    storage: SoulyStorage,
    modals: SoulyModals,
    carousel: SoulyCarousel
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize loader with speed optimization
    SoulyLoader.init(1800);

    // 2. Initialize carousels if present
    SoulyCarousel.init('.souly-carousel-track');
});
