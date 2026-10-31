/**
 * Showcase for Langgam Fikir Theme
 *
 * Pairs each title link with its background layer via data attributes.
 * Hover or keyboard focus activates the matching layer; leaving the
 * section restores the default layer. Cover images load lazily when the
 * section is within 600px of the viewport. No dependencies.
 *
 * Loaded on the front page only, when the showcase is enabled
 * (see inc/enqueue.php).
 */

(function() {
    'use strict';

    function initShowcase() {
        var section = document.querySelector('.lf-showcase');
        if (!section) return;

        var backgrounds = section.querySelectorAll('.lf-showcase-bg');
        var items = section.querySelectorAll('.lf-showcase-item');
        if (!backgrounds.length || !items.length) return;

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

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries, obs) {
                var near = entries.some(function(entry) { return entry.isIntersecting; });
                if (near) {
                    loadBackgrounds();
                    obs.disconnect();
                }
            }, { rootMargin: '600px 0px' });
            observer.observe(section);
        } else {
            loadBackgrounds();
        }

        function activate(id) {
            backgrounds.forEach(function(bg) {
                bg.classList.toggle('is-active', bg.getAttribute('data-showcase-bg') === id);
            });
        }

        items.forEach(function(item) {
            var target = item.getAttribute('data-showcase-target');
            if (!target) return;

            item.addEventListener('pointerenter', function() {
                activate(target);
            });

            item.addEventListener('focus', function() {
                activate(target);
            });
        });

        section.addEventListener('pointerleave', function() {
            activate('default');
        });

        section.addEventListener('focusout', function(event) {
            if (!section.contains(event.relatedTarget)) {
                activate('default');
            }
        });
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShowcase);
    } else {
        initShowcase();
    }

})();
