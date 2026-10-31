<?php
/**
 * Theme Settings schema: every editable colour, font and piece of page text.
 *
 * One definition drives the settings screen, sanitising and defaults, so adding a
 * field here is all that is needed to make it editable.
 *
 * @package Langgam_Fikir
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Default text for every content field (translatable). Colour and font fields default to ''/slug.
 *
 * @return array<string, string|int>
 */
function langgam_fikir_get_defaults() {
    static $defaults = null;
    if ( null !== $defaults ) {
        return $defaults;
    }

    $defaults = array(
        // Colours and fonts.
        'color_scheme'  => 'earth',
        'font_heading'  => 'sabon',
        'font_body'     => 'inter',
        'font_display'  => 'decorative',
        'fonts_mode'    => 'local',
        'corner_style'  => 'soft',
        'parallax'      => 'desktop',

        // Homepage.
        'hero_text'                => __( 'Book publisher of ideas that transcend borders—bringing critical, thought-provoking works to readers who seek truth.', 'langgam-fikir' ),
        'hero_primary_label'       => __( 'View Books', 'langgam-fikir' ),
        'hero_secondary_label'     => __( 'Learn More', 'langgam-fikir' ),
        'about_snippet_heading'    => __( 'Preserving the Art of Thoughtful Publishing', 'langgam-fikir' ),
        'about_snippet_text'       => __( "At Langgam Fikir, we believe that books are more than just paper and ink—they are vessels of culture, history, and human experience. Founded with a mission to elevate local voices and translate global wisdom.\n\nOur catalog spans history, philosophy, literature, and social sciences, carefully curated to challenge perspectives and enrich the mind.", 'langgam-fikir' ),
        'about_snippet_button'     => __( 'Read Our Full Story', 'langgam-fikir' ),
        'about_badge_number'       => '10+',
        'about_badge_label'        => __( 'Years of Excellence', 'langgam-fikir' ),
        'books_heading'            => __( 'Featured Publications', 'langgam-fikir' ),
        'books_subtitle'           => __( "Our editors' picks of the season, representing the finest in contemporary thought and literature.", 'langgam-fikir' ),
        'featured_books_count'     => 4,
        'resources_heading'        => __( 'Resources & Insights', 'langgam-fikir' ),
        'resources_subtitle'       => __( 'Articles, interviews, and updates from the world of Langgam Fikir.', 'langgam-fikir' ),
        'featured_resources_count' => 3,
        'view_more_label'          => __( 'VIEW MORE', 'langgam-fikir' ),

        // About page.
        'about_lead'            => __( 'Langgam Fikir is an independent publishing house dedicated to bringing profound narratives and critical thought to the forefront of the Malaysian literary scene.', 'langgam-fikir' ),
        'about_mission_heading' => __( 'Our Mission', 'langgam-fikir' ),
        'about_mission_text'    => __( "Founded in 2015, we operate with a singular vision: to publish books that matter. In an age of fleeting digital content, we believe in the enduring power of the physical book as a technology for preserving deep thought.\n\nWe focus on translating key works of global philosophy and literature into Malay, while also nurturing local voices that speak with clarity and courage on contemporary issues.", 'langgam-fikir' ),
        'about_card1_title'     => __( 'Translation', 'langgam-fikir' ),
        'about_card1_text'      => __( 'Bridging cultures by bringing world classics to local readers with high-fidelity translations.', 'langgam-fikir' ),
        'about_card2_title'     => __( 'Original Works', 'langgam-fikir' ),
        'about_card2_text'      => __( 'Providing a platform for Malaysian thinkers, poets, and storytellers to document our times.', 'langgam-fikir' ),
        'about_contact_heading' => __( 'Have any questions?', 'langgam-fikir' ),
        'about_contact_label'   => __( 'Contact us!', 'langgam-fikir' ),
        'about_contact_phone'   => '',

        // Contact page.
        'contact_description'  => __( 'We would love to hear from you. Send us a message and we will reply as soon as we can.', 'langgam-fikir' ),
        'contact_recipient'    => 'langgam.fikir@gmail.com',
        'contact_email'        => 'langgam.fikir@gmail.com',
        'contact_phone'        => '',
        'contact_follow_label' => __( 'Follow on:', 'langgam-fikir' ),
        'contact_ph_name'      => __( 'Name', 'langgam-fikir' ),
        'contact_ph_email'     => __( 'Email', 'langgam-fikir' ),
        'contact_ph_subject'   => __( 'Subject', 'langgam-fikir' ),
        'contact_ph_message'   => __( 'Type your message', 'langgam-fikir' ),
        'contact_submit_label' => __( 'Send message', 'langgam-fikir' ),
        'contact_order_subject' => __( 'Book Order - {title}', 'langgam-fikir' ),
        'contact_order_message' => __( "I am interested in ordering:\n\nBook: {title}\nAuthor: {author}\nYear: {year}\nPrice: RM {price}\n\nQuantity: \n\nPlease provide me with ordering details.\n\nThank you.", 'langgam-fikir' ),
        'privacy_contents_label' => __( 'Contents', 'langgam-fikir' ),
        'contact_msg_success'  => __( 'Thank you for your message! We will get back to you soon.', 'langgam-fikir' ),
        'contact_msg_error'    => __( 'There was an error sending your message. Please try again.', 'langgam-fikir' ),
        'contact_msg_throttled' => __( 'You are sending messages too quickly. Please wait a moment and try again.', 'langgam-fikir' ),

        // Publications and resources.
        'books_archive_heading'  => __( 'Our Publications', 'langgam-fikir' ),
        'books_archive_subtitle' => __( 'Explore our complete catalog of works, from contemporary fiction to critical essays and historical accounts.', 'langgam-fikir' ),
        'order_label'            => __( 'ORDER', 'langgam-fikir' ),
        'preorder_label'         => __( 'PRE-ORDER', 'langgam-fikir' ),
        'author_label'           => __( 'Penulis', 'langgam-fikir' ),
        'year_label'             => __( 'Tahun', 'langgam-fikir' ),
        'msg_coming_soon'        => __( 'This book is coming soon. Check back later for availability.', 'langgam-fikir' ),
        'msg_reprinting'         => __( 'This book is currently being reprinted. It will be available again soon.', 'langgam-fikir' ),
        'msg_out_of_print'       => __( 'This book is currently out of print.', 'langgam-fikir' ),
        'resources_featured_heading' => __( 'Featured Resources', 'langgam-fikir' ),
        'resources_other_heading'    => __( 'Other Resources', 'langgam-fikir' ),

        // Labels: book statuses and buttons.
        'status_published'    => __( 'Published', 'langgam-fikir' ),
        'status_pre_order'    => __( 'Pre-Order', 'langgam-fikir' ),
        'status_coming_soon'  => __( 'Coming Soon', 'langgam-fikir' ),
        'status_reprinting'   => __( 'Reprinting', 'langgam-fikir' ),
        'status_out_of_print' => __( 'Out of Print', 'langgam-fikir' ),
        'label_learn_more'    => __( 'Learn More', 'langgam-fikir' ),
        'label_read_more'     => __( 'Read More', 'langgam-fikir' ),
        'label_add_to_cart'   => __( 'ADD TO CART', 'langgam-fikir' ),
        'label_preorder_now'  => __( 'PRE-ORDER NOW', 'langgam-fikir' ),

        // Labels: single book.
        'label_author'       => __( 'Author', 'langgam-fikir' ),
        'label_publisher'    => __( 'Publisher', 'langgam-fikir' ),
        'label_editor'       => __( 'Editor', 'langgam-fikir' ),
        'label_translator'   => __( 'Translator', 'langgam-fikir' ),
        'label_year'         => __( 'Year', 'langgam-fikir' ),
        'label_isbn'         => __( 'ISBN', 'langgam-fikir' ),
        'label_price'        => __( 'Price', 'langgam-fikir' ),
        'label_synopsis'     => __( 'Synopsis', 'langgam-fikir' ),
        'label_qty'          => __( 'Quantity', 'langgam-fikir' ),
        'label_qty_decrease' => __( 'Decrease quantity', 'langgam-fikir' ),
        'label_qty_increase' => __( 'Increase quantity', 'langgam-fikir' ),

        // Labels: articles.
        'label_tags'           => __( 'Tags', 'langgam-fikir' ),
        'label_post_prev'      => __( 'Previous Article', 'langgam-fikir' ),
        'label_post_next'      => __( 'Next Article', 'langgam-fikir' ),
        'label_related'        => __( 'Related Articles', 'langgam-fikir' ),
        'label_read_time'      => __( '{n} min read', 'langgam-fikir' ),
        'label_pages'          => __( 'Pages:', 'langgam-fikir' ),
        'label_search_results' => __( 'Search Results for: {query}', 'langgam-fikir' ),

        // Labels: navigation and empty states.
        'label_prev'          => __( '← Previous', 'langgam-fikir' ),
        'label_next'          => __( 'Next →', 'langgam-fikir' ),
        'label_carousel_prev' => __( 'Previous', 'langgam-fikir' ),
        'label_carousel_next' => __( 'Next', 'langgam-fikir' ),
        'msg_no_books'        => __( 'No books found.', 'langgam-fikir' ),
        'msg_no_resources'    => __( 'No resources found.', 'langgam-fikir' ),
        'msg_no_posts'        => __( 'No posts found.', 'langgam-fikir' ),
        'msg_nothing_found'   => __( 'Nothing found.', 'langgam-fikir' ),

        // Labels: 404 page.
        'error_404_heading' => __( 'This page has wandered off the shelf.', 'langgam-fikir' ),
        'error_404_text'    => __( "We looked under every cover, behind the footnotes and between the lines. Nothing.\n\nThe page you wanted may have moved, been renamed, or never existed outside someone's imagination (which, to be fair, is where most good books begin).", 'langgam-fikir' ),
        'error_404_quips'   => __( "Misshelved, not lost. Probably.\nEven the best index misses an entry.\nThis chapter has been left intentionally blank.\nPlot twist: the link was broken all along.\nThe author is as surprised as you are.\nOur editors have been notified. They are pretending to be shocked.\nIt was here a minute ago. We checked the margins.", 'langgam-fikir' ),
        'error_404_button'  => __( 'Take me home', 'langgam-fikir' ),
        'error_404_suggestions_heading' => __( 'While you are here, these are definitely on the shelf', 'langgam-fikir' ),

        // Labels: comments.
        'label_comments_one'    => __( 'One comment on “{title}”', 'langgam-fikir' ),
        'label_comments_many'   => __( '{count} comments on “{title}”', 'langgam-fikir' ),
        'label_comments_closed' => __( 'Comments are closed.', 'langgam-fikir' ),

        // Footer.
        'footer_legal_heading' => __( 'Legal', 'langgam-fikir' ),
        'footer_rights'        => __( 'All rights reserved.', 'langgam-fikir' ),
        'footer_company_info'  => __( '{site} is a registered enterprise in Malaysia.', 'langgam-fikir' ),
    );

    return $defaults;
}

