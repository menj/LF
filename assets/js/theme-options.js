/**
 * Theme Options — Tab Navigation
 * Langgam Fikir
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var tabs      = document.querySelectorAll('.lf-tab');
        var panels    = document.querySelectorAll('.lf-panel');
        var hiddenTab = document.getElementById('lf-active-tab');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = this.getAttribute('data-tab');

                tabs.forEach(function (t) {
                    t.classList.remove('lf-tab--active');
                    t.setAttribute('aria-selected', 'false');
                });

                panels.forEach(function (p) {
                    p.classList.remove('lf-panel--active');
                });

                this.classList.add('lf-tab--active');
                this.setAttribute('aria-selected', 'true');

                var panel = document.querySelector('[data-panel="' + target + '"]');
                if (panel) {
                    panel.classList.add('lf-panel--active');
                }

                if (hiddenTab) {
                    hiddenTab.value = target;
                }
            });
        });

        // Palette swatches: each chip carries its colour in data-color (no inline styles in the markup).
        document.querySelectorAll('.lf-scheme-swatch i[data-color]').forEach(function (chip) {
            chip.style.backgroundColor = chip.getAttribute('data-color');
        });

        // Colour fields: the native picker and the hex box stay in sync; an empty box means "use the palette".
        document.querySelectorAll('.lf-color').forEach(function (wrap) {
            var pick  = wrap.querySelector('.lf-color-pick');
            var text  = wrap.querySelector('.lf-color-text');
            var clear = wrap.querySelector('.lf-color-clear');
            if (!pick || !text) return;

            pick.addEventListener('input', function () { text.value = pick.value.toUpperCase(); });
            text.addEventListener('input', function () {
                if (/^#[0-9A-Fa-f]{6}$/.test(text.value)) pick.value = text.value;
            });
            if (clear) clear.addEventListener('click', function () { text.value = ''; });
        });

        // Live preview for legal entity fields
        var nameField = document.getElementById('lf_legal_name');
        var regField  = document.getElementById('lf_reg_number');
        var preview   = document.getElementById('lf-legal-preview');

        if (nameField && regField && preview) {
            function updatePreview() {
                var name = nameField.value || 'Langgam Fikir Enterprise';
                var reg  = regField.value;

                if (reg) {
                    preview.textContent = name + ' is a registered enterprise in Malaysia (Reg. No.: ' + reg + ').';
                } else {
                    preview.textContent = name + ' is a registered enterprise in Malaysia.';
                }
            }

            nameField.addEventListener('input', updatePreview);
            regField.addEventListener('input', updatePreview);
        }

        // Hero background image uploader
        var heroBgBtn    = document.getElementById('lf-hero-bg-btn');
        var heroBgRemove = document.getElementById('lf-hero-bg-remove');
        var heroBgInput  = document.getElementById('lf_hero_bg');
        var heroBgPreview = document.getElementById('lf-hero-bg-preview');

        if (heroBgBtn && typeof wp !== 'undefined' && wp.media) {
            heroBgBtn.addEventListener('click', function (e) {
                e.preventDefault();

                var frame = wp.media({
                    title: 'Select Hero Background Image',
                    button: { text: 'Use this image' },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    heroBgInput.value = attachment.id;

                    var imgUrl = attachment.sizes && attachment.sizes.medium
                        ? attachment.sizes.medium.url
                        : attachment.url;

                    heroBgPreview.querySelector('img').src = imgUrl;
                    heroBgPreview.style.display = 'block';
                    heroBgRemove.style.display = 'inline-block';
                });

                frame.open();
            });

            if (heroBgRemove) {
                heroBgRemove.addEventListener('click', function (e) {
                    e.preventDefault();
                    heroBgInput.value = '';
                    heroBgPreview.style.display = 'none';
                    heroBgRemove.style.display = 'none';
                });
            }
        }
    });
})();
