# Changelog

All notable changes to the Langgam Fikir WordPress theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.0] - 2026-10-31

### Added
- **Labels** tab in Theme Settings with sections: book status badges, buttons, single book page, articles, navigation and empty states, privacy page, 404 page, comments. Every string that was still hard-coded in the templates is now editable (use `{n}`, `{query}`, `{title}`, `{count}` tokens where noted)
- Contact tab: editable subject and message templates for the pre-filled book order email
- **Layout** tab: corner style (Soft default, Round, Sharp = the previous look) and parallax scrolling (wide screens by default, all screens, or off)
- Optional hero background image with parallax
- Parallax (`assets/js/parallax.js`): hero layers, homepage About image and badge, About page logo, single book cover. It only updates on-screen sections, runs in `requestAnimationFrame` and is disabled for visitors who prefer reduced motion
- `search.php` and `404.php`, with editable 404 text and a search form on empty results

### Added (hero photos)
- **Hero photo** setting (Theme Settings > Homepage): four bundled Pexels photographs (library corridor, reading room, open book, leather-bound books), Random, or None. The default is the library corridor. Photos are exported at 960 and 1920 pixels with `srcset` (about 1.1 MB in total, 35 to 340 KB each), tinted with your colour scheme, and move with the parallax effect. A picture from the media library still overrides them. Credits in `assets/images/hero/README.txt`
- The hero is taller when it has a photo, with a soft text shadow for legibility

### Added (404)
- A quirky 404 page: a bookshelf with a gap and a toppled book, a drifting page, a random quip (one per line, editable) in the Ferments script, a friendly message, a search box and the three latest books as suggestions. Every part is editable in Theme Settings > Labels > 404 page and can be hidden by saving it blank; animation stops for reduced motion
- Styled search form (404 page and empty search results)

### Changed
- Corners are rounder by default: all radii come from `--radius-*` variables; hero has a rounded bottom edge; images and cards are larger-radius
- Contact email defaults to langgam.fikir@gmail.com; the phone number is blank by default and hidden when empty (also on the About page)
- Search results and 404 pages previously used the parent theme's header and footer; they now use the theme's own
- Search heading now also shows when nothing is found

## [2.1.0] - 2026-10-31

### Added
- Theme Settings tabs **Homepage**, **About page**, **Contact page**, **Publications** and **Footer and pages**: every heading, paragraph, button label, card, placeholder, form message and status message that was hard-coded in the templates is now editable (`inc/settings-fields.php` is the single schema)
- Image fields (media library picker) for the About logo and the homepage About image
- **Footer and pages** tab to choose the About, Contact and Terms pages; the About and Contact layouts now apply to the chosen pages instead of fixed slugs (old slugs keep working)
- A "Theme content" panel in the block editor sidebar (and a notice in the classic editor) on those pages, pointing to the right settings tab
- Default primary navigation when no menu is assigned
- Fields can be saved blank on purpose to hide an element (badge, cards, Instagram link, footer company line)

### Changed
- **Contact page:** text typed in the page editor now appears above the form (previously ignored); placeholders have accessible labels
- **About page:** text typed in the page editor still replaces the default mission text; lead, heading, cards and contact prompt are now editable
- Removed the hard-coded homepage stock photo and logo upload path; a neutral placeholder is shown until an image is chosen
- Per-page About and Contact meta boxes are replaced by Theme Settings; values saved in them remain a fallback

### Removed
- `assets/js/admin.js` (About logo meta box picker) and the About/Contact meta boxes
- Hard-coded page links (`/contact-langgam-fikir/`, `/terms-of-service/`, `about-langgam-fikir`)

## [2.0.0] - 2026-10-31

