/**
 * Parallax for elements marked with data-parallax="speed".
 * Langgam Fikir WordPress Theme
 *
 * speed 0.2 means the element drifts at 20% of the scroll speed relative to its section
 * (positive = lags behind, negative = floats ahead). Only on-screen elements are updated,
 * work is batched in requestAnimationFrame, and nothing runs with reduced motion.
 * The body class lf-parallax-desktop limits the effect to wide screens.
 */

(function() {
    'use strict';

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
    var desktopOnly = window.matchMedia && window.matchMedia('(min-width: 1024px)');
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-parallax]'));

    if (!items.length || !('IntersectionObserver' in window)) return;
    if (reduce && reduce.matches) return;

    var onlyDesktop = document.body.classList.contains('lf-parallax-desktop');
    var roots = new Map(); // section element -> its parallax children
    var visible = new Set(); // sections currently near the viewport
    var ticking = false;

    function enabled() {
        return !(onlyDesktop && desktopOnly && !desktopOnly.matches);
    }

    function update() {
        ticking = false;
        var on = enabled();
        var mid = window.innerHeight / 2;

        visible.forEach(function(root) {
            var rect = root.getBoundingClientRect();
            var center = rect.top + rect.height / 2 - mid;

            roots.get(root).forEach(function(el) {
                if (!on) {
                    el.style.removeProperty('--lf-py');
                    return;
                }
                var speed = parseFloat(el.getAttribute('data-parallax')) || 0;
                el.style.setProperty('--lf-py', (center * -speed).toFixed(1) + 'px');
            });
        });
    }

    function request() {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(update);
        }
    }

    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                visible.add(entry.target);
            } else {
                visible.delete(entry.target);
            }
        });
        request();
    }, { rootMargin: '20% 0px' });

    items.forEach(function(el) {
        var root = el.closest('[data-parallax-root]') || el.parentElement;
        if (!roots.has(root)) {
            roots.set(root, []);
            observer.observe(root);
        }
        roots.get(root).push(el);
    });

    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request);
    request();
})();
