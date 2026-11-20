/**
 * Citation Analytics admin page (Appearance > Citation Analytics)
 * Langgam Fikir WordPress Theme
 *
 * Sizes the style bars from their data-pct value and asks before deleting every count.
 */
(function () {
    'use strict';

    document.querySelectorAll('.lf-cite-admin__bar > span[data-pct]').forEach(function (bar) {
        var pct = Math.max(0, Math.min(100, parseInt(bar.getAttribute('data-pct'), 10) || 0));
        bar.style.width = pct + '%';
    });

    document.querySelectorAll('form.lf-cite-admin__purge').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm') || '')) {
                event.preventDefault();
            }
        });
    });
})();
