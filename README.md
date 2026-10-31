# Langgam Fikir - Twenty Twenty-Five Child Theme

A child theme of WordPress Twenty Twenty-Five (2025 default theme) with custom book publisher design.

## Theme Information

- **Theme Name:** Langgam Fikir
- **Parent Theme:** Twenty Twenty-Five
- **Version:** 2.2.0
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

✅ **Custom Post Type:** Books with metadata
✅ **Custom Templates:** Homepage, About, Contact, Privacy, Resources
✅ **Custom Styling:** Minimalist book publisher design
✅ **Contact Form:** Functional contact page with email notifications
✅ **Book System:** Complete book showcase and single pages
✅ **WooCommerce Support:** Optional e-commerce integration
✅ **Responsive Design:** Mobile-first approach

## File Structure

```
LF/
├── css/
│   ├── admin.css          # Admin-side styles
│   ├── main.css           # Main custom styles
│   └── additional.css     # Forms & extra pages
├── js/
│   ├── admin.js           # Admin-side JavaScript
│   └── main.js            # Custom JavaScript
├── archive.php            # Category/tag/date archives
├── archive-book.php       # Books archive
├── comments.php           # Comments override
├── footer.php             # Custom footer
├── front-page.php         # Custom homepage
├── functions.php          # Child theme functions
├── header.php             # Custom header
├── home.php               # Blog/resources archive
├── index.php              # Default fallback template
├── page.php               # Page template
├── page-about.php         # About template
├── page-contact.php       # Contact template
├── page-privacy.php       # Privacy template
├── screenshot.png         # Theme screenshot
├── single.php             # Single post
├── single-book.php        # Single book
└── style.css              # Child theme header (with Template: twentytwentyfive)
```

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

## Customization

### Changing Colors

Edit `/css/main.css`:

```css
:root {
    --color-primary: #6B5B4A;
    --color-accent: #C9A961;
}
```

### Changing Fonts

Edit `/css/main.css`:

```css
:root {
    --font-primary: 'Crimson Text', serif;
    --font-secondary: 'Inter', sans-serif;
}
```

## Version History / Changelog

### Version 2.2.0 (October 2026)
Labels tab (all remaining strings editable), rounded corners (Soft/Round/Sharp), parallax scrolling, search and 404 templates. See `CHANGELOG.md`.

### Version 2.1.0 (October 2026)
All page text editable from Appearance > Theme Settings (Homepage, About page, Contact page, Publications, Footer and pages); page roles chosen in settings. See `CHANGELOG.md`.

### Version 2.0.0 (October 2026)
Tabbed Theme Settings (Appearance > Theme Settings), colour schemes (Earth, Slate, Midnight, Auto), `theme.json`, self-hosted fonts, and removal of `!important` overrides. See `CHANGELOG.md`.

### Version 1.9.6 (August 2026)
- **SECURITY:** Hidden honeypot field on the contact form; filled submissions are silently discarded
- **SECURITY:** Per-IP rate limit of one contact submission per minute (WordPress transients)
- **SECURITY:** Contact notification email now escapes name, email, subject, and message with `esc_html()`
- **SECURITY:** Site description escaped on output in `header.php` and `footer.php`
- **SECURITY:** Schema.org JSON-LD hardened against `</script>` breakout in `single-book.php`
- Added a "sending messages too quickly" notice for throttled contact submissions

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
- **NEW:** Customizer settings for homepage content counts
- Added "Homepage Settings" section in Appearance > Customize
- Number of Featured Publications is now configurable (1-12, default 4)
- Number of Resources on homepage is now configurable (1-12, default 3)
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

## Credits

- **Parent Theme:** Twenty Twenty-Five by WordPress.org
- **Child Theme Design:** MENJ
- **Fonts:** Google Fonts (Crimson Text, Inter)