/**
 * Tabs and their fields. A field is array( type, label, [desc], [extra] ).
 * Types: scheme, color, select, text, textarea, email, url, number, image, page.
 *
 * @return array<string, array{label:string, fields:array}>
 */
function langgam_fikir_get_settings_fields() {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }

    $f = static function ( $type, $label, $desc = '', $extra = array() ) {
        return array_merge( array( 'type' => $type, 'label' => $label, 'desc' => $desc ), $extra );
    };

    $colour_help = __( 'Optional. Leave empty to use the selected scheme.', 'langgam-fikir' );
    $para_help   = __( 'Separate paragraphs with a blank line.', 'langgam-fikir' );
    $page_help   = __( 'This page layout takes its text from here. Text typed in the page editor is shown too, where noted.', 'langgam-fikir' );

    $tabs = array(
        'colours'      => array(
            'label'  => __( 'Colours', 'langgam-fikir' ),
            'fields' => array(
                'color_scheme'     => $f( 'scheme', __( 'Colour scheme', 'langgam-fikir' ) ),
                'color_primary'    => $f( 'color', __( 'Headings and hero', 'langgam-fikir' ), $colour_help ),
                'color_accent'     => $f( 'color', __( 'Accent', 'langgam-fikir' ), __( 'Buttons, links on hover and badges. Leave empty to use the selected scheme.', 'langgam-fikir' ) ),
                'color_background' => $f( 'color', __( 'Page background', 'langgam-fikir' ), $colour_help ),
                'color_surface'    => $f( 'color', __( 'Alternate sections', 'langgam-fikir' ), __( 'Cards and tinted bands. Leave empty to use the selected scheme.', 'langgam-fikir' ) ),
                'color_text'       => $f( 'color', __( 'Body text', 'langgam-fikir' ), $colour_help ),
            ),
        ),
        'typography'   => array(
            'label'  => __( 'Typography', 'langgam-fikir' ),
            'fields' => array(
                'font_heading' => $f( 'select', __( 'Heading font', 'langgam-fikir' ), '', array( 'choices' => array(
                    'sabon'        => __( 'Sabon Next LT (default)', 'langgam-fikir' ),
                    'crimson'      => __( 'Crimson Text', 'langgam-fikir' ),
                    'georgia'      => __( 'Georgia', 'langgam-fikir' ),
                    'system-serif' => __( 'System serif', 'langgam-fikir' ),
                    'system-sans'  => __( 'System sans-serif', 'langgam-fikir' ),
                ) ) ),
                'font_body'    => $f( 'select', __( 'Body font', 'langgam-fikir' ), '', array( 'choices' => array(
                    'inter'       => __( 'Inter (default)', 'langgam-fikir' ),
                    'sabon'       => __( 'Sabon Next LT (serif)', 'langgam-fikir' ),
                    'system-sans' => __( 'System sans-serif', 'langgam-fikir' ),
                ) ) ),
                'font_display' => $f( 'select', __( 'Titles and tagline', 'langgam-fikir' ), __( 'Decorative uses Kagnue for the site title and page titles, and Ferments for the tagline.', 'langgam-fikir' ), array( 'choices' => array(
                    'decorative' => __( 'Decorative (Kagnue and Ferments)', 'langgam-fikir' ),
                    'plain'      => __( 'Plain (heading font only)', 'langgam-fikir' ),
                ) ) ),
                'fonts_mode'   => $f( 'select', __( 'Font files', 'langgam-fikir' ), __( 'All fonts are served from this theme, so no request is made to Google. Choose "System fonts only" for the fastest page load.', 'langgam-fikir' ), array( 'choices' => array(
                    'local'  => __( 'Load bundled fonts (recommended)', 'langgam-fikir' ),
                    'system' => __( 'System fonts only', 'langgam-fikir' ),
                ) ) ),
            ),
        ),
        'layout'       => array(
            'label'  => __( 'Layout', 'langgam-fikir' ),
            'fields' => array(
                'corner_style' => $f( 'select', __( 'Corners', 'langgam-fikir' ), __( 'How rounded cards, images, buttons and form fields are.', 'langgam-fikir' ), array( 'choices' => array(
                    'soft'  => __( 'Soft (default)', 'langgam-fikir' ),
                    'round' => __( 'Round (pill buttons, large radii)', 'langgam-fikir' ),
                    'sharp' => __( 'Sharp (the original, near-square look)', 'langgam-fikir' ),
                ) ) ),
                'parallax'     => $f( 'select', __( 'Parallax scrolling', 'langgam-fikir' ), __( 'Hero layers, the homepage About image, the About page logo and the book cover drift at different speeds while scrolling. It is switched off automatically for visitors who prefer reduced motion.', 'langgam-fikir' ), array( 'choices' => array(
                    'desktop' => __( 'On for wide screens (default)', 'langgam-fikir' ),
                    'all'     => __( 'On for all screens', 'langgam-fikir' ),
                    'off'     => __( 'Off', 'langgam-fikir' ),
                ) ) ),
            ),
        ),
        'homepage'     => array(
            'label'  => __( 'Homepage', 'langgam-fikir' ),
            'fields' => array(
                'hero_text'                => $f( 'textarea', __( 'Hero statement', 'langgam-fikir' ), __( 'The large italic line at the top of the homepage.', 'langgam-fikir' ) ),
                'hero_image'               => $f( 'image', __( 'Hero background image', 'langgam-fikir' ), __( 'Optional. Shown behind the hero statement with a tint and a parallax effect. Leave empty for the soft decorative circles.', 'langgam-fikir' ) ),
                'hero_primary_label'       => $f( 'text', __( 'Hero button 1 (books)', 'langgam-fikir' ), __( 'Links to the publications archive.', 'langgam-fikir' ) ),
                'hero_secondary_label'     => $f( 'text', __( 'Hero button 2 (about)', 'langgam-fikir' ), __( 'Links to the About page chosen in Footer and pages. Hidden if none is set.', 'langgam-fikir' ) ),
                'about_snippet_image'      => $f( 'image', __( 'About section image', 'langgam-fikir' ), __( 'Falls back to the About page featured image.', 'langgam-fikir' ) ),
                'about_snippet_heading'    => $f( 'text', __( 'About section heading', 'langgam-fikir' ) ),
                'about_snippet_text'       => $f( 'textarea', __( 'About section text', 'langgam-fikir' ), $para_help, array( 'rows' => 6 ) ),
                'about_snippet_button'     => $f( 'text', __( 'About section button', 'langgam-fikir' ) ),
                'about_badge_number'       => $f( 'text', __( 'Badge number', 'langgam-fikir' ), __( 'For example "10+". Leave the number and label both empty to hide the badge.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'about_badge_label'        => $f( 'text', __( 'Badge label', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                'books_heading'            => $f( 'text', __( 'Publications heading', 'langgam-fikir' ) ),
                'books_subtitle'           => $f( 'textarea', __( 'Publications subtitle', 'langgam-fikir' ) ),
                'featured_books_count'     => $f( 'number', __( 'Featured publications', 'langgam-fikir' ), __( 'How many books to show (1 to 12).', 'langgam-fikir' ), array( 'min' => 1, 'max' => 12 ) ),
                'resources_heading'        => $f( 'text', __( 'Resources heading', 'langgam-fikir' ) ),
                'resources_subtitle'       => $f( 'textarea', __( 'Resources subtitle', 'langgam-fikir' ) ),
                'featured_resources_count' => $f( 'number', __( 'Homepage resources', 'langgam-fikir' ), __( 'How many posts to show (1 to 12).', 'langgam-fikir' ), array( 'min' => 1, 'max' => 12 ) ),
                'view_more_label'          => $f( 'text', __( '"View more" button', 'langgam-fikir' ) ),
            ),
        ),
        'about'        => array(
            'label'  => __( 'About page', 'langgam-fikir' ),
            'fields' => array(
                'about_logo'            => $f( 'image', __( 'Logo', 'langgam-fikir' ), __( 'Shown beside the text. Falls back to the site logo.', 'langgam-fikir' ) ),
                'about_lead'            => $f( 'textarea', __( 'Lead paragraph', 'langgam-fikir' ), __( 'The larger introduction under the page title.', 'langgam-fikir' ) ),
                'about_mission_heading' => $f( 'text', __( 'Section heading', 'langgam-fikir' ) ),
                'about_mission_text'    => $f( 'textarea', __( 'Section text', 'langgam-fikir' ), __( 'Used when the About page editor is empty. Anything typed in the page editor replaces this text. ', 'langgam-fikir' ) . $para_help, array( 'rows' => 6 ) ),
                'about_card1_title'     => $f( 'text', __( 'Card 1 title', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                'about_card1_text'      => $f( 'textarea', __( 'Card 1 text', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                'about_card2_title'     => $f( 'text', __( 'Card 2 title', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                'about_card2_text'      => $f( 'textarea', __( 'Card 2 text', 'langgam-fikir' ), __( 'Empty cards are hidden.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'about_contact_heading' => $f( 'text', __( 'Contact prompt', 'langgam-fikir' ) ),
                'about_contact_label'   => $f( 'text', __( 'Contact link text', 'langgam-fikir' ), __( 'Links to the Contact page chosen in Footer and pages.', 'langgam-fikir' ) ),
                'about_contact_phone'   => $f( 'text', __( 'Phone number', 'langgam-fikir' ), __( 'Leave empty to hide the phone number.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
            ),
        ),
        'contact'      => array(
            'label'  => __( 'Contact page', 'langgam-fikir' ),
            'fields' => array(
                'contact_recipient'    => $f( 'email', __( 'Send form messages to', 'langgam-fikir' ), __( 'Defaults to the site administrator email.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'contact_description'  => $f( 'textarea', __( 'Introduction', 'langgam-fikir' ), __( 'Shown beside the form. Text typed in the Contact page editor appears above the form.', 'langgam-fikir' ) ),
                'contact_email'        => $f( 'email', __( 'Public email', 'langgam-fikir' ), __( 'Leave empty to hide it.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'contact_phone'        => $f( 'text', __( 'Public phone', 'langgam-fikir' ), __( 'Leave empty to hide it.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'contact_instagram'    => $f( 'url', __( 'Instagram URL', 'langgam-fikir' ), __( 'Leave empty to hide the Instagram link.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                'contact_follow_label' => $f( 'text', __( 'Social label', 'langgam-fikir' ) ),
                'contact_ph_name'      => $f( 'text', __( 'Name placeholder', 'langgam-fikir' ) ),
                'contact_ph_email'     => $f( 'text', __( 'Email placeholder', 'langgam-fikir' ) ),
                'contact_ph_subject'   => $f( 'text', __( 'Subject placeholder', 'langgam-fikir' ) ),
                'contact_ph_message'   => $f( 'text', __( 'Message placeholder', 'langgam-fikir' ) ),
                'contact_submit_label' => $f( 'text', __( 'Submit button', 'langgam-fikir' ) ),
                'contact_order_subject' => $f( 'text', __( 'Order email subject', 'langgam-fikir' ), __( 'Pre-filled when a visitor clicks Order on a book. Use {title}.', 'langgam-fikir' ) ),
                'contact_order_message' => $f( 'textarea', __( 'Order email message', 'langgam-fikir' ), __( 'Pre-filled in the message box. Use {title}, {author}, {year} and {price}; a line whose author, year or price is empty is left out.', 'langgam-fikir' ), array( 'rows' => 8 ) ),
                'contact_msg_success'   => $f( 'textarea', __( 'Success message', 'langgam-fikir' ) ),
                'contact_msg_error'     => $f( 'textarea', __( 'Error message', 'langgam-fikir' ) ),
                'contact_msg_throttled' => $f( 'textarea', __( 'Too-fast message', 'langgam-fikir' ) ),
            ),
        ),
        'publications' => array(
            'label'  => __( 'Publications', 'langgam-fikir' ),
            'fields' => array(
                'books_archive_heading'  => $f( 'text', __( 'Archive heading', 'langgam-fikir' ) ),
                'books_archive_subtitle' => $f( 'textarea', __( 'Archive subtitle', 'langgam-fikir' ) ),
                'order_label'            => $f( 'text', __( 'Order button', 'langgam-fikir' ) ),
                'preorder_label'         => $f( 'text', __( 'Pre-order button', 'langgam-fikir' ) ),
                'author_label'           => $f( 'text', __( 'Card label: author', 'langgam-fikir' ) ),
                'year_label'             => $f( 'text', __( 'Card label: year', 'langgam-fikir' ) ),
                'msg_coming_soon'        => $f( 'textarea', __( 'Message: coming soon', 'langgam-fikir' ) ),
                'msg_reprinting'         => $f( 'textarea', __( 'Message: reprinting', 'langgam-fikir' ) ),
                'msg_out_of_print'       => $f( 'textarea', __( 'Message: out of print', 'langgam-fikir' ) ),
                'resources_featured_heading' => $f( 'text', __( 'Resources: featured heading', 'langgam-fikir' ) ),
                'resources_other_heading'    => $f( 'text', __( 'Resources: other heading', 'langgam-fikir' ) ),
            ),
        ),
        'labels'       => array(
            'label'  => __( 'Labels', 'langgam-fikir' ),
            'fields' => array(
                '_h_st' => $f( 'heading', __( 'Book status badges', 'langgam-fikir' ) ),
                'status_published' => $f( 'text', __( 'Published', 'langgam-fikir' ) ),
                'status_pre_order' => $f( 'text', __( 'Pre-order', 'langgam-fikir' ) ),
                'status_coming_soon' => $f( 'text', __( 'Coming soon', 'langgam-fikir' ) ),
                'status_reprinting' => $f( 'text', __( 'Reprinting', 'langgam-fikir' ) ),
                'status_out_of_print' => $f( 'text', __( 'Out of print', 'langgam-fikir' ) ),
                '_h_btn' => $f( 'heading', __( 'Buttons', 'langgam-fikir' ) ),
                'label_learn_more' => $f( 'text', __( 'Book card: learn more', 'langgam-fikir' ) ),
                'label_read_more' => $f( 'text', __( 'Article card: read more', 'langgam-fikir' ) ),
                'label_add_to_cart' => $f( 'text', __( 'Add to cart', 'langgam-fikir' ) ),
                'label_preorder_now' => $f( 'text', __( 'Pre-order now', 'langgam-fikir' ) ),
                '_h_book' => $f( 'heading', __( 'Single book page', 'langgam-fikir' ) ),
                'label_author' => $f( 'text', __( 'Author', 'langgam-fikir' ) ),
                'label_publisher' => $f( 'text', __( 'Publisher', 'langgam-fikir' ) ),
                'label_editor' => $f( 'text', __( 'Editor', 'langgam-fikir' ) ),
                'label_translator' => $f( 'text', __( 'Translator', 'langgam-fikir' ) ),
                'label_year' => $f( 'text', __( 'Year', 'langgam-fikir' ) ),
                'label_isbn' => $f( 'text', __( 'ISBN', 'langgam-fikir' ) ),
                'label_price' => $f( 'text', __( 'Price', 'langgam-fikir' ) ),
                'label_synopsis' => $f( 'text', __( 'Synopsis heading', 'langgam-fikir' ) ),
                'label_qty' => $f( 'text', __( 'Quantity (screen readers)', 'langgam-fikir' ) ),
                'label_qty_decrease' => $f( 'text', __( 'Decrease button (screen readers)', 'langgam-fikir' ) ),
                'label_qty_increase' => $f( 'text', __( 'Increase button (screen readers)', 'langgam-fikir' ) ),
                '_h_art' => $f( 'heading', __( 'Articles', 'langgam-fikir' ) ),
                'label_tags' => $f( 'text', __( 'Tags heading', 'langgam-fikir' ) ),
                'label_post_prev' => $f( 'text', __( 'Previous article', 'langgam-fikir' ) ),
                'label_post_next' => $f( 'text', __( 'Next article', 'langgam-fikir' ) ),
                'label_related' => $f( 'text', __( 'Related articles heading', 'langgam-fikir' ) ),
                'label_read_time' => $f( 'text', __( 'Reading time', 'langgam-fikir' ), 'Use {n} for the number of minutes.' ),
                'label_pages' => $f( 'text', __( 'Page links prefix', 'langgam-fikir' ) ),
                'label_search_results' => $f( 'text', __( 'Search results heading', 'langgam-fikir' ), 'Use {query} for the search words.' ),
                '_h_nav' => $f( 'heading', __( 'Navigation and empty states', 'langgam-fikir' ) ),
                'label_prev' => $f( 'text', __( 'Previous page link', 'langgam-fikir' ) ),
                'label_next' => $f( 'text', __( 'Next page link', 'langgam-fikir' ) ),
                'label_carousel_prev' => $f( 'text', __( 'Carousel previous (screen readers)', 'langgam-fikir' ) ),
                'label_carousel_next' => $f( 'text', __( 'Carousel next (screen readers)', 'langgam-fikir' ) ),
                'msg_no_books' => $f( 'text', __( 'No books found', 'langgam-fikir' ) ),
                'msg_no_resources' => $f( 'text', __( 'No resources found', 'langgam-fikir' ) ),
                'msg_no_posts' => $f( 'text', __( 'No posts found', 'langgam-fikir' ) ),
                'msg_nothing_found' => $f( 'text', __( 'Nothing found', 'langgam-fikir' ) ),
                '_h_priv' => $f( 'heading', __( 'Privacy page', 'langgam-fikir' ) ),
                'privacy_contents_label' => $f( 'text', __( 'Contents sidebar heading', 'langgam-fikir' ) ),
                '_h_404' => $f( 'heading', __( '404 page', 'langgam-fikir' ) ),
                'error_404_heading' => $f( 'text', __( 'Heading', 'langgam-fikir' ) ),
                'error_404_text'    => $f( 'textarea', __( 'Text', 'langgam-fikir' ), __( 'Separate paragraphs with a blank line.', 'langgam-fikir' ), array( 'rows' => 5 ) ),
                'error_404_quips'   => $f( 'textarea', __( 'Quips', 'langgam-fikir' ), __( 'One per line. A different one is shown at random each time. Leave empty to hide it.', 'langgam-fikir' ), array( 'rows' => 8, 'allow_empty' => true ) ),
                'error_404_button'  => $f( 'text', __( 'Button', 'langgam-fikir' ), '', array( 'allow_empty' => true ) ),
                'error_404_suggestions_heading' => $f( 'text', __( 'Suggested books heading', 'langgam-fikir' ), __( 'Shows the three latest books under the message. Leave empty to hide them.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
                '_h_com' => $f( 'heading', __( 'Comments', 'langgam-fikir' ) ),
                'label_comments_one' => $f( 'text', __( 'Comments title: one comment', 'langgam-fikir' ), 'Use {title} for the article title.' ),
                'label_comments_many' => $f( 'text', __( 'Comments title: several', 'langgam-fikir' ), 'Use {count} for the number and {title} for the article title.' ),
                'label_comments_closed' => $f( 'text', __( 'Comments closed', 'langgam-fikir' ) ),
            ),
        ),
        'site'         => array(
            'label'  => __( 'Footer and pages', 'langgam-fikir' ),
            'fields' => array(
                'about_page_id'        => $f( 'page', __( 'About page', 'langgam-fikir' ), $page_help ),
                'contact_page_id'      => $f( 'page', __( 'Contact page', 'langgam-fikir' ), __( 'The page that shows the contact form. Book "Order" buttons link here.', 'langgam-fikir' ) ),
                'terms_page_id'        => $f( 'page', __( 'Terms of Service page', 'langgam-fikir' ), __( 'Linked from the footer when no Footer Legal menu is set. The Privacy Policy page comes from Settings > Privacy.', 'langgam-fikir' ) ),
                'footer_legal_heading' => $f( 'text', __( 'Footer legal heading', 'langgam-fikir' ) ),
                'footer_rights'        => $f( 'text', __( 'Copyright suffix', 'langgam-fikir' ), __( 'Shown after "© year site name."', 'langgam-fikir' ) ),
                'footer_company_info'  => $f( 'text', __( 'Footer company line', 'langgam-fikir' ), __( 'Use {site} for the site name.', 'langgam-fikir' ), array( 'allow_empty' => true ) ),
            ),
        ),
    );

    // Defaults come from one translatable map.
    $defaults = langgam_fikir_get_defaults();
    foreach ( $tabs as $slug => $tab ) {
        foreach ( $tab['fields'] as $key => $field ) {
            if ( 'heading' === $field['type'] ) {
                continue;
            }
            $tabs[ $slug ]['fields'][ $key ]['default'] = array_key_exists( $key, $defaults ) ? $defaults[ $key ] : ( in_array( $field['type'], array( 'image', 'page' ), true ) ? 0 : '' );
        }
    }

    $cache = $tabs;

    return $cache;
}
