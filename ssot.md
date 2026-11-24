# Single Source of Truth — Langgam Fikir Theme

This document is the canonical reference for *what is true, structurally,* about the Langgam Fikir WordPress theme. It collects the brand identity, design system, architecture, conventions, hard rules, and integration contracts in one place.

When information about the theme appears in more than one document, this file is authoritative for everything except version-by-version history (CHANGELOG.md), upgrade guidance (UPGRADING.md), the WordPress.org listing format (readme.txt), and the GitHub-facing intro (README.md).

---

## 1. Document Role

| Doc | Owns |
|---|---|
| **ssot.md** | Brand facts, design tokens, architecture, conventions, hard rules, plugin contracts, current state pointer |
| **CHANGELOG.md** | What changed when, in semver order, every release |
| **UPGRADING.md** | Per-version upgrade actions, security findings tracker, full enhancement roadmap |
| **readme.txt** | WordPress.org submission format (Stable tag, condensed changelog, upgrade notice) |
| **README.md** | GitHub-friendly intro, version history summary, technical highlights |

If two docs disagree on a fact, **ssot.md wins** for everything in section 1's "Owns" column for ssot.md. Version-specific data (changelog entries, upgrade actions) defer to their owning doc.

---

## 2. Brand Identity

| Field | Value |
|---|---|
| Brand name (display) | **Langgam Fikir** |
| Legal entity name | **Langgam Fikir Enterprise** |
| Brand tagline | **Dari Aksara Ke Angkasa** *(Malay; preserved as-is, never translated)* |
| Registration number | **202503137729** |
| Country | Malaysia |
| Author / lead developer | Mohd Elfie Nieshaem Juferi (MENJ) |
| Author URI | https://langgamfikir.com |
| Theme URI | https://langgamfikir.com |

**Naming rule:** the brand name appears in headers, footers, copy, the copyright line. The legal entity name appears in the footer "registered enterprise" line and any legal context. The two are distinct and must not be substituted for each other.

The tagline is Malay and is the brand's permanent tagline regardless of UI language. It is set via the WordPress site tagline option (`bloginfo('description')`), not hardcoded in theme code.

---

## 3. Design System

### 3.1 Colour palette

CSS custom properties in `:root` (assets/css/main.css). Reconciled against the stylesheet at 2.9.3; earlier editions of this table carried stale values.

| Token | Value | Role |
|---|---|---|
| `--color-primary` | `#6B5B4A` | Warm brown — headings, primary buttons, hero base |
| `--color-secondary` | `#B8985F` | Darker gold — button gradient end, footer headings/icons |
| `--color-accent` | `#C9A961` | Gold — accent borders, hover states, highlights |
| `--color-accent-dark` | `#A8893D` | Deep gold — hover on gold elements |
| `--color-text` | `#333333` | Body text, site title, nav links |
| `--color-text-light` | `#6B6057` | Secondary text — the token for any small text that must pass AA |
| `--color-text-muted` | `#8C7F72` | Tertiary text: 3.9:1 on white, 3.6:1 on `--color-bg-secondary`. **Large text only** (≥ 24px, or ≥ 18.66px bold). Not AA at body sizes |
| `--color-bg-primary` | `#FFFFFF` | Page background |
| `--color-bg-secondary` | `#F5F5F0` | Alternating section and card backgrounds |
| `--color-bg-muted` | `#EDEAE4` | Badges, subtle panels |
| `--color-bg-hero` | `#6B5B4A` | Hero fallback when no background image is set |
| `--color-border` | `#D9D0C7` | Card borders, header/footer dividers, hairlines |

Contrast reference (WCAG AA is 4.5:1 for normal text, 3:1 for large): `--color-text-light` is 6.1:1 on white, 5.6:1 on `--color-bg-secondary`, 5.1:1 on `--color-bg-muted`. `--color-primary` is 5.4:1 on `--color-bg-muted`. Gold `--color-accent` on white is 2.2:1 and is decorative only; never body text.

### 3.2 Typography

Five typefaces share the work, each with a role. All five are part of the default design; each role can be changed in Theme Options → Design.

