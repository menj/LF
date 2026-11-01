=== Langgam Fikir ===
Contributors: MENJ
Tags: books, publishing, minimalist, clean, custom-colors, custom-menu, featured-images, threaded-comments, translation-ready
Requires at least: 6.7
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.11.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A minimalist, modernist WordPress child theme for book publishers, built on Twenty Twenty-Five.

== Description ==

Langgam Fikir is a professionally designed child theme of WordPress Twenty Twenty-Five, specifically crafted for independent book publishers. The theme features elegant typography, a warm earthy color palette, and a sophisticated minimalist design approach.

= Key Features =

* **Custom Book Post Type** - Manage books with comprehensive metadata including author, translator, editor, publisher, ISBN, price, and availability status
* **Ten Book Statuses** - Announced, Coming Soon, Pre-Order, New Release, Published, Limited Edition, Digital Only, Sold Out, Reprinting, Out of Print
* **Custom Templates** - Homepage, About, Contact, Privacy Policy, Blog Archive, Book Archive, and Single Book pages
* **Contact Form** - Built-in functional contact form with email notifications
* **Schema.org Markup** - Complete JSON-LD structured data for books and organization for enhanced SEO
* **WooCommerce Ready** - Optional e-commerce integration support
* **Responsive Design** - Mobile-first approach with optimized layouts for all devices
* **Theme Options Page** - Tabbed settings interface under Appearance > Theme Options for full control over homepage, contact, footer, and legal entity settings
* **Showcase** - Interactive homepage section: hover a book title and the full-width background crossfades to its cover. Showcased books never repeat in the Latest Publications grid
* **Agent Tools (WebMCP)** - In-browser AI agents can search the catalogue and look up books through read-only tools, and fill in (never send) the contact form; a public read-only REST endpoint backs it
* **Professional Typography** - Crimson Text for headings, Inter for body text
* **Configurable Colors** - CSS custom properties system for easy theming

= Perfect For =

* Independent book publishers
* Literary organizations
* Academic presses
* Small publishing houses
* Author portfolios
* Literary magazines

= Design Philosophy =

Langgam Fikir embraces modernist, minimalist design principles with:
* Clean, uncluttered layouts
* Warm, earthy color palette (browns, golds, cream)
* Generous whitespace
* Refined typography with restrained font weights
* Subtle transitions and hover states
* Focus on content and readability

= Technical Highlights =

* Child theme architecture (safe parent theme updates)
* Proper asset separation (CSS in /css/, JS in /js/)
* WordPress Coding Standards compliant
* Accessibility-ready with ARIA attributes
* Performance optimized with lazy loading
* Translation ready (text domain: langgam-fikir)
* Security-conscious with nonce verification and input sanitization

== Installation ==

= Prerequisites =

The parent theme **Twenty Twenty-Five** must be installed first. Twenty Twenty-Five comes pre-installed with WordPress 6.7+.

= Installation Steps =

1. Install Twenty Twenty-Five (if not already installed):
   * Go to Appearance > Themes > Add New
   * Search for "Twenty Twenty-Five"
   * Click Install
   * Do NOT activate the parent theme

2. Install Langgam Fikir child theme:
   * Go to Appearance > Themes > Add New
   * Click Upload Theme
   * Choose the LF.zip file
   * Click Install Now
   * Click Activate

3. **CRITICAL:** Flush permalinks:
   * Go to Settings > Permalinks
   * Click Save Changes (without changing anything)
   * This fixes custom post type URL routing

= Quick Setup =

1. Create navigation menus (Appearance > Menus)
2. Set homepage as static page (Settings > Reading)
3. Create required pages: Home, About, Contact, Privacy Policy
4. Add your first book (Books > Add New)
5. Configure settings (Appearance > Theme Options)

== Frequently Asked Questions ==

= Do I need the parent theme installed? =

Yes, Twenty Twenty-Five must be installed. The child theme inherits core functionality from the parent theme while adding custom features.

= Why are my book URLs showing 404 errors? =

Go to Settings > Permalinks and click Save Changes. This flushes the rewrite rules and fixes custom post type URLs.

= Can I use this theme without WooCommerce? =

Absolutely! WooCommerce support is optional. The theme has built-in book management and contact forms without needing e-commerce.

= How do I change the color scheme? =

Edit /css/main.css and modify the CSS custom properties in the :root section. All colors are defined as variables for easy customization.

