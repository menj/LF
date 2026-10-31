/**
 * Publications showcase: the backdrop follows the active book.
 * Langgam Fikir WordPress Theme
 *
 * Hover or keyboard focus on a card makes it active; otherwise the showcase rotates slowly
 * through the books. Rotation pauses while the visitor interacts, stops when the section
 * is off screen, and never runs with reduced motion. Without JS the first book is active.
 */

(function() {
    'use strict';

    var INTERVAL = 4500;
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-showcase]').forEach(function(root) {
        var cards = Array.prototype.slice.call(root.querySelectorAll('.showcase-card'));
        var backdrops = Array.prototype.slice.call(root.querySelectorAll('.showcase__bg'));
        var current = 0;
        var timer = null;
        var paused = false;

        if (cards.length < 2) return;

        function setActive(index) {
            current = index;
            cards.forEach(function(card, i) {
                card.classList.toggle('is-active', i === index);
            });
            backdrops.forEach(function(bg) {
                bg.classList.toggle('is-active', parseInt(bg.getAttribute('data-index'), 10) === index);
            });
        }

        function stop() {
            window.clearInterval(timer);
            timer = null;
        }

        function start() {
            if (reduce || !root.hasAttribute('data-autoplay') || timer || paused) return;
            timer = window.setInterval(function() {
                setActive((current + 1) % cards.length);
            }, INTERVAL);
        }

        cards.forEach(function(card, i) {
            card.addEventListener('mouseenter', function() { setActive(i); });
            card.addEventListener('focusin', function() { setActive(i); });
        });

        root.addEventListener('mouseenter', function() { paused = true; stop(); });
        root.addEventListener('mouseleave', function() { paused = false; start(); });
        root.addEventListener('focusin', function() { paused = true; stop(); });
        root.addEventListener('focusout', function() { paused = false; start(); });

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function(entries) {
                entries[0].isIntersecting ? start() : stop();
            }, { threshold: 0.2 }).observe(root);
        } else {
            start();
        }
    });
})();