| Family | Variable | Role |
|---|---|---|
| Crimson Text (serif) | `--font-primary` | Headings, book titles, hero statement |
| Inter (sans-serif) | `--font-secondary` | Body, UI, navigation, meta, controls |
| Sabon Next LT (serif) | `--font-reading` | Reading text: About text, article content, synopses, lead paragraphs |
| Kagnue (display) | `--font-display` | Site title, page titles, section titles |
| Ferments (script) | `--font-script` | Tagline and script accents (the 404 quip) |

All are self-hosted. Crimson Text and Inter are declared in `/assets/css/fonts.css`; Sabon Next LT, Kagnue and Ferments are licensed to the site owner and declared in `/assets/css/fonts-premium.css`, which loads only when the files exist. WOFF2 files live in `/assets/fonts/`. Role-to-selector rules and the alternatives live in `/assets/css/design.css`.

### 3.2a Palettes, corners and design layer

Earth (the table above) is the base in `:root`. **Folio** (cream page, oxblood headings, sage surfaces, brass accent) is the default palette from 2.12.0, applied as `body.lf-scheme-folio`; choosing Earth removes the class. Slate, Sage and Clay are light alternatives declared as `body.lf-scheme-*` in `assets/css/design.css`; individually picked colours are emitted as one `body.lf-has-custom-colours` block (see `inc/design.php`). Additional tokens: `--color-bg-tint`, `--color-on-accent`, `--color-on-hero` (palette), `--radius-sm/md/lg/xl/btn/pill` (corners: Soft default, Sharp, Round). Book-status colours and brand share colours are semantic and stay literal. Dark mode is not offered (see 9.2).

### 3.3 Spacing scale

Spacing tokens (`--spacing-xs` through `--spacing-xxl`) are defined in `:root`. All spacing should reference these tokens rather than hard-coded pixel values, except for fine-grained component-level adjustments.

### 3.4 Style discipline

- **No inline styles.** All styling is in external CSS via class-based approaches. The single allowed exception is dynamically computed values (e.g., `style="display:none;"` toggled by JS state).
- **No `!important`** unless overriding parent-theme rules from Twenty Twenty-Five.
- **CSS custom properties first.** Component CSS should reference tokens, not literal values.

---

## 4. Theme Metadata

| Field | Value |
|---|---|
| Theme slug | `langgam-fikir` |
| Text domain | `langgam-fikir` |
| Parent theme | Twenty Twenty-Five |
| Requires at least | WordPress 6.7 |
| Tested up to | WordPress 6.9 |
| Requires PHP | 7.4 |
| License | GPL v2 or later |
| Source language | en_US |

**Current version** is single-sourced from `style.css`'s `Version:` header. Synced copies must match across `style.css`, `functions.php` (`LANGGAM_FIKIR_VERSION` constant + `@version` docblock), `readme.txt` (`Stable tag:`), and `README.md` (`Version:` line). The `.pot` and `.po` `Project-Id-Version` headers should track major/minor releases.

**PHP constants** defined in `functions.php`:

- `LANGGAM_FIKIR_VERSION` — current version string
- `LANGGAM_FIKIR_DIR` — `get_stylesheet_directory()`
- `LANGGAM_FIKIR_URI` — `get_stylesheet_directory_uri()`

---

## 5. Architecture

### 5.1 File layout

