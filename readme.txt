=== Langgam Fikir ===
Contributors: MENJ
Tags: books, publishing, minimalist, clean, custom-colors, custom-menu, featured-images, threaded-comments, translation-ready
Requires at least: 6.7
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A minimalist, modernist WordPress child theme for book publishers, built on Twenty Twenty-Five.

== Description ==

Langgam Fikir is a professionally designed child theme of WordPress Twenty Twenty-Five, specifically crafted for independent book publishers. The theme features elegant typography, a warm earthy color palette, and a sophisticated minimalist design approach.

= Key Features =

* **Custom Book Post Type** - Manage books with comprehensive metadata including author, translator, editor, publisher, ISBN, price, and availability status
* **Five Book Statuses** - Published, Pre-Order, Coming Soon, Reprinting, Out of Print with smart conditional display
* **Custom Templates** - Homepage, About, Contact, Privacy Policy, Blog Archive, Book Archive, and Single Book pages
* **Contact Form** - Built-in functional contact form with email notifications
* **Schema.org Markup** - Complete JSON-LD structured data for books and organization for enhanced SEO
* **WooCommerce Ready** - Optional e-commerce integration support
* **Responsive Design** - Mobile-first approach with optimized layouts for all devices
* **Customizer Integration** - Configure homepage content counts and settings through WordPress Customizer
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
5. Customize settings (Appearance > Customize > Homepage Settings)

== Frequently Asked Questions ==

= Do I need the parent theme installed? =

Yes, Twenty Twenty-Five must be installed. The child theme inherits core functionality from the parent theme while adding custom features.

= Why are my book URLs showing 404 errors? =

Go to Settings > Permalinks and click Save Changes. This flushes the rewrite rules and fixes custom post type URLs.

= Can I use this theme without WooCommerce? =

Absolutely! WooCommerce support is optional. The theme has built-in book management and contact forms without needing e-commerce.

= How do I change the color scheme? =

Edit /css/main.css and modify the CSS custom properties in the :root section. All colors are defined as variables for easy customization.

= Can I add more book metadata fields? =

Yes! The theme is developer-friendly. Add custom fields in functions.php following the existing patterns for author, translator, editor, etc.

= Does this theme support Gutenberg? =

Yes, the theme is fully compatible with the block editor (Gutenberg) and includes support for WordPress Full Site Editing features.

= Is the theme translation ready? =

Yes! The theme uses proper internationalization functions and the 'langgam-fikir' text domain. POT file can be generated for translations.

= How do I customize the homepage featured books count? =

Go to Appearance > Customize > Homepage Settings. You can set the number of featured publications (1-12) and resources (1-12).

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

= 2.2.0 (October 2026) =
* New Labels tab: every remaining front-end string is editable (status badges, buttons, single book labels, article, search, comments and 404 text)
* Rounder corners with a Soft, Round or Sharp choice, and optional parallax scrolling (Theme Settings > Layout)
* Optional hero background image
* Fixed search and 404 pages showing the parent theme's header and footer

= 2.1.0 (October 2026) =
* Every page text is now editable in Theme Settings: Homepage, About page, Contact page, Publications, Footer and pages
* Choose which pages are About and Contact; no more fixed slugs
* Text typed in the About and Contact page editors now appears on the page
* Default navigation when no menu is set

= 2.0.0 (October 2026) =
* New tabbed Theme Settings page: Colours, Typography, Homepage, Contact and About
* New colour schemes: Earth, Slate, Midnight and Auto (dark mode), with custom colour overrides
* Added theme.json; removed all !important rules; self-hosted fonts
* Existing Customizer homepage values are still honoured

= 1.9.6 (August 2026) =
* SECURITY: Added hidden honeypot field to the contact form to block automated submissions
* SECURITY: Added per-IP rate limit of one contact submission per minute
* SECURITY: Escaped name, email, subject, and message in the contact notification email
* SECURITY: Escaped the site description output in header.php and footer.php
* SECURITY: Hardened the Schema.org JSON-LD block against script-tag breakout
* Added a "sending messages too quickly" notice on the contact page

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
