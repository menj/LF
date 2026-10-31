/**
 * WebMCP tool registration for Langgam Fikir Theme
 *
 * Registers two read-only catalogue tools with document.modelContext and
 * makes the contact form's spam honeypot agent-aware. Feature-detected:
 * does nothing in browsers without WebMCP. No dependencies.
 *
 * Config arrives via wp_localize_script as window.langgamFikirWebMCP
 * (see inc/webmcp.php).
 */

(function() {
    'use strict';

    var config = window.langgamFikirWebMCP || null;

    /**
     * Wrap a result in the MCP content shape agents expect.
     */
    function textResult(summary, data) {
        var text = summary;
        if (data !== undefined) {
            text += '\n\n' + JSON.stringify(data, null, 2);
        }
        return { content: [ { type: 'text', text: text } ] };
    }

    function fetchJson(url, signal) {
        return fetch(url, { credentials: 'omit', signal: signal }).then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        });
    }

    function registerCatalogueTools() {
        if (!config || !config.restUrl) return;

        var site = config.siteName || 'this publisher';

        document.modelContext.registerTool({
            name: 'search-publications',
            title: 'Search publications',
            description: 'Search the ' + site + ' book catalogue. Returns matching titles with author, year, lifecycle status, price when orderable, categories, and a link to each book page. Use get-publication for full details on one book.',
            inputSchema: {
                type: 'object',
                properties: {
                    query: {
                        type: 'string',
                        description: 'Free-text search across titles and descriptions. Optional.'
                    },
                    status: {
                        type: 'string',
                        description: 'Filter by lifecycle status.',
                        enum: config.statuses || []
                    },
                    category: {
                        type: 'string',
                        description: 'Filter by category slug (e.g. teologi, sejarah).'
                    },
                    limit: {
                        type: 'integer',
                        description: 'Maximum results, 1-20. Default 10.',
                        minimum: 1,
                        maximum: 20
                    }
                }
            },
            annotations: { readOnlyHint: true, untrustedContentHint: false },
            execute: function(input, options) {
                var params = new URLSearchParams();
                input = input || {};
                if (input.query) params.set('search', input.query);
                if (input.status) params.set('status', input.status);
                if (input.category) params.set('category', input.category);
                if (input.limit) params.set('per_page', String(input.limit));

                var url = config.restUrl + (params.toString() ? '?' + params.toString() : '');
                var signal = options && options.signal;

                return fetchJson(url, signal).then(function(data) {
                    var summary = data.count + ' of ' + data.total + ' publication(s)';
                    if (input.query) summary += ' matching "' + input.query + '"';
                    summary += '. Browse the full catalogue at ' + (data.archive || config.archive) + '.';
                    return textResult(summary, data.items);
                }).catch(function(error) {
                    return textResult('The catalogue could not be searched right now (' + error.message + ').');
                });
            }
        });

        document.modelContext.registerTool({
            name: 'get-publication',
            title: 'Get publication details',
            description: 'Full details for one ' + site + ' book by its slug (the last part of its URL), including description, credits, ISBN, status, and price when orderable.',
            inputSchema: {
                type: 'object',
                properties: {
                    slug: {
                        type: 'string',
                        description: 'Book slug, e.g. "polis-raja-di-malaysia". Slugs appear in search-publications results.'
                    }
                },
                required: [ 'slug' ]
            },
            annotations: { readOnlyHint: true, untrustedContentHint: false },
            execute: function(input, options) {
                var slug = String((input && input.slug) || '').toLowerCase().replace(/[^a-z0-9-]/g, '');
                if (!slug) {
                    return Promise.resolve(textResult('A slug is required.'));
                }
                var signal = options && options.signal;
                return fetchJson(config.restUrl + '/' + slug, signal).then(function(book) {
                    return textResult(book.title + ' — ' + (book.status ? book.status.label : 'no status') + '. Page: ' + book.url, book);
                }).catch(function(error) {
                    return textResult('No publication found for "' + slug + '" (' + error.message + ').');
                });
            }
        });
    }

    /**
     * The contact form carries declarative tool attributes. Its honeypot is a
     * named text field, so an agent filling every field would trip the spam
     * check. Clear it on agent-invoked submissions; humans never see it.
     */
    function makeContactFormAgentAware() {
        var form = document.querySelector('form.contact-form[toolname]');
        if (!form) return;

        form.addEventListener('submit', function(event) {
            if (event.agentInvoked) {
                var honeypot = form.querySelector('input[name="website_url"]');
                if (honeypot) honeypot.value = '';
            }
        });
    }

    function init() {
        if (!('modelContext' in document) || !document.modelContext) return;

        try {
            registerCatalogueTools();
        } catch (error) {
            // Registration is optional; never let it disturb the page.
        }

        makeContactFormAgentAware();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
