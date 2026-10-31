<?php
/**
 * Theme Options Admin Page
 *
 * All theme configuration lives here under Appearance > Theme Options.
 * Settings are stored via the WordPress Options API as a single option array.
 *
 * @package Langgam_Fikir
 * @since 1.9.7
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the option and the admin page
 */
function langgam_fikir_register_options() {
    register_setting(
        'langgam_fikir_options_group',
        'langgam_fikir_options',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'langgam_fikir_sanitize_options',
            'default'           => langgam_fikir_option_defaults(),
        )
    );
}
add_action( 'admin_init', 'langgam_fikir_register_options' );

/**
 * Sanitize all options on save
 */
function langgam_fikir_sanitize_options( $input ) {
    $defaults  = langgam_fikir_option_defaults();
    $sanitized = array();

    // Text fields
    $text_keys = array(
        'hero_text', 'footer_business_name', 'footer_address',
        'footer_phone', 'legal_entity_name', 'registration_number',
        'about_badge_number', 'about_badge_label', 'showcase_heading',
    );
    foreach ( $text_keys as $key ) {
        $sanitized[ $key ] = isset( $input[ $key ] )
            ? sanitize_text_field( wp_unslash( $input[ $key ] ) )
            : $defaults[ $key ];
    }

    // Email fields
    $email_keys = array( 'contact_email', 'footer_email' );
    foreach ( $email_keys as $key ) {
        $sanitized[ $key ] = isset( $input[ $key ] )
            ? sanitize_email( wp_unslash( $input[ $key ] ) )
            : $defaults[ $key ];
    }

    // Number fields (clamped 1–12)
    $number_keys = array( 'featured_books_count', 'featured_resources_count' );
    foreach ( $number_keys as $key ) {
        $val = isset( $input[ $key ] ) ? absint( $input[ $key ] ) : $defaults[ $key ];
        $sanitized[ $key ] = max( 1, min( 12, $val ) );
    }

    // Featured showcase
    $sanitized['showcase_enabled'] = ! empty( $input['showcase_enabled'] ) ? 1 : 0;

    $showcase_count = isset( $input['showcase_count'] ) ? absint( $input['showcase_count'] ) : $defaults['showcase_count'];
    $sanitized['showcase_count'] = max( 2, min( 9, $showcase_count ) );
    $sanitized['showcase_autoplay'] = ! empty( $input['showcase_autoplay'] ) ? 1 : 0;

    $showcase_scope = isset( $input['showcase_scope'] ) ? sanitize_key( $input['showcase_scope'] ) : $defaults['showcase_scope'];
    $sanitized['showcase_scope'] = in_array( $showcase_scope, array( 'all', 'available', 'pipeline' ), true )
        ? $showcase_scope
        : $defaults['showcase_scope'];

    // WebMCP agent tools
    $sanitized['webmcp_enabled']            = ! empty( $input['webmcp_enabled'] ) ? 1 : 0;
    $sanitized['webmcp_contact_form']       = ! empty( $input['webmcp_contact_form'] ) ? 1 : 0;
    $sanitized['webmcp_origin_trial_token'] = isset( $input['webmcp_origin_trial_token'] )
        ? preg_replace( '/[^A-Za-z0-9+\/=_-]/', '', wp_unslash( $input['webmcp_origin_trial_token'] ) )
        : '';

    // Attachment ID fields
    $sanitized['hero_background_image'] = isset( $input['hero_background_image'] )
        ? absint( $input['hero_background_image'] )
        : '';

    // URL fields (social links)
    $url_keys = array( 'social_facebook', 'social_instagram', 'social_twitter', 'social_whatsapp' );
    foreach ( $url_keys as $key ) {
        $sanitized[ $key ] = isset( $input[ $key ] )
            ? esc_url_raw( wp_unslash( $input[ $key ] ) )
            : $defaults[ $key ];
    }

    // Default meta description
    $sanitized['default_meta_description'] = isset( $input['default_meta_description'] )
        ? sanitize_text_field( wp_unslash( $input['default_meta_description'] ) )
        : $defaults['default_meta_description'];

    return $sanitized;
}

