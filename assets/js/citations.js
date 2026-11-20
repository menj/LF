/**
 * Citations
 * Langgam Fikir WordPress Theme
 *
 * Front end of the citation box (inc/citations.php): switch styles, copy, download
 * (BibTeX, RIS, EndNote, CSL-JSON, CFF, Dublin Core), collapse, and optional counting.
 * The citation itself is already rendered on the server; this only enhances it.
 */
(function () {
    'use strict';

    var config = window.lfCite || {};
    var templates = config.templates || { web: {}, book: {} };
    var i18n = config.i18n || {};

    /* ------------------------------------------------------------------ helpers */

    function esc(text) {
        return String(text == null ? '' : text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    /** Same token replacement as langgam_fikir_cite_render() in PHP. */
    function render(work, style) {
        var set = templates[work.kind] || templates.web || {};
        var template = set[style] || set.apa || '';
        var isMarkup = style === 'wikipedia';
        var tokens = {
            '{author}': esc(work.author),
            '{title}': esc(work.title),
            '{sitename}': esc(work.site),
            '{publisher}': esc(work.publisher),
            '{permalink}': isMarkup ? esc(work.url) : '<a href="' + esc(work.url) + '">' + esc(work.url) + '</a>',
            '{permalink_url}': esc(work.url),
            '{date}': esc(work.accessed),
            '{date_dmy}': esc(work.accessedDMY),
            '{publication_date}': esc(work.published),
            '{publication_date_dmy}': esc(work.publishedDMY),
            '{publication_year}': esc(work.year),
            '{language}': esc(work.language),
            '{isbn}': esc(work.isbn),
            '{edition}': esc(work.edition),
            '{ed}': work.edition ? ' (' + esc(work.edition) + ')' : '',
            '{isbn_iso}': work.isbn ? ' ISBN ' + esc(work.isbn) + '.' : ''
        };
        var out = template.replace(/\{[a-z_]+\}/g, function (token) {
            return Object.prototype.hasOwnProperty.call(tokens, token) ? tokens[token] : token;
        });
        if (isMarkup) {
            return out.replace(/ \|[a-z-]+=(?= \||\}\})/g, '');
        }
        return out.replace(/\s+\./g, '.').replace(/\s+,/g, ',').replace(/\s{2,}/g, ' ').replace(/\.\.+/g, '.').trim();
    }

    function announce(box, message) {
        var status = box.querySelector('.lf-cite__status');
        if (!status) return;
        status.textContent = '';
        window.setTimeout(function () { status.textContent = message; }, 30);
        window.clearTimeout(status._lfTimer);
        status._lfTimer = window.setTimeout(function () { status.textContent = ''; }, 4000);
    }

    function track(work, action, style) {
        if (!config.analytics || !config.ajaxUrl || !work.id) return;
        var body = new URLSearchParams();
        body.set('action', 'lf_cite_track');
        body.set('post_id', String(work.id));
        body.set('action_type', action);
        body.set('citation_style', style);
        if (navigator.sendBeacon) {
            navigator.sendBeacon(config.ajaxUrl, body);
        } else {
            window.fetch(config.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin', keepalive: true }).catch(function () {});
        }
    }

    function plainText(html) {
        var div = document.createElement('div');
        div.innerHTML = html;
        return (div.textContent || '').trim();
    }

    /* ------------------------------------------------------------------ downloads */

    function slug(text) {
        var s = String(text || 'citation').toLowerCase().normalize('NFKD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        return (s || 'citation').slice(0, 60);
    }

    function citeKey(work) {
        var first = (work.authors && work.authors[0]) || work.publisher || work.site || 'ref';
        var surname = String(first).trim().split(/\s+/).pop();
        var word = String(work.title || '').split(/\s+/)[0] || '';
        return (slug(surname) + (String(work.year).match(/\d{4}/) ? work.year : '') + slug(word)).replace(/-/g, '');
    }

    function bib(value) {
        return String(value == null ? '' : value).replace(/\\/g, '\\textbackslash{}').replace(/([{}&%$#_])/g, '\\$1');
    }

    function yaml(value) {
        return '"' + String(value == null ? '' : value).replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"';
    }

    function xml(value) {
        return String(value == null ? '' : value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function people(work) {
        return work.authors && work.authors.length ? work.authors : [work.author];
    }

    function dateParts(iso) {
        var m = String(iso || '').match(/^(\d{4})(?:-(\d{2})(?:-(\d{2}))?)?/);
        if (!m) return null;
        return [m[1], m[2], m[3]].filter(Boolean).map(function (n) { return parseInt(n, 10); });
    }

    var builders = {
        bibtex: function (w) {
            var book = w.kind === 'book';
            var f = [
                ['author', people(w).map(bib).join(' and ')],
                ['title', '{' + bib(w.title) + '}'],
                book ? ['publisher', bib(w.publisher)] : ['organization', bib(w.site)],
                ['year', /\d{4}/.test(w.year) ? w.year : ''],
                book ? ['edition', bib(w.edition)] : null,
                book ? ['isbn', bib(w.isbn)] : null,
                ['url', w.url],
                ['urldate', w.accessedISO],
                ['language', w.language]
            ].filter(function (p) { return p && p[1]; });
            return '@' + (book ? 'book' : 'online') + '{' + citeKey(w) + ',\n' +
                f.map(function (p) { return '  ' + p[0] + ' = {' + p[1] + '}'; }).join(',\n') + '\n}\n';
        },
        ris: function (w) {
            var book = w.kind === 'book';
            var lines = ['TY  - ' + (book ? 'BOOK' : 'BLOG')];
            people(w).forEach(function (a) { lines.push('AU  - ' + a); });
            lines.push('TI  - ' + w.title);
            if (book) {
                lines.push('PB  - ' + w.publisher);
                if (w.edition) lines.push('ET  - ' + w.edition);
                if (w.isbn) lines.push('SN  - ' + w.isbn);
            } else {
                lines.push('T2  - ' + w.site);
                if (w.publishedISO) lines.push('DA  - ' + w.publishedISO.replace(/-/g, '/'));
            }
            if (/\d{4}/.test(w.year)) lines.push('PY  - ' + w.year);
            lines.push('UR  - ' + w.url);
            lines.push('Y2  - ' + w.accessedISO.replace(/-/g, '/'));
            if (w.language) lines.push('LA  - ' + w.language);
            lines.push('ER  - ');
            return lines.join('\r\n') + '\r\n';
        },
        endnote: function (w) {
            var book = w.kind === 'book';
            var lines = ['%0 ' + (book ? 'Book' : 'Web Page')];
            people(w).forEach(function (a) { lines.push('%A ' + a); });
            lines.push('%T ' + w.title);
            lines.push('%I ' + (book ? w.publisher : w.site));
            if (/\d{4}/.test(w.year)) lines.push('%D ' + w.year);
            if (book && w.edition) lines.push('%7 ' + w.edition);
            if (book && w.isbn) lines.push('%@ ' + w.isbn);
            lines.push('%U ' + w.url);
            if (w.language) lines.push('%G ' + w.language);
            return lines.join('\n') + '\n';
        },
        'csl-json': function (w) {
            var book = w.kind === 'book';
            var item = {
                id: citeKey(w),
                type: book ? 'book' : 'post-weblog',
                title: w.title,
                author: people(w).map(function (a) { return { literal: a }; }),
                URL: w.url,
                language: w.language,
                accessed: { 'date-parts': [dateParts(w.accessedISO)] }
            };
            var issued = dateParts(w.publishedISO);
            if (issued) item.issued = { 'date-parts': [issued] };
            if (book) {
                item.publisher = w.publisher;
                if (w.edition) item.edition = w.edition;
                if (w.isbn) item.ISBN = w.isbn;
            } else {
                item['container-title'] = w.site;
            }
            return JSON.stringify([item], null, 2) + '\n';
        },
        cff: function (w) {
            var book = w.kind === 'book';
            var authors = people(w).map(function (a) { return '  - name: ' + yaml(a); }).join('\n');
            var out = 'cff-version: 1.2.0\n' +
                'message: ' + yaml(i18n.message || 'If you use this work, please cite it as below.') + '\n' +
                'title: ' + yaml(w.title) + '\n' +
                'authors:\n' + authors + '\n';
            if (w.publishedISO && /^\d{4}-\d{2}-\d{2}$/.test(w.publishedISO)) out += 'date-released: ' + w.publishedISO + '\n';
            out += 'url: ' + yaml(w.url) + '\n' +
                'preferred-citation:\n' +
                '  type: ' + (book ? 'book' : 'blog') + '\n' +
                '  title: ' + yaml(w.title) + '\n' +
                '  authors:\n' + people(w).map(function (a) { return '    - name: ' + yaml(a); }).join('\n') + '\n' +
                (/\d{4}/.test(w.year) ? '  year: ' + w.year + '\n' : '') +
                '  publisher:\n    name: ' + yaml(book ? w.publisher : w.site) + '\n' +
                (book && w.isbn ? '  isbn: ' + yaml(w.isbn) + '\n' : '') +
                (book && w.edition ? '  edition: ' + yaml(w.edition) + '\n' : '') +
                '  url: ' + yaml(w.url) + '\n' +
                (w.language ? '  languages:\n    - ' + w.language + '\n' : '');
            return out;
        },
        'dublin-core': function (w) {
            var book = w.kind === 'book';
            var out = '<?xml version="1.0" encoding="UTF-8"?>\n<metadata xmlns:dc="http://purl.org/dc/elements/1.1/">\n';
            out += '  <dc:title>' + xml(w.title) + '</dc:title>\n';
            people(w).forEach(function (a) { out += '  <dc:creator>' + xml(a) + '</dc:creator>\n'; });
            out += '  <dc:publisher>' + xml(book ? w.publisher : w.site) + '</dc:publisher>\n';
            if (w.publishedISO) out += '  <dc:date>' + xml(w.publishedISO) + '</dc:date>\n';
            out += '  <dc:type>Text</dc:type>\n';
            out += '  <dc:format>' + (book ? 'text' : 'text/html') + '</dc:format>\n';
            out += '  <dc:identifier>' + xml(w.url) + '</dc:identifier>\n';
            if (book && w.isbn) out += '  <dc:identifier>urn:isbn:' + xml(w.isbn) + '</dc:identifier>\n';
            if (w.language) out += '  <dc:language>' + xml(w.language) + '</dc:language>\n';
            return out + '</metadata>\n';
        }
    };

    var fileTypes = {
        bibtex: ['bib', 'application/x-bibtex'],
        ris: ['ris', 'application/x-research-info-systems'],
        endnote: ['enw', 'application/x-endnote-refer'],
        'csl-json': ['json', 'application/vnd.citationstyles.csl+json'],
        cff: ['cff', 'application/x-yaml'],
        'dublin-core': ['xml', 'application/xml']
    };

    function download(work, format) {
        if (!builders[format]) return;
        var type = fileTypes[format];
        var blob = new Blob([builders[format](work)], { type: type[1] + ';charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = slug(work.title) + '.' + type[0];
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    }

    /* ------------------------------------------------------------------ one box */

    function init(box) {
        var work;
        try {
            work = JSON.parse(box.getAttribute('data-cite') || '{}');
        } catch (e) {
            return;
        }
        var output = box.querySelector('.lf-cite__output');
        var select = box.querySelector('.lf-cite__select');
        var buttons = Array.prototype.slice.call(box.querySelectorAll('.lf-cite__style'));
        var isTabs = box.getAttribute('data-picker') === 'tabs';
        var current = output ? output.getAttribute('data-style') : 'apa';

        function show(style, fromUser) {
            current = style;
            output.setAttribute('data-style', style);
            output.classList.toggle('is-markup', style === 'wikipedia');
            if (style === 'wikipedia') {
                output.textContent = plainText(render(work, style));
            } else {
                output.innerHTML = render(work, style);
            }
            buttons.forEach(function (btn) {
                var on = btn.getAttribute('data-style') === style;
                btn.classList.toggle('is-active', on);
                btn.setAttribute(isTabs ? 'aria-selected' : 'aria-checked', on ? 'true' : 'false');
                if (isTabs) btn.setAttribute('tabindex', on ? '0' : '-1');
            });
            if (fromUser) track(work, 'view', style);
        }

        if (select) {
            select.addEventListener('change', function () { show(select.value, true); });
        }

        buttons.forEach(function (btn, index) {
            btn.addEventListener('click', function () { show(btn.getAttribute('data-style'), true); });
            btn.addEventListener('keydown', function (event) {
                var keys = { ArrowRight: 1, ArrowDown: 1, ArrowLeft: -1, ArrowUp: -1 };
                var next = -1;
                if (keys[event.key]) next = (index + keys[event.key] + buttons.length) % buttons.length;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = buttons.length - 1;
                if (next < 0) return;
                event.preventDefault();
                buttons[next].focus();
                buttons[next].click();
            });
        });

        // Copy
        var copy = box.querySelector('.lf-cite__copy');
        if (copy && (navigator.clipboard || document.queryCommandSupported)) {
            copy.hidden = false;
            copy.addEventListener('click', function () {
                var text = (output.textContent || '').trim();
                var done = function () { announce(box, i18n.copied || 'Copied.'); track(work, 'copy', current); };
                var fallback = function () {
                    var area = document.createElement('textarea');
                    area.value = text;
                    area.setAttribute('readonly', '');
                    area.style.position = 'fixed';
                    area.style.opacity = '0';
                    document.body.appendChild(area);
                    area.select();
                    var ok = false;
                    try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
                    area.remove();
                    if (ok) { done(); } else { announce(box, i18n.copyFailed || 'Copy failed.'); }
                };
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(done, fallback);
                } else {
                    fallback();
                }
            });
        }

        // Download menu
        var wrap = box.querySelector('.lf-cite__export');
        if (wrap && window.Blob && window.URL && URL.createObjectURL) {
            wrap.hidden = false;
            var trigger = wrap.querySelector('.lf-cite__export-btn');
            var menu = wrap.querySelector('.lf-cite__menu');
            var items = Array.prototype.slice.call(menu.querySelectorAll('.lf-cite__menu-item'));

            var open = function (focusFirst) {
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                if (focusFirst && items[0]) items[0].focus();
            };
            var close = function (focusTrigger) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                if (focusTrigger) trigger.focus();
            };

            trigger.addEventListener('click', function () {
                if (menu.hidden) { open(false); } else { close(false); }
            });
            trigger.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown') { event.preventDefault(); open(true); }
            });
            items.forEach(function (item, index) {
                item.addEventListener('click', function () {
                    var format = item.getAttribute('data-format');
                    download(work, format);
                    track(work, 'export', format);
                    announce(box, i18n.downloaded || 'Download started.');
                    close(true);
                });
                item.addEventListener('keydown', function (event) {
                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        items[(index + (event.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length].focus();
                    } else if (event.key === 'Escape') {
                        event.preventDefault();
                        close(true);
                    } else if (event.key === 'Tab') {
                        close(false);
                    }
                });
            });
            document.addEventListener('click', function (event) {
                if (!menu.hidden && !wrap.contains(event.target)) close(false);
            });
        }

        // Collapse
        var toggle = box.querySelector('.lf-cite__toggle');
        var body = box.querySelector('.lf-cite__body');
        if (toggle && body) {
            toggle.hidden = false;
            toggle.addEventListener('click', function () {
                var expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                body.hidden = expanded;
                box.classList.toggle('is-collapsed', expanded);
            });
        }

        // The first view of the box (once per page load; the server limits repeats).
        track(work, 'view', current);
    }

    function start() {
        Array.prototype.forEach.call(document.querySelectorAll('.lf-cite[data-cite]'), init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
