# Langgam Fikir - Twenty Twenty-Five Child Theme

A child theme of WordPress Twenty Twenty-Five (2025 default theme) with custom book publisher design.

## Theme Information

- **Theme Name:** Langgam Fikir
- **Parent Theme:** Twenty Twenty-Five
- **Version:** 2.10.0
- **Author:** MENJ
- **License:** GPL v2 or later
- **Requires WordPress:** 6.7+
- **Requires PHP:** 7.4+
- **Text Domain:** langgam-fikir

## What is a Child Theme?

A child theme inherits all functionality and styling from its parent theme (Twenty Twenty-Five) while allowing you to make customizations without modifying the parent theme files. This means:

✅ **Safe Updates** - Parent theme can update without losing your customizations
✅ **WordPress Core Integration** - Inherits all Twenty Twenty-Five features
✅ **Stable Foundation** - Built on WordPress default theme
✅ **Custom Design** - Your unique book publisher design on top

## Installation

### Prerequisites

**IMPORTANT:** The parent theme **Twenty Twenty-Five** must be installed first.

### Step 1: Install Parent Theme

Twenty Twenty-Five comes pre-installed with WordPress 6.7+. If you don't have it:

1. Go to **Appearance > Themes**
2. Click **Add New**
3. Search for "Twenty Twenty-Five"
4. Click **Install**
5. **DO NOT ACTIVATE** - just install it

### Step 2: Install Child Theme (LF)

1. Go to **Appearance > Themes > Add New**
2. Click **Upload Theme**
3. Choose `LF.zip`
4. Click **Install Now**
5. Click **Activate**

### Step 3: Fix Permalinks (CRITICAL!)

After activating the child theme:

