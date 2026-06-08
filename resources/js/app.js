import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Apply dark class before Alpine starts to prevent flash of wrong theme
(function () {
    const stored = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (stored === 'true' || (stored === null && prefersDark)) {
        document.documentElement.classList.add('dark');
    }
})();

Alpine.start();
