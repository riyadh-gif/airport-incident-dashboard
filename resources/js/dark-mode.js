/**
 * Dark-mode controller.
 *
 * The initial theme is applied by a tiny inline script in the layout <head>
 * (see layouts/app + layouts/guest) so there is no flash of the wrong theme.
 * This module exposes a small API on window so the Alpine topbar toggle can
 * flip the theme and persist the choice, and notifies listeners so the
 * Chart.js doughnut can re-theme its text/legend on the fly.
 */

const STORAGE_KEY = 'theme';

function systemPrefersDark() {
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function currentlyDark() {
    return document.documentElement.classList.contains('dark');
}

function apply(isDark) {
    document.documentElement.classList.toggle('dark', isDark);
    try {
        localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
    } catch (e) {
        /* localStorage unavailable — ignore */
    }
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));
}

window.darkMode = {
    isDark: currentlyDark,
    systemPrefersDark,
    toggle() {
        apply(!currentlyDark());
    },
    set(isDark) {
        apply(isDark);
    },
};

// Follow the OS preference if the user has not made an explicit choice.
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        let stored = null;
        try {
            stored = localStorage.getItem(STORAGE_KEY);
        } catch (err) {
            stored = null;
        }
        if (stored !== 'dark' && stored !== 'light') {
            apply(e.matches);
        }
    });
}