/**
 * Add the menu page under Appearance
 */
function langgam_fikir_add_options_page() {
    add_theme_page(
        __( 'Theme Options', 'langgam-fikir' ),
        __( 'Theme Options', 'langgam-fikir' ),
        'edit_theme_options',
        'langgam-fikir-options',
        'langgam_fikir_render_options_page'
    );
}
add_action( 'admin_menu', 'langgam_fikir_add_options_page' );

/**
 * Enqueue admin assets for the Theme Options page only
 */
function langgam_fikir_options_assets( $hook ) {
    if ( 'appearance_page_langgam-fikir-options' !== $hook ) {
        return;
    }

    wp_enqueue_style(
        'langgam-fikir-theme-options',
        LANGGAM_FIKIR_URI . '/assets/css/theme-options.css',
        array(),
        LANGGAM_FIKIR_VERSION
    );

    wp_enqueue_media();

    wp_enqueue_script(
        'langgam-fikir-theme-options',
        LANGGAM_FIKIR_URI . '/assets/js/theme-options.js',
        array(),
        LANGGAM_FIKIR_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'langgam_fikir_options_assets' );

/**
 * Render the Theme Options page
 */
function langgam_fikir_render_options_page() {
    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'homepage';
    $tabs = array(
        'homepage' => array( 'label' => __( 'Homepage', 'langgam-fikir' ),        'icon' => 'dashicons-admin-home' ),
        'contact'  => array( 'label' => __( 'Contact & Footer', 'langgam-fikir' ), 'icon' => 'dashicons-phone' ),
        'legal'    => array( 'label' => __( 'Legal Entity', 'langgam-fikir' ),     'icon' => 'dashicons-shield' ),
        'social'   => array( 'label' => __( 'Social & SEO', 'langgam-fikir' ),    'icon' => 'dashicons-share' ),
    );
    ?>
    <div class="wrap lf-options-wrap">
        <h1 class="lf-options-title">
            <span class="dashicons dashicons-book lf-options-icon"></span>
            <?php _e( 'Langgam Fikir — Theme Options', 'langgam-fikir' ); ?>
            <span class="lf-version">v<?php echo esc_html( LANGGAM_FIKIR_VERSION ); ?></span>
        </h1>

        <?php settings_errors( 'langgam_fikir_options' ); ?>

        <nav class="lf-tabs" role="tablist">
            <?php foreach ( $tabs as $slug => $tab ) : ?>
                <button
                    type="button"
                    class="lf-tab <?php echo esc_attr( $active_tab ) === $slug ? 'lf-tab--active' : ''; ?>"
                    data-tab="<?php echo esc_attr( $slug ); ?>"
                    role="tab"
                    aria-selected="<?php echo esc_attr( $active_tab ) === $slug ? 'true' : 'false'; ?>"
                >
                    <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
                    <?php echo esc_html( $tab['label'] ); ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <form method="post" action="options.php" class="lf-options-form">
            <?php
            settings_fields( 'langgam_fikir_options_group' );
            $opts = wp_parse_args(
                get_option( 'langgam_fikir_options', array() ),
                langgam_fikir_option_defaults()
            );
            ?>
            <input type="hidden" name="_lf_active_tab" id="lf-active-tab" value="<?php echo esc_attr( $active_tab ); ?>">

            <!-- ==================== Homepage Tab ==================== -->
            <div class="lf-panel <?php echo esc_attr( $active_tab ) === 'homepage' ? 'lf-panel--active' : ''; ?>" data-panel="homepage" role="tabpanel">
                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Hero Section', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'The headline text and background image for the homepage hero banner.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_hero_text"><?php _e( 'Hero Text', 'langgam-fikir' ); ?></label>
                        <textarea id="lf_hero_text" name="langgam_fikir_options[hero_text]" rows="3" class="large-text"><?php echo esc_textarea( $opts['hero_text'] ); ?></textarea>
                    </div>

                    <div class="lf-field">
                        <label><?php _e( 'Hero Background Image', 'langgam-fikir' ); ?></label>
                        <input type="hidden" id="lf_hero_bg" name="langgam_fikir_options[hero_background_image]" value="<?php echo esc_attr( $opts['hero_background_image'] ); ?>">
                        <div class="lf-image-field">
                            <button type="button" class="button lf-upload-btn" id="lf-hero-bg-btn"><?php _e( 'Select Image', 'langgam-fikir' ); ?></button>
                            <button type="button" class="button lf-remove-btn" id="lf-hero-bg-remove" <?php echo empty( $opts['hero_background_image'] ) ? 'style="display:none;"' : ''; ?>><?php _e( 'Remove', 'langgam-fikir' ); ?></button>
                        </div>
                        <?php
                        $hero_preview = '';
                        if ( ! empty( $opts['hero_background_image'] ) ) {
                            $hero_preview = wp_get_attachment_image_url( $opts['hero_background_image'], 'medium' );
                        }
                        ?>
                        <div class="lf-image-preview" id="lf-hero-bg-preview" <?php echo $hero_preview ? '' : 'style="display:none;"'; ?>>
                            <img src="<?php echo esc_url( $hero_preview ); ?>" alt="">
                        </div>
                        <p class="lf-help"><?php _e( 'Optional. Adds a parallax background image behind the hero text. Leave empty for solid colour.', 'langgam-fikir' ); ?></p>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Showcase', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'Full-width interactive section on the homepage. Each book is a readable card with its cover, status and a link; the blurred backdrop crossfades to the highlighted book\'s cover. Only books with a featured image appear, and books shown here are excluded from the Latest Publications grid below it.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_showcase_enabled">
                            <input type="checkbox" id="lf_showcase_enabled" name="langgam_fikir_options[showcase_enabled]" value="1" <?php checked( ! empty( $opts['showcase_enabled'] ) ); ?>>
                            <?php _e( 'Enable the Showcase section', 'langgam-fikir' ); ?>
                        </label>
                        <p class="lf-help"><?php _e( 'The section hides itself automatically when fewer than 2 matching books have featured images.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_showcase_heading"><?php _e( 'Section Heading', 'langgam-fikir' ); ?></label>
                        <input type="text" id="lf_showcase_heading" name="langgam_fikir_options[showcase_heading]"
                               value="<?php echo esc_attr( $opts['showcase_heading'] ?? '' ); ?>" class="regular-text">
                        <p class="lf-help"><?php _e( 'Optional. Leave empty for a heading-free section.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_showcase_autoplay">
                            <input type="checkbox" id="lf_showcase_autoplay" name="langgam_fikir_options[showcase_autoplay]" value="1" <?php checked( ! empty( $opts['showcase_autoplay'] ) ); ?>>
                            <?php _e( 'Rotate the highlighted book automatically', 'langgam-fikir' ); ?>
                        </label>
                        <p class="lf-help"><?php _e( 'Moves the highlight (and the cover backdrop) from book to book. Pauses while a visitor hovers or tabs through the cards, and never runs for visitors who prefer reduced motion.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_showcase_count"><?php _e( 'Number of Books', 'langgam-fikir' ); ?></label>
                            <input type="number" id="lf_showcase_count" name="langgam_fikir_options[showcase_count]"
                                   min="2" max="9" step="1" value="<?php echo esc_attr( $opts['showcase_count'] ?? 6 ); ?>">
                            <p class="lf-help"><?php _e( 'Between 2 and 9. Cards sit three to a row: six gives two rows, nine gives three.', 'langgam-fikir' ); ?></p>
                        </div>

                        <div class="lf-field">
                            <label for="lf_showcase_scope"><?php _e( 'Which Books', 'langgam-fikir' ); ?></label>
                            <select id="lf_showcase_scope" name="langgam_fikir_options[showcase_scope]">
                                <option value="all" <?php selected( $opts['showcase_scope'] ?? 'all', 'all' ); ?>><?php _e( 'Latest books (any status)', 'langgam-fikir' ); ?></option>
                                <option value="available" <?php selected( $opts['showcase_scope'] ?? 'all', 'available' ); ?>><?php _e( 'Available only (orderable statuses)', 'langgam-fikir' ); ?></option>
                                <option value="pipeline" <?php selected( $opts['showcase_scope'] ?? 'all', 'pipeline' ); ?>><?php _e( 'Pipeline only (Announced, Coming Soon)', 'langgam-fikir' ); ?></option>
                            </select>
                            <p class="lf-help"><?php _e( 'Filter by lifecycle category. Newest first within the chosen scope.', 'langgam-fikir' ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'About Badge', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'The badge overlaid on the About image on the homepage.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_badge_number"><?php _e( 'Badge Number', 'langgam-fikir' ); ?></label>
                            <input type="text" id="lf_badge_number" name="langgam_fikir_options[about_badge_number]"
                                   value="<?php echo esc_attr( $opts['about_badge_number'] ?? '2+' ); ?>">
                            <p class="lf-help"><?php _e( 'E.g. "2+", "10", "100+".', 'langgam-fikir' ); ?></p>
                        </div>

                        <div class="lf-field">
                            <label for="lf_badge_label"><?php _e( 'Badge Label', 'langgam-fikir' ); ?></label>
                            <input type="text" id="lf_badge_label" name="langgam_fikir_options[about_badge_label]"
                                   value="<?php echo esc_attr( $opts['about_badge_label'] ?? __( 'Years of Excellence', 'langgam-fikir' ) ); ?>">
                            <p class="lf-help"><?php _e( 'E.g. "Years of Excellence", "Titles Published".', 'langgam-fikir' ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Content Counts', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'Control how many items appear in each homepage section.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_books_count"><?php _e( 'Latest Publications', 'langgam-fikir' ); ?></label>
                            <input type="number" id="lf_books_count" name="langgam_fikir_options[featured_books_count]"
                                   min="1" max="12" step="1" value="<?php echo esc_attr( $opts['featured_books_count'] ); ?>">
                            <p class="lf-help"><?php _e( 'Number of books shown (1–12).', 'langgam-fikir' ); ?></p>
                        </div>

                        <div class="lf-field">
                            <label for="lf_resources_count"><?php _e( 'Resources & Insights', 'langgam-fikir' ); ?></label>
                            <input type="number" id="lf_resources_count" name="langgam_fikir_options[featured_resources_count]"
                                   min="1" max="12" step="1" value="<?php echo esc_attr( $opts['featured_resources_count'] ); ?>">
                            <p class="lf-help"><?php _e( 'Number of resource posts shown (1–12).', 'langgam-fikir' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Contact & Footer Tab ==================== -->
            <div class="lf-panel <?php echo esc_attr( $active_tab ) === 'contact' ? 'lf-panel--active' : ''; ?>" data-panel="contact" role="tabpanel">
                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Contact Form', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'Where contact form submissions are sent.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_contact_email"><?php _e( 'Recipient Email', 'langgam-fikir' ); ?></label>
                        <input type="email" id="lf_contact_email" name="langgam_fikir_options[contact_email]"
                               value="<?php echo esc_attr( $opts['contact_email'] ); ?>"
                               placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="regular-text">
                        <p class="lf-help"><?php printf( __( 'Leave blank to use the admin email (%s).', 'langgam-fikir' ), esc_html( get_option( 'admin_email' ) ) ); ?></p>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Footer Contact Column', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'Displayed in the footer alongside Legal links and site branding. Also used for Schema.org Organisation markup.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_biz_name"><?php _e( 'Business Name', 'langgam-fikir' ); ?></label>
                        <input type="text" id="lf_biz_name" name="langgam_fikir_options[footer_business_name]"
                               value="<?php echo esc_attr( $opts['footer_business_name'] ); ?>" class="regular-text">
                        <p class="lf-help"><?php _e( 'Used in Schema.org Organisation markup.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_address"><?php _e( 'Address', 'langgam-fikir' ); ?></label>
                        <textarea id="lf_address" name="langgam_fikir_options[footer_address]" rows="2" class="regular-text"><?php echo esc_textarea( $opts['footer_address'] ); ?></textarea>
                    </div>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_phone"><?php _e( 'Phone', 'langgam-fikir' ); ?></label>
                            <input type="text" id="lf_phone" name="langgam_fikir_options[footer_phone]"
                                   value="<?php echo esc_attr( $opts['footer_phone'] ); ?>" placeholder="+60 1234 1234">
                        </div>

                        <div class="lf-field">
                            <label for="lf_email"><?php _e( 'Display Email', 'langgam-fikir' ); ?></label>
                            <input type="email" id="lf_email" name="langgam_fikir_options[footer_email]"
                                   value="<?php echo esc_attr( $opts['footer_email'] ); ?>" placeholder="hello@langgamfikir.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== Legal Entity Tab ==================== -->
            <div class="lf-panel <?php echo esc_attr( $active_tab ) === 'legal' ? 'lf-panel--active' : ''; ?>" data-panel="legal" role="tabpanel">
                <div class="lf-card">
                    <h2 class="lf-card-title"><?php _e( 'Legal Entity', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php _e( 'Shown in the footer bar at the bottom of every page. The legal entity name is distinct from the brand name.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_legal_name"><?php _e( 'Legal Entity Name', 'langgam-fikir' ); ?></label>
                        <input type="text" id="lf_legal_name" name="langgam_fikir_options[legal_entity_name]"
                               value="<?php echo esc_attr( $opts['legal_entity_name'] ); ?>" class="regular-text">
                        <p class="lf-help"><?php _e( 'Your registered business name (e.g. "Langgam Fikir Enterprise").', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_reg_number"><?php _e( 'Registration Number (SSM)', 'langgam-fikir' ); ?></label>
                        <input type="text" id="lf_reg_number" name="langgam_fikir_options[registration_number]"
                               value="<?php echo esc_attr( $opts['registration_number'] ); ?>"
                               placeholder="202503137729" class="regular-text">
                    </div>

                    <div class="lf-preview">
                        <span class="lf-preview-label"><?php _e( 'Footer bar preview:', 'langgam-fikir' ); ?></span>
                        <span class="lf-preview-text" id="lf-legal-preview">
                            <?php
                            if ( $opts['registration_number'] ) {
                                printf(
                                    esc_html__( '%1$s is a registered enterprise in Malaysia (Reg. No.: %2$s).', 'langgam-fikir' ),
                                    esc_html( $opts['legal_entity_name'] ),
                                    esc_html( $opts['registration_number'] )
                                );
                            } else {
                                printf(
                                    esc_html__( '%s is a registered enterprise in Malaysia.', 'langgam-fikir' ),
                                    esc_html( $opts['legal_entity_name'] )
                                );
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ==================== Social & SEO Tab ==================== -->
            <div class="lf-panel <?php echo esc_attr( $active_tab ) === 'social' ? 'lf-panel--active' : ''; ?>" data-panel="social" role="tabpanel">
                <?php
                // HrefMaster integration status card (v2.7.0)
                if ( function_exists( 'langgam_fikir_is_hrefmaster_active' ) && langgam_fikir_is_hrefmaster_active() ) :
                    $hm_version = defined( 'HREFMASTER_VERSION' ) ? HREFMASTER_VERSION : '';
                    ?>
                    <div class="lf-card lf-card-notice lf-card-notice-info">
                        <h2 class="lf-card-title">
                            <?php esc_html_e( 'Hreflang & HTML Lang — managed by HrefMaster', 'langgam-fikir' ); ?>
                            <?php if ( $hm_version ) : ?>
                                <span class="lf-version-tag">v<?php echo esc_html( $hm_version ); ?></span>
                            <?php endif; ?>
                        </h2>
                        <p class="lf-card-desc">
                            <?php esc_html_e( 'HrefMaster is active. Hreflang tag emission and HTML lang attribute control are delegated to it. The theme automatically aligns Schema.org inLanguage and Open Graph og:locale with HrefMaster\'s per-page settings — including the Book and Article schemas.', 'langgam-fikir' ); ?>
                        </p>
                        <p class="lf-card-desc">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=hrefmaster' ) ); ?>" class="button button-secondary">
                                <?php esc_html_e( 'Open HrefMaster', 'langgam-fikir' ); ?>
                            </a>
                        </p>
                    </div>
                <?php else : ?>
                    <div class="lf-card lf-card-notice lf-card-notice-muted">
                        <h2 class="lf-card-title"><?php esc_html_e( 'Hreflang & HTML Lang', 'langgam-fikir' ); ?></h2>
                        <p class="lf-card-desc">
                            <?php esc_html_e( 'For multilingual sites, install the HrefMaster plugin to add hreflang tags and per-page HTML lang attribute control. The theme will automatically align Schema.org inLanguage and Open Graph og:locale with its settings once it\'s active.', 'langgam-fikir' ); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php esc_html_e( 'Social Links', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php esc_html_e( 'Used in Schema.org sameAs property and available for social sharing buttons.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_social_fb"><?php esc_html_e( 'Facebook URL', 'langgam-fikir' ); ?></label>
                            <input type="url" id="lf_social_fb" name="langgam_fikir_options[social_facebook]"
                                   value="<?php echo esc_attr( $opts['social_facebook'] ?? '' ); ?>"
                                   placeholder="https://facebook.com/langgamfikir">
                        </div>
                        <div class="lf-field">
                            <label for="lf_social_ig"><?php esc_html_e( 'Instagram URL', 'langgam-fikir' ); ?></label>
                            <input type="url" id="lf_social_ig" name="langgam_fikir_options[social_instagram]"
                                   value="<?php echo esc_attr( $opts['social_instagram'] ?? '' ); ?>"
                                   placeholder="https://instagram.com/langgamfikir">
                        </div>
                    </div>

                    <div class="lf-field-row">
                        <div class="lf-field">
                            <label for="lf_social_tw"><?php esc_html_e( 'Twitter / X URL', 'langgam-fikir' ); ?></label>
                            <input type="url" id="lf_social_tw" name="langgam_fikir_options[social_twitter]"
                                   value="<?php echo esc_attr( $opts['social_twitter'] ?? '' ); ?>"
                                   placeholder="https://x.com/langgamfikir">
                        </div>
                        <div class="lf-field">
                            <label for="lf_social_wa"><?php esc_html_e( 'WhatsApp URL', 'langgam-fikir' ); ?></label>
                            <input type="url" id="lf_social_wa" name="langgam_fikir_options[social_whatsapp]"
                                   value="<?php echo esc_attr( $opts['social_whatsapp'] ?? '' ); ?>"
                                   placeholder="https://wa.me/60123456789">
                        </div>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php esc_html_e( 'SEO Defaults', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php esc_html_e( 'Fallback values when pages don\'t have their own excerpt or description.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_meta_desc"><?php esc_html_e( 'Default Meta Description', 'langgam-fikir' ); ?></label>
                        <textarea id="lf_meta_desc" name="langgam_fikir_options[default_meta_description]" rows="2" class="large-text"><?php echo esc_textarea( $opts['default_meta_description'] ?? '' ); ?></textarea>
                        <p class="lf-help"><?php esc_html_e( 'Used when a page has no excerpt. Max 160 characters recommended.', 'langgam-fikir' ); ?></p>
                    </div>
                </div>

                <div class="lf-card">
                    <h2 class="lf-card-title"><?php esc_html_e( 'Agent Tools (WebMCP)', 'langgam-fikir' ); ?></h2>
                    <p class="lf-card-desc"><?php esc_html_e( 'Lets in-browser AI agents search the catalogue and look up books through two read-only tools, and fill in (never send) the contact form. Browsers without WebMCP ignore all of it. As of 2026 the API is behind Origin Trials in Chrome and Edge and supported in Brave Leo and ChatGPT Desktop.', 'langgam-fikir' ); ?></p>

                    <div class="lf-field">
                        <label for="lf_webmcp_enabled">
                            <input type="checkbox" id="lf_webmcp_enabled" name="langgam_fikir_options[webmcp_enabled]" value="1" <?php checked( ! empty( $opts['webmcp_enabled'] ) ); ?>>
                            <?php esc_html_e( 'Enable agent tools', 'langgam-fikir' ); ?>
                        </label>
                        <p class="lf-help"><?php esc_html_e( 'Registers search-publications and get-publication, and exposes a public read-only endpoint at /wp-json/langgam-fikir/v1/publications.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_webmcp_contact_form">
                            <input type="checkbox" id="lf_webmcp_contact_form" name="langgam_fikir_options[webmcp_contact_form]" value="1" <?php checked( ! empty( $opts['webmcp_contact_form'] ) ); ?>>
                            <?php esc_html_e( 'Let agents fill in the contact form', 'langgam-fikir' ); ?>
                        </label>
                        <p class="lf-help"><?php esc_html_e( 'Adds tool attributes to the contact form. Agents can complete the fields but sending always requires the visitor to press the button.', 'langgam-fikir' ); ?></p>
                    </div>

                    <div class="lf-field">
                        <label for="lf_webmcp_origin_trial_token"><?php esc_html_e( 'Origin Trial Token', 'langgam-fikir' ); ?></label>
                        <textarea id="lf_webmcp_origin_trial_token" name="langgam_fikir_options[webmcp_origin_trial_token]" rows="3" class="large-text code"><?php echo esc_textarea( $opts['webmcp_origin_trial_token'] ?? '' ); ?></textarea>
                        <p class="lf-help"><?php esc_html_e( 'Optional. Paste the token from the Chrome or Edge Origin Trials console to test WebMCP in those browsers. Emitted as a meta http-equiv="origin-trial" tag. Tokens are tied to this domain and expire.', 'langgam-fikir' ); ?></p>
                    </div>
                </div>
            </div>

            <div class="lf-submit">
                <?php submit_button( __( 'Save Settings', 'langgam-fikir' ), 'primary', 'submit', false ); ?>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Redirect back to the active tab after save
 */
function langgam_fikir_options_redirect( $location ) {
    if ( isset( $_POST['_lf_active_tab'] ) && strpos( $location, 'options.php' ) !== false ) {
        // After options.php processes, WordPress redirects back — we append the tab
        $tab = sanitize_key( $_POST['_lf_active_tab'] );
        if ( strpos( $location, 'page=langgam-fikir-options' ) !== false ) {
            $location = add_query_arg( 'tab', $tab, $location );
        }
    }
    return $location;
}
add_filter( 'wp_redirect', 'langgam_fikir_options_redirect' );

/**
 * Migrate old theme_mod values to the new options array (one-time)
 */
function langgam_fikir_maybe_migrate_theme_mods() {
    if ( get_option( 'langgam_fikir_migrated' ) ) {
        return;
    }

    $mapping = array(
        'hero_text'               => 'hero_text',
        'featured_books_count'    => 'featured_books_count',
        'featured_resources_count'=> 'featured_resources_count',
        'contact_email'           => 'contact_email',
        'footer_business_name'    => 'footer_business_name',
        'footer_address'          => 'footer_address',
        'footer_phone'            => 'footer_phone',
        'footer_email'            => 'footer_email',
        'legal_entity_name'       => 'legal_entity_name',
        'registration_number'     => 'registration_number',
    );

    $defaults = langgam_fikir_option_defaults();
    $existing = get_option( 'langgam_fikir_options', array() );
    $migrated = wp_parse_args( $existing, $defaults );

    foreach ( $mapping as $mod_key => $opt_key ) {
        $val = get_theme_mod( $mod_key );
        if ( $val && ( ! isset( $existing[ $opt_key ] ) || '' === $existing[ $opt_key ] ) ) {
            $migrated[ $opt_key ] = $val;
        }
    }

    update_option( 'langgam_fikir_options', $migrated );
    update_option( 'langgam_fikir_migrated', '1' );
}
add_action( 'admin_init', 'langgam_fikir_maybe_migrate_theme_mods' );