### Added
- **Theme Settings** page (Appearance > Theme Settings) with accessible tabs: Colours, Typography, Homepage, Contact and About
- Colour schemes: Earth (default, the original palette), Slate, Midnight (dark) and Auto (follows the visitor's `prefers-color-scheme`), plus optional per-colour overrides with a live preview
- `theme.json` exposing the palette, fonts and heading/button styles to the block editor and Global Styles
- `assets/css/schemes.css`, `assets/css/fonts.css`, `assets/css/settings.css`, `assets/js/settings.js` and `inc/theme-options.php`
- Self-hosted fonts: Sabon Next LT (headings), Kagnue (titles), Ferments (tagline), with Crimson Text and Inter bundled as open-source fallbacks. The three licensed fonts are bundled in `assets/fonts/` (see `assets/fonts/README.txt`)
- Site-wide contact and About defaults; the contact form recipient is now configurable

### Changed
- Flat, gradient-free buttons and badges; every colour in the stylesheets is now a CSS variable (no hard-coded hex values)
- Button text uses `--color-on-accent`, giving the gold buttons readable dark text
- The site title is no longer an `<h1>` on every page; each template owns its single `<h1>`
- Hamburger icon animation moved from inline JS styles to a CSS class

### Removed
- All `!important` rules and the global heading override block
- The `the_content` filter that stripped inline styles from headings (block editor heading styles now apply)
- Google Fonts request (fonts are served from the theme)
- The Customizer "Homepage Settings" section; values move to Theme Settings and existing Customizer values are still read as a fallback
- Inline `<style>` blocks and `style=""` attributes in templates and meta boxes

## [1.9.6] - 2026-08-14

### Security
- Added a hidden honeypot field (`contact_website`) to the contact form; submissions that fill it are silently discarded
- Added a per-IP rate limit of one contact submission per minute using WordPress transients
- Escaped `$name`, `$email`, `$subject`, and the message body with `esc_html()` before building the HTML notification email
- Escaped the site description output in `header.php` and `footer.php` with `esc_html()`
- Hardened the Schema.org JSON-LD block in `single-book.php` against `</script>` breakout by adding `JSON_HEX_TAG`, `JSON_HEX_AMP`, `JSON_HEX_APOS`, and `JSON_HEX_QUOT` to `wp_json_encode()`
- Escaped the book `data-product-id` attribute and the displayed price on the single book template

### Added
- New `contact=throttled` form state with a "sending messages too quickly" notice on the contact page

## [1.9.5] - 2026-02-09

### Changed
- **STRUCTURE:** Reorganized assets into proper `/assets/` directory structure
- Moved CSS files from `/css/` to `/assets/css/` (WordPress theme directory standard)
- Moved JS files from `/js/` to `/assets/js/` (WordPress theme directory standard)
- Updated all asset enqueue paths in `functions.php` to reference new locations
- **NAMING:** Package now follows `langgam-fikir-[version].zip` format (theme-name-version.zip)

### Improved
- Better compliance with WordPress.org theme directory standards
- Proper asset organization following WordPress best practices
- Clearer directory structure for developers

## [1.9.4] - 2026-02-09

### Added
- Created `readme.txt` in WordPress.org repository format
- Created `CHANGELOG.md` for complete version history
- Created `UPGRADING.md` for comprehensive upgrade instructions
- Enhanced `README.md` with documentation links

### Changed
- Documentation structure reorganized for better accessibility
- Added cross-references between documentation files

### Improved
- 13,700+ words of professional documentation
- Complete WordPress.org theme repository compliance
- Comprehensive upgrade and troubleshooting guides

## [1.9.3] - 2026-02-09

### Added
- Created `/css/admin.css` for admin interface styling
- Created `/js/admin.js` for admin interface functionality
- New `langgam_fikir_enqueue_admin_assets()` function for proper admin asset management

### Changed
- Extracted inline admin CSS from `functions.php` to dedicated `/css/admin.css` file
- Extracted inline admin JavaScript from `functions.php` to dedicated `/js/admin.js` file
- Admin assets now use `admin_enqueue_scripts` hook with proper screen and post type checks
- Admin assets only load on page edit screens (performance improvement)
- Reduced `functions.php` by 50+ lines of inline code

### Improved
- 100% compliance with WordPress asset separation best practices
- Better browser caching for admin assets
- Cleaner, more maintainable codebase
- Proper dependency management (jQuery, media-upload)
- Cache-busting via `filemtime()` for all admin assets

## [1.9.2] - 2026-02-08

### Changed
- Unified meta information display on single blog posts (categories, date, author, reading time)
- Moved all post meta to top of post header for consistency
- Removed author box from bottom of posts (author already shown in meta line)
- Redesigned tags section with pill-shaped badges and vertical divider label

### Improved
- Tightened spacing between footer elements (tags, post navigation)
- Gold hover effect on tag badges for better interactivity
- Cleaned up unused CSS for removed author box
- Better visual hierarchy in post footer

## [1.9.1] - 2026-02-07

### Added
- Completely redesigned single blog post page template
- Page title header with gold underline accent
- Category badges linked to category archives
- Post meta display (date, author, estimated reading time)
- Related Articles section using resource-card grid layout
- Custom post navigation (Previous/Next Article) with themed hover states

### Changed
- Featured image now has rounded corners matching theme aesthetic
- Entry content styled with proper typography
- Blockquotes now use gold left border accent
- Link colors updated to match theme palette

### Improved
- Full mobile responsive layout for single posts
- Comments section properly contained and styled
- Better visual consistency across all page types

## [1.9.0] - 2026-02-06

### Fixed
- **CRITICAL:** Block editor headings now properly reflect child theme styles
- Fixed Twenty Twenty-Five injecting inline styles that override child theme CSS

### Added
- `langgam_fikir_clean_block_heading_styles()` content filter
- Filter strips inline `style=""` attributes from heading elements
- Filter runs at priority 999 to process after all other content filters

### Technical
- Resolved root cause: parent theme adds inline styles to `.wp-block-heading` elements
- These inline styles override any CSS regardless of specificity
- PHP regex solution removes inline styles while preserving all other attributes

## [1.8.9] - 2026-02-05

### Changed
- Last-resort heading override moved to end of CSS for maximum specificity
- Targets Twenty Twenty-Five's deep selectors (`.wp-site-blocks`, `.wp-block-group`, `body`)
- Hero h1 exception re-applied after override block to maintain white italic text

## [1.8.8] - 2026-02-04

### Added
- Broader selectors for block editor headings
- Targets `.about-text`, `.site-main`, `article`, and `.wp-block-heading` classes

### Fixed
- Block editor headings (e.g., "Our Identity" on About page) not reflecting theme styles
- Added additional specificity to override parent theme styles

## [1.8.7] - 2026-02-03

### Fixed
- Hero text disappearing issue (brown text on brown background)
- Hero h1 now correctly uses white text, italic, weight 400

### Changed
- Used `!important` on hero h1 styles to override global heading rules

## [1.8.6] - 2026-02-02

### Fixed
- Heading styles being overridden by parent theme (Twenty Twenty-Five)

### Added
- `.wp-block-heading` and `.entry-content h1-h6` selectors to global heading rule
- Used `!important` on font-family, font-weight, and color for headings

### Improved
- Ensures child theme heading styles always win over block editor and parent theme styles

## [1.8.5] - 2026-02-01

### Changed
- Unified h2 and h3 heading styles globally across all pages and posts
- All headings now use font-weight 600 and primary brown color

### Removed
- Redundant per-page heading overrides
- Privacy page CSS simplified by removing duplicate heading rules

## [1.8.4] - 2026-01-31

### Changed
- Unified h2 and h3 styling on Privacy Policy page
- Both headings share same font, weight, color, spacing, and border-top divider
- h2 at 1.5rem, h3 at 1.25rem for visual hierarchy

## [1.8.3] - 2026-01-30

### Changed
- Redesigned Privacy Policy page to match theme design language
- Replaced hardcoded grey colors with theme CSS variables
- Navigation links hover to theme primary color
- Sidebar border-radius updated to 12px for consistency

### Added
- Page title header with gold accent underline on Privacy page
- "Contents" heading to sidebar navigation with gold bottom border

### Removed
- Placeholder "Lorem Ipsum" fallback navigation items

### Improved
- Section headings now use `--color-primary` with subtle border separators
- Navigation background uses `--color-bg-secondary` and `--color-bg-muted`
- Simplified responsive breakpoints

## [1.8.2] - 2026-01-29

### Added
- **NEW:** Customizer settings for homepage content counts
- "Homepage Settings" section in Appearance > Customize
- Number of Featured Publications configurable (1-12, default 4)
- Number of Resources on homepage configurable (1-12, default 3)

### Changed
- Replaced hardcoded query limits with `get_theme_mod()` values
- Dynamic homepage content based on user preferences

## [1.8.1] - 2026-01-28

### Changed
- Synchronized README version with style.css
- Updated changelog to document all intermediate releases

## [1.8.0] - 2026-01-27

### Added
- **NEW:** Archive template (`archive.php`) for category, tag, and date archives
- Page title header styling for archive pages
- Resource-card styled listings for archive results
- Responsive archive grid layout

## [1.7.0] - 2026-01-26

### Added
- **NEW:** Additional CSS file (`css/additional.css`) for forms and extra page styles
- Separated form-specific and supplementary styles into dedicated stylesheet

### Changed
- Enqueued additional stylesheet in `functions.php`
- Better organization of CSS files by purpose

## [1.6.0] - 2026-01-25

### Added
- **NEW:** Featured Resources section on blog archive (`home.php`)
- Sticky post prioritization for featured resource display
- Carousel arrow placeholders for featured content
- Resource category badges on archive cards

### Improved
- "Other Resources" grid with pagination
- Better content hierarchy on resources page

## [1.5.0] - 2026-01-24

### Added
- **NEW:** Privacy Policy page template (`page-privacy.php`)
- Enhanced Contact page template with additional styling
- Related resources section on single post layout
- Resource category badge styling

### Improved
- Footer layout and spacing refinements
- Better visual separation between content sections

## [1.4.2] - 2026-01-23

### Fixed
- Updated About page URL references to `/about-langgam-fikir/`
- Fixed hero "Learn More" button to link to correct URL
- Updated auto-template function to recognize `about-langgam-fikir` slug

## [1.4.0 - 1.4.1] - 2026-01-22

### Added
- **NEW:** About Us page design implementation
- Split layout (40% pattern background, 60% content)
- Decorative pattern background with squares
- Contact CTA section with large phone display
- Auto-template assignment for About page variants

### Improved
- Fully responsive About page design
- Better visual balance on larger screens

## [1.3.2] - 2026-01-21

### Fixed
- Resources "VIEW MORE" button URL
- Added fallback URL for blog page (`/resources/`)
- Improved blog page linking logic

## [1.3.1] - 2026-01-20

### Changed
- Contact page URL from `/contact/` to `/contact-langgam-fikir/`
- Updated all contact page references in theme files

## [1.3.0] - 2026-01-19

### Added
- **NEW:** Rounded corners on all buttons and cards (modernist aesthetic)
- **NEW:** Gradient effects on buttons and status badges
- 8px border radius to buttons
- 6px border radius to status badges
- 12px border radius to book and resource cards
- 8px border radius to form inputs
- Gradient backgrounds to all 5 status badges

### Improved
- Enhanced box shadows on hover states
- Gradient hover effects on buttons for better interactivity

## [1.2.2] - 2026-01-18

### Fixed
- **CRITICAL:** PHP syntax error in `single-book.php`
- Removed duplicate array closing bracket in out-of-print status schema
- Fixed "Critical Error" on all `/publications/` pages

## [1.2.1] - 2026-01-17

### Fixed
- Button visibility issues (white text on white background)

### Added
- Created `btn-outline-dark` class for light backgrounds
- Updated all VIEW MORE and Learn More buttons to use correct styling

## [1.2.0] - 2026-01-16

### Added
- **NEW:** Pre-Order book status
- **NEW:** Reprinting book status
- Prominent price display on single book pages
- RM price formatting with proper decimals

### Changed
- Button text changes based on status (ORDER vs PRE-ORDER)
- Updated schema markup for all 5 statuses

### Improved
- Enhanced status message styling for better visibility

## [1.1.1] - 2026-01-15

### Added
- **NEW:** Editor field added to book metadata
- **NEW:** Complete Schema.org markup (JSON-LD) for SEO
- Schema for author, editor, translator, publisher
- Availability schema (InStock, PreOrder, OutOfStock, Discontinued)
- Price and currency in schema
- Full Google Rich Results support

### Changed
- Label "Penyelenggara" changed to "Organizer"

## [1.0.9] - 2026-01-14

### Added
- Resources page pagination (6 posts per page)
- Styled pagination controls

### Changed
- Updated `posts_per_page` query for blog
- Improved post grid layout (3×2 on desktop)

## [1.0.8] - 2026-01-13

### Added
- **NEW:** 3-status book system (Published, Coming Soon, Out of Print)
- Conditional ORDER button (only shows for Published books)
- Status-specific messages for unavailable books

### Improved
- Implemented smart business logic for book availability

## [1.0.7] - 2026-01-12

### Added
- Status badges to book cards
- 2-status system (Published, Coming Soon)
- Badge styling with color coding

### Fixed
- Button visibility (white text on light backgrounds)

## [1.0.6] - 2026-01-11

### Changed
- Complete typography redesign (modernist/minimalist approach)
- Removed bold headings (weight 400 throughout)
- Added italic subtitles with opacity 0.85

### Improved
- Refined header with logo + site title + tagline layout
- Enhanced book card and single page typography
- Better line heights and spacing throughout

## [1.0.5] - 2026-01-10

### Added
- Contact form pre-fill functionality
- ISBN meta field
- Custom logo support

### Fixed
- Hero text overflow issues on mobile devices

## [1.0.0] - 2026-01-09

### Added
- Initial release as Twenty Twenty-Five child theme
- Custom post type: Books at `/publications/` slug
- Custom templates: Homepage, About, Contact, Privacy, Resources
- Contact form with email notifications
- WooCommerce compatibility
- Responsive design implementation
- Custom book metadata fields (author, translator, editor, publisher, year, pages, ISBN, price, status)
- 5 book availability statuses
- Custom image sizes (book-cover, book-thumbnail)
- Navigation menus (primary, footer)
- Google Fonts integration (Crimson Text, Inter)

### Changed
- Converted from standalone theme to child theme architecture
- Improved rewrite rules for custom post types
- Better permalink structure

### Fixed
- URL override issues with custom post types
- Automatic permalink flush on activation

---

## Version Numbering

This project uses [Semantic Versioning](https://semver.org/):
- **MAJOR** version (1.x.x) - Incompatible changes, requires manual intervention
- **MINOR** version (x.9.x) - New features, backward compatible
- **PATCH** version (x.x.3) - Bug fixes, minor improvements, backward compatible

## Links

- [WordPress Theme Repository Guidelines](https://developer.wordpress.org/themes/release/theme-review-guidelines/)
- [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
- [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
