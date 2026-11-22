/**
 * Google preferred sources button
 * Langgam Fikir WordPress Theme
 *
 * Every button is a link to Google's source preferences page, so it always works. When the
 * reader scrolls near a button, Google's publisher.js is loaded in manual mode; once it is
 * ready, a click runs Google's in-page "Add as a preferred source" flow instead of leaving the
 * page. In link-only mode (Theme Options) Google's script is never loaded.
 */
(function () {
    'use strict';

    var cfg = window.lfPreferredSource || {};
    var buttons = Array.prototype.slice.call(document.querySelectorAll('[data-lf-preferred-source]'));
    if (!buttons.length || cfg.load === 'link' || !cfg.src) {
        return;
    }

    var api = null;
    var requested = false;

    function ready(preferredSource) {
        try {
            preferredSource.init({ theme: 'light', lang: cfg.lang || 'en' });
            api = preferredSource;
            buttons.forEach(function (button) {
                button.classList.add('is-ready');
                button.removeAttribute('target');
            });
        } catch (e) {
            api = null; // Keep the plain link.
        }
    }

    // Google's callback queue: runs once publisher.js has loaded.
    (self.PREFERRED_SOURCE = self.PREFERRED_SOURCE || []).push(ready);

    function load() {
        if (requested) {
            return;
        }
        requested = true;
        var script = document.createElement('script');
        script.async = true;
        script.src = cfg.src;
        script.setAttribute('preferred-sources-control', 'manual');
        document.head.appendChild(script);
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            if (!api) {
                return; // Not ready (or blocked): follow the link to Google.
            }
            event.preventDefault();
            try {
                api.addPreferredSource();
            } catch (e) {
                window.open(button.href, '_blank', 'noopener');
            }
        });
        // A keyboard or pointer reaching the button early also starts the load.
        button.addEventListener('focus', load, { once: true });
        button.addEventListener('pointerenter', load, { once: true });
    });

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            if (entries.some(function (entry) { return entry.isIntersecting; })) {
                observer.disconnect();
                load();
            }
        }, { rootMargin: '600px 0px' });
        buttons.forEach(function (button) { observer.observe(button); });
    } else {
        window.addEventListener('load', function () { window.setTimeout(load, 2000); });
    }
})();
