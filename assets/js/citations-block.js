/**
 * Citation block (langgam-fikir/citation)
 * Langgam Fikir WordPress Theme
 *
 * A citation box, in-text citation or reference list entry for this post, or for another
 * post or book (postId). The preview is the real server output (ServerSideRender), so it
 * always matches the front end.
 */
(function (wp) {
    'use strict';

    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var TextControl = wp.components.TextControl;
    var ServerSideRender = wp.serverSideRender;

    var data = window.lfCiteBlock || {};
    var formats = [{ value: '', label: __('First style in Theme Options', 'langgam-fikir') }].concat(data.formats || []);

    wp.blocks.registerBlockType('langgam-fikir/citation', {
        apiVersion: 2,
        title: __('Citation', 'langgam-fikir'),
        description: __('A "Cite this" box, an in-text citation or a reference list entry for this post, or for another post or book.', 'langgam-fikir'),
        icon: 'format-quote',
        category: 'text',
        keywords: [__('cite', 'langgam-fikir'), __('citation', 'langgam-fikir'), __('reference', 'langgam-fikir'), __('bibliography', 'langgam-fikir')],
        supports: { html: false },
        attributes: {
            citationStyle: { type: 'string', default: 'apa' },
            mode: { type: 'string', default: 'box' },
            showExport: { type: 'boolean', default: true },
            showToggle: { type: 'boolean', default: true },
            showCopy: { type: 'boolean', default: true },
            showLink: { type: 'boolean', default: true },
            customAuthor: { type: 'string', default: '' },
            formats: { type: 'string', default: '' },
            excludeFormats: { type: 'string', default: '' },
            page: { type: 'string', default: '' },
            postId: { type: 'number', default: 0 }
        },

        edit: function (props) {
            var a = props.attributes;
            var set = props.setAttributes;
            var blockProps = useBlockProps({ className: 'lf-cite-block-editor' });

            var controls = el(InspectorControls, null,
                el(PanelBody, { title: __('Citation', 'langgam-fikir'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Show as', 'langgam-fikir'),
                        value: a.mode,
                        options: [
                            { value: 'box', label: __('Citation box', 'langgam-fikir') },
                            { value: 'inline', label: __('In-text citation', 'langgam-fikir') },
                            { value: 'bibliography', label: __('Reference only', 'langgam-fikir') }
                        ],
                        onChange: function (v) { set({ mode: v }); }
                    }),
                    el(SelectControl, {
                        label: __('Style shown first', 'langgam-fikir'),
                        value: a.citationStyle,
                        options: formats,
                        onChange: function (v) { set({ citationStyle: v }); }
                    }),
                    el(TextControl, {
                        label: __('Cite another post or book (ID)', 'langgam-fikir'),
                        help: __('Leave empty to cite this post.', 'langgam-fikir'),
                        type: 'number',
                        min: 0,
                        value: a.postId ? String(a.postId) : '',
                        onChange: function (v) { set({ postId: parseInt(v, 10) || 0 }); }
                    }),
                    a.mode === 'inline' && el(TextControl, {
                        label: __('Page', 'langgam-fikir'),
                        value: a.page,
                        onChange: function (v) { set({ page: v }); }
                    })
                ),
                a.mode === 'box' && el(PanelBody, { title: __('Buttons', 'langgam-fikir'), initialOpen: false },
                    el(ToggleControl, { label: __('Copy', 'langgam-fikir'), checked: a.showCopy, onChange: function (v) { set({ showCopy: v }); } }),
                    el(ToggleControl, { label: __('Download', 'langgam-fikir'), checked: a.showExport, onChange: function (v) { set({ showExport: v }); } }),
                    el(ToggleControl, { label: __('Collapse', 'langgam-fikir'), checked: a.showToggle, onChange: function (v) { set({ showToggle: v }); } })
                ),
                a.mode === 'inline' && el(PanelBody, { title: __('Link', 'langgam-fikir'), initialOpen: false },
                    el(ToggleControl, { label: __('Link the citation', 'langgam-fikir'), checked: a.showLink, onChange: function (v) { set({ showLink: v }); } })
                ),
                el(PanelBody, { title: __('Advanced', 'langgam-fikir'), initialOpen: false },
                    el(TextControl, {
                        label: __('Only these styles', 'langgam-fikir'),
                        help: __('Comma-separated, for example apa,mla,chicago. Empty uses Theme Options.', 'langgam-fikir'),
                        value: a.formats,
                        onChange: function (v) { set({ formats: v }); }
                    }),
                    el(TextControl, {
                        label: __('Leave out these styles', 'langgam-fikir'),
                        value: a.excludeFormats,
                        onChange: function (v) { set({ excludeFormats: v }); }
                    }),
                    el(TextControl, {
                        label: __('Author shown instead', 'langgam-fikir'),
                        value: a.customAuthor,
                        onChange: function (v) { set({ customAuthor: v }); }
                    })
                )
            );

            return el('div', blockProps,
                controls,
                el(ServerSideRender, {
                    block: 'langgam-fikir/citation',
                    attributes: a,
                    urlQueryArgs: { post_id: wp.data.select('core/editor') ? wp.data.select('core/editor').getCurrentPostId() : 0 },
                    EmptyResponsePlaceholder: function () {
                        return el('p', { className: 'lf-cite-block-editor__empty' }, __('Save the post to preview its citation, or choose a post or book to cite.', 'langgam-fikir'));
                    }
                })
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