= PageSpeed says my CSS is render-blocking and nothing is cached. Is that the theme? =

Mostly not. The theme loads its JavaScript in the footer, uses no jQuery on the front end, loads page-specific styles only where needed, and versions every asset so it can be cached for a year. Compression and cache headers are set by your web server, not the theme. See the Server & Performance section of README.md for a ready .htaccess block, or use the LiteSpeed Cache plugin on LiteSpeed hosting. Very large PNG uploads (logos, covers) also show up in these audits; keep a logo under 20 KiB and upload covers as JPEG or AVIF.

= Can I add more book metadata fields? =

Yes! The theme is developer-friendly. Add custom fields in functions.php following the existing patterns for author, translator, editor, etc.

= Does this theme support Gutenberg? =

Yes, the theme is fully compatible with the block editor (Gutenberg) and includes support for WordPress Full Site Editing features.

= Is the theme translation ready? =

Yes! The theme uses proper internationalization functions and the 'langgam-fikir' text domain. POT file can be generated for translations.

= How do I customize the homepage featured books count? =

Go to Appearance > Theme Options > Homepage. You can set the number of featured publications (1-12) and resources (1-12), as well as the hero text.

== Screenshots ==

1. Homepage - Hero section with featured publications
2. Single Book Page - Complete book information with metadata
3. Books Archive - Grid layout of published books
4. About Page - Split layout with company information
5. Contact Page - Functional contact form
6. Blog/Resources - Featured articles with carousel
7. Mobile Navigation - Responsive hamburger menu
8. Admin - Book edit screen with metadata fields

== Changelog ==

= 2.11.0 (October 2026) =
* NEW: Theme Options > Design: four light palettes with custom colours, five-typeface font roles (Kagnue, Crimson Text, Sabon Next LT, Inter, Ferments), corner style, parallax setting
* NEW: Hero photo choice (four bundled Pexels photographs, random or none)
* NEW: Editable text for the homepage, About, Contact, footer and 404 pages, plus a quirky animated 404
* NEW: Fuller book information (original title, translated from, edition, series, format, pages, size) with category and topic chips on the book page and richer Schema.org
* NEW: OCLC number on books (links to WorldCat, Schema.org identifier)
* NEW: Social icons for profiles (footer, Contact page) and share buttons on books, articles and pages, with a theme-matched hover effect; more networks in Theme Options > Social & SEO
* NEW: Category, topic and sort filters on the Publications archive; a sectioned Book Details editor box and Author/Status/Year columns in the Books list
* CHANGED: Showcase is a readable grid of the theme's own book cards with a parallax cover backdrop (two rows by default)
* CHANGED: Rounder corners by default (Sharp restores the previous look); stylesheet colours are variables
* FIXED: Horizontal scroll on phones before the About section was revealed

= 2.10.0 (September 2026) =
* NEW: WebMCP agent tools — search-publications and get-publication registered in supporting browsers (read-only), backed by a public REST endpoint at /wp-json/langgam-fikir/v1/publications
* NEW: Contact form annotated as a declarative WebMCP tool; agents can fill it, visitors send it; spam honeypot cleared on agent-invoked submissions
* NEW: Theme Options > Social & SEO > Agent Tools card: enable toggle, contact-form toggle, Origin Trial token
* Progressive enhancement: browsers without WebMCP see no change

= 2.9.9 (September 2026) =
* CHANGED: Book cards show the status once (cover badge only); the footer label is removed
* NEW: 20-word excerpt on book cards, between subtitle and author/year
* FIXED: Duplicate footer CSS introduced in 2.9.7 removed; the long-standing rules remain the single definition

= 2.9.8 (September 2026) =
* FIXED: Mobile menu button now has an accessible name (aria-label="Menu")
* FIXED: Homepage About image, the LCP element on mobile, is no longer lazy-loaded and carries fetchpriority="high"; preconnect added for the fallback image origin
* CHANGED: Custom logo no longer receives fetchpriority="high"

= 2.9.7 (September 2026) =
* FIXED: Book card footer had no styles — prices and status labels rendered as plain text and Learn More buttons sat at uneven heights across a row. Footer is now pinned to the card bottom with a styled price slot

= 2.9.6 (September 2026) =
* FIXED: Showcase backgrounds for covers lacking the book-showcase size fell back to the full original upload; the fallback is now capped at the large size
* CHANGED: Showcase background images load lazily when the section approaches the viewport
* DOCS: Second PageSpeed baseline recorded; roadmap item added for splitting main.css by context