1. Go to **Settings > Permalinks**
2. Click **Save Changes** (don't change anything, just save)
3. This flushes the rewrite rules and fixes URL issues

**This step fixes the URL override issue you experienced!**

## Quick Setup

1. Install parent theme (Twenty Twenty-Five)
2. Install and activate child theme (LF)
3. **Settings > Permalinks > Save Changes** (fixes URLs)
4. Create navigation menus
5. Set homepage as static page
6. Create required pages (About, Contact, Privacy)
7. Add books via Books > Add New
8. Configure theme via **Appearance > Theme Options**

## Features Inherited from Parent

From Twenty Twenty-Five you get:

✅ WordPress 6.7 compatibility
✅ Block editor (Gutenberg) support
✅ Full Site Editing (FSE) compatibility
✅ Accessibility features
✅ Performance optimizations
✅ Core WordPress functionality

## Custom Features (Child Theme)

Your custom design adds:

✅ **Theme Options Page:** Tabbed settings interface (Homepage, Contact & Footer, Legal Entity)
✅ **Custom Post Type:** Books with metadata
✅ **Custom Templates:** Homepage, About, Contact, Privacy, Resources
✅ **Custom Styling:** Minimalist book publisher design
✅ **Contact Form:** Functional contact page with email notifications
✅ **Book System:** Complete book showcase and single pages
✅ **WooCommerce Support:** Optional e-commerce integration
✅ **Responsive Design:** Mobile-first approach
✅ **Agent Tools (WebMCP):** Read-only catalogue tools and an agent-fillable contact form for in-browser AI agents, backed by a public REST endpoint

## File Structure

```
LF/
├── assets/
│   ├── css/
│   │   ├── admin.css            # Admin meta box styles
│   │   ├── main.css             # Main theme styles
│   │   ├── additional.css       # Forms & extra pages
│   │   ├── showcase.css         # Showcase (front page only)
│   │   └── theme-options.css    # Theme Options page styles
│   └── js/
│       ├── admin.js             # Admin media uploader
│       ├── main.js              # Frontend JavaScript
│       ├── showcase.js          # Showcase (front page only)
│       ├── webmcp.js            # WebMCP tool registration (feature-detected)
│       └── theme-options.js     # Theme Options tab navigation
├── inc/
│   ├── admin/
│   │   ├── meta-boxes.php       # Admin meta box callbacks
│   │   └── theme-options.php    # Theme Options page (tabbed)
│   ├── customizer.php           # Customizer (core WP controls only)
│   ├── enqueue.php              # Asset enqueuing
│   ├── meta-boxes.php           # Meta box registration
│   ├── post-types.php           # Custom post types & taxonomies
│   ├── setup.php                # Theme setup & configuration
│   ├── template-functions.php   # Helper functions & option accessors
│   ├── template-hooks.php       # Hooks and filters
│   └── woocommerce.php          # WooCommerce support (conditional)
├── template-parts/              # Reusable template components
├── languages/                   # Translation files
├── archive.php                  # Category/tag/date archives
├── archive-book.php             # Books archive
├── comments.php                 # Comments override
├── footer.php                   # Footer (3-column + bottom bar)
├── front-page.php               # Homepage
├── functions.php                # Theme bootstrap (~30 lines)
├── header.php                   # Header with navigation
├── home.php                     # Blog/resources archive
├── index.php                    # Default fallback
├── page.php                     # Page template
├── page-about.php               # About template
├── page-contact.php             # Contact template
├── page-privacy.php             # Privacy template
├── screenshot.png               # Theme screenshot
├── single.php                   # Single post
├── single-book.php              # Single book
├── style.css                    # Child theme header
├── readme.txt                   # WordPress.org readme
├── README.md                    # Developer documentation
├── CHANGELOG.md                 # Version history
└── UPGRADING.md                 # Upgrade guide
```

## Server & Performance

The theme keeps its own footprint small: all theme JavaScript loads in the footer, no theme script depends on jQuery on the front end, `additional.css` and the Showcase assets load only where they're used, fonts are self-hosted, and every asset is versioned with `filemtime()`. On the homepage that comes to roughly 86 KiB of CSS/JS before compression and about 20 KiB after.

Two things the theme cannot do for you, because they're response headers on static files: compression and browser caching. A PageSpeed audit of the live site (6 September 2026) found neither active. If your host runs Apache or LiteSpeed, add this to the `.htaccess` in the WordPress root, above the `# BEGIN WordPress` block:

```apache
# BEGIN LF Performance — compression + browser caching
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json image/svg+xml font/woff2
</IfModule>
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/avif "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType text/html "access plus 0 seconds"
</IfModule>
# END LF Performance
```

The one-year TTL is safe because the theme's `filemtime()` versioning changes the query string whenever a file changes. On LiteSpeed hosting, the LiteSpeed Cache plugin achieves the same result and adds brotli, page caching, and CSS/JS minification.

**Images.** The site's image pipeline converts covers to AVIF; the theme only registers the sizes (`book-cover` 400×533, `book-thumbnail` 280×373, `book-showcase` 1600×900). Keep source uploads sane: a logo belongs under 20 KiB (SVG or an optimised PNG), and a cover should be a JPEG or AVIF, not a multi-hundred-KiB PNG. After upgrading past 2.9.0, run Regenerate Thumbnails once so existing covers get the `book-showcase` crop.

**Plugins.** Front-end jQuery and `jquery-migrate` come from plugins (Contact Form 7 in the baseline), not the theme. If nothing on the site relies on deprecated jQuery APIs, this mu-plugin drops `jquery-migrate` (about 14 KiB):

```php
<?php
// wp-content/mu-plugins/lf-no-jquery-migrate.php
add_action( 'wp_default_scripts', function ( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            array( 'jquery-migrate' )
        );
    }
} );
```

**Showcase images.** The Showcase uses the `book-showcase` crop and, since 2.9.6, falls back to the `large` size (never the original upload) for covers that predate it. Its background layers load only when the section nears the viewport. Two habits keep it light: run Regenerate Thumbnails after any theme upgrade that adds an image size, and upload covers as JPEG or AVIF, since a PNG source stays PNG through every crop.

**Speculative loading.** If Lighthouse shows repeated `ERR_TIMED_OUT` on your own URL and failed `robots.txt` / `llms.txt` fetches, the likely cause is prerender requests from a speculative-loading plugin being throttled by the host. Set speculation to conservative or disable it, then re-test.

Theme-side performance work (stylesheet consolidation, context-splitting `main.css`, minification, critical CSS, LCP preload) is scheduled under Phase 8 in `UPGRADING.md`.

## Troubleshooting

### URLs Not Working / 404 Errors / Permalink Issues

**This is the issue you experienced!**

**Solution:**
1. Go to **Settings > Permalinks**
2. Click **Save Changes**
3. This flushes rewrite rules and fixes custom post type URLs

The theme now includes automatic permalink flushing on activation, but you may need to manually flush once after first install.

### URL Conflict with Existing Category

**If you have an existing category at `/books/`:**

The theme now uses `/publications/` for the custom post type to avoid conflicts.

- Your category: `https://yoursite.com/books/` ✅
- Book archive: `https://yoursite.com/publications/` ✅

**After installation:**
1. Flush permalinks (Settings > Permalinks > Save)
2. Update menu links if needed

See `URL-CONFLICT-FIX.txt` for detailed information.

### Parent Theme Not Found Error

**Solution:**
1. Make sure Twenty Twenty-Five is installed
2. Go to **Appearance > Themes**
3. Install Twenty Twenty-Five if missing
4. Don't activate parent - activate child theme only

### Styles Not Loading

**Solution:**
1. Clear browser cache
2. Clear WordPress cache (if using cache plugin)
3. Check that both parent and child themes are properly installed

### Book Pages Return 404

**Solution:** Same as permalink fix above - flush permalinks.

## Why Child Theme Fixes URL Issues

The previous standalone theme was registering custom post types which can override WordPress URL settings. As a child theme:

1. **Proper Rewrite Rules:** The theme now includes `'with_front' => false` in CPT registration
2. **Automatic Flush:** Permalink rules flush automatically on theme activation
3. **Better Integration:** Child themes integrate better with WordPress core
4. **Cleaner Inheritance:** Leverages Twenty Twenty-Five's solid foundation

## Agent Tools (WebMCP)

Since 2.10.0 the theme exposes what it already does to in-browser AI agents via the [WebMCP](https://github.com/webmachinelearning/webmcp) draft. Nothing here changes the page for ordinary visitors.

- **Tools** (registered by `assets/js/webmcp.js` when `document.modelContext` exists): `search-publications` (free text, lifecycle status, category slug, limit) and `get-publication` (by slug). Both are read-only and return the same public data the site renders.
- **Endpoint**: `GET /wp-json/langgam-fikir/v1/publications` and `/publications/{slug}`, public, cached for five minutes. Useful on its own; try `?per_page=3` to confirm it's live.
- **Contact form**: annotated as a declarative tool (`toolname="contact-publisher"`). Agents may fill it; the visitor sends it. There is no `toolautosubmit` by design.
- **Settings**: Appearance → Theme Options → Social & SEO → Agent Tools. Enable toggle, contact-form toggle, and an Origin Trial token field for Chrome 149+ / Edge 150+ while the API is in trials.

Browser support in September 2026: Chrome and Edge Origin Trials, Brave Leo (experimental), ChatGPT Desktop. Firefox and Safari have not shipped it. The declarative form part of the spec is still moving, which is why the contact form is the only declarative use.

## Customization

### Changing Colors

Edit `/assets/css/main.css`:

```css
:root {
    --color-primary: #6B5B4A;
    --color-accent: #C9A961;
}
```

### Changing Fonts

Edit `/assets/css/main.css`:

```css
:root {
    --font-primary: 'Crimson Text', serif;
    --font-secondary: 'Inter', sans-serif;
}
```

## Version History / Changelog

### Version 2.10.0 (September 2026) — WebMCP Agent Tools

- **NEW:** `inc/webmcp.php` + `assets/js/webmcp.js`: `search-publications` and `get-publication` tools (read-only), public REST routes under `langgam-fikir/v1`
- **NEW:** Contact form annotated as a declarative WebMCP tool; honeypot cleared on `agentInvoked` submissions; no autosubmit
- **NEW:** Theme Options → Social & SEO → Agent Tools card (enable, contact-form toggle, Origin Trial token)

### Version 2.9.9 (September 2026) — Book Card Content

- **CHANGED:** Status appears once per card (cover badge); footer status label removed
- **NEW:** 20-word excerpt on book cards
- **FIXED:** 2.9.7's duplicate footer CSS removed; the original footer rules near the end of `main.css` are the single definition (2.9.7's "no CSS" diagnosis was a stale-cache misread)

### Version 2.9.8 (September 2026) — Mobile A11y & LCP

- **FIXED:** `.menu-toggle` had no accessible name; now `aria-label="Menu"`
- **FIXED:** About image (mobile LCP) rendered `loading="lazy"`; now eager with `fetchpriority="high"`, plus a `preconnect` to the fallback image origin
- **CHANGED:** Custom logo no longer takes `fetchpriority="high"` (`langgam_fikir_custom_logo_attributes()`)

### Version 2.9.7 (September 2026) — Book Card Footer

- **FIXED:** `.book-card-footer` and its price/status slot had no CSS; footer now pinned to the card bottom, price in Crimson Text, status label in small caps, buttons level across a row

### Version 2.9.6 (September 2026) — Showcase Performance

- **FIXED:** Showcase covers without a `book-showcase` size fell back to the original upload (one was a 1.7 MB PNG); new `langgam_fikir_showcase_image_url()` caps the fallback at `large`
- **CHANGED:** Showcase background layers now lazy-load via `data-showcase-src` + IntersectionObserver; zero image bytes on initial load for a below-the-fold section
- **DOCS:** Second PageSpeed run recorded (Accessibility 100 confirms 2.9.3/2.9.4); roadmap item 8.9 added

### Version 2.9.5 (September 2026) — Contact Page Fix

- **FIXED:** `additional.css` is now enqueued by assigned page template (`page-contact.php`, `page-about.php`, `page-privacy.php`) instead of literal slugs, so it loads on pages like `contact-langgam-fikir`
- **FIXED:** Honeypot hiding rule moved to `main.css`; the "Website" anti-spam field can no longer appear on any page

### Version 2.9.4 (September 2026) — Accessibility Patch II

- **FIXED:** Repeated "Learn More" / "Read More" / "VIEW MORE" links now have unique accessible names via `.screen-reader-text` suffixes ("Learn More about {book title}", "VIEW MORE publications", etc.); visible labels unchanged
- **NOTE:** The "no main landmark" finding was resolved by the 2.9.3 homepage `<main>` wrapper

### Version 2.9.3 (September 2026) — Accessibility Patch

- **FIXED:** About badge label (3.2:1) and section subtitles (3.6:1) now use `--color-text-light` and pass WCAG AA
- **FIXED:** Homepage skip link had no target; `front-page.php` is now wrapped in `<main id="primary" class="site-main" tabindex="-1">`, and all 15 main wrappers carry `tabindex="-1"`
- **DOCS:** Phase 8 baseline records the main-thread and accessibility findings; `ssot.md` palette table reconciled with `main.css`, with contrast guidance per token

### Version 2.9.2 (September 2026) — Documentation Patch

- **NEW:** Server & Performance section in this README: what the theme guarantees, the `.htaccess` compression and caching block, image sizing guidance, and an optional `jquery-migrate` mu-plugin
- **NEW:** Performance contract recorded in `ssot.md` §5.4; Phase 8 roadmap updated with the 6 September 2026 PageSpeed baseline and two new items (stylesheet consolidation, LCP preload)
- No code changes

### Version 2.9.1 (June 2026) — Showcase & Latest Publications

- **CHANGED:** "Featured Showcase" → "Showcase" and "Featured Publications" → "Latest Publications"; the spotlight and the catalogue feed are now clearly distinct (option keys unchanged)
- **CHANGED:** Showcase defaults to 3 titles (single hairline row); the Latest Publications grid excludes showcased books, so the two sections never repeat a title
- **NEW:** `langgam_fikir_get_showcase_items()` / `langgam_fikir_showcase_book_ids()` helpers; Showcase query moved to `inc/template-functions.php`; part renamed to `template-parts/content/showcase.php`

### Version 2.9.0 (June 2026) — Featured Showcase

- **NEW:** Interactive Featured Showcase on the homepage — hover (or tab to) a book title and the full-width background crossfades to that book's cover
- **NEW:** Featured Showcase settings card on Theme Options → Homepage (enable toggle, optional heading, count 2–6, lifecycle scope)
- **NEW:** `book-showcase` image size (1600×900 crop), `assets/css/showcase.css`, `assets/js/showcase.js`, `template-parts/content/featured-showcase.php`
- Assets load on the front page only while the section is enabled; keyboard focus drives the effect like hover; honours `prefers-reduced-motion`

### Version 2.8.1 (April 2026) — Documentation Patch

- **NEW:** `ssot.md` — Single Source of Truth document at theme root
- Canonical reference for brand identity, design tokens, architecture, conventions, plugin contracts, hard rules, authoritative ownership matrix
- Documentation set now exactly 5 files: `ssot.md`, `CHANGELOG.md`, `UPGRADING.md`, `readme.txt`, `README.md`
- No code changes

### Version 2.8.0 (April 2026) — Book Language Display + HrefMaster Independence

- **NEW:** Book Language meta box field with 24 BCP 47 language choices
- **NEW:** Visible Language row on single book page with proper `lang` attribute
- **NEW:** `langgam_fikir_book_language_choices()`, `langgam_fikir_format_language_name()`, `langgam_fikir_book_has_explicit_language()` helpers
- **CHANGED:** Resolver fallback chain — HrefMaster meta → HrefMaster resolver → theme `_book_language` → site locale
- **IMPROVED:** Theme works fully without HrefMaster; books can declare language via native meta box

### Version 2.7.0 (April 2026) — HrefMaster Integration

- **NEW:** Dedicated `inc/hrefmaster-integration.php` module with detection, page-language resolver, locale switcher, admin notice
- **NEW:** Schema.org `inLanguage` on WebSite, Article, WebPage, AboutPage, ContactPage, and Book schemas
- **NEW:** Open Graph `og:locale` respects HrefMaster per-page lang
- **NEW:** Theme Options status card showing HrefMaster integration state
- **CHANGED:** Orphaned hreflang scaffolding consolidated into the integration module

### Version 2.6.1 (April 2026) — i18n Housekeeping

- **FIX:** `about_badge_label` default wrapped in `__()` for proper translation
- **FIX:** `.pot` regenerated from current source (260 → 319 strings)
- **FIX:** `.pot` extraction keyword spec corrected (no spurious msgctxt entries)
- **FIX:** `ms_MY` `.po`/`.mo` merged with new strings via `msgmerge`
- **VERIFIED:** All source strings in en_US

### Version 2.6.0 (April 2026) — Catalog Enrichment

- **NEW:** Book Reviews meta box (repeatable, drag-to-reorder) with rating, headline, body, date
- **NEW:** Review + aggregateRating properties on Book JSON-LD — Google star-rating rich results
- **NEW:** Visible reviews section + inline aggregate rating below book subtitle
- **NEW:** Auto-TOC for posts >1200 words (anchor-links from H2 headings, filterable threshold)
- **NEW:** Per-post Search Engine Visibility toggle (noindex/nofollow via `wp_robots`)
- **SEO/AEO Score:** 88/100 → 92/100

### Version 2.5.0 (April 2026) — AEO Layer

- **NEW:** FAQ meta box on `book` and `page` CPTs (repeatable Q/A pairs, drag-to-reorder)
- **NEW:** `FAQPage` JSON-LD output paired with visible FAQ accordion
- **NEW:** `ItemList` JSON-LD on `/publications/` archive and book taxonomy archives
- **IMPROVED:** H2 sectioning on `single-book.php` ("About the Book", visually-hidden Details / Order / Share)
- **SEO/AEO Score:** 80/100 → 88/100

### Version 2.4.0 (April 2026) — Authorship & Navigation Layer

- **CRITICAL FIX:** Front-page double-H1 resolved (site-title demoted to `<p>`, hero `<h1>` is canonical primary heading)
- **NEW:** Visible breadcrumb component on all inner pages (mirrors existing JSON-LD logic)
- **NEW:** Author bio template part after `the_content()` on `single.php` with avatar and archive link
- **NEW:** Last-updated date display when `dateModified` exceeds `datePublished` by more than a day
- **IMPROVED:** Resource card category now an internal link to category archive
- **SEO/AEO Score:** 69/100 → 80/100

### Version 2.3.0 (April 2026) — Catalog & Sharing Enhancements

- **NEW:** Price on book cards (`RM X.XX`) for orderable books, muted status label otherwise
- **NEW:** Related books section on single book page (4 books from same `book-category`)
- **NEW:** Social sharing bar on single book page (WhatsApp, Facebook, X, copy-link)
- **NEW:** Full social links on Contact page sourced from Theme Options
- **NEW:** Configurable homepage badge via Theme Options (`about_badge_number`, `about_badge_label`)

### Version 2.2.1 (April 2026) — Bug Fixes

- **FIX:** About page logo upload chain unified on `_about_logo_id` attachment ID pattern
- **FIX:** `fonts.css` `@font-face` paths now correctly resolve to `/assets/fonts/`
- **FIX:** Removed hardcoded `/wp-content/uploads/` logo path from `page-about.php`
- **FIX:** Removed duplicate `add_theme_support('woocommerce')` from `setup.php`

### Version 2.1.0 (April 2026) — Phase 3: SEO & Structured Data
- **NEW:** Complete Schema.org system — BreadcrumbList, Article, WebSite+SearchAction, Organization, AboutPage, ContactPage
- **NEW:** Speakability schema on book synopses and blog articles for voice search
- **NEW:** Open Graph + Twitter Card meta tags — book covers show in social previews
- **NEW:** Auto-generated meta descriptions, font preloading, skip-to-content link
- **NEW:** Social & SEO tab in Theme Options with social links and default meta description
- **FIX:** Heading hierarchy — H1 only on front page, footer H2 → `<p>`
- Added `inc/schema.php` (~450 lines)

### Version 2.0.1 (April 2026) — Phase 2: DRY & Architecture
- **ARCHITECTURE:** Extracted `book-card.php` and `resource-card.php` template parts
- Replaced ~200 lines of duplicated markup across 5 templates with `get_template_part()`
- **NEW:** `taxonomy-book-category.php` and `taxonomy-book-tag.php` archive templates
- Resource cards now consistently show category badge, date, and read-more link

### Version 2.0.0 (April 2026) — Phase 1 Complete
- **NEW:** Branded 404 page with search box, book suggestions, scroll animations
- **NEW:** Search results template with type badges, thumbnails, pagination, no-results state
- **CRITICAL FIX:** Contact form was silently broken — action name mismatch caused all emails to be lost (SEC-01)
- **SECURITY:** Email From header fixed — uses site domain, visitor email in Reply-To only (SEC-02)
- **SECURITY:** Contact form spam protection — honeypot, timestamp check, IP rate limiting (SEC-03)
- **SECURITY:** Full escape audit across 4 templates (SEC-04)
- **SECURITY:** Self-hosted fonts — Google Fonts CDN removed for GDPR compliance (SEC-05)
- **COMPLIANCE:** All inline styles removed, .pot file generated (233 strings), server headers documented

### Version 1.9.9 (April 2026)
- **NEW:** 10-status book system — added Announced status (warm plum)
- **NEW:** Animated scroll-reveal system (fade-up, slide-left/right, staggered grids)
- **NEW:** Parallax hero with configurable background image from Theme Options
- **NEW:** Hero text entrance animation, book card hover effects (lift, shadow bloom, cover zoom)
- **NEW:** Badge shimmer on New Release and Pre-Order, section header underline animation
- **NEW:** Button ripple effect, footer fade-in, about snippet image hover
- Full `prefers-reduced-motion` accessibility support
- Ten distinct badge colours covering complete publishing lifecycle

### Version 1.9.8 (April 2026)
- **EXPANDED:** Book status system from 5 to 9 statuses covering full publishing lifecycle
- New statuses: New Release, Limited Edition, Digital Only, Sold Out
- Status radio buttons grouped by category in admin (Available, Upcoming, Unavailable)
- Helper functions for status rendering, labels, and orderable checks
- Schema.org offers updated for all 9 statuses
- Nine distinct badge colours with full gradient system

### Version 1.9.7 (April 2026)
- **NEW:** Tabbed Theme Options page (Appearance > Theme Options) — replaces Customizer for all custom settings
- Three tabs: Homepage, Contact & Footer, Legal Entity
- All settings use WordPress Options API (`langgam_fikir_options`)
- Customizer stripped to native WordPress controls only (Site Identity, Menus)
- Restored footer Contact column with address, phone, email, SVG icons, Schema.org markup
- Footer bar reads legal entity name and registration number from Theme Options
- Added hero text and contact form recipient email to Theme Options
- Automatic one-time migration of old `theme_mod` values
- Live preview for legal entity footer bar text in admin
- New helper: `langgam_fikir_get_option()` for unified option access
- New assets: `assets/css/theme-options.css`, `assets/js/theme-options.js`

### Version 1.9.6 (April 2026)
- **ARCHITECTURE:** Decomposed monolithic `functions.php` into `/inc/` modules
- Modular includes: setup, enqueue, post-types, meta-boxes, customizer, template-functions, template-hooks
- Conditional loading for admin and WooCommerce contexts
- Added CHANGELOG.md, UPGRADING.md documentation

### Version 1.9.5 (February 2026)
- **STRUCTURE:** Reorganized assets into proper `/assets/` directory structure
- Moved CSS files from `/css/` to `/assets/css/` (WordPress theme directory standard)
- Moved JS files from `/js/` to `/assets/js/` (WordPress theme directory standard)
- Updated all asset enqueue paths in `functions.php`
- **NAMING:** Renamed package to follow `langgam-fikir-[version].zip` format (theme-name-version.zip)
- Improved compliance with WordPress.org theme directory standards
- Updated all documentation to reflect new structure

### Version 1.9.4 (February 2026)
- **Enhanced footer design** — Three-column grid layout with branding, navigation, and legal sections
- Added site logo display in footer branding column
- Added "Navigate" and "Legal" column headings with gold uppercase styling
- Registered new `footer-legal` menu location for Terms of Service and Privacy Policy links
- Added fallback function that auto-links Privacy Policy page and Terms of Service when no legal menu is assigned
- Footer navigation links now stack vertically with gold hover effect
- Added separated bottom bar with copyright (left) and company info (right)
- Responsive: columns stack to single-column centered layout on mobile
- **Post meta icons** — Added inline SVG icons (calendar, person, clock) to single post meta line
- Meta items use gold-colored icons with vertical dividers between items
- Replaced dot separators with styled vertical dividers for visual consistency with tags section
- **Fixed "Our Mission" heading** — Removed hardcoded `page-title-header` wrapper from About page template
- Section headings now render consistently across all pages (no special gold underline on sub-headings)

### Version 1.9.3 (February 2026)
- **CODE QUALITY:** Extracted inline admin CSS to `/css/admin.css`
- **CODE QUALITY:** Extracted inline admin JavaScript to `/js/admin.js`
- Added proper admin asset enqueuing via `admin_enqueue_scripts` hook
- Admin assets now only load on page edit screens (improved performance)
- Implements WordPress best practices for asset separation
- All CSS now in dedicated `/css/` directory
- All JavaScript now in dedicated `/js/` directory
- Admin styles and scripts use proper dependency management and cache-busting

### Version 1.9.2 (February 2026)
- **Unified meta information** — categories, date, author, and reading time all consolidated at the top of the post header
- Removed redundant author box from the bottom (author already shown in meta line)
- **Beautified tags section** — pill-shaped tag badges with tag icon, vertical divider label, and gold hover effect
- Tightened spacing between footer elements (tags, post navigation)
- Cleaned up unused CSS for removed author box

### Version 1.9.1 (February 2026)
- **Redesigned single blog post page** to fully match the child theme design language
- Added page-title-header with gold underline accent on post title
- Added category badges (resource-category style) linked to category archives
- Added post meta with date, author, and estimated reading time
- Featured image with rounded corners matching theme aesthetic
- Entry content styled with proper typography, blockquote gold accents, and link colors
- Tags displayed as styled badges with hover-to-gold effect
- Custom post navigation (Previous/Next Article) with themed hover states
- Added Related Articles section at bottom using resource-card grid layout
- Comments section properly contained and styled
- Full mobile responsive layout (stacked navigation, centered author box)

### Version 1.9.0 (February 2026)
- **FIX:** Block editor headings (e.g. "Our Identity" on About page) now match theme styles
- Added PHP content filter to strip inline styles injected by parent theme on heading elements
- Filter runs at priority 999 to ensure it processes after all other content filters
- This resolves the root cause: Twenty Twenty-Five adds inline `style=""` attributes to block headings which override any CSS regardless of specificity

### Version 1.8.9 (February 2026)
- Last-resort heading override placed at end of CSS to guarantee specificity
- Targets Twenty Twenty-Five's deep selectors: `.wp-site-blocks`, `.wp-block-group`, `body` prefixed rules
- Hero h1 exception re-applied after the override block to maintain white italic text

### Version 1.8.8 (February 2026)
- Fixed block editor headings (e.g. "Our Identity" on About page) not reflecting theme styles
- Added broader selectors targeting `.about-text`, `.site-main`, `article`, and element-level `.wp-block-heading` classes to override parent theme specificity

### Version 1.8.7 (February 2026)
- Fixed hero text disappearing (brown text on brown background)
- Hero h1 now correctly uses white text, italic, weight 400 with `!important` to override global heading styles

### Version 1.8.6 (February 2026)
- Fixed heading styles being overridden by parent theme (Twenty Twenty-Five)
- Added `.wp-block-heading` and `.entry-content h1-h6` selectors to global heading rule
- Used `!important` on font-family, font-weight, and color to ensure child theme styles always win over block editor and parent theme styles

### Version 1.8.5 (February 2026)
- Unified h2 and h3 heading styles globally across all pages and posts
- All headings now use font-weight 600 and primary brown colour (`--color-primary`)
- Removed redundant per-page heading overrides (Privacy page CSS simplified)

### Version 1.8.4 (February 2026)
- Unified h2 and h3 styling on Privacy Policy page
- Both share the same font, weight, colour, spacing, and border-top divider
- h2 at 1.5rem, h3 at 1.25rem for visual hierarchy

### Version 1.8.3 (February 2026)
- Redesigned Privacy Policy page to match theme design language
- Added `page-title-header` with gold accent underline (consistent with About and Contact pages)
- Added "Contents" heading to sidebar navigation with gold bottom border
- Replaced hardcoded grey colors with theme CSS variables (`--color-bg-secondary`, `--color-border`, etc.)
- Section headings now use `--color-primary` with subtle border separators
- Navigation links hover to `--color-primary` text on `--color-bg-muted` background
- Sidebar border-radius updated to 12px (consistent with book cards and other elements)
- Removed placeholder "Lorem Ipsum" fallback navigation items
- Simplified responsive breakpoints

### Version 1.8.2 (February 2026)
- **NEW:** Homepage content count settings (now in Theme Options since v1.9.7)
- Number of Featured Publications configurable (1-12, default 4)
- Number of Resources on homepage configurable (1-12, default 3)
- Replaced hardcoded query limits with `get_theme_mod()` values

### Version 1.8.1 (February 2026)
- Synchronized README version with style.css
- Updated changelog to document all intermediate releases

### Version 1.8.0 (February 2026)
- **NEW:** Archive template (`archive.php`) for category, tag, and date archives
- Added page-title-header styling for archive pages
- Resource-card styled listings for archive results
- Responsive archive grid layout

### Version 1.7.0 (February 2026)
- **NEW:** Additional CSS file (`css/additional.css`) for forms and extra page styles
- Separated form-specific and supplementary styles into dedicated stylesheet
- Enqueued additional stylesheet in `functions.php`

### Version 1.6.0 (February 2026)
- **NEW:** Featured Resources section on blog archive (`home.php`)
- Added sticky post prioritization for featured resource display
- Added carousel arrow placeholders for featured content
- Improved "Other Resources" grid with pagination
- Resource category badges on archive cards

### Version 1.5.0 (February 2026)
- **NEW:** Privacy Policy page template (`page-privacy.php`)
- Enhanced Contact page template with additional styling
- Improved single post layout with related resources section
- Added resource-category badge styling
- Refined footer layout and spacing

### Version 1.4.2 (February 2026)
- Updated About page URL references to `/about-langgam-fikir/`
- Fixed hero "Learn More" button to link to correct URL
- Updated auto-template function to recognize `about-langgam-fikir` slug

### Version 1.4.0-1.4.1 (February 2026)
- **NEW:** About Us page design implementation
- Added split layout (40% pattern, 60% content)
- Added decorative pattern background with squares
- Added contact CTA section with large phone display
- Fully responsive About page design
- Auto-template assignment for About page variants

### Version 1.3.2 (February 2026)
- Fixed Resources "VIEW MORE" button URL
- Added fallback URL for blog page (`/resources/`)
- Improved blog page linking logic

### Version 1.3.1 (February 2026)
- Changed Contact page URL from `/contact/` to `/contact-langgam-fikir/`
- Updated all contact page references in theme

### Version 1.3.0 (February 2026)
- **NEW:** Rounded corners added to all buttons and cards
- **NEW:** Gradient effects on buttons and status badges
- Added 8px border radius to buttons
- Added 6px border radius to status badges  
- Added 12px border radius to book and resource cards
- Added 8px border radius to form inputs
- Added gradient backgrounds to all 5 status badges
- Added gradient hover effects to buttons
- Enhanced box shadows on hover states

### Version 1.2.2 (February 2026)
- **CRITICAL FIX:** Fixed PHP syntax error in single-book.php
- Removed duplicate array closing in out-of-print status schema
- Fixed "Critical Error" on all /publications/ pages

### Version 1.2.1 (February 2026)
- Fixed button visibility issues (white text on white background)
- Created `btn-outline-dark` class for light backgrounds
- Updated all VIEW MORE and Learn More buttons to use correct styling

### Version 1.2.0 (February 2026)
- **NEW:** Pre-Order book status added
- **NEW:** Reprinting book status added
- Added prominent price display on single book pages
- Updated button text (ORDER vs PRE-ORDER based on status)
- Updated schema markup for all 5 statuses
- Added RM price formatting with proper decimals
- Enhanced status message styling

### Version 1.1.1 (February 2026)
- **NEW:** Editor field added to book metadata
- **NEW:** Complete Schema.org markup (JSON-LD) for SEO
- Changed "Penyelenggara" to "Organizer" label
- Added schema for author, editor, translator, publisher
- Added availability schema (InStock, PreOrder, OutOfStock, Discontinued)
- Added price and currency to schema
- Full Google Rich Results support

### Version 1.0.9 (February 2026)
- Implemented Resources page pagination (6 posts per page)
- Added styled pagination controls
- Updated posts_per_page query for blog
- Improved post grid layout (3×2 on desktop)

### Version 1.0.8 (February 2026)
- **NEW:** 3-status book system (Published, Coming Soon, Out of Print)
- Added conditional ORDER button (only shows for Published)
- Added status-specific messages for unavailable books
- Implemented smart business logic for book availability

### Version 1.0.7 (February 2026)
- Fixed button visibility (white text on light backgrounds)
- Added status badges to book cards
- Implemented 2-status system (Published, Coming Soon)
- Added badge styling with color coding

### Version 1.0.6 (February 2026)
- Complete typography redesign (modernist/minimalist)
- Removed bold headings (weight 400 throughout)
- Added italic subtitles with opacity 0.85
- Refined header with logo + site title + tagline layout
- Improved book card and single page typography
- Enhanced line heights and spacing

### Version 1.0.5 (February 2026)
- Fixed hero text overflow issues
- Added contact form pre-fill functionality
- Created ISBN meta field
- Implemented custom logo support

### Version 1.0.0 (February 2026)
- Converted to Twenty Twenty-Five child theme
- Added automatic permalink flush on activation
- Fixed URL override issues
- Improved rewrite rules for custom post types
- Full compatibility with WordPress 6.7+
- Custom post type: Books at `/publications/`
- Custom templates: Homepage, About, Contact, Privacy, Resources
- Contact form with email notifications
- WooCommerce compatibility
- Responsive design implementation

## License

GPL v2 or later (same as parent theme)

## Recommended Server Headers

For optimal security, configure your web server (Apache/Nginx) or hosting panel to send the following HTTP headers:

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; img-src 'self' data: https:;
```

### Apache (.htaccess)

```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Permissions-Policy "camera=(), microphone=(), geolocation=()"
</IfModule>
```

### Nginx

```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;
```

## Credits

- **Parent Theme:** Twenty Twenty-Five by WordPress.org
- **Child Theme Design:** MENJ
- **Fonts:** Google Fonts (Crimson Text, Inter)