```
LF/
├── style.css                          Theme header + design tokens
├── functions.php                      Loader only (~50 lines)
├── *.php                              Template files (single-book, archive-book, page-*, etc.)
├── inc/
│   ├── setup.php                      add_theme_support, post-types init
│   ├── enqueue.php                    Front-end + admin asset registration
│   ├── post-types.php                 book CPT, book-category, book-tag
│   ├── meta-boxes.php                 Book details, contact, about (registration + save)
│   ├── options-schema.php             Schema for the Design, About & Contact Pages and Labels tabs and extra cards; defaults, sanitising, langgam_fikir_text()
│   ├── book-fields.php                Book field schema (editor box, saving, page rows, Schema.org extras), archive sort, admin columns
│   ├── social.php                     Profile icons, share bar and networks (from langgam_fikir_social_catalog() in options-schema.php); inlines assets/icons/*.svg
│   ├── print.php                      Print stylesheet set-up (@page), Print button, paper-only header and footer
│   ├── dev-files.php                  Removes development files uploaded by FTP (.git, hidden files, tools/, tests/): lease, version record, hourly cron
│   ├── pdf.php                        Server PDFs with headless Chrome: detection, making, caching, download, admin status
│   ├── preferred-sources.php          Google preferred sources button (card, footer, shortcode), eligibility, privacy text
│   ├── design.php                     Body classes for palette/corners/fonts/parallax, custom-colour CSS, hero photo
│   ├── template-functions.php         Shared helpers, language helpers
│   ├── template-hooks.php             Action/filter hooks
│   ├── schema.php                     Schema.org JSON-LD + OG/Twitter
│   ├── breadcrumbs.php                Visible breadcrumb component
│   ├── toc.php                        Auto-TOC for long posts
│   ├── hrefmaster-integration.php     HrefMaster plugin recognition
│   ├── webmcp.php                     WebMCP agent tools + public catalogue REST (langgam-fikir/v1)
│   ├── woocommerce.php                Conditional WC support (loaded only when WC active)
│   └── admin/
│       ├── meta-boxes.php             Book details + about callbacks
│       ├── meta-boxes-faq.php         FAQ repeatable meta box
│       ├── meta-boxes-reviews.php     Reviews repeatable meta box
│       ├── meta-boxes-robots.php      Per-post robots toggle
│       ├── options-extra.php          Renderers for the schema-driven Theme Options tabs and cards
│       └── theme-options.php          Tabbed Theme Options page
├── template-parts/
│   └── content/
│       ├── book-card.php              Book grid card
│       ├── resource-card.php          Blog post card
│       ├── author-bio.php             Single post author bio
│       ├── faq-section.php            FAQ accordion
│       ├── book-filters.php           Category/topic chips, result count and sort menu for the book archives
│       ├── showcase.php               Interactive homepage Showcase (books here are excluded from Latest Publications)
│       └── reviews-section.php        Reviews list with star widget
├── assets/
│   ├── css/                           main.css, design.css, additional.css, admin.css, theme-options.css, fonts.css, fonts-premium.css, showcase.css, print.css, preferred-source.css, login.css, justify.css, citations.css, citations-admin.css
│   ├── js/                            main.js, admin.js, theme-options.js, showcase.js, webmcp.js, print.js, preferred-source.js, citations.js, citations-block.js, citations-admin.js
│   ├── icons/                         Monochrome social SVGs (GPL icon pack; LinkedIn drawn for the theme, no Scribd; see its README.txt)
│   ├── images/hero/                   Bundled hero photographs (Pexels), 1920 and 960 px
│   └── fonts/                         Self-hosted WOFF2
├── languages/                         .pot / .po / .mo
├── .htaccess                          Refuses hidden files and tools, tests, bin, docs until the clean-up has run
├── tools/                             Development only (self-tests); deleted from live sites by inc/dev-files.php
├── readme.txt
├── README.md
├── UPGRADING.md
├── CHANGELOG.md
└── ssot.md                            ← this file
```

### 5.2 Loading conventions

- **functions.php is a loader only.** It defines the version constant and `require_once`'s the modules in `/inc/`.
- **Admin files load only on `is_admin()`** via a guarded block in `functions.php`.
- **WooCommerce support loads only when WC is active** via `class_exists('WooCommerce')` guard.
- **Asset enqueue is hook-scoped.** Front-end assets fire on `wp_enqueue_scripts`. Admin assets fire on `admin_enqueue_scripts` and check `$screen->post_type` to scope to relevant edit screens (currently `page` and `book`).
- **Cache-busting** uses `filemtime()` on the asset file, not the theme version, so per-file changes invalidate independently.
- **`webmcp.js` is the one site-wide optional script.** Catalogue search is a site-level capability, so the tool-registration script loads on every front-end page while `webmcp_enabled` is on (footer, ~4 KiB, no dependencies) and exits immediately where `document.modelContext` is absent.

### 5.3 Settings storage

The theme uses the **WordPress Options API**, not the Customizer, for all custom settings. All options live under a single `langgam_fikir_options` array option, accessed via `langgam_fikir_get_option( $key, $default )` defined in `inc/template-functions.php`.

The Customizer is reserved for WordPress core features only (custom logo, site identity, menu locations, widgets).