= 2.9.5 (September 2026) =
* FIXED: additional.css did not load on the Contact, About, and Privacy Policy pages when their slugs differed from the literal defaults; the stylesheet is now enqueued by assigned page template
* FIXED: Contact form honeypot ("Website" field) was visible on affected pages; its hiding rule now lives in main.css and applies everywhere

= 2.9.4 (September 2026) =
* FIXED: "Learn More", "Read More", and "VIEW MORE" links now carry visually hidden suffixes so each has a unique accessible name (e.g. "Learn More about {book title}"); visible text unchanged
* NOTE: The "no main landmark" audit finding was fixed by the homepage <main> wrapper in 2.9.3

= 2.9.3 (September 2026) =
* FIXED: Badge label and section subtitle contrast — both moved from the muted text colour to the secondary text colour to pass WCAG AA
* FIXED: Homepage skip link now has a target — front-page.php wrapped in <main id="primary"> like every other template
* FIXED: All <main id="primary"> wrappers carry tabindex="-1" so the skip-link target is programmatically focusable
* DOCS: Phase 8 baseline gains the main-thread and accessibility audit findings; ssot palette table reconciled with main.css

= 2.9.2 (September 2026) =
* DOCS: Server & Performance section in README.md (compression + caching .htaccess block, image guidance, optional jquery-migrate mu-plugin)
* DOCS: Performance contract recorded in ssot.md; Phase 8 roadmap gains the September 2026 PageSpeed baseline and two items (stylesheet consolidation, LCP preload)
* DOCS: New FAQ entry on PageSpeed render-blocking and caching findings
* No code changes

= 2.9.1 (June 2026) =
* CHANGED: "Featured Showcase" renamed to "Showcase"; "Featured Publications" renamed to "Latest Publications" (option keys unchanged, saved settings carry over)
* CHANGED: Showcase default count is now 3 (single row); range stays 2–6
* CHANGED: Latest Publications grid excludes showcased books — no title appears in both homepage sections
* CHANGED: Showcase grid adapts columns to count (2 books render as 2 clean columns)
* NEW: langgam_fikir_get_showcase_items() and langgam_fikir_showcase_book_ids() helpers; the Showcase query now lives in inc/template-functions.php
* CHANGED: template-parts/content/featured-showcase.php renamed to template-parts/content/showcase.php

= 2.9.0 (June 2026) =
* NEW: Featured Showcase — full-width interactive homepage section; hover (or tab to) a book title and the background crossfades to that book's cover
* NEW: Featured Showcase card on Theme Options > Homepage — enable toggle, optional heading, book count (2–6), lifecycle scope (latest / available only / pipeline only)
* NEW: book-showcase image size (1600×900 crop) for the full-bleed backgrounds
* NEW: assets/css/showcase.css and assets/js/showcase.js, loaded on the front page only while the section is enabled
* NEW: template-parts/content/featured-showcase.php — self-guarding; renders nothing when disabled or when fewer than 2 matching books have featured images
* Keyboard focus drives the effect like hover; prefers-reduced-motion collapses the crossfade; touch devices just navigate

= 2.8.1 (April 2026) =
* NEW: ssot.md — Single Source of Truth document at theme root, canonical reference for brand identity, design tokens, architecture, conventions, plugin contracts, hard rules
* Documentation set now exactly 5 files: ssot.md, CHANGELOG.md, UPGRADING.md, readme.txt, README.md
* No code changes in this release

= 2.8.0 (April 2026) =
* NEW: Book Language meta box field — language picker dropdown with 24 BCP 47 choices, stored as _book_language post meta
* NEW: Visible Language row on single book page (book-meta-grid) — shows human-readable language name with proper lang attribute
* NEW: langgam_fikir_book_language_choices() helper (filterable), langgam_fikir_format_language_name() formatter, langgam_fikir_book_has_explicit_language() presence check
* CHANGED: Resolution chain extended with theme's _book_language as third-tier fallback before site locale
* IMPROVED: Theme is now fully functional without HrefMaster — book language can be declared via native meta box
* IMPROVED: HrefMaster still takes precedence when both are set; new theme meta is a fallback, not a replacement

