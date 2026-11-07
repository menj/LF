<?php
/**
 * Extra Theme Options: design, page text and labels
 *
 * One schema drives the extra Theme Options tabs and cards, their defaults and
 * their sanitising, so a field added here is editable, saved and readable with
 * langgam_fikir_get_option() / langgam_fikir_text() and nothing else.
 *
 * The original Homepage, Contact & Footer, Legal Entity and Social & SEO fields
 * stay hand-written in inc/admin/theme-options.php; this file adds to them.
 *
 * Storage is the existing single langgam_fikir_options array (see ssot.md 5.3).
 *
 * @package Langgam_Fikir
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Bundled hero photographs (assets/images/hero/{slug}-960.jpg and -1920.jpg).
 * Photos are from Pexels; credits are in assets/images/hero/README.txt and README.md.
 *
 * @return array<string, string> slug => label
 */
function langgam_fikir_hero_photos() {
    return array(
        'library-corridor' => __( 'Library corridor', 'langgam-fikir' ),
        'reading-room'     => __( 'Reading room', 'langgam-fikir' ),
        'open-book'        => __( 'Open book', 'langgam-fikir' ),
        'leather-books'    => __( 'Leather-bound books', 'langgam-fikir' ),
    );
}

/**
 * Light colour palettes. Dark mode is out of scope (ssot.md 9.2); every palette has a light page.
 * The values here drive the swatches and preview in Theme Options; the same palettes live in
 * assets/css/design.css as body.lf-scheme-* rules, so keep the two in sync.
 *
 * @return array<string, array{label:string, note:string, colors:array}>
 */
function langgam_fikir_color_schemes() {
    return array(
        'folio' => array(
            'label'  => __( 'Folio', 'langgam-fikir' ),
            'note'   => __( 'Cream, oxblood and sage with brass. Bookish and warm.', 'langgam-fikir' ),
            'colors' => array( '#5A1F2A', '#C79A3B', '#E8E6D8', '#F4EDE0' ),
        ),
        'earth' => array(
            'label'  => __( 'Earth', 'langgam-fikir' ),
            'note'   => __( 'Warm browns and gold. The original look.', 'langgam-fikir' ),
            'colors' => array( '#6B5B4A', '#C9A961', '#F5F5F0', '#FFFFFF' ),
        ),
        'slate' => array(
            'label'  => __( 'Slate', 'langgam-fikir' ),
            'note'   => __( 'Cool blue-grey, crisp and calm.', 'langgam-fikir' ),
            'colors' => array( '#2E3A46', '#4F7288', '#F3F5F7', '#FFFFFF' ),
        ),
        'sage'  => array(
            'label'  => __( 'Sage', 'langgam-fikir' ),
            'note'   => __( 'Muted green with soft brass.', 'langgam-fikir' ),
            'colors' => array( '#3F5247', '#8A9A5B', '#F2F4EE', '#FFFFFF' ),
        ),
        'clay'  => array(
            'label'  => __( 'Clay', 'langgam-fikir' ),
            'note'   => __( 'Terracotta and warm sand.', 'langgam-fikir' ),
            'colors' => array( '#6E3F33', '#C26B5A', '#F7F1EC', '#FFFFFF' ),
        ),
    );
}

/**
 * The extra Theme Options tabs and cards.
 *
 * Each tab: label, icon, optional 'existing' => true (adds cards to a hand-written tab),
 * and cards. Each card: title, desc, fields. Each field:
 *   type  text | textarea | email | url | number | select | checkbox | color | image | scheme
 *   label, desc, default, and where relevant: choices, min, max, rows, allow_empty.
 *
 * allow_empty fields may be saved blank on purpose (the element is then hidden);
 * for every other text field a blank value falls back to its default.
 *
 * @return array
 */