The Theme Options page is registered under **Appearance → Theme Options**, with tabs for Homepage, Design, About & Contact Pages, Contact & Footer, Labels & 404, Legal Entity, Social & SEO. The Design, About & Contact Pages and Labels & 404 tabs, and the extra cards on Homepage and Contact & Footer, are defined once in `inc/options-schema.php`; the original four tabs stay hand-written in `inc/admin/theme-options.php`. Text options are read with `langgam_fikir_text( $key )` (blank falls back to the default unless the field is flagged `allow_empty`). Tabs are rendered via `tab=` query string and a single underlying page render.

### 5.4 Performance contract

What the theme guarantees, and what it leaves to the host. Baseline: PageSpeed Insights audit of langgamfikir.my, 6 September 2026 (see `UPGRADING.md`, Phase 8).

**Theme guarantees:**
- **No front-end jQuery dependency.** `main.js` and `showcase.js` are vanilla and declare no dependencies. jQuery is used only in admin (`jquery-ui-sortable` for the FAQ meta box). Any front-end jQuery on the live site is a plugin's.
- **All theme JS loads in the footer** (`in_footer = true`), so no theme script is render-blocking.
- **Conditional assets.** `additional.css` loads only on pages using the Contact, About, or Privacy Policy templates (matched via `is_page_template()`, with a slug fallback; never by slug alone, since Template Name templates are slug-independent); `showcase.css`/`showcase.js` load only on the front page while the Showcase is enabled. Anything that must hold on every page (the spam honeypot's `.lf-hp`) lives in `main.css`.
- **Fonts are self-hosted** (`assets/fonts/`, `font-display: swap`); no third-party font requests.
- **Display sizes never fall back to the original upload.** Where a registered size may be missing (covers that predate it), resolve via `wp_get_attachment_image_src()` and honour its `is_intermediate` flag, falling back to `large`. `langgam_fikir_showcase_image_url()` is the reference implementation.
- **LCP candidates are never lazy-loaded.** The homepage About image is the LCP element at phone widths: it renders `loading="eager" fetchpriority="high"` in both branches, and the custom logo is held at `fetchpriority="auto"` so WordPress does not hand the priority slot to a 60px header image.
- **Below-the-fold imagery is deferred.** Showcase background layers carry `data-showcase-src` and are applied by `showcase.js` within 600px of the viewport; no inline `background-image` in the template.
- **Assets are safe to cache for a year.** Every enqueue is versioned with `filemtime()`, so a changed file gets a new query string and a long `Cache-Control` TTL never serves stale CSS/JS.
- **Front-end footprint** at 2.9.x: ~86 KiB of CSS/JS uncompressed (`main.css` 84 KiB, the rest under 10 KiB combined), ~20 KiB once the server compresses. That was ~4% of first-party bytes in the baseline audit.

**Host responsibilities (the theme ships no `.htaccess` and sets no response headers):**
- Compression (gzip or brotli) for text assets. The baseline audit showed none active: `jquery.min.js` transferred at its full 86 KiB.
- Browser caching headers. The baseline showed `Cache-Control` absent on every asset. The reference `.htaccess` block lives in `README.md` → Server & Performance.
- Image weight. The site's image pipeline converts covers to AVIF; the theme only registers sizes (`book-cover`, `book-thumbnail`, `book-showcase`). Oversized source uploads (the baseline had a 639 KiB PNG cover and a 459 KiB PNG logo) are content, not theme.
- Plugin payloads (Contact Form 7's jQuery, Site Kit, Wikipedia Preview) are outside the theme's control and are not to be dequeued from theme code.

---

## 6. Book Lifecycle System

### 6.1 The 10 statuses

Stored as `_book_status` post meta on the `book` CPT.

| Category | Statuses |
|---|---|
| Pipeline | `announced`, `coming-soon` |
| Available (orderable) | `published`, `new-release`, `limited-edition`, `digital-only`, `pre-order` |
| Unavailable | `sold-out`, `reprinting`, `out-of-print` |

The admin UI groups status radio buttons by category fieldset.

### 6.1a Book fields (2.11.0)

Stored as `_book_{key}` post meta. Original fields: `subtitle`, `author`, `editor`, `translator`, `publisher`, `year`, `isbn`, `language`, `status`, `price`. Added in 2.11.0: `original_title`, `translated_from`, `edition`, `series`, `format` (`paperback`, `hardcover`, `ebook`, `audiobook`), `pages`, `dimensions`. The editor box, the saving, the information card on the book page and the Schema.org extras are all driven by `inc/book-fields.php`; add a field there and it appears in all four. Categories (`book-category`) and tags (`book-tag`, shown as "Topics") appear as linked chips on the book page and as filters on the archives.

### 6.2 Helper functions

Defined in `inc/template-functions.php`:

- `langgam_fikir_status_is_orderable( $status )` — returns true for the 5 available statuses, gates the ORDER button
- `langgam_fikir_status_has_price( $status )` — returns true when price should be displayed (matches orderable)
- `langgam_fikir_get_status_label( $status )` — human-readable label for a status code
- `langgam_fikir_get_showcase_items()` — Showcase query (statically cached); empty array when the section won't render
- `langgam_fikir_showcase_book_ids()` — IDs of showcased books; the Latest Publications grid excludes them so the two homepage sections never repeat a title
- `langgam_fikir_showcase_image_url( $attachment_id )` — Showcase background URL: `book-showcase` → `large` → original, using the `is_intermediate` flag so a missing size never yields the full upload
- `langgam_fikir_webmcp_book_data( $post_id, $full )` — public agent-facing representation of a book (see §7.4); `langgam_fikir_webmcp_form_attrs()` / `langgam_fikir_webmcp_param_attr()` echo declarative tool attributes when enabled

### 6.3 Schema.org offers

Each status maps to a `schema.org/Offer.availability` value in the Book JSON-LD. The mapping lives in `single-book.php`'s schema-build block.

---

## 7. Plugin Integration Contracts

### 7.1 HrefMaster (hreflang + per-post HTML lang)

**Detection:** `defined('HREFMASTER_VERSION')` or `function_exists('hrefmaster_output_tags')`.

**What HrefMaster owns when active:**
- `<link rel="alternate" hreflang>` tag emission on `wp_head` priority 1
- `<html lang="">` attribute via its own `language_attributes` filter (when its option is on)

**What the theme does:**
- Reads `_hrefmaster_html_lang` post meta as the highest-priority language signal
- Aligns Schema.org `inLanguage` (WebSite, Article, WebPage, AboutPage, ContactPage, Book) and Open Graph `og:locale` with the resolved page language
- Optionally switches WordPress locale on singular pages with HrefMaster lang override, when a matching `langgam-fikir-{locale}.mo` file ships in `/languages/`
- Surfaces a status card on the Theme Options Social & SEO tab showing integration state

**Resolution priority** (canonical, used by `langgam_fikir_get_page_lang()`):

1. `_hrefmaster_html_lang` post meta (when HrefMaster active and meta set)
2. `hrefmaster_get_self_lang()` (when HrefMaster active, no per-post override)
3. `_book_language` post meta (book CPT only, plugin-independent)
4. `get_locale()` (final fallback; `en-US` if WordPress reports none)

**Default language is en_US.** The site runs in English (Settings > General > Site Language: English (United States)), so `<html lang>`, `og:locale` and Schema.org `inLanguage` are `en-US` unless a book's Language field or HrefMaster says otherwise. The Malay translation (`ms_MY`) is optional and applies only when the site language is switched to Bahasa Melayu.

### 7.1b Plugin detection (2.15.0)

Text justification (`inc/justify.php`), citations (`inc/citations.php`, `inc/citations-analytics.php`) and the private login address (`inc/login-address.php`) are theme features with no reference to any plugin. Only `inc/plugin-takeover.php` knows the plugins they replace, listed in `langgam_fikir_absorbed_plugins()`. Each entry gives the plugin's name, where its settings now live, the feature key, a `running` check, an `import` callback and optional `content` rules.

While this theme is active such a plugin is:
1. imported once into `langgam_fikir_options`;
2. deactivated, with a notice;
3. once off, its block and shortcode names in post content and widgets are converted to the theme's own (once; flag `langgam_fikir_migrated_content`);
4. blocked from re-activation on the Plugins screen.

While its code is still loaded, `langgam_fikir_feature_conflict( $feature )` is true and the matching feature registers nothing. The plugin's files and options are left untouched.

| Plugin | Feature | Settings | Imported | Content converted |
|---|---|---|---|---|
| Auto Justify Content 2.0.0 | `justify` | Theme Options > Design > Text Justification | `ajc_enabled`, `ajc_scope`, `ajc_hyphen`, `ajc_mobile`, `ajc_fallback`, `ajc_exclude` | none |
| CitePress 3.0.1 | `citations` | Theme Options > Citations; Appearance > Citation Analytics | `citepress_setting` (all keys); rows of `{prefix}citepress_analytics` → `{prefix}lf_citation_analytics` | `citepress/block` → `langgam-fikir/citation`; `[citepress_cite]` → `[cite]`; `[citepress_cite_bibliography]` → `[cite_bibliography]` |
| WPS Hide Login 1.9.20 | `login_address` | Theme Options > Login Page > Login Address | `whl_page` → `login_slug`, `whl_redirect_admin` → `login_redirect`, and `login_hide` on (site option fallback on multisite) | none |

The citation style and download-format catalogues live in `inc/options-schema.php` (Theme Options needs them before `inc/citations.php` loads).

The private login address recognises the request at `after_setup_theme` priority 1 (a theme cannot run at `plugins_loaded`, where the plugin did) and serves it at `wp_loaded`, before WordPress picks a screen. It reads its settings straight from `langgam_fikir_options`, without building the options schema, so no translations load before `init`. Recovery from a lock-out: rename the theme folder.

To absorb another plugin: build its feature as an `inc/` module with settings in the options schema and no mention of the plugin, then add an entry to the list in `inc/plugin-takeover.php` with its import callback (also in that file) and any content rules. WordPress.org themes may not deactivate plugins; this is a site-specific choice.

### 7.2 WooCommerce (light support only)

When `class_exists('WooCommerce')`, `inc/woocommerce.php` loads and adds:
- `add_theme_support('woocommerce')` and gallery zoom/lightbox/slider
- 3-column product loop
- 12 products per page

**Deep WooCommerce integration is deferred indefinitely** (see Section 9).

### 7.3 Rank Math SEO (no contract)

The theme does not depend on Rank Math. HrefMaster has its own optional Rank Math sitemap integration; that's plugin-to-plugin and the theme stays out of it.

### 7.4 WebMCP (agent tools)

Not a plugin: a browser API (W3C Web Machine Learning CG draft) that the theme targets directly from `inc/webmcp.php` and `assets/js/webmcp.js`.

- **Tools registered:** `search-publications`, `get-publication`. Names are fixed; changing them breaks agents that learned them. Both are `readOnlyHint: true` and must stay so: neither may ever write, authenticate, or read session-bound data.
- **Data source:** `GET /wp-json/langgam-fikir/v1/publications[/{slug}]`, public, `permission_callback => __return_true`, published books only, fields limited to what public templates already render. Adding a field here means it is public; treat the endpoint as part of the site's public surface.
- **Declarative use:** only the contact form (`toolname="contact-publisher"`), never with `toolautosubmit`. Any future declarative form must keep irreversible actions in the visitor's hands.
- **Honeypot rule:** `webmcp.js` clears `website_url` when `SubmitEvent.agentInvoked` is true. If the honeypot field is renamed, update both `page-contact.php` and `webmcp.js`.
- **Exposure:** WebMCP default (same-origin). Do not set `exposedTo` unless a specific trusted embed needs it.
- **Options:** `webmcp_enabled`, `webmcp_contact_form`, `webmcp_origin_trial_token` under the Social & SEO tab. The token is emitted as `<meta http-equiv="origin-trial">` at `wp_head` priority 1.
- **Spec status:** draft; the declarative API's schema synthesis and response delivery are open upstream. Re-check the spec before extending declarative use.

### 7.5 Print and server PDFs (2.16.0)

Not a plugin: the print stylesheet (`assets/css/print.css`) loads on every page with `@page` rules from **Theme Options > Print & PDF** added inline (`langgam_fikir_print_page_css()`). Print colours come from turning the colour tokens to ink inside `@media print`, so new components print correctly as long as they use the tokens.

Server PDFs (`inc/pdf.php`) run headless Chrome on the site's own permalink with `?lf-print=pdf`. Rules:
- **No shell:** `proc_open()` with an argument array, a 90-second limit, and a throw-away profile; background networking is off.
- **Program check:** the program must be an executable whose name looks like Chrome, Chromium or Edge (`langgam_fikir_pdf_path_ok()`).
- **Page check:** the page is fetched first and must return 200 with the `lf-print-pdf` body class, so error pages and cached foreign copies are never printed.
- **What gets a PDF:** published, public, non-password-protected articles, books and pages whose Print button is on.
- **One PDF at a time:** transient `lf_pdf_lock`; anyone else, and every failure, goes to `?lf-print=1`, which opens the print dialog.
- **Storage:** files go in `uploads/langgam-fikir-pdf/{ID}-{fingerprint}.pdf`. The fingerprint covers `post_modified_gmt`, the theme version and the print, palette and citation settings. Files are removed on `save_post`, on post deletion and when the print settings change.
- **Status:** the admin status (`langgam_fikir_pdf_status_html()`) is the place to check what the server can do.

### 7.6 Google preferred sources (2.16.0)

Not a plugin: Google's preferred-sources library, used the "advanced, custom design" way. Rules:
- **Script:** `https://news.google.com/swg/js/v1/publisher.js`, always with `preferred-sources-control="manual"` so Google never draws its own button. It is initialised through the `self.PREFERRED_SOURCE` queue with `{ theme: 'light', lang }`.
- **When it loads:** only on pages that print a button (`data-lf-preferred-source`), and only when the reader nears it. Never in link-only mode.
- **The link:** every button is the deeplink first (`langgam_fikir_gps_deeplink()`). The script only upgrades a click once Google's API is ready, so a blocked script never breaks the button.
- **Eligibility:** domain and subdomain sites only (`langgam_fikir_gps_eligible()`); a site in a subfolder shows no button.
- **Look:** design tokens only (`--color-primary`, `--color-accent`, `--color-bg-*`, `--font-*`, `--radius-*`). The one exception is Google's "G" mark: an inline SVG in Google's own colours, on a white disc (`.lf-gps__mark`) on the filled button. Don't recolour it. Hover keeps the text colour so contrast stays at least 4.5:1 in every palette.
- **Options:** `gps_enabled` (off by default), `gps_on_posts`, `gps_on_books`, `gps_on_footer`, `gps_heading` (`{site}` placeholder), `gps_text`, `gps_label`, `gps_script` (`near` | `link`).

### 7.7 Development files clean-up (2.17.0)

`inc/dev-files.php` deletes what an FTP upload from a working copy leaves in the theme folder: hidden entries at the top (not `.htaccess`, `.user.ini`, `.well-known`) and `tools/` and `tests/`. Rules:
- **When:** on `init` for the first request after `LANGGAM_FIKIR_VERSION` differs from the option `lf_devfiles_version`, and hourly through the WP-Cron event `langgam_fikir_devfiles_hourly` (a host with `DISABLE_WP_CRON` needs a server cron on `wp-cron.php`). The hourly callback does nothing when `langgam_fikir_devfiles_list()` is empty; otherwise it deletes the recorded version and runs the same clean-up.
- **Kept:** `wp_get_environment_type()` of `local` or `development`, or the constant `LANGGAM_FIKIR_KEEP_DOTFILES` set to true. The event is not scheduled there.
- **One request at a time:** the lease is the option `lf_devfiles_lease` (`token|time`, autoload off). `add_option()` takes a free one; the running clean-up renews it at most once a minute; one older than five minutes is taken over with a single compare-and-set `UPDATE`; it is released with a single `DELETE ... AND option_value = <own value>`. Direct SQL is always followed by dropping the cached copy. A request that loses its lease stops.
- **Recording:** the version is recorded only after the clean-up returns and `langgam_fikir_devfiles_list()` is empty. On failure the version stays unrecorded, the lease is renewed and kept (retry in five minutes), and `lf_devfiles_logged` limits the log line to one a day.
- **Removal:** a symlink is removed itself (never followed); read-only files and folders are made writable first.
- **Test:** `php tools/test-dev-files.php`. Add a development folder name to `langgam_fikir_devfiles_list()` and to the `.htaccess` together.

---

## 8. Documentation Conventions

### 8.1 The five files

Exactly five documentation files exist in the theme root. **Never create TODO.md or other extra .md files.**

| File | Format | Update on |
|---|---|---|
| ssot.md | Stable structural reference | Architectural changes, new conventions, plugin contracts |
| CHANGELOG.md | Keep a Changelog format | Every release |
| UPGRADING.md | Per-version upgrade notes + roadmap | Every release |
| readme.txt | WordPress.org plugin/theme directory format | Every release |
| README.md | GitHub-facing intro | Every release |

### 8.2 Version sync rule

When the theme version changes, **all of the following must be updated to match in the same commit:**

- `style.css` `Version:` header
- `functions.php` `LANGGAM_FIKIR_VERSION` constant
- `functions.php` `@version` docblock
- `readme.txt` `Stable tag:`
- `README.md` `Version:` field

The `.pot` and `.po` `Project-Id-Version` headers should also track major/minor releases.

### 8.3 Source language

All theme source strings are in **en_US**, and en_US is the default site language. Translations live in `/languages/` as `.po`/`.mo` files. Brand identifiers ("Langgam Fikir", "Langgam Fikir Enterprise", the tagline) are intentionally untranslated as proper nouns.

---

## 9. Hard Rules / Constraints

### 9.1 Always

- **Zip root folder is `/LF/`** — never renamed to `langgam-fikir/` despite the slug. WordPress.org compliance is preserved by keeping the slug correct in `style.css`; the zip wrapper is a local convention.
- **Zip filenames use dots for version numbers** — `langgam-fikir-2.8.1.zip`, never `langgam-fikir-2_8_1.zip`.
- **The Unsplash fallback image in `front-page.php` about-snippet section is preserved** — do not remove or replace.

### 9.2 Out of scope

- **Dark Mode** is permanently out of scope. No dark palette, no `prefers-color-scheme` switching, no toggle. *Amended at 2.11.0 on MENJ's instruction:* a palette selector is in scope, limited to light palettes (Folio, Earth, Slate, Sage, Clay) plus custom colours in Theme Options → Design.

### 9.3 Deferred

- **WooCommerce Deep Integration** is deferred indefinitely. The minimal stub in `inc/woocommerce.php` remains but template overrides, cart/checkout/account styling, Book↔Product linking, and mini-cart will not be built unless explicitly re-requested.

### 9.4 Authority

When in doubt, defer to MENJ. Brand decisions, scope decisions, and "is this in or out" questions all flow up. This document is a snapshot of his stated direction; if it conflicts with a current MENJ instruction, the current instruction wins and this document gets updated.

---

## 10. Current State

| Field | Value |
|---|---|
| Latest released version | **2.17.0** |
| Active development phase | Phase 4 (Admin Power Features) — next |
| In-progress phase | Phase 5 (Frontend Polish) — 3 of 8 items delivered |
| SEO/AEO audit score | **92/100** (last assessed at v2.6.0) |
| WordPress.org submission ready? | Not yet — gated on Phase 8 (v3.0.0) |

The full enhancement roadmap and per-phase details live in **UPGRADING.md**. This SSOT only points to the current state; it does not duplicate the roadmap.

---

## 11. Authoritative Ownership Matrix

When more than one doc covers a topic, this is which one wins:

| Topic | Authoritative |
|---|---|
| Brand name vs legal entity distinction | **ssot.md** §2 |
| Color palette values | **ssot.md** §3.1 |
| Font choices | **ssot.md** §3.2 |
| File structure | **ssot.md** §5.1 |
| Loading conventions | **ssot.md** §5.2 |
| Settings storage approach (Options API not Customizer) | **ssot.md** §5.3 |
| Book status list and helpers | **ssot.md** §6 |
| Language resolution priority | **ssot.md** §7.1 |
| Documentation file set | **ssot.md** §8.1 |
| Version sync rule | **ssot.md** §8.2 |
| Hard rules (zip root, Unsplash, no Dark Mode, no Deep WC) | **ssot.md** §9 |
| Per-version changelog entries | **CHANGELOG.md** |
| Per-version upgrade actions | **UPGRADING.md** |
| Roadmap (full detail, security findings, phase tables) | **UPGRADING.md** |
| WordPress.org listing copy | **readme.txt** |
| GitHub intro and version-history summaries | **README.md** |

---

**Last Updated:** November 18, 2026 (v2.12.0)
**Maintained by:** MENJ
