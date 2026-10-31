/**
 * Showcase for Langgam Fikir Theme
 *
 * Pairs each book card with its backdrop layer via data attributes. The
 * highlighted card decides which cover the backdrop shows. A card is
 * highlighted on hover or keyboard focus, or by an optional slow rotation
 * (data-showcase-autoplay) that pauses while the visitor interacts, stops
 * when the section is off screen, and never runs with reduced motion.
 * Cover images load lazily when the section is within 600px of the
 * viewport, and the backdrop drifts slightly while scrolling (parallax).
 * No dependencies.
 *
 * Loaded on the front page only, when the showcase is enabled
 * (see inc/enqueue.php).
 */

(function() {
    'use strict';

    var INTERVAL = 4500;

    function initShowcase() {
        var section = document.querySelector('.lf-showcase');
        if (!section) return;

        var backgrounds = section.querySelectorAll('.lf-showcase-bg');
        var items = Array.prototype.slice.call(section.querySelectorAll('[data-showcase-target]'));
        var layer = section.querySelector('.lf-showcase-backgrounds');
        if (!backgrounds.length || !items.length) return;

        var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var current = 0;
        var timer = null;
        var paused = false;
        var onScreen = false;

        // Cover images are deferred until the section nears the viewport.
        function loadBackgrounds() {
            backgrounds.forEach(function(bg) {
                var src = bg.getAttribute('data-showcase-src');
                if (src) {
                    bg.style.backgroundImage = 'url("' + src + '")';
                    bg.removeAttribute('data-showcase-src');
                }
            });
        }

        function activate(id) {
            backgrounds.forEach(function(bg) {
                bg.classList.toggle('is-active', bg.getAttribute('data-showcase-bg') === id);
            });
            items.forEach(function(item, index) {
                var on = item.getAttribute('data-showcase-target') === id;
                item.classList.toggle('is-active', on);
                if (on) current = index;
            });
        }

        function idAt(index) {
            return items[index].getAttribute('data-showcase-target');
        }

        function stop() {
            window.clearInterval(timer);
            timer = null;
        }

        function start() {
            if (reduceMotion || timer || paused || !onScreen || !section.hasAttribute('data-showcase-autoplay')) return;
            timer = window.setInterval(function() {
                activate(idAt((current + 1) % items.length));
            }, INTERVAL);
        }

        // Start with the first book highlighted so the backdrop is never empty.
        activate(idAt(0));

        items.forEach(function(item) {
            var target = item.getAttribute('data-showcase-target');
            if (!target) return;

            item.addEventListener('pointerenter', function() { activate(target); });
            item.addEventListener('focusin', function() { activate(target); });
        });

        section.addEventListener('pointerenter', function() { paused = true; stop(); });
        section.addEventListener('pointerleave', function() { paused = false; start(); });
        section.addEventListener('focusin', function() { paused = true; stop(); });
        section.addEventListener('focusout', function(event) {
            if (!section.contains(event.relatedTarget)) {
                paused = false;
                start();
            }
        });

        // Parallax: drift the backdrop at a fraction of the scroll, inside its overhang.
        var ticking = false;
        function parallax() {
            ticking = false;
            if (reduceMotion || !layer) return;

            var rect = section.getBoundingClientRect();
            var center = rect.top + rect.height / 2 - window.innerHeight / 2;
            var overhang = rect.height * 0.1;
            var shift = Math.max(-overhang, Math.min(overhang, center * -0.12));
            layer.style.setProperty('--lf-showcase-shift', shift.toFixed(1) + 'px');
        }

        function requestParallax() {
            if (!ticking && onScreen) {
                ticking = true;
                window.requestAnimationFrame(parallax);
            }
        }

        if ('IntersectionObserver' in window) {
            var nearObserver = new IntersectionObserver(function(entries, obs) {
                if (entries.some(function(entry) { return entry.isIntersecting; })) {
                    loadBackgrounds();
                    obs.disconnect();
                }
            }, { rootMargin: '600px 0px' });
            nearObserver.observe(section);

            new IntersectionObserver(function(entries) {
                onScreen = entries[0].isIntersecting;
                if (onScreen) {
                    start();
                    requestParallax();
                } else {
                    stop();
                }
            }, { threshold: 0.2 }).observe(section);
        } else {
            loadBackgrounds();
            onScreen = true;
            start();
        }

        window.addEventListener('scroll', requestParallax, { passive: true });
        window.addEventListener('resize', requestParallax);
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShowcase);
    } else {
        initShowcase();
    }

})();