function langgam_fikir_extra_options_schema() {
    static $schema = null;
    if ( null !== $schema ) {
        return $schema;
    }

    $f = static function ( $type, $label, $default = '', $desc = '', $extra = array() ) {
        return array_merge( array( 'type' => $type, 'label' => $label, 'default' => $default, 'desc' => $desc ), $extra );
    };
    $para = __( 'Separate paragraphs with a blank line.', 'langgam-fikir' );

    $schema = array(

        /* ---------------------------------------------------------- Design */
        'design'   => array(
            'label' => __( 'Design', 'langgam-fikir' ),
            'icon'  => 'dashicons-art',
            'cards' => array(
                array(
                    'title'  => __( 'Colour Palette', 'langgam-fikir' ),
                    'desc'   => __( 'Pick a light palette, then optionally override individual colours. Dark mode is not offered.', 'langgam-fikir' ),
                    'fields' => array(
                        'color_scheme'     => $f( 'scheme', __( 'Palette', 'langgam-fikir' ), 'folio' ),
                        'color_primary'    => $f( 'color', __( 'Headings and hero', 'langgam-fikir' ), '', __( 'Optional. Empty uses the palette.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'color_accent'     => $f( 'color', __( 'Accent', 'langgam-fikir' ), '', __( 'Buttons, highlights and badges. Empty uses the palette.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'color_background' => $f( 'color', __( 'Page background', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'color_surface'    => $f( 'color', __( 'Alternate sections', 'langgam-fikir' ), '', __( 'Cards and tinted bands.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'color_text'       => $f( 'color', __( 'Body text', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Typography', 'langgam-fikir' ),
                    'desc'   => __( 'Five typefaces share the work: Kagnue for titles, Crimson Text for headings, Sabon Next LT for reading text, Inter for the interface and Ferments for the tagline. Change any role here.', 'langgam-fikir' ),
                    'fields' => array(
                        'font_titles'  => $f( 'select', __( 'Site title and page titles', 'langgam-fikir' ), 'kagnue', '', array( 'choices' => array(
                            'kagnue'  => __( 'Kagnue', 'langgam-fikir' ),
                            'heading' => __( 'Same as headings', 'langgam-fikir' ),
                        ) ) ),
                        'font_heading' => $f( 'select', __( 'Headings and book titles', 'langgam-fikir' ), 'crimson', '', array( 'choices' => array(
                            'crimson' => __( 'Crimson Text', 'langgam-fikir' ),
                            'sabon'   => __( 'Sabon Next LT', 'langgam-fikir' ),
                            'georgia' => __( 'Georgia (system)', 'langgam-fikir' ),
                        ) ) ),
                        'font_reading' => $f( 'select', __( 'Reading text', 'langgam-fikir' ), 'sabon', __( 'Articles, About text, synopses and lead paragraphs.', 'langgam-fikir' ), array( 'choices' => array(
                            'sabon'   => __( 'Sabon Next LT', 'langgam-fikir' ),
                            'crimson' => __( 'Crimson Text', 'langgam-fikir' ),
                            'inter'   => __( 'Inter', 'langgam-fikir' ),
                        ) ) ),
                        'font_body'    => $f( 'select', __( 'Interface and body', 'langgam-fikir' ), 'inter', __( 'Navigation, buttons, forms and small text.', 'langgam-fikir' ), array( 'choices' => array(
                            'inter'  => __( 'Inter', 'langgam-fikir' ),
                            'sabon'  => __( 'Sabon Next LT', 'langgam-fikir' ),
                            'system' => __( 'System sans-serif', 'langgam-fikir' ),
                        ) ) ),
                        'font_tagline' => $f( 'select', __( 'Tagline and accents', 'langgam-fikir' ), 'ferments', '', array( 'choices' => array(
                            'ferments' => __( 'Ferments (script)', 'langgam-fikir' ),
                            'heading'  => __( 'Same as headings (italic)', 'langgam-fikir' ),
                        ) ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Corners and Motion', 'langgam-fikir' ),
                    'desc'   => __( 'How rounded cards, images, buttons and fields are, and whether parts of the page drift while scrolling.', 'langgam-fikir' ),
                    'fields' => array(
                        'corner_style' => $f( 'select', __( 'Corners', 'langgam-fikir' ), 'soft', '', array( 'choices' => array(
                            'soft'  => __( 'Soft (default)', 'langgam-fikir' ),
                            'round' => __( 'Round (pill buttons, large radii)', 'langgam-fikir' ),
                            'sharp' => __( 'Sharp (the pre-2.11 look)', 'langgam-fikir' ),
                        ) ) ),
                        'parallax'     => $f( 'select', __( 'Parallax scrolling', 'langgam-fikir' ), 'desktop', __( 'Hero image, Showcase backdrop, the homepage About image, the About page logo and the book cover. Always off for visitors who prefer reduced motion.', 'langgam-fikir' ), array( 'choices' => array(
                            'desktop' => __( 'On for wide screens (default)', 'langgam-fikir' ),
                            'all'     => __( 'On for all screens', 'langgam-fikir' ),
                            'off'     => __( 'Off', 'langgam-fikir' ),
                        ) ) ),
                    ),
                ),
            ),
        ),

        /* ---------------------------------------------------- Homepage (adds) */
        'homepage' => array(
            'existing' => true,
            'cards'    => array(
                array(
                    'title'  => __( 'Hero Photo and Buttons', 'langgam-fikir' ),
                    'desc'   => __( 'A photograph behind the hero statement, tinted with your colours, with a parallax effect. A picture chosen under Hero Background Image above overrides it. Photos are from Pexels.', 'langgam-fikir' ),
                    'fields' => array(
                        'hero_layout'          => $f( 'select', __( 'Hero layout', 'langgam-fikir' ), 'split', __( 'Split puts the heading and text beside the photo. Banner puts the statement over a full-width photo.', 'langgam-fikir' ), array( 'choices' => array(
                            'split'  => __( 'Split (text beside a photo)', 'langgam-fikir' ),
                            'banner' => __( 'Banner (text over a full-width photo)', 'langgam-fikir' ),
                        ) ) ),
                        'hero_visual'          => $f( 'select', __( 'Hero picture (Split layout)', 'langgam-fikir' ), 'covers', __( 'Covers scatters your latest book covers; it falls back to the photo until at least three books have a cover image.', 'langgam-fikir' ), array( 'choices' => array(
                            'covers' => __( 'Floating book covers', 'langgam-fikir' ),
                            'photo'  => __( 'Photo', 'langgam-fikir' ),
                        ) ) ),
                        'hero_heading'         => $f( 'text', __( 'Hero heading (Split layout)', 'langgam-fikir' ), __( 'Books that cross borders.', 'langgam-fikir' ), __( 'The large heading. The Hero Text above becomes the paragraph under it.', 'langgam-fikir' ) ),
                        'hero_photo'           => $f( 'select', __( 'Hero photo', 'langgam-fikir' ), 'library-corridor', '', array( 'choices' => array_merge(
                            langgam_fikir_hero_photos(),
                            array(
                                'random' => __( 'Random (a different photo each visit)', 'langgam-fikir' ),
                                'none'   => __( 'No photo (solid colour)', 'langgam-fikir' ),
                            )
                        ) ) ),
                        'hero_primary_label'   => $f( 'text', __( 'First button', 'langgam-fikir' ), __( 'View Books', 'langgam-fikir' ), __( 'Links to the publications archive.', 'langgam-fikir' ) ),
                        'hero_secondary_label' => $f( 'text', __( 'Second button', 'langgam-fikir' ), __( 'Learn More', 'langgam-fikir' ), __( 'Links to the About page.', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Highlights Strip', 'langgam-fikir' ),
                    'desc'   => __( 'Three short facts in a band under the hero. Empty ones are hidden.', 'langgam-fikir' ),
                    'fields' => array(
                        'highlights_enabled' => $f( 'checkbox', __( 'Show the highlights strip', 'langgam-fikir' ), 1 ),
                        'highlight_1'        => $f( 'text', __( 'Highlight 1', 'langgam-fikir' ), __( 'Independent Malaysian publisher', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'highlight_2'        => $f( 'text', __( 'Highlight 2', 'langgam-fikir' ), __( 'Translations and original works', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'highlight_3'        => $f( 'text', __( 'Highlight 3', 'langgam-fikir' ), __( 'Publishing since 2015', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                    ),
                ),
                array(
                    'title'  => __( 'About Section Text', 'langgam-fikir' ),
                    'desc'   => __( 'The text beside the About image on the homepage. The image comes from the About page\'s featured image.', 'langgam-fikir' ),
                    'fields' => array(
                        'about_snippet_heading' => $f( 'text', __( 'Heading', 'langgam-fikir' ), __( 'Preserving the Art of Thoughtful Publishing', 'langgam-fikir' ) ),
                        'about_snippet_text'    => $f( 'textarea', __( 'Text', 'langgam-fikir' ), __( "At Langgam Fikir, we believe that books are more than just paper and ink—they are vessels of culture, history, and human experience. Founded with a mission to elevate local voices and translate global wisdom.\n\nOur catalog spans history, philosophy, literature, and social sciences, carefully curated to challenge perspectives and enrich the mind.", 'langgam-fikir' ), $para, array( 'rows' => 6 ) ),
                        'about_snippet_button'  => $f( 'text', __( 'Button', 'langgam-fikir' ), __( 'Read Our Full Story', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Section Headings', 'langgam-fikir' ),
                    'desc'   => __( 'Headings and subtitles of the Latest Publications and Resources sections.', 'langgam-fikir' ),
                    'fields' => array(
                        'books_heading'      => $f( 'text', __( 'Publications heading', 'langgam-fikir' ), __( 'Latest Publications', 'langgam-fikir' ) ),
                        'books_subtitle'     => $f( 'textarea', __( 'Publications subtitle', 'langgam-fikir' ), __( 'Our newest releases, representing the finest in contemporary thought and literature.', 'langgam-fikir' ) ),
                        'resources_heading'  => $f( 'text', __( 'Resources heading', 'langgam-fikir' ), __( 'Resources & Insights', 'langgam-fikir' ) ),
                        'resources_subtitle' => $f( 'textarea', __( 'Resources subtitle', 'langgam-fikir' ), __( 'Articles, interviews, and updates from the world of Langgam Fikir.', 'langgam-fikir' ) ),
                        'view_more_label'    => $f( 'text', __( '"View more" button', 'langgam-fikir' ), __( 'VIEW MORE', 'langgam-fikir' ) ),
                    ),
                ),
            ),
        ),

        /* ------------------------------------------------------ Pages tab */
        'pages'    => array(
            'label' => __( 'About & Contact Pages', 'langgam-fikir' ),
            'icon'  => 'dashicons-admin-page',
            'cards' => array(
                array(
                    'title'  => __( 'About Page', 'langgam-fikir' ),
                    'desc'   => __( 'Text on the About page layout. Anything typed in the About page editor replaces the "Mission text" below.', 'langgam-fikir' ),
                    'fields' => array(
                        'about_lead'            => $f( 'textarea', __( 'Lead paragraph', 'langgam-fikir' ), __( 'Langgam Fikir is an independent publishing house dedicated to bringing profound narratives and critical thought to the forefront of the Malaysian literary scene.', 'langgam-fikir' ), __( 'The larger introduction under the page title.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'about_mission_heading' => $f( 'text', __( 'Section heading', 'langgam-fikir' ), __( 'Our Mission', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'about_mission_text'    => $f( 'textarea', __( 'Mission text', 'langgam-fikir' ), __( "Founded in 2015, we operate with a singular vision: to publish books that matter. In an age of fleeting digital content, we believe in the enduring power of the physical book as a technology for preserving deep thought.\n\nWe focus on translating key works of global philosophy and literature into Malay, while also nurturing local voices that speak with clarity and courage on contemporary issues.", 'langgam-fikir' ), $para, array( 'rows' => 6 ) ),
                        'about_card1_title'     => $f( 'text', __( 'Card 1 title', 'langgam-fikir' ), __( 'Translation', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'about_card1_text'      => $f( 'textarea', __( 'Card 1 text', 'langgam-fikir' ), __( 'Bridging cultures by bringing world classics to local readers with high-fidelity translations.', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'about_card2_title'     => $f( 'text', __( 'Card 2 title', 'langgam-fikir' ), __( 'Original Works', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'about_card2_text'      => $f( 'textarea', __( 'Card 2 text', 'langgam-fikir' ), __( 'Providing a platform for Malaysian thinkers, poets, and storytellers to document our times.', 'langgam-fikir' ), __( 'Empty cards are hidden.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'about_contact_heading' => $f( 'text', __( 'Contact prompt', 'langgam-fikir' ), __( 'Have any questions?', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'about_contact_label'   => $f( 'text', __( 'Contact link text', 'langgam-fikir' ), __( 'Contact us!', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Contact Page', 'langgam-fikir' ),
                    'desc'   => __( 'Text on the Contact page and in its form. The email, phone and address shown beside the form come from Contact & Footer.', 'langgam-fikir' ),
                    'fields' => array(
                        'contact_description'   => $f( 'textarea', __( 'Introduction', 'langgam-fikir' ), __( 'We would love to hear from you. Send us a message and we will reply as soon as we can.', 'langgam-fikir' ), __( 'Shown beside the form. Leave empty to hide it.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                        'contact_follow_label'  => $f( 'text', __( 'Social links label', 'langgam-fikir' ), __( 'Find us on:', 'langgam-fikir' ) ),
                        'contact_ph_name'       => $f( 'text', __( 'Name placeholder', 'langgam-fikir' ), __( 'Name', 'langgam-fikir' ) ),
                        'contact_ph_email'      => $f( 'text', __( 'Email placeholder', 'langgam-fikir' ), __( 'Email', 'langgam-fikir' ) ),
                        'contact_ph_subject'    => $f( 'text', __( 'Subject placeholder', 'langgam-fikir' ), __( 'Subject', 'langgam-fikir' ) ),
                        'contact_ph_message'    => $f( 'text', __( 'Message placeholder', 'langgam-fikir' ), __( 'Type your message', 'langgam-fikir' ) ),
                        'contact_submit_label'  => $f( 'text', __( 'Submit button', 'langgam-fikir' ), __( 'Send message', 'langgam-fikir' ) ),
                        'contact_msg_success'   => $f( 'textarea', __( 'Success message', 'langgam-fikir' ), __( 'Thank you for your message! We will get back to you soon.', 'langgam-fikir' ) ),
                        'contact_msg_error'     => $f( 'textarea', __( 'Error message', 'langgam-fikir' ), __( 'There was an error sending your message. Please try again.', 'langgam-fikir' ) ),
                        'contact_order_subject' => $f( 'text', __( 'Order email subject', 'langgam-fikir' ), __( 'Book Order - {title}', 'langgam-fikir' ), __( 'Pre-filled when a visitor clicks Order on a book. Use {title}.', 'langgam-fikir' ) ),
                        'contact_order_message' => $f( 'textarea', __( 'Order email message', 'langgam-fikir' ), __( "I am interested in ordering:\n\nBook: {title}\nAuthor: {author}\nYear: {year}\nPrice: RM {price}\n\nQuantity: \n\nPlease provide me with ordering details.\n\nThank you.", 'langgam-fikir' ), __( 'Use {title}, {author}, {year} and {price}. A line whose author, year or price is empty is left out.', 'langgam-fikir' ), array( 'rows' => 8 ) ),
                    ),
                ),
            ),
        ),

        /* ------------------------------------------- Contact & Footer (adds) */
        'contact'  => array(
            'existing' => true,
            'cards'    => array(
                array(
                    'title'  => __( 'Footer Wording', 'langgam-fikir' ),
                    'desc'   => __( 'Headings and the copyright suffix in the footer.', 'langgam-fikir' ),
                    'fields' => array(
                        'footer_legal_heading'   => $f( 'text', __( 'Legal column heading', 'langgam-fikir' ), __( 'Legal', 'langgam-fikir' ) ),
                        'footer_contact_heading' => $f( 'text', __( 'Contact column heading', 'langgam-fikir' ), __( 'Contact', 'langgam-fikir' ) ),
                        'footer_rights'          => $f( 'text', __( 'Copyright suffix', 'langgam-fikir' ), __( 'All rights reserved.', 'langgam-fikir' ), __( 'Shown after "© year site name."', 'langgam-fikir' ) ),
                    ),
                ),
            ),
        ),

        /* -------------------------------------------- Social & SEO (adds) */
        'social'   => array(
            'existing' => true,
            'cards'    => array(
                array(
                    'title'  => __( 'More Social Profiles', 'langgam-fikir' ),
                    'desc'   => __( 'Further profiles shown as icons beside the four above (in the footer and on the Contact page) and listed in Schema.org sameAs. Leave a field empty to hide that icon.', 'langgam-fikir' ),
                    'fields' => array(
                        'social_linkedin' => $f( 'url', __( 'LinkedIn URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_threads' => $f( 'url', __( 'Threads URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_tiktok' => $f( 'url', __( 'TikTok URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_youtube' => $f( 'url', __( 'YouTube URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_telegram' => $f( 'url', __( 'Telegram URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_bluesky' => $f( 'url', __( 'Bluesky URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_mastodon' => $f( 'url', __( 'Mastodon URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_goodreads' => $f( 'url', __( 'Goodreads URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_medium' => $f( 'url', __( 'Medium URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_substack' => $f( 'url', __( 'Substack URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_spotify' => $f( 'url', __( 'Spotify URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'social_pinterest' => $f( 'url', __( 'Pinterest URL', 'langgam-fikir' ), '', '', array( 'allow_empty' => true ) ),
                        'footer_social' => $f( 'checkbox', __( 'Show the profile icons in the footer', 'langgam-fikir' ), 1 ),
                    ),
                ),
                array(
                    'title'  => __( 'Share Buttons', 'langgam-fikir' ),
                    'desc'   => __( 'The buttons visitors use to share a book, article or page, and where they appear.', 'langgam-fikir' ),
                    'fields' => array(
                        'share_whatsapp' => $f( 'checkbox', __( 'WhatsApp', 'langgam-fikir' ), 1 ),
                        'share_facebook' => $f( 'checkbox', __( 'Facebook', 'langgam-fikir' ), 1 ),
                        'share_x' => $f( 'checkbox', __( 'X', 'langgam-fikir' ), 1 ),
                        'share_linkedin' => $f( 'checkbox', __( 'LinkedIn', 'langgam-fikir' ), 0 ),
                        'share_telegram' => $f( 'checkbox', __( 'Telegram', 'langgam-fikir' ), 1 ),
                        'share_threads' => $f( 'checkbox', __( 'Threads', 'langgam-fikir' ), 0 ),
                        'share_bluesky' => $f( 'checkbox', __( 'Bluesky', 'langgam-fikir' ), 0 ),
                        'share_reddit' => $f( 'checkbox', __( 'Reddit', 'langgam-fikir' ), 0 ),
                        'share_copy' => $f( 'checkbox', __( 'Copy link button', 'langgam-fikir' ), 1 ),
                        'share_on_books' => $f( 'checkbox', __( 'Show on book pages', 'langgam-fikir' ), 1 ),
                        'share_on_posts' => $f( 'checkbox', __( 'Show on articles', 'langgam-fikir' ), 1 ),
                        'share_on_pages' => $f( 'checkbox', __( 'Show on pages (About and other standard pages)', 'langgam-fikir' ), 1, __( 'The Contact and Privacy Policy pages never show it.', 'langgam-fikir' ) ),
                    ),
                ),
            ),
        ),

        /* ------------------------------------------------------ Labels tab */
        'labels'   => array(
            'label' => __( 'Labels & 404', 'langgam-fikir' ),
            'icon'  => 'dashicons-editor-textcolor',
            'cards' => array(
                array(
                    'title'  => __( 'Buttons', 'langgam-fikir' ),
                    'desc'   => __( 'Button text used across the site.', 'langgam-fikir' ),
                    'fields' => array(
                        'label_learn_more' => $f( 'text', __( 'Book card: learn more', 'langgam-fikir' ), __( 'Learn More', 'langgam-fikir' ) ),
                        'label_read_more'  => $f( 'text', __( 'Article card: read more', 'langgam-fikir' ), __( 'Read More', 'langgam-fikir' ) ),
                        'lbl_share'        => $f( 'text', __( 'Share bar label', 'langgam-fikir' ), __( 'Share:', 'langgam-fikir' ) ),
                        'lbl_follow_us'    => $f( 'text', __( 'Social profiles label', 'langgam-fikir' ), __( 'Follow us', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Book Information Labels', 'langgam-fikir' ),
                    'desc'   => __( 'The labels in the information card on each book page.', 'langgam-fikir' ),
                    'fields' => array(
                        'lbl_author' => $f( 'text', __( 'Author', 'langgam-fikir' ), __( 'Author', 'langgam-fikir' ) ),
                        'lbl_translator' => $f( 'text', __( 'Translator', 'langgam-fikir' ), __( 'Translator', 'langgam-fikir' ) ),
                        'lbl_translated_from' => $f( 'text', __( 'Translated from', 'langgam-fikir' ), __( 'Translated from', 'langgam-fikir' ) ),
                        'lbl_editor' => $f( 'text', __( 'Editor', 'langgam-fikir' ), __( 'Editor', 'langgam-fikir' ) ),
                        'lbl_original_title' => $f( 'text', __( 'Original title', 'langgam-fikir' ), __( 'Original title', 'langgam-fikir' ) ),
                        'lbl_publisher' => $f( 'text', __( 'Publisher', 'langgam-fikir' ), __( 'Publisher', 'langgam-fikir' ) ),
                        'lbl_year' => $f( 'text', __( 'Year', 'langgam-fikir' ), __( 'Year', 'langgam-fikir' ) ),
                        'lbl_edition' => $f( 'text', __( 'Edition', 'langgam-fikir' ), __( 'Edition', 'langgam-fikir' ) ),
                        'lbl_series' => $f( 'text', __( 'Series', 'langgam-fikir' ), __( 'Series', 'langgam-fikir' ) ),
                        'lbl_format' => $f( 'text', __( 'Format', 'langgam-fikir' ), __( 'Format', 'langgam-fikir' ) ),
                        'lbl_pages' => $f( 'text', __( 'Pages', 'langgam-fikir' ), __( 'Pages', 'langgam-fikir' ) ),
                        'lbl_dimensions' => $f( 'text', __( 'Size', 'langgam-fikir' ), __( 'Size', 'langgam-fikir' ) ),
                        'lbl_isbn' => $f( 'text', __( 'ISBN', 'langgam-fikir' ), __( 'ISBN', 'langgam-fikir' ) ),
                        'lbl_oclc' => $f( 'text', __( 'OCLC number', 'langgam-fikir' ), __( 'OCLC number', 'langgam-fikir' ) ),
                        'lbl_category' => $f( 'text', __( 'Category', 'langgam-fikir' ), __( 'Category', 'langgam-fikir' ) ),
                        'lbl_topics' => $f( 'text', __( 'Topics (tags)', 'langgam-fikir' ), __( 'Topics', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( 'Browsing and Sorting', 'langgam-fikir' ),
                    'desc'   => __( 'The category and topic filters and the sort menu on the Publications archive and the category and tag pages.', 'langgam-fikir' ),
                    'fields' => array(
                        'lbl_browse_category' => $f( 'text', __( 'Category filter heading', 'langgam-fikir' ), __( 'Browse by category', 'langgam-fikir' ) ),
                        'lbl_browse_topics'   => $f( 'text', __( 'Topic filter heading', 'langgam-fikir' ), __( 'Browse by topic', 'langgam-fikir' ) ),
                        'lbl_all'             => $f( 'text', __( '"All" chip', 'langgam-fikir' ), __( 'All', 'langgam-fikir' ) ),
                        'lbl_sort_by'         => $f( 'text', __( 'Sort menu label', 'langgam-fikir' ), __( 'Sort by', 'langgam-fikir' ) ),
                        'lbl_sort_newest'     => $f( 'text', __( 'Sort: newest', 'langgam-fikir' ), __( 'Newest first', 'langgam-fikir' ) ),
                        'lbl_sort_oldest'     => $f( 'text', __( 'Sort: oldest', 'langgam-fikir' ), __( 'Oldest first', 'langgam-fikir' ) ),
                        'lbl_sort_title'      => $f( 'text', __( 'Sort: title', 'langgam-fikir' ), __( 'Title A to Z', 'langgam-fikir' ) ),
                        'lbl_apply'           => $f( 'text', __( 'Apply button', 'langgam-fikir' ), __( 'Apply', 'langgam-fikir' ) ),
                        'lbl_titles_count'    => $f( 'text', __( 'Result count', 'langgam-fikir' ), __( '{n} titles', 'langgam-fikir' ), __( 'Use {n} for the number of books.', 'langgam-fikir' ) ),
                    ),
                ),
                array(
                    'title'  => __( '404 Page', 'langgam-fikir' ),
                    'desc'   => __( 'The "page not found" screen: a bookshelf with a gap, one random quip, a message and a few books to try instead.', 'langgam-fikir' ),
                    'fields' => array(
                        'error_404_heading'     => $f( 'text', __( 'Heading', 'langgam-fikir' ), __( 'This page has wandered off the shelf.', 'langgam-fikir' ) ),
                        'error_404_text'        => $f( 'textarea', __( 'Message', 'langgam-fikir' ), __( "We looked under every cover, behind the footnotes and between the lines. Nothing.\n\nThe page you wanted may have moved, been renamed, or never existed outside someone's imagination (which, to be fair, is where most good books begin).", 'langgam-fikir' ), $para, array( 'rows' => 5 ) ),
                        'error_404_quips'       => $f( 'textarea', __( 'Quips', 'langgam-fikir' ), __( "Misshelved, not lost. Probably.\nEven the best index misses an entry.\nThis chapter has been left intentionally blank.\nPlot twist: the link was broken all along.\nThe author is as surprised as you are.\nOur editors have been notified. They are pretending to be shocked.\nIt was here a minute ago. We checked the margins.", 'langgam-fikir' ), __( 'One per line. A different one is shown at random each time. Empty hides it.', 'langgam-fikir' ), array( 'rows' => 8, 'allow_empty' => true ) ),
                        'error_404_home_label'  => $f( 'text', __( 'Home button', 'langgam-fikir' ), __( 'Take me home', 'langgam-fikir' ) ),
                        'error_404_books_label' => $f( 'text', __( 'Books button', 'langgam-fikir' ), __( 'Browse Books', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                        'error_404_suggest'     => $f( 'text', __( 'Suggested books heading', 'langgam-fikir' ), __( 'While you are here, these are definitely on the shelf', 'langgam-fikir' ), __( 'Shows three books under the message. Empty hides them.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                    ),
                ),
            ),
        ),
    );

    return $schema;
}

/**
 * All extra fields, flattened: key => field definition.
 */
function langgam_fikir_extra_fields() {
    static $fields = null;
    if ( null !== $fields ) {
        return $fields;
    }
    $fields = array();
    foreach ( langgam_fikir_extra_options_schema() as $tab ) {
        foreach ( $tab['cards'] as $card ) {
            $fields += $card['fields'];
        }
    }
    return $fields;
}

/**
 * Defaults for every extra field (merged into langgam_fikir_option_defaults()).
 */
function langgam_fikir_extra_option_defaults() {
    $defaults = array();
    foreach ( langgam_fikir_extra_fields() as $key => $field ) {
        $defaults[ $key ] = $field['default'];
    }
    return $defaults;
}

/**
 * Sanitise the extra fields from a submitted options array.
 *
 * @param array $input Raw submitted options.
 * @return array
 */
function langgam_fikir_sanitize_extra_options( $input ) {
    $input  = is_array( $input ) ? $input : array();
    $clean  = array();
    $schemes = langgam_fikir_color_schemes();

    foreach ( langgam_fikir_extra_fields() as $key => $field ) {
        $raw = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? wp_unslash( (string) $input[ $key ] ) : '';

        switch ( $field['type'] ) {
            case 'scheme':
                $clean[ $key ] = isset( $schemes[ $raw ] ) ? $raw : $field['default'];
                break;
            case 'select':
                $clean[ $key ] = isset( $field['choices'][ $raw ] ) ? $raw : $field['default'];
                break;
            case 'color':
                $clean[ $key ] = (string) sanitize_hex_color( $raw );
                break;
            case 'checkbox':
                $clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
                break;
            case 'number':
                $clean[ $key ] = '' === $raw ? $field['default'] : max( $field['min'], min( $field['max'], absint( $raw ) ) );
                break;
            case 'image':
                $id            = absint( $raw );
                $clean[ $key ] = ( $id && wp_attachment_is_image( $id ) ) ? $id : 0;
                break;
            case 'email':
                $clean[ $key ] = sanitize_email( $raw );
                break;
            case 'url':
                $clean[ $key ] = esc_url_raw( $raw );
                break;
            case 'textarea':
                $clean[ $key ] = sanitize_textarea_field( $raw );
                break;
            default:
                $clean[ $key ] = sanitize_text_field( $raw );
        }
    }

    return $clean;
}

/**
 * A piece of editable text: the saved value, or the default when it was left blank.
 * Fields flagged allow_empty keep a deliberately blank value (so the element can be hidden).
 *
 * @param string $key Option key.
 * @return string
 */
function langgam_fikir_text( $key ) {
    $fields  = langgam_fikir_extra_fields();
    $options = get_option( 'langgam_fikir_options', array() );
    $default = isset( $fields[ $key ] ) ? $fields[ $key ]['default'] : '';

    if ( is_array( $options ) && array_key_exists( $key, $options ) ) {
        $value = (string) $options[ $key ];
        if ( '' !== $value ) {
            return $value;
        }
        if ( isset( $fields[ $key ]['allow_empty'] ) ) {
            return '';
        }
    }

    return (string) $default;
}

/**
 * Escaped paragraphs from a plain-text field (blank line = new paragraph).
 */
function langgam_fikir_paragraphs( $text ) {
    return wp_kses_post( wpautop( esc_html( $text ) ) );
}