= 2.7.0 (April 2026) =
* NEW: HrefMaster plugin integration — auto-detects via HREFMASTER_VERSION constant
* NEW: inc/hrefmaster-integration.php — dedicated integration module with detection, lang resolver, locale switcher, admin notice
* NEW: Schema.org inLanguage on WebSite, Article, WebPage, AboutPage, ContactPage, and Book schemas — pulls from HrefMaster per-page setting when active
* NEW: Open Graph og:locale aligned with HrefMaster per-page lang
* NEW: Theme Options Social & SEO tab — status card showing HrefMaster integration state with "Open HrefMaster" link
* NEW: Per-page locale switching for theme strings on singular pages with HrefMaster lang override (when matching .mo file ships)
* CHANGED: Orphaned hreflang scaffolding moved from template-functions.php into the dedicated integration module

= 2.6.1 (April 2026) =
* FIX: about_badge_label default wrapped in __() for proper translation
* FIX: .pot regenerated from current source (260 → 319 strings, picking up v2.4.0–v2.6.0 additions)
* FIX: .pot extraction now uses correct WordPress keyword spec (textdomain no longer treated as msgctxt)
* FIX: ms_MY .po and .mo merged with new strings via msgmerge, preserving existing translations
* VERIFIED: All source strings in en_US; brand identifiers (Langgam Fikir Enterprise, tagline) intentionally untranslated

= 2.6.0 (April 2026) =
* NEW: Book Reviews meta box — repeatable entries with reviewer, rating (1-5), optional headline, body, date
* NEW: Review + aggregateRating properties on Book JSON-LD — star-rating rich results in Google
* NEW: Visible reviews section on single book page with aggregate score and individual review cards
* NEW: Inline aggregate rating displayed under book subtitle (links to reviews section)
* NEW: Auto-TOC for blog posts longer than 1200 words — anchor-links from H2 headings
* NEW: Per-post Search Engine Visibility toggle (sends noindex/nofollow when checked)
* NEW: Star rating widget helper langgam_fikir_render_stars() with fractional/half-star support
* IMPROVED: New template part template-parts/content/reviews-section.php
* SEO/AEO score lift: 88/100 → 92/100

= 2.5.0 (April 2026) =
* NEW: FAQ meta box on book and page post types — repeatable Q/A pairs with drag-to-reorder
* NEW: FAQPage JSON-LD output — Google rich-result eligibility for FAQ snippets
* NEW: Visible FAQ accordion section paired with the schema (aria-expanded, keyboard support)
* NEW: ItemList JSON-LD on /publications/ archive and book taxonomy archives
* IMPROVED: H2 sectioning on single book pages — "About the Book" + visually-hidden Details / Order / Share headings
* IMPROVED: Admin JS now loads on both page and book screens (FAQ meta box needs both)
* SEO/AEO score lift: 80/100 → 88/100

= 2.4.0 (April 2026) =
* FIX (CRITICAL): Double-H1 conflict on front page resolved — site-title demoted from h1 to p, hero h1 is now sole primary heading
* NEW: Visible breadcrumb component above H1 on every inner page (mirrors existing BreadcrumbList JSON-LD)
* NEW: Author bio template part after the_content() on single.php with avatar, archive link, last-updated date
* NEW: Last-updated date shown when dateModified > datePublished + 1 day
* IMPROVED: Resource card category is now a link to category archive (was a span) — adds internal-link equity
* IMPROVED: home.php has its own H1 (visually hidden) for accessibility
* SEO/AEO score lift: 69/100 → 80/100

= 2.3.0 (April 2026) =
* NEW: Price on book cards — book-card.php shows RM price for orderable books, muted status label otherwise; flex column footer alignment
* NEW: Related books section on single book page — up to 4 books from same book-category taxonomy, random order
* NEW: Social sharing bar on single book page — WhatsApp, Facebook, X (Twitter) + copy-link button with Clipboard API
* NEW: Full social links on Contact page — reads all four social URLs from Theme Options; per-page Instagram override retained
* NEW: Configurable homepage badge — Theme Options fields about_badge_number and about_badge_label replace hardcoded "Years of Excellence" string
* IMPROVED: Theme Options Homepage tab — new About Badge card with Number and Label fields
* IMPROVED: assets/js/main.js — initShareCopyLink() helper added with Clipboard API + execCommand fallback
* IMPROVED: assets/css/main.css — new sections for Book Card Footer, Book Share Bar, Related Books, Contact Social Icons Row

