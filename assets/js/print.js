/**
 * Print buttons
 * Langgam Fikir WordPress Theme
 *
 * The Print button opens the print dialog (where readers can also choose Save as PDF).
 * ?lf-print=1 opens it straight away: the Download PDF button lands there when the server
 * cannot make the file.
 */
(function () {
    'use strict';

    function print() {
        window.print();
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-lf-print]');
        if (button) {
            event.preventDefault();
            print();
        }
    });

    var params = new URLSearchParams(window.location.search);
    if (params.get('lf-print') === '1') {
        // Remove the marker so a reload or a shared link does not print again.
        params.delete('lf-print');
        var query = params.toString();
        window.history.replaceState(null, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
        window.addEventListener('load', function () {
            window.setTimeout(print, 300);
        });
    }
})();
