/**
 * Block editor sidebar panel: points editors of the About and Contact pages to the
 * Theme Settings tab that holds the rest of the page's text.
 * Langgam Fikir WordPress Theme
 */

(function(wp, data) {
    'use strict';

    if (!data || !wp.plugins || !wp.element) return;

    var el = wp.element.createElement;
    var Panel = (wp.editor && wp.editor.PluginDocumentSettingPanel) ||
        (wp.editPost && wp.editPost.PluginDocumentSettingPanel);

    if (!Panel) return;

    wp.plugins.registerPlugin('langgam-fikir-page-content', {
        render: function() {
            return el(
                Panel,
                { name: 'langgam-fikir-page-content', title: data.title, className: 'langgam-fikir-page-content', initialOpen: true },
                el('p', null, data.message),
                el('a', { className: 'components-button is-primary', href: data.url }, data.button)
            );
        }
    });
})(window.wp, window.langgamFikirEditor);