= 2.2.1 (April 2026) =
* FIX: About page logo upload chain fully unified on attachment ID pattern (was broken across save/render/JS/template)
* FIX: fonts.css @font-face paths now correctly resolve to /assets/fonts/ directory
* FIX: Removed hardcoded /wp-content/uploads/ logo path from page-about.php
* FIX: Removed duplicate WooCommerce add_theme_support from setup.php

= 2.2 (April 2026) =
* Removed empty customizer.php stub — all settings managed via Theme Options
* Synced with upstream v2.1.0.1 and v2.1.1-patched releases
* Tested up to bumped from 6.7 to 6.9

= 2.1.0 (April 2026) =
* NEW: Complete Schema.org system — BreadcrumbList, Article, WebSite+SearchAction, Organization, WebPage/AboutPage/ContactPage
* NEW: Speakability schema on book synopses and blog articles — enables Google Assistant voice reading
* NEW: Open Graph meta tags — book covers show in WhatsApp/Facebook/Twitter previews (og:type=book with ISBN)
* NEW: Twitter Card meta (summary_large_image) across all content types
* NEW: Auto-generated meta descriptions — book pages use "Author — Subtitle" format
* NEW: Font preload hints for critical WOFF2 files (eliminates FOUT)
* NEW: Skip-to-content accessibility link
* NEW: Social & SEO tab in Theme Options (Facebook, Instagram, Twitter/X, WhatsApp URLs, default meta description)
* FIX: Site title H1 now only on front page — inner pages use <p> (heading hierarchy fix)
* FIX: Footer site title changed from <h2> to <p>
* Added inc/schema.php (~450 lines) with full structured data and social meta system

= 2.0.1 (April 2026) =
* ARCHITECTURE: Extracted book-card.php template part — single source for all book card rendering
* ARCHITECTURE: Extracted resource-card.php template part — unified across homepage, blog, related articles
* Replaced duplicated markup in front-page.php, archive-book.php, 404.php, home.php, single.php with get_template_part()
* NEW: taxonomy-book-category.php — dedicated archive for book categories with staggered grid
* NEW: taxonomy-book-tag.php — dedicated archive for book tags
* Resource cards now include category badge, date, and read-more link consistently
* Added .resource-card-footer CSS with date/link layout

= 2.0.0 (April 2026) =
* NEW: 404 page template — branded design with search, book suggestions, scroll animations
* NEW: Search results template — type badges (Book/Article/Page), thumbnails, no-results state
* CRITICAL FIX: Contact form action mismatch — forms were silently failing, all emails lost (SEC-01)
* SECURITY: Email From header now uses site domain, visitor email in Reply-To only (SEC-02)
* SECURITY: Contact form spam protection — honeypot, timestamp check, IP rate limiting (SEC-03)
* SECURITY: Escape audit across header.php, page-privacy.php, single-book.php, page-contact.php (SEC-04)
* SECURITY: Self-hosted Google Fonts via /assets/fonts/ — GDPR compliant (SEC-05)
* SECURITY: Theme Options tab parameter escaped (SEC-07)
* COMPLIANCE: All inline styles removed from PHP templates
* COMPLIANCE: Recommended server security headers documented in README (SEC-10)
* COMPLIANCE: Generated .pot translation file with 233 strings in /languages/
* Added /assets/css/fonts.css, /assets/fonts/ with download instructions

