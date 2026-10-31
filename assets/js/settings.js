/**
 * Theme Settings screen: accessible tabs, colour pickers and live preview.
 * Langgam Fikir WordPress Theme
 */

(function($) {
    'use strict';

    var STORAGE_KEY = 'langgamFikirSettingsTab';
    var root = document.querySelector('.lf-settings');

    if (!root) return;

    var tabs = Array.prototype.slice.call(root.querySelectorAll('.lf-tabs__tab'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('.lf-panel'));

    /*----------------------------------------------------------
     * Tabs
     *--------------------------------------------------------*/
    function remember(slug) {
        try { window.sessionStorage.setItem(STORAGE_KEY, slug); } catch (e) { /* storage unavailable */ }
    }

    function recall() {
        try { return window.sessionStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
    }

    function showTab(slug, focus) {
        tabs.forEach(function(tab) {
            var active = tab.getAttribute('data-tab') === slug;
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
            tab.tabIndex = active ? 0 : -1;
            if (active && focus) tab.focus();
        });

        panels.forEach(function(panel) {
            panel.hidden = panel.getAttribute('data-panel') !== slug;
        });

        remember(slug);

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '#' + slug);
        }
    }

    function initTabs() {
        if (!tabs.length) return;

        root.classList.add('lf-js');

        tabs.forEach(function(tab, index) {
            tab.addEventListener('click', function() {
                showTab(tab.getAttribute('data-tab'), false);
            });

            // Arrow keys move between tabs (WAI-ARIA tabs pattern)
            tab.addEventListener('keydown', function(event) {
                var next = null;
                if (event.key === 'ArrowRight') next = tabs[(index + 1) % tabs.length];
                if (event.key === 'ArrowLeft') next = tabs[(index - 1 + tabs.length) % tabs.length];
                if (event.key === 'Home') next = tabs[0];
                if (event.key === 'End') next = tabs[tabs.length - 1];
                if (next) {
                    event.preventDefault();
                    showTab(next.getAttribute('data-tab'), true);
                }
            });
        });

        var wanted = window.location.hash.replace('#', '') || recall();
        var valid = tabs.some(function(tab) { return tab.getAttribute('data-tab') === wanted; });
        showTab(valid ? wanted : tabs[0].getAttribute('data-tab'), false);
    }

    /*----------------------------------------------------------
     * Live preview
     *--------------------------------------------------------*/
    var preview = document.getElementById('lf-preview');
    var palettes = (window.langgamFikirSettings && window.langgamFikirSettings.palettes) || {};

    // Black or white text, whichever contrasts better (mirrors the PHP helper).
    function onColour(hex) {
        var h = hex.replace('#', '');
        if (h.length === 3) h = h.replace(/(.)/g, '$1$1');
        if (h.length !== 6) return '#FFFFFF';

        var lum = [0, 2, 4].reduce(function(sum, start, i) {
            var c = parseInt(h.substr(start, 2), 16) / 255;
            c = c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
            return sum + [0.2126, 0.7152, 0.0722][i] * c;
        }, 0);

        return 1.05 / (lum + 0.05) >= (lum + 0.05) / 0.059 ? '#FFFFFF' : '#1A1610';
    }

    function updatePreview() {
        if (!preview) return;

        var checked = root.querySelector('input[name$="[color_scheme]"]:checked');
        var palette = palettes[checked ? checked.value : 'earth'] || palettes.earth || {};
        var map = { primary: 'primary', accent: 'accent', bg: 'background', surface: 'surface', text: 'text' };
        var values = {};

        Object.keys(map).forEach(function(key) {
            var input = root.querySelector('[data-preview="color_' + map[key] + '"]');
            values[key] = (input && input.value) || palette[key];
        });

        preview.style.setProperty('--lf-p-primary', values.primary);
        preview.style.setProperty('--lf-p-accent', values.accent);
        preview.style.setProperty('--lf-p-bg', values.bg);
        preview.style.setProperty('--lf-p-surface', values.surface);
        preview.style.setProperty('--lf-p-text', values.text);
        preview.style.setProperty('--lf-p-on-accent', onColour(values.accent));
    }

    function initColourPickers() {
        $('.lf-color').wpColorPicker({
            change: function() {
                // The picker updates the input after this callback fires.
                window.setTimeout(updatePreview, 0);
            },
            clear: function() {
                window.setTimeout(updatePreview, 0);
            }
        });

        root.querySelectorAll('input[name$="[color_scheme]"]').forEach(function(radio) {
            radio.addEventListener('change', updatePreview);
        });

        updatePreview();
    }

    $(function() {
        initTabs();
        initColourPickers();
    });

})(jQuery);
