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