= 1.9.9 (April 2026) =
* NEW: 10-status book system — added "Announced" status for pipeline titles
* NEW: Animated scroll-reveal system across all homepage sections
* NEW: Parallax hero with configurable background image via Theme Options
* NEW: Hero text entrance animation (fade-up with staggered timing)
* NEW: Book card hover enhancements — deeper lift, shadow bloom, cover image zoom
* NEW: Badge shimmer animation on New Release and Pre-Order badges
* NEW: Section header golden underline animation on scroll reveal
* NEW: Button ripple hover effect
* NEW: Staggered card entrance — books and resources cascade into view
* NEW: About snippet image hover zoom and badge scale effect
* NEW: Footer fade-in animation on scroll
* NEW: Hero Background Image setting in Theme Options (Homepage tab)
* Full prefers-reduced-motion support — all animations disabled for accessibility
* Announced status colour: warm plum (#8E7193)
* Ten fully distinct badge colours across the complete publishing lifecycle

= 1.9.8 (April 2026) =
* EXPANDED: Book status system from 5 to 9 statuses
* NEW: New Release — for titles in their launch window
* NEW: Limited Edition — signals scarcity for special print runs
* NEW: Digital Only — e-book/PDF available, no physical copies
* NEW: Sold Out — stock exhausted, no confirmed reprint yet
* Status radio buttons grouped by category in admin (Available, Upcoming, Unavailable)
* Added helper functions: langgam_fikir_status_badge(), langgam_fikir_get_status_label(), langgam_fikir_status_is_orderable(), langgam_fikir_status_has_price()
* Schema.org offers updated for all 9 statuses (InStock, LimitedAvailability, OnlineOnly, PreOrder, OutOfStock, Discontinued)
* Status badge rendering refactored to single helper function across all templates
* Admin CSS loads on book edit screens (previously only page screens)
* Nine distinct badge colours with full gradient system

= 1.9.7 (April 2026) =
* NEW: Tabbed Theme Options page (Appearance > Theme Options) replacing Customizer for all custom settings
* Three tabs: Homepage, Contact & Footer, Legal Entity
* All settings use WordPress Options API (single langgam_fikir_options array)
* Customizer stripped to native WordPress controls only (Site Identity, Menus)
* Restored footer Contact column with address, phone, email, SVG icons, Schema.org markup
* Footer bar reads legal entity name and registration number from Theme Options
* Added hero text, contact form recipient email to Theme Options
* Automatic one-time migration of existing theme_mod values
* Live preview for legal entity footer bar text
* New helper: langgam_fikir_get_option() for unified option access
* New assets: assets/css/theme-options.css, assets/js/theme-options.js

= 1.9.6 (February 2026) =
* STRUCTURE: Decomposed monolithic functions.php into /inc/ modules
* Modular architecture: setup, enqueue, post-types, meta-boxes, customizer, template-functions, template-hooks
* Conditional loading for admin and WooCommerce contexts
* Added CHANGELOG.md, UPGRADING.md documentation
* WordPress.org compliance improvements

= 1.9.5 (February 2026) =
* STRUCTURE: Reorganized assets into proper /assets/ directory
* Moved CSS files from /css/ to /assets/css/ (WordPress standard)
* Moved JS files from /js/ to /assets/js/ (WordPress standard)
* Updated all asset enqueue paths in functions.php
* NAMING: Renamed package to langgam-fikir-[version].zip format
* Improved WordPress.org theme directory standards compliance

= 1.9.4 (February 2026) =
* Documentation improvements - Added readme.txt, CHANGELOG.md, and UPGRADING.md
* Complete WordPress.org theme repository compliance
* Enhanced footer design with three-column grid layout
* Added site logo display in footer branding column
* Added "Navigate" and "Legal" column headings
* Registered new footer-legal menu location for Terms/Privacy links

= 1.9.3 (February 2026) =
* CODE QUALITY: Extracted inline admin CSS to /css/admin.css
* CODE QUALITY: Extracted inline admin JavaScript to /js/admin.js
* Added proper admin asset enqueuing via admin_enqueue_scripts hook
* Admin assets now only load on page edit screens (improved performance)
* Implements WordPress best practices for asset separation
* All CSS now in dedicated /css/ directory
* All JavaScript now in dedicated /js/ directory
* Admin styles and scripts use proper dependency management and cache-busting

= 1.9.2 (February 2026) =
* Unified meta information - categories, date, author, reading time at post header
* Removed redundant author box from bottom
* Beautified tags section with pill-shaped badges and gold hover effect
* Tightened spacing between footer elements
* Cleaned up unused CSS

= 1.9.1 (February 2026) =
* Redesigned single blog post page to match theme design language
* Added page-title-header with gold underline accent
* Added category badges linked to archives
* Custom post navigation with themed hover states
* Added Related Articles section
* Full mobile responsive layout

= 1.9.0 (February 2026) =
* FIX: Block editor headings now match theme styles
* Added PHP content filter to strip inline styles from parent theme
* Filter runs at priority 999 for proper execution order

= 1.8.0 - 1.8.9 (February 2026) =
* Multiple heading style fixes and overrides
* Archive template for categories, tags, and dates
* Additional CSS file for forms and extra pages
* Customizer settings for homepage content counts

= 1.7.0 (February 2026) =
* Separated form and page styles into css/additional.css
* Improved asset organization

= 1.0.0 - 1.6.0 (February 2026) =
* Initial release as Twenty Twenty-Five child theme
* Custom book post type with 5 status system
* Custom page templates (Home, About, Contact, Privacy)
* Contact form implementation
* WooCommerce compatibility
* Schema.org markup
* Responsive design
* Typography and color system implementation

== Upgrade Notice ==

= 2.11.0 =
Design and content options in Theme Options, a card-based Showcase, a hero photo, and a new 404. Hard-refresh after updating; defaults change the look slightly (rounder corners, hero photo, five-font stack) and can be reverted in Theme Options > Design and Homepage.

= 2.10.0 =
Adds WebMCP agent tools and a public read-only catalogue endpoint. On by default, inert without a WebMCP browser. Re-save Permalinks if the endpoint 404s.

= 2.9.9 =
Book cards: status shown once, excerpt added. Markup and CSS only; hard-refresh after updating.

= 2.9.8 =
Mobile fixes: named menu button, faster About-image LCP. Set a featured image on your About page to replace the fallback photo.

= 2.9.7 =
Fixes unstyled prices/status labels and uneven buttons on book cards. CSS only; clear browser cache.

= 2.9.6 =
Showcase performance fix: safe image fallback and lazy-loaded backgrounds. Run Regenerate Thumbnails after updating.

= 2.9.5 =
Fixes missing styles and a visible anti-spam field on the Contact page. Clear browser cache after updating.

= 2.9.4 =
Accessibility: unique screen-reader names for repeated Learn More / Read More / View More links. Markup only.

= 2.9.3 =
Accessibility fixes: AA contrast on the About badge and section subtitles, and a working skip link on the homepage. CSS and markup only. Clear browser cache after updating.

= 2.9.2 =
Documentation only. Adds server-side performance guidance (compression, caching, image weight) to README.md. No theme code changed.

= 2.9.1 =
Renames the homepage sections (Showcase / Latest Publications) and stops them showing the same titles. Settings carry over. If you customised featured-showcase.php, move changes to showcase.php.

= 2.9.0 =
Adds the interactive Featured Showcase to the homepage, on by default. Disable under Appearance > Theme Options > Homepage if unwanted. Regenerate thumbnails for sharp 1600×900 backgrounds.

= 2.8.1 =
Adds ssot.md (Single Source of Truth) at theme root. Documentation only; no code changes.

= 2.8.0 =
Book Language picker in admin + visible Language row on book pages. Works with or without the HrefMaster plugin. Recommended.

= 2.7.0 =
HrefMaster plugin integration: Schema inLanguage and OG locale now align with per-page hreflang settings. Recommended for multilingual sites.

= 2.6.1 =
i18n housekeeping: regenerated .pot, fixed badge label default, merged ms_MY translations. Safe upgrade.

= 2.6.0 =
Catalog Enrichment: book reviews + aggregateRating schema (star-rating rich results), auto-TOC for long posts, per-post robots toggle. Recommended.

= 2.5.0 =
AEO Layer: FAQ schema + accordion, ItemList on book archive, H2 sectioning on book pages. Recommended.

= 2.4.0 =
Critical SEO fix (front-page double-H1) plus authorship & navigation layer (visible breadcrumbs, author bio, last-updated date). Strongly recommended.

= 2.3.0 =
Adds book pricing on cards, related books, social sharing on book pages, configurable homepage badge. Recommended upgrade.

= 2.2.1 =
Fixes broken About page logo upload, incorrect font file paths, and removes hardcoded external URLs. Recommended upgrade.

= 1.9.3 =
Code quality improvements with proper asset separation. No breaking changes. Safe to upgrade.

= 1.9.0 =
Fixes block editor heading styles. Recommended upgrade for users experiencing heading style issues.

= 1.0.0 =
Initial release as child theme. If upgrading from standalone version, please flush permalinks after activation.

== Credits ==

* Parent Theme: Twenty Twenty-Five by WordPress.org
* Child Theme Design: MENJ
* Fonts: Google Fonts (Crimson Text, Inter)
* Icons: WordPress Dashicons

== Support ==

For support, please visit https://langgamfikir.com/contact

== Copyright ==

Langgam Fikir WordPress Theme, Copyright 2025 MENJ
Langgam Fikir is distributed under the terms of the GNU GPL v2 or later

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

Twenty Twenty-Five Theme
Copyright WordPress.org
License: GPLv2 or later
