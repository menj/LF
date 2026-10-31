# Upgrading Langgam Fikir Theme

This guide provides instructions for upgrading the Langgam Fikir WordPress theme between versions.

## Table of Contents

1. [Before You Upgrade](#before-you-upgrade)
2. [Upgrade Methods](#upgrade-methods)
3. [Version-Specific Upgrade Notes](#version-specific-upgrade-notes)
4. [Post-Upgrade Steps](#post-upgrade-steps)
5. [Troubleshooting](#troubleshooting)
6. [Rolling Back](#rolling-back)
7. [Enhancement Roadmap](#enhancement-roadmap)
8. [Implementation Plan](#implementation-plan)

---

## Before You Upgrade

### Prerequisites Checklist

- [ ] **Backup your site** (files and database)
- [ ] **Test on staging environment** (if available)
- [ ] **Check PHP version** (minimum 7.4 required)
- [ ] **Check WordPress version** (minimum 6.7 required)
- [ ] **Check parent theme** (Twenty Twenty-Five must be installed)
- [ ] **Deactivate caching plugins** (temporarily)
- [ ] **Note your current version** (check Appearance > Themes)

### Compatibility Check

| Component | Minimum Version | Recommended |
|-----------|----------------|-------------|
| WordPress | 6.7 | 6.7+ |
| PHP | 7.4 | 8.0+ |
| Twenty Twenty-Five | 1.0 | Latest |
| MySQL | 5.6 | 8.0+ |

### What to Backup

1. **Theme Files**
   - Entire `/wp-content/themes/LF/` directory
   - Any customizations you've made

2. **Database**
   - Full WordPress database backup
   - Export via phpMyAdmin or backup plugin

3. **Theme Options**
   - Screenshot your Theme Options settings (Appearance > Theme Options)
   - Note: Settings persist automatically but screenshot for safety

4. **Content**
   - Export all Books (Tools > Export)
   - Export all Pages and Posts

---

## Upgrade Methods

### Method 1: WordPress Dashboard (Recommended)

When updates are available through WordPress.org:

1. Go to **Dashboard > Updates**
2. Find Langgam Fikir in the themes list
3. Click **Update Now**
4. Wait for completion
5. Proceed to [Post-Upgrade Steps](#post-upgrade-steps)

### Method 2: Manual Upload

For custom versions or private updates:

1. **Backup first!** (See checklist above)

2. Download new theme version (`LF_vX.X.X.zip`)

3. Go to **Appearance > Themes**

4. Click **Add New > Upload Theme**

5. Choose the new `.zip` file

6. Click **Install Now**

7. When prompted, click **Replace current with uploaded**

8. Click **Activate** (if not auto-activated)

9. Proceed to [Post-Upgrade Steps](#post-upgrade-steps)

### Method 3: FTP/SFTP Upload

For manual server access:

1. **Backup first!**

2. Download and extract new theme version

3. Connect via FTP/SFTP to your server

4. Navigate to `/wp-content/themes/`

5. **Rename existing** `LF` folder to `LF-backup`

6. Upload new `LF` folder

7. Go to WordPress dashboard

8. Navigate to **Appearance > Themes**

9. Activate the updated theme (if not active)

10. Proceed to [Post-Upgrade Steps](#post-upgrade-steps)

---

## Version-Specific Upgrade Notes

### Upgrading to 2.10.0 (Latest) ✅

**Status:** RELEASED — WebMCP agent tools (Phase 5 item 5.9).

**Changes:**
- New module `inc/webmcp.php` and script `assets/js/webmcp.js`
- Public read-only REST routes `GET /wp-json/langgam-fikir/v1/publications` and `/publications/{slug}`
- Two WebMCP tools registered in supporting browsers: `search-publications`, `get-publication`
- Contact form annotated as a declarative tool (agent fills, visitor sends); honeypot cleared on agent-invoked submissions
- Theme Options → Social & SEO gains an Agent Tools card: enable toggle, contact-form toggle, Origin Trial token

**Action Required:**
- None — on by default, inert in browsers without WebMCP, no data or option migration
- To test in Chrome 149+ or Edge 150+: register langgamfikir.my in the browser's Origin Trials console, paste the token into the new field. Tokens are domain-bound and expire
- Verify the endpoint works after deploying: open `/wp-json/langgam-fikir/v1/publications?per_page=3` in a browser. If it 404s, re-save Settings → Permalinks
- If you'd rather agents never touch the contact form, untick the second toggle; the catalogue tools stay

---

### Upgrading to 2.9.9 ✅

**Status:** RELEASED — book card content pass.

**Changes:**
- Footer status label removed from book cards (the cover badge is the single status indicator); price slot shows a price or nothing
- 20-word excerpt added to book cards, between subtitle and author/year
- Duplicate footer CSS from 2.9.7 removed; the long-standing block near the end of `main.css` is the only definition

**Action Required:**
- None. Hard-refresh after deploying; the earlier "unstyled footer" appearance was a stale cached stylesheet, and the `.htaccess` cache headers in `README.md` prevent that class of confusion going forward
- Optional: write manual excerpts for books whose opening paragraph doesn't read well when trimmed to 20 words

---

### Upgrading to 2.9.8 ✅

**Status:** RELEASED — mobile accessibility and LCP fixes.

**Changes:**
- `.menu-toggle` gains `aria-label="Menu"`
- Homepage About image (featured image or Unsplash fallback) renders `loading="eager" fetchpriority="high"`; it is the LCP element on mobile
- `preconnect` to `images.unsplash.com` on the front page while the fallback is in use
- Custom logo no longer receives `fetchpriority="high"`

**Action Required:**
- None in the theme
- Set a featured image on the About page (Appearance → the About page's Featured Image box). The Unsplash fallback is a stopgap; a local image gets sized, converted, and cached like your covers

---

### Upgrading to 2.9.7 ✅

**Status:** RELEASED — book card footer styles.

**Changes:**
- Added the missing CSS for `.book-card-footer`, `.book-card-price`, `.book-card-price-amount`, and `.book-card-price-label`: footer pinned to the card bottom, price styled, status label in small caps

**Action Required:**
- None. Clear browser cache to pick up `main.css`; every book grid (homepage, Publications archive, taxonomies, search) benefits

---

### Upgrading to 2.9.6 ✅

**Status:** RELEASED — Showcase performance fix.

**Changes:**
- Showcase cover URLs now resolve via `langgam_fikir_showcase_image_url()`: `book-showcase` crop, else `large`, never the original upload
- Showcase background layers load lazily (`data-showcase-src` applied by `showcase.js` near the viewport) instead of inline `background-image`
- Roadmap item 8.9 (context-split `main.css`) added from the audit

**Action Required:**
- **Regenerate thumbnails now** (Regenerate Thumbnails plugin or `wp media regenerate`) so every cover gets a real 1600×900 `book-showcase` file. Until then legacy covers use the `large` size, which is safe but soft at full-bleed
- Convert or re-upload the Paulus Perosak cover: its source is a 1.7 MB PNG, so even the regenerated crop will be PNG unless your image pipeline converts it
- Clear browser cache to pick up the new `showcase.js`

---

### Upgrading to 2.9.5 ✅

**Status:** RELEASED — bug fix for the Contact, About, and Privacy Policy pages.

**Changes:**
- `additional.css` now loads by assigned template (`is_page_template()`), not by literal slug, so it reaches pages whose slugs are not exactly `contact` / `about` / `privacy-policy`
- Honeypot hiding rule (`.lf-hp`) moved from `additional.css` into `main.css`

**Action Required:**
- None in the theme. Clear browser cache and reload the Contact page; the "Website" field should be gone and the form styled
- While you're there: the live Contact page still shows placeholder email/phone and lorem ipsum. Set the real values under Appearance → Theme Options → Contact & Footer and in the page content

---

### Upgrading to 2.9.4 ✅

**Status:** RELEASED — accessibility patch, second pass.

**Changes:**
- Repeated generic links ("Learn More", "Read More", "VIEW MORE") now have unique accessible names via visually hidden `.screen-reader-text` suffixes. Visible labels unchanged
- Touches `front-page.php`, `index.php`, `search.php`, `template-parts/content/book-card.php`, `template-parts/content/resource-card.php`

**Action Required:**
- None — markup only, no CSS, options, or data changes
- The "no main landmark" audit finding is already fixed by 2.9.3; re-run Lighthouse after deploying this build

---

### Upgrading to 2.9.3 ✅

**Status:** RELEASED — accessibility patch from the 6 September 2026 Lighthouse audit.

**Changes:**
- Badge label and section subtitles now use `--color-text-light` instead of `--color-text-muted`: both were below AA contrast at their sizes
- Homepage wrapped in `<main id="primary" class="site-main" tabindex="-1">`, so the header skip link finally has a target on the front page
- `tabindex="-1"` added to all 15 `<main id="primary">` wrappers; outline suppressed on that programmatic focus target
- Phase 8 baseline updated with the main-thread finding

**Action Required:**
- None — CSS and template markup only, no options or data touched
- If a child override or custom CSS restyles `.section-header-subtitle` or `.badge-label`, re-check its contrast against the new base colour
- Clear browser cache to pick up the updated `main.css`

---

### Upgrading to 2.9.2 ✅

**Status:** RELEASED — documentation patch following the 6 September 2026 PageSpeed audit. No code changes.

**Changes:**
- `README.md` gains a Server & Performance section: the theme's performance guarantees, an `.htaccess` block for compression and browser caching, image guidance, and an optional `jquery-migrate` mu-plugin
- `ssot.md` §5.4 records the performance contract (what the theme guarantees vs. what the host must provide)
- Phase 8 roadmap below now carries the audit baseline and two new items: 8.7 stylesheet consolidation, 8.8 LCP preload; 8.3 marked largely superseded by the site's AVIF pipeline
- `readme.txt` FAQ answers the "why does PageSpeed flag caching/compression" question

**Action Required:**
- None in the theme. On the host: add the `.htaccess` block (or enable LiteSpeed Cache), re-export the logo under 20 KiB, and re-upload the one PNG cover so the image pipeline converts it
- Verify which build is live: the audit showed `additional.css` on the homepage and no Showcase assets, consistent with a pre-2.9 build

---

### Upgrading to 2.9.1 ✅

**Status:** RELEASED — naming and separation patch for the homepage book sections.

**Changes:**
- "Featured Showcase" is now "Showcase"; "Featured Publications" is now "Latest Publications"
- The Latest Publications grid excludes whatever the Showcase displays — no title appears in both sections
- Showcase default count is 3 (single row); the 2–6 range is unchanged
- Showcase query moved into `langgam_fikir_get_showcase_items()` / `langgam_fikir_showcase_book_ids()` helpers
- Template part renamed: `template-parts/content/featured-showcase.php` → `template-parts/content/showcase.php`

**Action Required:**
- None — option keys are unchanged, so saved Showcase settings carry over
- If you saved Theme Options under 2.9.0, your stored count (likely 6) is kept; set it to 3 under Appearance → Theme Options → Homepage → Showcase if you want the new single-row default
- If you copied `featured-showcase.php` into a child theme or customised it, move those changes to `showcase.php` — the old filename is no longer loaded

---

### Upgrading to 2.9.0 ✅

**Status:** RELEASED — Featured Showcase added to Phase 5 (Frontend Polish) as item 5.8 and delivered.

**Changes:**
- Interactive Featured Showcase section on the homepage: hover (or tab to) a book title and the full-width background crossfades to that book's cover
- New Featured Showcase card on Theme Options → Homepage: enable toggle, optional heading, book count (2–6), lifecycle scope (latest / available only / pipeline only)
- New `book-showcase` image size (1600×900 crop) for the backgrounds
- New asset pair `assets/css/showcase.css` / `assets/js/showcase.js`, loaded on the front page only while the section is enabled
- New template part `template-parts/content/featured-showcase.php`, consumed by `front-page.php` between the About snippet and the Featured Publications grid

**Action Required:**
- None — fully backward compatible. The section is on by default; disable it under Appearance → Theme Options → Homepage if unwanted
- Run Regenerate Thumbnails (or `wp media regenerate`) so existing covers get the 1600×900 crop; until then they serve their full-size files
- Books without a featured image never appear in the showcase — set covers on the books you want featured
- Clear browser cache to pick up new CSS/JS

---

### Upgrading to 2.8.1 ✅

**Status:** RELEASED — documentation patch.

**Changes:**
- New `ssot.md` (Single Source of Truth) at theme root
- Canonical reference for brand identity, design tokens, theme metadata, architecture, conventions, plugin integration contracts, hard rules, and authoritative ownership matrix
- The maintained documentation set is now exactly five files: `ssot.md`, `CHANGELOG.md`, `UPGRADING.md`, `readme.txt`, `README.md`
- No code changes

**Action Required:**
- None — fully backward compatible
- Read `ssot.md` for the canonical statement of theme conventions, brand rules, and integration contracts

---

### Upgrading to 2.8.0 ✅

**Status:** RELEASED — Book Language Display + HrefMaster Independence release.

**Changes:**
- New Book Language meta box field (dropdown) on the `book` CPT
- 24 BCP 47 language choices, filterable via `langgam_fikir_book_language_choices`
- Visible Language row in book-meta-grid on `single-book.php` (only shows when language is explicitly set)
- New helpers: `langgam_fikir_book_language_choices()`, `langgam_fikir_format_language_name()`, `langgam_fikir_book_has_explicit_language()`
- `langgam_fikir_get_page_lang()` resolver extended with theme `_book_language` meta as third tier (between HrefMaster and site locale)
- The theme is now fully functional without HrefMaster — books can declare language through the native meta box

**Resolution priority for book language:**
1. HrefMaster `_hrefmaster_html_lang` (when plugin active and meta set)
2. HrefMaster's `hrefmaster_get_self_lang()` resolver (when plugin active)
3. Theme's `_book_language` meta (book CPT only, plugin-independent)
4. WordPress `get_locale()` (final fallback)

**Action Required:**
- None — fully backward compatible
- To set language for an existing book: edit the book → Book Details meta box → Language dropdown → save
- The visible Language row appears automatically on books with language set; absent on books without
- The Book schema's `inLanguage` property automatically picks up whichever value resolves first

---

### Upgrading to 2.7.0 ✅

**Status:** RELEASED — HrefMaster plugin integration release.

**Changes:**
- New `inc/hrefmaster-integration.php` module with:
  - `langgam_fikir_is_hrefmaster_active()` detection helper
  - `langgam_fikir_get_page_lang()` single source of truth for page language
  - `langgam_fikir_lang_to_og_locale()` BCP 47 → OG locale converter
  - Locale-switching for singular pages with HrefMaster per-post override
  - Admin notice on Theme Options screen
- Schema.org `inLanguage` added to WebSite / Article / WebPage / AboutPage / ContactPage / Book
- Open Graph `og:locale` now respects HrefMaster per-page lang
- Theme Options Social & SEO tab gains a status card (active/inactive variants)
- Orphaned hreflang scaffolding from earlier versions consolidated into the integration module

**Action Required:**
- None — fully backward compatible, even when HrefMaster is not installed
- Install the [HrefMaster plugin](https://github.com/hrefmaster/hrefmaster) if you need hreflang tags or per-page HTML lang attribute control
- Once installed, the theme's status card under **Appearance → Theme Options → Social & SEO** will turn from muted-grey to blue-info

**Roadmap shift:**
- Phase E (Polish & Edge Cases) shifted from v2.7.0 → v2.8.0
- Dark Mode — REMOVED from roadmap (explicitly out of scope per project owner)
- WooCommerce Deep Integration — DEFERRED INDEFINITELY (recorded but not implemented unless re-requested)
- Performance & Launch → v3.0.0 (major version since it gates WordPress.org submission)

---

### Upgrading to 2.6.1 ✅

**Status:** RELEASED — i18n housekeeping patch.

**Changes:**
- `about_badge_label` default value in `langgam_fikir_option_defaults()` wrapped in `__()` for proper translation
- `languages/langgam-fikir.pot` regenerated from current source (260 → 319 strings, capturing all additions through v2.6.0)
- `.pot` extraction keyword spec corrected — textdomain no longer treated as msgctxt
- `languages/langgam-fikir-ms_MY.po` and `.mo` merged via `msgmerge` to add new string placeholders while preserving existing translations
- All `.pot`/`.po` `Project-Id-Version` headers synced to 2.6.1

**Action Required:**
- None — fully backward compatible
- Translators should pull the updated `.pot` and translate the ~59 new strings introduced in v2.4.0 through v2.6.0
- Clear browser cache

---

### Upgrading to 2.6.0 ✅

**Status:** RELEASED — Phase D (Catalog Enrichment) of the SEO/AEO audit roadmap.

**Changes:**
- Book Reviews meta box (repeatable entries with rating, headline, body, date, drag-to-reorder)
- Review + aggregateRating properties on Book JSON-LD (star-rating rich results eligibility)
- Visible reviews section template part with aggregate score block and individual review cards
- Inline aggregate rating below book subtitle linking to full reviews
- Auto-TOC for blog posts longer than 1200 words (filterable via `langgam_fikir_toc_threshold`)
- Per-post Search Engine Visibility toggle outputting `noindex`/`nofollow` via `wp_robots`
- Star rating widget helper `langgam_fikir_render_stars()` (front-end available)
- Three new files: `inc/admin/meta-boxes-reviews.php`, `inc/admin/meta-boxes-robots.php`, `inc/toc.php`, `template-parts/content/reviews-section.php`

**Action Required:**
- None — fully backward compatible
- To activate reviews on a book: edit any book → "Book Reviews" meta box → add reviewer, select 1–5 stars, paste body, save
- To noindex a specific page: edit it → Search Engine Visibility side meta box → tick the checkbox → update
- Auto-TOC activates automatically once a post exceeds 1200 words and has 2+ H2 headings
- Clear browser cache for new CSS/JS

---

### Upgrading to 2.5.0 ✅

**Status:** RELEASED — Phase C (AEO Layer) of the SEO/AEO audit roadmap.

**Changes:**
- FAQ meta box on book and page CPTs with drag-to-reorder
- FAQPage JSON-LD output paired with visible accordion
- ItemList JSON-LD on `/publications/` archive and book taxonomy archives
- H2 sectioning on `single-book.php` (visible "About the Book", hidden Details/Order/Share)
- New `inc/admin/meta-boxes-faq.php` and `template-parts/content/faq-section.php`
- Admin JS now loads on book screens too (was page-only)

**Action Required:**
- None — fully backward compatible
- To activate FAQ surface: edit any book or page, scroll to the new "Frequently Asked Questions (FAQ)" meta box, add Q/A pairs, save
- Clear browser cache to pick up new CSS/JS

---

### Upgrading to 2.4.0 ✅

**Status:** RELEASED — Phase A (critical SEO fix) + Phase B (Authorship & Navigation Layer) of the SEO/AEO audit roadmap.

**Changes:**
- Front-page double-H1 conflict fixed (site-title demoted to `<p>`, hero `<h1>` is canonical)
- Visible breadcrumb component above H1 on all inner pages
- Author bio template part after `the_content()` on `single.php` with avatar, archive link, bio
- Last-updated date display when `dateModified` exceeds `datePublished` by more than a day
- Resource card category converted from `<span>` to internal link
- `home.php` has its own (visually hidden) H1
- `screen-reader-text` utility class added

**Action Required:**
- None — fully backward compatible
- Author bio renders only when authors have a bio set — visit Users → your profile → Biographical Info to populate
- Clear browser cache to pick up new CSS

---

### Upgrading to 2.3.0 ✅

**Status:** RELEASED — Phase 5 (Frontend Polish) partially delivered.

**Changes:**
- Price on book cards (RM X.XX for orderable books, status label otherwise)
- Related books section on single book page (4 books from same `book-category`)
- Social sharing bar on single book page (WhatsApp, Facebook, X, copy-link)
- Full social links on Contact page sourced from Theme Options
- Configurable homepage badge via Theme Options (`about_badge_number`, `about_badge_label`)

**Action Required:**
- None — fully backward compatible
- Visit Appearance → Theme Options → Homepage tab to configure the new About Badge fields (defaults to "2+ Years of Excellence")
- Clear browser cache to pick up new CSS/JS

---

### Upgrading to 2.2.1 ✅

**Status:** RELEASED — bug fix release.

**Changes:**
- About page logo upload chain unified on `_about_logo_id` attachment ID pattern
- `fonts.css` `@font-face` paths corrected to `../fonts/`
- Removed hardcoded `/wp-content/uploads/` logo path from `page-about.php`
- Removed duplicate `add_theme_support('woocommerce')` from `setup.php`

**Action Required:**
- If you previously set an About page logo, re-upload it (the meta key changed from `_about_logo_url` to `_about_logo_id`)
- Clear browser cache for new font paths

---

### Upgrading to 2.2 ✅

**Status:** RELEASED — Theme Options consolidation.

**Changes:**
- Empty `inc/customizer.php` stub removed; all custom settings managed via Theme Options page
- `Tested up to` bumped from 6.7 to 6.9

**Action Required:**
- None — fully backward compatible

---

### Upgrading to 2.0.1 ✅

**Status:** RELEASED — Phase 2 complete.

**Changes:**
- Extracted book-card.php and resource-card.php template parts
- All consuming templates refactored to use get_template_part()
- ~200 lines of duplicated markup eliminated
- New taxonomy archive templates for book categories and tags

**Action Required:**
- None — fully backward compatible
- Clear browser cache
- Flush permalinks (Settings > Permalinks > Save) for new taxonomy archives

---

### Upgrading to 2.0.0 ✅

**Status:** RELEASED — Phase 1 complete (Session 1A + 1B).

**Changes:**
- 404 and search templates added
- All security findings (SEC-01 through SEC-07) resolved
- Contact form fixed and spam-protected
- Google Fonts self-hosted for GDPR compliance
- All inline styles removed, .pot file generated

**Action Required:**
- Download WOFF2 font files and place in /assets/fonts/ (see /assets/fonts/README.md)
- Clear browser cache
- Test contact form submission

---

### Upgrading to 1.9.9

**Changes:**
- 10th book status added: Announced (for pipeline titles)
- Full animation system: scroll reveals, parallax hero, card hover effects, badge shimmer
- Hero background image configurable from Theme Options
- All animations respect `prefers-reduced-motion`

**Action Required:**
- None — fully backward compatible
- Optionally upload a hero background image: go to **Appearance > Theme Options > Homepage > Hero Background Image**
- Clear browser cache to pick up new CSS and JS

**New Features:**
- **Announced status** — use for titles that have been confirmed but have no timeline yet
- **Parallax hero** — upload a background image for a layered scrolling effect
- **Scroll animations** — homepage sections fade and slide into view as the user scrolls
- **Card effects** — book covers zoom on hover, cards lift with shadow bloom
- **Badge shimmer** — New Release and Pre-Order badges subtly animate to draw attention

---

### Upgrading to 1.9.8

**Changes:**
- Book status system expanded from 5 to 9 statuses
- Four new statuses: New Release, Limited Edition, Digital Only, Sold Out
- Admin status selector reorganised into grouped fieldsets

**Action Required:**
- None — fully backward compatible. Existing books retain their current status.
- Review any books that might benefit from the new statuses (e.g. mark recent titles as "New Release")
- Clear browser cache to pick up the new CSS colours

**New Statuses Available:**
- **New Release** — for titles in their launch window (between Coming Soon and Published)
- **Limited Edition** — special/limited print runs
- **Digital Only** — e-book or PDF only, no physical copies
- **Sold Out** — stock exhausted, no confirmed reprint yet (between Reprinting and Out of Print)

---

### Upgrading to 1.9.7

**Changes:**
- All custom settings moved from Customizer to a new Theme Options page
- Customizer stripped to WordPress core defaults only
- Footer Contact column restored with Schema.org markup
- Settings now stored via WordPress Options API (`langgam_fikir_options`)

**Action Required:**
- **Automatic migration:** Old `theme_mod` values are migrated to the new options array on first admin load — no manual action needed
- After upgrading, go to **Appearance > Theme Options** to verify your settings
- Clear browser and server caches

**Important:**
- The Customizer no longer contains Homepage, Footer, or Contact settings
- All theme configuration is now under **Appearance > Theme Options**

---

### Upgrading to 1.9.3

**Changes:**
- Inline admin CSS/JS moved to separate files
- No breaking changes
- No data migration required

**Action Required:**
- None - Safe upgrade from any previous version

**Benefits:**
- Better performance (admin assets only load when needed)
- Improved browser caching
- Cleaner codebase

---

### Upgrading to 1.9.0 - 1.9.2

**Changes:**
- Block editor heading style fixes
- Single post layout redesign
- Privacy policy page redesign

**Action Required:**
- Clear browser cache after upgrade
- Review any custom CSS for heading styles
- Check Privacy Policy page appearance

**Notes:**
- Version 1.9.0 fixes critical issue with block editor headings
- Recommended upgrade if experiencing heading style problems

---

### Upgrading to 1.8.0 - 1.8.9

**Changes:**
- Multiple heading style improvements
- Archive template addition
- Customizer settings for homepage

**Action Required:**
- Clear site cache (if using caching plugin)
- Flush permalinks (Settings > Permalinks > Save)
- Review homepage in Customizer (Appearance > Customize > Homepage Settings)

**New Features:**
- Configure number of featured publications (1-12)
- Configure number of resources on homepage (1-12)

---

### Upgrading to 1.3.0 - 1.7.0

**Changes:**
- Rounded corners on all UI elements
- Additional CSS file for forms
- Featured resources section
- Privacy Policy template

**Action Required:**
- Clear browser and server cache
- Review all pages for visual consistency
- Check contact form appearance

---

### Upgrading to 1.2.0 - 1.2.2

**Changes:**
- Pre-Order and Reprinting statuses added
- Critical bug fix in single-book.php

**Action Required:**
- **If on 1.2.1:** Upgrade immediately to fix critical error
- Review all book statuses
- Test book single pages

**CRITICAL:**
- Version 1.2.2 fixes critical PHP error affecting all book pages
- Do not use versions 1.2.0 or 1.2.1 in production

---

### Upgrading to 1.0.0 (Child Theme Architecture)

**If upgrading from standalone theme:**

**IMPORTANT:** This is a major architectural change.

**Before Upgrading:**
1. Export all Books content
2. Screenshot all Customizer settings
3. Note all custom code modifications
4. Full site backup

**Upgrade Steps:**
1. Install Twenty Twenty-Five parent theme
2. Upload and activate LF child theme
3. **CRITICAL:** Go to Settings > Permalinks > Save Changes
4. Reimport books if needed
5. Reconfigure Customizer settings
6. Test all functionality

**Why This Change:**
- Better WordPress integration
- Safe parent theme updates
- Improved URL handling
- Standard child theme benefits

---

## Post-Upgrade Steps

### Required Steps (Every Upgrade)

1. **Flush Permalinks**
   - Go to **Settings > Permalinks**
   - Click **Save Changes** (don't change anything)
   - This refreshes URL rewrite rules

2. **Clear Caches**
   - Browser cache (Ctrl+Shift+R or Cmd+Shift+R)
   - WordPress object cache (if using)
   - CDN cache (if using)
   - Caching plugin cache (W3 Total Cache, WP Super Cache, etc.)

3. **Test Critical Pages**
   - [ ] Homepage
   - [ ] Books archive (/publications/)
   - [ ] Single book page
   - [ ] Contact page form submission
   - [ ] About page
   - [ ] Blog/Resources archive
   - [ ] Privacy policy page

4. **Test Admin Functionality**
   - [ ] Add new book
   - [ ] Edit existing book
   - [ ] Upload book cover image
   - [ ] Edit pages
   - [ ] Check Theme Options settings (Appearance > Theme Options)

5. **Verify Navigation**
   - [ ] Primary menu works
   - [ ] Footer menu works
   - [ ] Mobile menu toggles correctly
   - [ ] All links work

### Optional Steps

1. **Review Theme Options**
   - Go to **Appearance > Theme Options**
   - Check **Homepage** tab settings
   - Verify **Contact & Footer** details
   - Verify **Legal Entity** information

2. **Update Content**
   - Review and update outdated content
   - Add new books if needed
   - Update about page information

3. **Performance Check**
   - Test page load times
   - Check mobile responsiveness
   - Verify lazy loading works
   - Test with browser developer tools

4. **SEO Verification**
   - Check Schema.org markup (use Google Rich Results Test)
   - Verify meta descriptions
   - Check sitemap generation

---

## Troubleshooting

### Issue: 404 Errors on Book Pages

**Cause:** Permalinks need to be flushed after upgrade.

**Solution:**
1. Go to **Settings > Permalinks**
2. Click **Save Changes**
3. Test book pages again

---

### Issue: Styles Not Loading

**Cause:** Browser or server caching old CSS files.

**Solution:**
1. Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)
2. Clear WordPress cache
3. Clear CDN cache if using
4. Check file permissions (should be 644 for CSS files)

---

### Issue: JavaScript Not Working

**Cause:** Cached JavaScript files or jQuery conflicts.

**Solution:**
1. Clear all caches
2. Check browser console for errors (F12 > Console)
3. Deactivate other plugins temporarily to test for conflicts
4. Verify jQuery is loaded (check page source)

---

### Issue: Admin Meta Boxes Not Styled (After 1.9.3)

**Cause:** New admin.css file not loading.

**Solution:**
1. Clear browser cache
2. Hard refresh admin page (Ctrl+Shift+R)
3. Check file exists: `/wp-content/themes/LF/css/admin.css`
4. Check file permissions (should be 644)

---

### Issue: Contact Form Not Sending

**Cause:** Email configuration or plugin conflict.

**Solution:**
1. Test WordPress email: Install WP Mail SMTP plugin
2. Check spam folder
3. Verify server can send email
4. Check error logs: `/wp-content/debug.log`

---

### Issue: Custom Modifications Lost

**Cause:** Direct theme file edits (not recommended).

**Solution:**
1. Restore from backup
2. **Future:** Use child theme of child theme for customizations
3. Or use Custom CSS in Customizer
4. Or create plugin for custom functionality

---

### Issue: White Screen After Upgrade

**Cause:** PHP error, usually compatibility issue.

**Solution:**
1. Enable WordPress debug mode (edit `wp-config.php`):
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```
2. Check `/wp-content/debug.log` for errors
3. Restore from backup if needed
4. Contact support with error log

---

## Rolling Back

If you need to revert to a previous version:

### Quick Rollback (If you have backup)

1. Go to **Appearance > Themes**
2. Activate a default WordPress theme (Twenty Twenty-Five)
3. Delete current LF theme
4. Upload backup version via **Add New > Upload Theme**
5. Activate restored theme
6. Go to **Settings > Permalinks > Save Changes**
7. Clear all caches

### Manual Rollback (FTP)

1. Connect via FTP/SFTP
2. Navigate to `/wp-content/themes/`
3. Delete current `LF` folder
4. Upload backup `LF` folder
5. Follow Quick Rollback steps 6-7

### Database Rollback (If needed)

If content is missing or corrupted:

1. Access phpMyAdmin
2. Drop current database
3. Import backup database
4. Update site URL if needed:
   ```sql
   UPDATE wp_options 
   SET option_value = 'https://yoursite.com' 
   WHERE option_name = 'siteurl' 
   OR option_name = 'home';
   ```

---

## Best Practices

### Development Workflow

1. **Always test on staging first**
   - Set up staging environment
   - Test upgrade there first
   - Verify everything works
   - Then upgrade production

2. **Use version control**
   - Git repository for theme files
   - Track all customizations
   - Easy rollback capability

3. **Document customizations**
   - Keep changelog of custom modifications
   - Note which files were changed
   - Record custom CSS/JavaScript added

4. **Regular backups**
   - Daily database backups
   - Weekly full site backups
   - Test restore process periodically

### Upgrade Schedule

- **Patch versions (x.x.3):** Upgrade within 1 week
- **Minor versions (x.9.x):** Test first, upgrade within 1 month
- **Major versions (2.x.x):** Test thoroughly, plan migration

---

## Support

If you encounter issues during upgrade:

1. **Check this guide** for common solutions
2. **Review CHANGELOG.md** for version-specific changes
3. **Check WordPress.org forums** for similar issues
4. **Contact theme support** at https://langgamfikir.com/contact
5. **Provide details:**
   - WordPress version
   - PHP version
   - Current theme version
   - Target theme version
   - Error messages from debug.log
   - Steps taken so far

---

## Appendix: Command Line Tools

For advanced users with SSH access:

### Backup via WP-CLI

```bash
# Backup database
wp db export backup.sql

# Backup theme files
tar -czf lf-theme-backup.tar.gz wp-content/themes/LF/
```

### Update via WP-CLI

```bash
# Update theme
wp theme update langgam-fikir

# Flush permalinks
wp rewrite flush
```

### Check version

```bash
# Current theme version
wp theme list --name=langgam-fikir --field=version
```

---

## Security Review & Hardening

Full security audit of v1.9.9, conducted April 2026. Findings are prioritised as **Critical** (exploitable / data loss), **High** (significant risk), **Medium** (best-practice violation), and **Low** (hardening / defence-in-depth).

### Audit Summary

| Area | Rating | Notes |
|------|--------|-------|
| CSRF Protection | ⚠️ Partial | Nonces present on all forms, but contact form action name mismatch means handler never fires |
| Input Sanitisation | ✅ Good | sanitize_text_field, sanitize_email, absint used consistently on save |
| Output Escaping | ⚠️ Gaps | Several unescaped echo statements in templates |
| Spam Protection | ❌ Missing | No honeypot, no rate limiting, no CAPTCHA on contact form |
| Direct Access Guards | ✅ Good | All /inc/ files have ABSPATH check |
| Capability Checks | ✅ Good | edit_post and edit_theme_options verified before saves |
| SQL Injection | ✅ N/A | No direct $wpdb queries — all data via WordPress APIs |
| File Operations | ✅ N/A | No direct file read/write — all via WordPress APIs |
| External Dependencies | ⚠️ 2 found | Google Fonts CDN (GDPR), hardcoded Unsplash URL |
| Email Security | ⚠️ Spoofing risk | From: header uses user-supplied email address |

### Critical Findings

#### SEC-01: Contact Form Action Name Mismatch (Critical)

**File:** `page-contact.php` line 79, `inc/template-functions.php` line 155–156

**Issue:** The form submits `action=langgam_fikir_contact_form` but the handler is registered as `admin_post_langgam_fikir_contact`. WordPress routes by action name, so the form submission hits a **silent 404** on admin-post.php — no email is ever sent, and the user sees no error.

**Impact:** Contact form is completely non-functional. All submissions are silently lost.

**Fix:**
```php
// page-contact.php — change form action value:
<input type="hidden" name="action" value="langgam_fikir_contact">

// OR change handler registration in template-functions.php:
add_action( 'admin_post_nopriv_langgam_fikir_contact_form', ... );
add_action( 'admin_post_langgam_fikir_contact_form', ... );
```

#### SEC-02: Email From Header Spoofing (Critical)

**File:** `inc/template-functions.php` line 131

**Issue:** The `From:` header is built using user-supplied name and email:
```php
'From: ' . $name . ' <' . $email . '>',
```
This allows:
- Email header injection if $name contains newlines (mitigated by sanitize_text_field, but risky pattern)
- SPF/DKIM failures because the sending domain doesn't match the From address
- Many mail servers will reject or spam-folder these emails

**Fix:** Use the site's own address as From, user email only in Reply-To:
```php
'From: ' . get_bloginfo('name') . ' <wordpress@' . parse_url(home_url(), PHP_URL_HOST) . '>',
'Reply-To: ' . $name . ' <' . $email . '>',
```

### High Findings

#### SEC-03: No Contact Form Spam Protection (High)

**File:** `page-contact.php`, `inc/template-functions.php`

**Issue:** The form has CSRF protection (nonce) but no anti-spam measures. Automated bots can submit the form at scale once they discover the endpoint.

**Fix (multi-layer):**
1. **CSS honeypot field** — hidden input that humans don't fill but bots do. Reject if filled.
2. **Timestamp check** — embed form render time, reject if submitted in < 3 seconds.
3. **Rate limiting** — WordPress transient storing IP + submission count, block after 5/hour.
4. **Optional reCAPTCHA v3** — Theme Options toggle with site key / secret key fields.

#### SEC-04: Unescaped Output in Templates (High)

**Files and lines:**

| File | Line | Issue |
|------|------|-------|
| `header.php` | 32, 46 | `echo $description` — tagline output not escaped |
| `page-privacy.php` | 76 | `echo $content` — raw post content (should use `the_content()` or `wp_kses_post()`) |
| `single-book.php` | 259 | `echo get_the_ID()` in data attribute — should use `absint()` |
| `page-contact.php` | 31, 36 | `$_GET['contact']` compared but value not sanitised before comparison |

**Fix:** Apply `esc_html()` to all text outputs, `absint()` to IDs, `sanitize_key()` to $_GET values used in logic.

### Medium Findings

#### SEC-05: External Google Fonts (GDPR Risk) (Medium)

**File:** `inc/enqueue.php` line 42

**Issue:** Loading fonts from `fonts.googleapis.com` sends the visitor's IP address to Google on every page load. This has been ruled a GDPR violation in EU courts (LG München, January 2022).

**Fix:** Self-host fonts:
1. Download Crimson Text and Inter as WOFF2
2. Place in `/assets/fonts/`
3. Create `@font-face` declarations in `/assets/css/fonts.css`
4. Remove Google Fonts CDN enqueue

#### SEC-06: Hardcoded External Image URL (Medium)

**File:** `front-page.php` line 52

**Issue:** Fallback image loads from `images.unsplash.com`. This is an external dependency that could break, change, or track visitors.

**Fix:** Replace with local SVG placeholder or a theme option for the about snippet image.

#### SEC-07: Theme Options Page — Tab Parameter Not Escaped (Medium)

**File:** `inc/admin/theme-options.php` lines 138, 141, 160, 213, 260

**Issue:** `$active_tab` is run through `sanitize_key()` via `$_GET` but not escaped with `esc_attr()` when output in HTML attributes.

**Fix:** Wrap all `$active_tab` outputs in `esc_attr()`:
```php
class="lf-tab <?php echo $active_tab === $slug ? 'lf-tab--active' : ''; ?>"
// should be:
class="lf-tab <?php echo esc_attr( $active_tab ) === $slug ? 'lf-tab--active' : ''; ?>"
```

### Low Findings

#### SEC-08: No Content-Security-Policy Header (Low)

**Issue:** No CSP header is set. While this is typically handled at server level, the theme could output a meta CSP tag via `wp_head` to provide baseline protection against XSS.

**Recommendation:** Add CSP meta tag blocking inline scripts from untrusted sources. Deferred — handle at server/hosting level.

#### SEC-09: No Subresource Integrity on External Scripts (Low)

**Issue:** If any external scripts are loaded in future (CDN libraries, analytics), they should use SRI hashes. Currently N/A since the only external resource is Google Fonts (CSS, not JS).

**Recommendation:** Document as a policy — any future external script must include `integrity` and `crossorigin` attributes.

#### SEC-10: Missing Security Headers Documentation (Low)

**Issue:** The theme cannot set HTTP headers (that's server config), but UPGRADING.md should recommend the following headers to site operators:

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

**Recommendation:** Add a "Recommended Server Headers" section to documentation.

### Hardening Checklist

The following should be verified after every release:

- [ ] All form handlers verify nonces
- [ ] All save functions check `current_user_can()`
- [ ] All /inc/ files have `ABSPATH` direct access guard
- [ ] All `echo` statements use appropriate escaping (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- [ ] All `$_GET` / `$_POST` values are sanitised before use
- [ ] No inline `style=""` attributes in PHP templates
- [ ] No hardcoded external URLs (images, scripts, fonts)
- [ ] Contact form has spam protection (honeypot + timestamp)
- [ ] Email From: header uses site domain, not user-supplied address
- [ ] Translation strings use `esc_html__()` / `esc_html_e()` where output is in HTML context
- [ ] No debug output or `var_dump()` / `error_log()` calls in production code

### Remediation Priority

| ID | Finding | Severity | Phase |
|----|---------|----------|-------|
| SEC-01 | Contact form action mismatch | Critical | 1 (v2.0.0) |
| SEC-02 | Email From header spoofing | Critical | 1 (v2.0.0) |
| SEC-03 | No spam protection | High | 1 (v2.0.0) |
| SEC-04 | Unescaped template outputs | High | 1 (v2.0.0) |
| SEC-05 | External Google Fonts (GDPR) | Medium | 1 (v2.0.0) |
| SEC-06 | Hardcoded Unsplash URL | Medium | 1 (v2.0.0) |
| SEC-07 | Theme Options tab not escaped | Medium | 1 (v2.0.0) |
| SEC-08 | No CSP header | Low | 8 (v2.6.0) |
| SEC-09 | No SRI policy | Low | Document only |
| SEC-10 | Missing server headers docs | Low | 1 (v2.0.0) |

All Critical and High findings are scheduled for Phase 1 (v2.0.0). No public launch should occur before these are resolved.

---

## Enhancement Roadmap

The following roadmap was produced from a full audit of v1.9.9 (April 2026) and updated as work progresses. Severity levels: **critical** (blockers / security), **high** (significant gaps), **important** (quality improvements), **enhancement** (valuable additions), **polish** (refinements).

### Progress Tracker

| Phase | Version | Status | Items | Done | Remaining | Completed |
|-------|---------|--------|-------|------|-----------|-----------|
| 1. Foundation Fixes + Security | 2.0.0 | ✅ Done | 23 | 23 | 0 | April 9, 2026 |
| 2. DRY & Architecture | 2.0.1 | ✅ Done | 7 | 7 | 0 | April 9, 2026 |
| 3. SEO & Structured Data | 2.1.0 | ✅ Done | 20 | 20 | 0 | April 9, 2026 |
| 4. Admin Power Features | 2.1.x | 🔲 Next | 9 | 0 | 9 | — |
| 5. Frontend Polish | 2.3.x | 🟡 In Progress | 9 | 4 | 5 | Social sharing + related books in v2.3.0; showcase in v2.9.0; WebMCP in v2.10.0 |
| ~~6. Dark Mode & Theming~~ | — | ❌ Removed | — | — | — | Out of scope per project owner |
| 7. WooCommerce Deep | — | ⏸️ Deferred | 7 | 0 | 7 | Indefinitely deferred — record only |
| 8. Performance & Launch | 3.0.0 | 🔲 Queued | 9 | 0 | 9 | Gates WordPress.org submission |
| **Total active** | | | **77** | **52** | **25** | Excludes deferred/removed phases |

**Overall progress: 52/77 active items (68%)**

```
Phase 1  ████████████████████ 23/23  ✅
Phase 2  ████████████████████  7/7   ✅
Phase 3  ████████████████████ 20/20  ✅
Phase 4  ░░░░░░░░░░░░░░░░░░░░  0/9   ← NEXT
Phase 5  █████████░░░░░░░░░░░  4/9   (in progress)
Phase 6  ─────────────────────         ❌ Removed (Dark Mode out of scope)
Phase 7  ─────────────────────         ⏸️ Deferred (WooCommerce Deep)
Phase 8  ░░░░░░░░░░░░░░░░░░░░  0/9
```

### Security Findings Status

| ID | Finding | Severity | Status | Fixed In |
|----|---------|----------|--------|----------|
| SEC-01 | Contact form action mismatch | Critical | ✅ Fixed | v2.0.0 |
| SEC-02 | Email From header spoofing | Critical | ✅ Fixed | v2.0.0 |
| SEC-03 | No spam protection | High | ✅ Fixed | v2.0.0 |
| SEC-04 | Unescaped template outputs | High | ✅ Fixed | v2.0.0 |
| SEC-05 | External Google Fonts (GDPR) | Medium | ✅ Fixed | v2.0.0 |
| SEC-06 | Hardcoded Unsplash URL | Medium | ⚠️ Deferred | Kept as fallback — will bundle locally in future |
| SEC-07 | Theme Options tab not escaped | Medium | ✅ Fixed | v2.0.0 |
| SEC-08 | No CSP header | Low | 🔲 Phase 8 (v3.0.0) | Server-level config |
| SEC-09 | No SRI policy | Low | 📝 Documented | Policy noted |
| SEC-10 | Missing server headers docs | Low | ✅ Fixed | v2.0.0 |

### Current Health (v2.1.0 — updated after Phase 1 + 2 + 3)

| Area | Score | Change | Notes |
|------|-------|--------|-------|
| Architecture | 10/10 | — | Template parts, zero duplication, taxonomy archives, modular schema |
| Design System | 8/10 | — | CSS variables, consistent palette, proper typography pairing |
| Book Management | 9/10 | — | 10-status system, rich metadata, Schema.org, helper functions |
| Animations | 8/10 | — | Scroll reveals, parallax, card effects, badge shimmer, reduced-motion |
| Admin UX | 7/10 | — | Tabbed Theme Options (4 tabs now), grouped status radios — but no list columns |
| SEO | 9/10 | ⬆️ +4 | Full schema coverage, OG/Twitter, meta descriptions, breadcrumbs, Speakability |
| Security | 9/10 | — | All Critical/High fixed. Contact form secured. Fonts self-hosted. |
| Compliance | 10/10 | ⬆️ +1 | All templates present, zero inline styles, .pot file, skip link, heading hierarchy fixed |
| WooCommerce | 4/10 | — | Basic support declared but no styled templates |
| Performance | 8/10 | ⬆️ +1 | Self-hosted fonts with preload, no external deps. Minification still pending. |

### Phase 1 — Foundation Fixes + Security (v2.0.0) ✅ COMPLETE

**Completed:** April 9, 2026 — Sessions 1A + 1B

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 1.1 | 404 page template | Critical | Branded 404 with search box and book suggestions |
| 1.2 | Search results template | Critical | search.php matching theme design |
| 1.3 | Remove all inline styles | Critical | comments.php, page.php, index.php, admin meta-boxes |
| 1.4 | Contact form action mismatch (SEC-01) | Critical | Form sends wrong action name — handler never fires, emails silently lost |
| 1.5 | Email From header spoofing (SEC-02) | Critical | From: uses user-supplied email — causes SPF/DKIM failures |
| 1.6 | Contact form spam protection (SEC-03) | High | Honeypot + timestamp + rate limiting + optional reCAPTCHA |
| 1.7 | Escape audit (SEC-04) | High | Fix unescaped outputs in header.php, page-privacy.php, single-book.php, page-contact.php |
| 1.8 | Self-host Google Fonts (SEC-05) | Medium | Download to /assets/fonts/ — GDPR compliance + performance |
| 1.9 | Remove hardcoded Unsplash URL (SEC-06) | Medium | Replace with local placeholder or theme option |
| 1.10 | Escape Theme Options tab param (SEC-07) | Medium | esc_attr() on all $active_tab outputs |
| 1.11 | Server headers documentation (SEC-10) | Low | Recommended headers section in README.md |
| 1.12 | Generate .pot translation file | Important | Populate /languages/ directory |

### Phase 2 — DRY & Architecture (v2.0.x) ✅ COMPLETE

**Completed:** April 9, 2026

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 2.1 | Extract book-card.php template part | Important | Deduplicate from front-page.php, archive-book.php |
| 2.2 | Extract resource-card.php template part | Important | Deduplicate from front-page.php, home.php, single.php |
| 2.3 | Status badge template part | Enhancement | Wrap badge + cover container into reusable partial |
| 2.4 | Book taxonomy archive templates | Enhancement | taxonomy-book-category.php, taxonomy-book-tag.php |

### Phase 3 — SEO & Structured Data (v2.1.0) ✅ COMPLETE

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 3.1 | BreadcrumbList JSON-LD | Critical | All page types — books, posts, archives, pages |
| 3.2 | Article schema on blog posts | Critical | headline, datePublished, dateModified, author, image, publisher on single.php |
| 3.3 | Open Graph meta tags | Critical | og:title, og:description, og:image — book covers on shares |
| 3.4 | Twitter Card meta tags | Critical | summary_large_image for all content types |
| 3.5 | Meta description output | High | Auto-generated from content/excerpt via wp_head |
| 3.6 | Speakability schema | Important | Identifies TTS-suitable sections (book synopsis, article body) for Google Assistant / voice search |
| 3.7 | WebSite schema with SearchAction | High | Enables Google sitelinks search box |
| 3.8 | Organization schema enrichment | High | Add logo, foundingDate, sameAs social links, convert to JSON-LD |
| 3.9 | AboutPage schema | Enhancement | page-about.php |
| 3.10 | ContactPage schema | Enhancement | page-contact.php |
| 3.11 | CollectionPage / ItemList schema | Enhancement | archive-book.php with book listing |
| 3.12 | Fix header.php H1 | Critical | Site title as H1 only on front page, `<p>` elsewhere |
| 3.13 | Fix footer.php H2 | Medium | Site title from `<h2>` to `<p>` |
| 3.14 | Fix index.php H1 branches | High | Ensure one H1 per conditional |
| 3.15 | Skip-to-content link | High | First element after `<body>` in header.php |
| 3.16 | Font preload hints | Medium | Preload critical WOFF2 files via wp_head |
| 3.17 | Social links in Theme Options | Enhancement | Facebook, Instagram, Twitter, WhatsApp URLs |
| 3.18 | Default meta description field | Enhancement | Fallback for pages without excerpts |

### Phase 4 — Admin Power Features (v2.1.x) 🔲 NEXT

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 4.1 | Custom admin columns for books | Important | Status badge, Price, Year, Author in list table |
| 4.2 | Sortable/filterable columns | Enhancement | Filter by status dropdown, sort by price/year |
| 4.3 | Quick Edit for book status | Enhancement | Change status from list view |
| 4.4 | Dashboard widget — stats | Polish | At-a-glance count by status |
| 4.5 | Book export to CSV | Polish | Export metadata for inventory/reporting |

### Phase 5 — Frontend Polish (v2.3.x) 🟡 IN PROGRESS

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 5.1 | Back-to-top button | Enhancement | Appears after scroll, matches theme aesthetic |
| 5.2 | Reading progress bar | Enhancement | Gold line on blog posts showing read progress |
| 5.3 | Book archive filtering | Important | Filter /publications/ by category, tag, or status |
| 5.4 | Social sharing buttons | Enhancement | WhatsApp, Facebook, Twitter, copy link — no third-party scripts |
| 5.5 | Related books section | Enhancement | "You may also like" on single-book.php |
| 5.6 | Image lightbox for covers | Polish | Click-to-zoom, CSS/JS only |
| 5.7 | Sticky header shrink | Polish | Compact header height on scroll |
| 5.8 | Interactive showcase | Enhancement | Hover-driven background crossfade on homepage — delivered in v2.9.0, refined in v2.9.1 ✅ |
| 5.9 | WebMCP agent tools | Enhancement | Read-only catalogue tools + agent-fillable contact form via the W3C WebMCP draft; public REST endpoint — delivered in v2.10.0 ✅ |

### Phase 6 — Dark Mode & Theming ❌ REMOVED

**Status:** Permanently removed from the roadmap. The project owner has declared dark mode out of scope. The CSS-variable foundation in `:root` is retained for general theming flexibility, but no dark palette, `prefers-color-scheme` switching, toggle button, or palette selector will be built. If priorities change in the future, this entire phase would need to be re-scoped from scratch — these notes are not preserved as design guidance.

### Phase 7 — WooCommerce Deep Integration ⏸️ DEFERRED INDEFINITELY

**Status:** Deferred indefinitely. The work is recorded here but will not be implemented unless explicitly re-requested by the project owner. The existing minimal WooCommerce stub in `inc/woocommerce.php` (loop columns, per-page count, conditional load) remains as-is.

If revived, the scope would cover:

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 7.1 | Styled WooCommerce templates | Important | Cart, checkout, my-account, shop matching theme design |
| 7.2 | Book ↔ Product sync | Enhancement | Link Book CPT to WooCommerce product, auto-sync status |
| 7.3 | Single product template override | Important | Use book detail layout for products |
| 7.4 | Mini cart with count badge | Enhancement | Dropdown preview in header |

### Phase 8 — Performance & Launch (v3.0.0) 🔲 QUEUED

This is now the WordPress.org submission gate and warrants the major-version bump.

| # | Item | Severity | Detail |
|---|------|----------|--------|
| 8.1 | Critical CSS inlining | Enhancement | Above-the-fold CSS inline, rest loaded async |
| 8.2 | Asset minification | Enhancement | Minified production builds of CSS/JS (or delegated to a caching plugin; see baseline) |
| 8.3 | WebP image support | Polish | Largely superseded: the live site's image pipeline already serves AVIF. Remaining theme work is limited to `<picture>` fallbacks if ever needed |
| 8.4 | Lighthouse audit pass | Important | Target 90+ across all four categories |
| 8.5 | WordPress.org review checklist | Critical | Final pass against official requirements |
| 8.6 | Unit tests for helpers | Polish | PHPUnit tests for option/status/sanitisation functions |
| 8.7 | Stylesheet consolidation | Polish | Fold `fonts.css` (@font-face) and the few rules in `style.css` into `main.css`: two fewer render-blocking requests |
| 8.8 | LCP image preload | Enhancement | `fetchpriority="high"` / preload for the hero background or first Showcase cover, whichever is the LCP element |
| 8.9 | Context-split `main.css` | Enhancement | 64 KiB of the 84 KiB monolith was unused on the homepage (6 Sept 2026 run). Split single-book, archive, and page styles into conditionally loaded files, as `additional.css` and `showcase.css` already are |

**Baseline — PageSpeed Insights, langgamfikir.my, 6 September 2026**

Flagged audits across the Performance and Accessibility reports, and where each one lives:

| Audit | Finding | Owner |
|---|---|---|
| Render-blocking requests | Est. 560 ms. `main.css` 65 KiB / 630 ms; jQuery + migrate 100 KiB; six small stylesheets at ~430 ms each | Mostly host (no compression) and plugins (jQuery); theme share is 8.7 + 8.2 |
| Efficient cache lifetimes | `Cache-Control` absent on all 2,227 KiB of first-party assets | Host. Reference `.htaccess` in `README.md` → Server & Performance |
| Image delivery | Est. 1,210 KiB. A 639 KiB PNG cover and a 459 KiB PNG logo account for 1,098 KiB | Content. Other covers are already 18–21 KiB AVIFs |
| Long main-thread tasks | 9 tasks, ~1.7 s: Google tag 1,052 ms (a `GT-` container plus a separate `G-` gtag loader, i.e. gtag installed twice), Performance Lab View Transitions inline script 286 ms, FAQ accordion plugin 122 ms on a page with no FAQ block, core packages + jQuery 231 ms | Plugins and Site Kit configuration. No theme script appears in the list |
| Accessibility: contrast + skip link | `.badge-label` at 3.2:1; homepage skip link pointed at a `#primary` that did not exist on `front-page.php` | **Theme — fixed in 2.9.3** |
| Accessibility: landmark + link names | No `<main>` landmark on the homepage (same root cause as the skip link); "Learn More" / "Read More" / "VIEW MORE" repeated with different destinations | **Theme — landmark fixed in 2.9.3, link names in 2.9.4** |

Theme front-end footprint in that audit: ~86 KiB uncompressed (~4% of first-party bytes), ~20 KiB once compression is enabled. Front-end jQuery is a plugin dependency (Contact Form 7); the theme uses jQuery only in admin. The audit also showed `additional.css` on the homepage and no Showcase assets, which points to a pre-2.9 build being live at the time.

**Second run — 08:05, same day, first run against a 2.9.x build.** Scores: Performance 79, Accessibility 100, Best Practices 96, SEO 92. FCP 0.6 s, LCP 2.2 s, TBT 110 ms, CLS 0.04, Speed Index 3.9 s. The accessibility patches (2.9.3, 2.9.4) are confirmed. New or sharpened findings:

| Finding | Detail | Owner |
|---|---|---|
| Showcase serving original uploads | `div.lf-showcase-bg` loaded a 1,720 KiB PNG (cover without a `book-showcase` size) plus two 108–131 KiB full-size AVIFs, all eagerly, below the fold | **Theme — fixed in 2.9.6** (fallback capped at `large`; layers lazy-loaded). Regenerate thumbnails to finish |
| "No compression applied" | Stated outright under Document request latency; `main.css` moved at 84 KiB | Host |
| Cache TTL none on 3,328 KiB | Unchanged from the first run | Host |
| Unused CSS 64 of 84 KiB in `main.css` | Homepage uses a quarter of the monolith | Theme — roadmap 8.9 |
| Unused JS 356 KiB | Two gtag loaders (185 KiB), jQuery, Wikipedia Preview, Site Kit consent | Plugins |
| Request timeouts | Three `ERR_TIMED_OUT` on the document URL, `robots.txt` and `llms.txt` fetches timed out, 28–33 s branches in the dependency tree | Speculative-loading prerenders under host bot throttling; also what drags Best Practices and SEO below 100 |
| Logo `fetchpriority="high"` at 500×500 for a 60×60 slot | 459 KiB PNG, 452 KiB avoidable | Content: re-export the logo. Theme side, 2.9.8 stops WordPress assigning it high priority |

**Mobile half of the same run — emulated Moto G Power, slow 4G.** Scores: Performance 47, Accessibility 94, Best Practices 96, SEO 92, Agentic 1/3. FCP 2.8 s, LCP 13.5 s, TBT 540 ms, CLS 0.053, Speed Index 11.3 s.

| Finding | Detail | Owner |
|---|---|---|
| Hamburger button without a name | `.menu-toggle` had no text or label; hidden on desktop so invisible to that pass. Also failed the Agentic "well-formed tree" audit | **Theme — fixed in 2.9.8** |
| About image is the mobile LCP | `loading="lazy"`, no `fetchpriority`, no preconnect to the Unsplash origin; resource load delay 5.8 s | **Theme — fixed in 2.9.8**; set a featured image on the About page to retire the fallback |
| Logo takes `fetchpriority="high"` | WordPress auto-assigns it to the first sizeable image | **Theme — fixed in 2.9.8** |
| Everything else | Compression, cache headers, PNG cover and logo, plugin JS, prerender timeouts (this run also timed out a self-hosted font file) | Unchanged owners: host, content, plugins |

---

## Implementation Plan

### Session Plan — Phase 1 (v2.0.0: Foundation Fixes) ✅

**Estimated effort: 1–2 sessions**

#### Session 1A: Missing Templates & Compliance

**Goal:** Close all WordPress.org review blockers.

1. **Create 404.php**
   - Branded layout with hero-style header
   - Search form
   - "Popular Books" and "Latest Resources" suggestions
   - Matching theme typography and colours

2. **Create search.php**
   - Page-title-header showing "Search results for: {query}"
   - Results displayed as resource-card grid
   - No-results state with search form and suggestions
   - Pagination

3. **Purge all inline styles**
   - comments.php — move `style=""` attributes to main.css classes
   - page.php — extract section padding to CSS class
   - index.php — extract section padding and meta styling to CSS classes
   - inc/admin/meta-boxes.php — extract logo preview max-width to admin.css

4. **Escape audit**
   - Replace `_e()` with `esc_html_e()` where output is in HTML content
   - Escape `get_the_ID()` in data attributes with `absint()`
   - Audit every `echo` statement for proper escaping

5. **Remove hardcoded Unsplash URL**
   - Replace with theme option or local SVG placeholder

#### Session 1B: Security Hardening & Fonts

**Goal:** Close all Critical and High security findings (SEC-01 through SEC-07).

6. **Fix contact form action mismatch (SEC-01)**
   - Change `page-contact.php` action value from `langgam_fikir_contact_form` to `langgam_fikir_contact`
   - Test: submit form, verify email received

7. **Fix email From header spoofing (SEC-02)**
   - Change From header to use site domain: `wordpress@{site_host}`
   - Keep user email in Reply-To only
   - Test: submit contact form, check email headers

8. **Contact form spam protection (SEC-03)**
   - Add CSS honeypot field (hidden via CSS class, not inline style)
   - Server-side check: if honeypot filled, silently reject
   - Add timestamp field — reject if submitted < 3 seconds after page load
   - Add rate limiting via WordPress transient (5 submissions/hour/IP)
   - Optional: add reCAPTCHA v3 setting in Theme Options (off by default)

9. **Fix all unescaped outputs (SEC-04)**
   - header.php: `echo $description` → `echo esc_html( $description )` (lines 32, 46)
   - page-privacy.php: `echo $content` → use `the_content()` or `wp_kses_post()`
   - single-book.php: `echo get_the_ID()` → `echo absint( get_the_ID() )`
   - page-contact.php: sanitise `$_GET['contact']` with `sanitize_key()`
   - Theme Options: wrap `$active_tab` outputs in `esc_attr()`

10. **Self-host Google Fonts (SEC-05)**
    - Download Crimson Text (400, 400i, 600) and Inter (400, 500, 600) as WOFF2
    - Place in /assets/fonts/
    - Create /assets/css/fonts.css with @font-face declarations
    - Update enqueue.php to load local fonts instead of Google CDN
    - Remove Google Fonts external stylesheet enqueue

11. **Remove hardcoded Unsplash URL (SEC-06)**
    - Replace with local SVG placeholder or Theme Options about snippet image

12. **Escape Theme Options tab parameter (SEC-07)**
    - Wrap all `$active_tab` outputs in `esc_attr()` in theme-options.php

13. **Add Recommended Server Headers documentation (SEC-10)**
    - Add section to README.md with recommended headers configuration

14. **Generate .pot translation file**
    - Run wp i18n make-pot or equivalent
    - Place langgam-fikir.pot in /languages/

**Deliverable:** v2.0.0 ZIP with all 14 items complete. All Critical/High security findings resolved.

---

### Session Plan — Phase 2 (v2.0.x: DRY & Architecture) ✅

**Estimated effort: 1 session**

1. **Create template-parts/content/book-card.php**
   - Extract the full book card markup (cover, badge, title, subtitle, meta, button)
   - Accepts book post in loop context
   - Add `reveal` class support via parameter
   - Replace markup in front-page.php and archive-book.php with `get_template_part()`

2. **Create template-parts/content/resource-card.php**
   - Extract resource/blog card markup
   - Replace in front-page.php, home.php, single.php (related articles)

3. **Create taxonomy-book-category.php**
   - Uses book-card.php template part
   - Section header shows taxonomy name and description
   - Grid layout matching archive-book.php

4. **Create taxonomy-book-tag.php**
   - Same pattern, references book-card.php

**Deliverable:** v2.0.1 ZIP. Template parts populated, all duplicated markup eliminated.

---

### Session Plan — Phase 3 (v2.1.0: SEO & Structured Data) ✅

**Estimated effort: 1–2 sessions**

#### Session 3A: Schema & Headings

1. **Fix H1 heading structure**
   - `header.php` — wrap site title in `<h1>` only when `is_front_page()`, use `<p>` elsewhere
   - `footer.php` — change `<h2 class="site-title">` to `<p class="site-title">`
   - `index.php` — ensure only one `<h1>` renders per conditional branch
   - Add skip-to-content link as first element after `<body>` in header.php

2. **BreadcrumbList JSON-LD**
   - Create `langgam_fikir_breadcrumb_schema()` in template-functions.php
   - Output via `wp_head` hook
   - Paths: Home → Books → {Book Title}, Home → Resources → {Post Title}, Home → {Page Title}, Home → {Category} → {Book Title}

3. **Article schema on single.php**
   - JSON-LD block with @type Article, headline, datePublished, dateModified, author (Person), image, publisher (Organization with logo)

4. **Speakability schema**
   - Add `speakable` property to Book schema in `single-book.php` — target the synopsis section (`.book-synopsis-text`) using CSS selector
   - Add `speakable` property to Article schema in `single.php` — target the entry content (`.entry-content`) and the first paragraph
   - Uses `SpeakableSpecification` with `cssSelector` array
   - Enables Google Assistant to read book descriptions and article content aloud
   - Critical for voice search: "Hey Google, tell me about [book title]"

5. **WebSite schema with SearchAction**
   - Single JSON-LD block via `wp_head` hook
   - SearchAction pointing to `/?s={search_term_string}`

6. **Organization schema enrichment**
   - Convert footer microdata to JSON-LD output via `wp_head` hook
   - Add: logo (from custom_logo), foundingDate, sameAs (social links array from Theme Options)
   - Remove inline microdata from footer.php (keep semantic HTML, lose itemprop attributes)

7. **Page-type schemas**
   - `page-about.php` — AboutPage schema
   - `page-contact.php` — ContactPage schema
   - `archive-book.php` — CollectionPage with ItemList of books

#### Session 3B: Social Meta & Theme Options

8. **Open Graph meta tags**
   - Create `langgam_fikir_og_meta()` hooked to `wp_head` at priority 5
   - og:title, og:description, og:image, og:url, og:type, og:site_name, og:locale
   - Book pages: og:type = "book", og:image = book cover
   - Blog posts: og:type = "article", og:image = featured image
   - Pages: og:type = "website"
   - Fallback og:image from custom_logo if no featured image

9. **Twitter Card meta**
   - twitter:card = summary_large_image
   - twitter:title, twitter:description, twitter:image
   - Same image logic as OG

10. **Meta description**
    - Auto-generated: excerpt if available, first 160 chars of content as fallback
    - Book pages: "{author} — {subtitle or excerpt}" format
    - Respects manual override from Theme Options default or per-page

11. **Font preload hints**
    - Add `<link rel="preload" as="font" type="font/woff2" crossorigin>` for 2 critical font files (Crimson Text regular, Inter regular) via `wp_head` at priority 1

12. **Theme Options additions**
    - Social Links tab or section within Contact & Footer: Facebook URL, Instagram URL, Twitter/X URL, WhatsApp number
    - Default Meta Description field (fallback for pages without excerpts)

**Deliverable:** v2.1.0 ZIP. Full structured data coverage, social sharing previews, voice search support, heading hierarchy fixed.

---

### Session Plan — Phase 4 (v2.1.x: Admin Power Features) 🔲

**Estimated effort: 1 session**

1. **Custom admin columns**
   - Hook `manage_book_posts_columns` to add: Status, Author, Year, Price
   - Hook `manage_book_posts_custom_column` to render content
   - Status renders as coloured badge matching frontend
   - Price shows formatted RM value

2. **Sortable columns**
   - Hook `manage_edit-book_sortable_columns`
   - Year and Price sortable via meta_key orderby

3. **Status filter dropdown**
   - Hook `restrict_manage_posts` to add status select on book list
   - Hook `parse_query` to filter by `_book_status` meta

4. **Quick Edit for status**
   - Hook `quick_edit_custom_box` to inject status radio group
   - JS to populate from row data on Quick Edit open
   - Save hook via `save_post_book`

5. **Dashboard widget**
   - Hook `wp_dashboard_setup` to add "Publication Stats" widget
   - Query books grouped by status, display count with badge colours

**Deliverable:** v2.1.1 ZIP.

---

### Phases 5–8: Outline

**Phase 5 (v2.3.x: Frontend Polish)** — 1–2 sessions. Back-to-top, progress bar, book filtering, social sharing, related books, lightbox, header shrink, showcase, WebMCP agent tools. All CSS/JS with no external dependencies. Social sharing and related books delivered in v2.3.0; interactive showcase delivered in v2.9.0 (refined in v2.9.1); WebMCP agent tools delivered in v2.10.0.

**Phase 6 (Dark Mode)** — ❌ REMOVED from roadmap. Out of scope per project owner.

**Phase 7 (WooCommerce Deep Integration)** — ⏸️ DEFERRED INDEFINITELY. Recorded for reference; not implemented unless re-requested.

**Phase 8 (v3.0.0: Performance & Launch)** — 1 session. Extract critical CSS, minify assets, consolidate stylesheets, preload the LCP image, Lighthouse audit, WordPress.org checklist walkthrough, `.pot` regeneration, final documentation pass. Major-version bump because this gates WordPress.org submission. Server-side items from the September 2026 baseline (compression, cache headers, image weight) are documented in `README.md` and are not theme work.

---

### Summary Timeline

| Phase | Version | Sessions | Depends On | Status |
|-------|---------|----------|------------|--------|
| 1. Foundation Fixes + Security | 2.0.0 | 2 | — | ✅ Done |
| 2. DRY & Architecture | 2.0.x | 1 | Phase 1 | ✅ Done |
| 3. SEO & Structured Data | 2.1.0 | 1–2 | Phase 2 | ✅ Done |
| 4. Admin Power Features | 2.1.x | 1 | Phase 1 | 🔲 Next |
| 5. Frontend Polish | 2.3.x | 1–2 | Phase 2 | 🟡 In Progress |
| ~~6. Dark Mode & Theming~~ | — | — | — | ❌ Removed |
| 7. WooCommerce Deep | — | — | — | ⏸️ Deferred |
| 8. Performance & Launch | 3.0.0 | 1 | Phase 5 | 🔲 Queued |

**Total active effort: ~3 sessions to reach v3.0.0 (WordPress.org submission ready).**
**Completed: Phases 1–3 + audit-derived Phases A–D + HrefMaster integration + Book Language Display.**

Phases 1–4 are sequential. Phase 5 can run in parallel after Phase 2 is done. Phase 8 is always last.

---

**Last Updated:** April 29, 2026  
**Current Version:** 2.8.1  
**Compatibility:** WordPress 6.7+ | PHP 7.4+ | Twenty Twenty-Five 1.0+
