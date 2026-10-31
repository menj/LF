# Changelog

All notable changes to the Langgam Fikir WordPress theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.11.0] - 2026-10-31

Design and content release. The Showcase becomes a readable card grid, the look is configurable from Theme Options, and the text of the homepage, About, Contact, footer and 404 pages is editable.

### Added
- **Theme Options → Design** tab: four light palettes (Earth, the original; Slate; Sage; Clay) with optional per-colour overrides (primary, accent, background, surface, text; text colours on accent and hero are chosen automatically for contrast); a font-role selector; corner style (Soft default, Round, Sharp = the pre-2.11 look); parallax (wide screens, all screens, off)
- **Five-typeface stack**, all part of the default: Kagnue (site title, page and section titles), Crimson Text (headings, book titles, hero statement), Sabon Next LT (reading text: About, articles, synopses), Inter (interface and body), Ferments (tagline and script accents). Every role can be changed. Sabon Next LT, Kagnue and Ferments are licensed to the site owner; their WOFF2 files are in `assets/fonts/` and load from `assets/css/fonts-premium.css` only when present, so a copy without them falls back to Crimson Text with no 404s
- **Hero photo** (Homepage tab): four bundled Pexels photographs (library corridor, reading room, open book, leather-bound books), Random, or None. Default is the library corridor. A picture chosen under Hero Background Image still overrides them. Each photo ships at 1920 and 960 px; phones load the 960 px file through a CSS variable (no extra request). Credits are in `assets/images/hero/README.txt`
- **About & Contact Pages** tab: lead, mission text, two cards, contact prompt and link on the About page; introduction, form placeholders, button, success/error messages and the pre-filled order email (subject and message templates with `{title}`, `{author}`, `{year}`, `{price}`) on the Contact page
- Homepage tab: hero button labels, About section heading/text/button, publications and resources headings and subtitles, the "view more" label. Contact & Footer tab: footer headings and copyright suffix
- **Labels & 404** tab: book-card and article-card button text, and the 404 page text
- **Quirky 404**: an animated bookshelf with a gap, a toppled book and a drifting page; a random quip (one per line, editable) in Ferments; a message, search box and buttons; three suggested books. Animation stops for reduced motion
- Parallax on the homepage About image, the About page logo and the book cover (the hero and Showcase already had it); clamped so an image never exposes its frame; off for reduced motion and under the setting above
- `inc/options-schema.php` (one schema drives the new tabs, their defaults and sanitising), `inc/admin/options-extra.php`, `inc/design.php`, `assets/css/design.css`, `assets/css/fonts-premium.css`, and `langgam_fikir_text()` for editable text

- **Fuller book information.** New book fields: original title, translated from, edition, series, format (paperback, hardcover, e-book, audiobook), pages and size. The book page's information card shows every field that is filled in, in a fixed order, plus linked **category** and **topic (tag)** chips. Schema.org gains `numberOfPages`, `bookFormat`, `bookEdition`, `alternateName` (original title), `isPartOf` (series), `genre` (categories) and `keywords` (tags). Labels are editable under Theme Options → Labels & 404
- **Browse the catalogue.** The Publications archive and the category and tag pages share a filter bar (`template-parts/content/book-filters.php`): category chips with counts, topic chips, a result count and a sort menu (newest, oldest, title A to Z; `?sort=`). Chips are plain links to the taxonomy archives, so every state is crawlable, and they keep the chosen sort
- **A better Book Details editor box**: sections (title & credits, publication, physical details, availability) in a two-column grid, status picker kept, and a summary of what is still empty (cover, excerpt, author, year, ISBN, pages, format, category, topics). The Books list gains Author, Status and Year columns
- `inc/book-fields.php`: one field schema drives the editor box, saving, the page display and Schema.org

### Changed
- **Showcase** renders the theme's own book card (`template-parts/content/book-card.php`, the same component as Latest Publications) on the dark band, instead of bare title links; nothing is dimmed when another card is hovered. Three cards per row, so six books give two rows. A blurred cover backdrop crossfades to the highlighted book and drifts with a clamped parallax; an optional slow auto-rotation (`showcase_autoplay`, default on) pauses on hover/focus, stops off screen and never runs with reduced motion. Count range is now 2-9 (was 2-6), default 6 (was 3)
- **Stylesheet tokens**: the hard-coded colours in `main.css` that belong to the palette are now variables (`--color-bg-tint`, `--color-on-accent`, `--color-on-hero`, and the existing ones); gold and status gradients are flat colours; the hero overlay derives from `--color-bg-hero`. Status and brand share colours stay literal on purpose. Gold buttons keep their white text in Earth
- **Rounder corners**: `--radius-sm/md/lg/xl/btn/pill` tokens (Soft is the default; Sharp restores the previous radii)
- The Contact and About pages no longer show placeholder values: the public email defaults to langgam.fikir@gmail.com (Footer Contact), the phone number is blank by default and is hidden when empty; the lorem-ipsum intro is replaced by a real sentence
- `contact_email` (form recipient) and `footer_email` default to langgam.fikir@gmail.com

### Fixed
- Phones scrolled horizontally until the About section was revealed, because the scroll-reveal start offset (`.reveal-right`) pushed it past the viewport edge. `.about-snippet` now clips it

### Notes
- Dark mode remains out of scope; every palette has a light page (`ssot.md` §9.2 amended: a palette selector is now in scope, dark mode is not)
- The Unsplash fallback image in `front-page.php` is untouched
- New strings are pending the next `.pot` regeneration; defaults are translatable, so Malay installs fall back to the existing catalogue where a string already exists
- "Pinned books" mode for the Showcase, previously pencilled in for 2.11.0, moves to 2.12.0

## [2.10.0] - 2026-09-06

WebMCP release. The theme now exposes its existing public functionality to in-browser AI agents through the WebMCP proposal (W3C Web Machine Learning CG draft): two read-only catalogue tools and an agent-fillable, human-sent contact form. Everything is progressive enhancement; browsers without `document.modelContext` see no change.

### Added
- **`inc/webmcp.php`** — the module. Registers a public, read-only REST namespace `langgam-fikir/v1` with `GET /publications` (params: `search`, `status` restricted to the 10 lifecycle keys, `category` slug, `per_page` 1–20) and `GET /publications/{slug}`. Responses reuse `langgam_fikir_get_book_meta()`, `langgam_fikir_get_status_label()`, `langgam_fikir_status_is_orderable()` and `langgam_fikir_status_has_price()`, so agents see exactly the data the public site renders: title, subtitle, author, year, language, status key + label, orderable flag, price (MYR) only when the status has one, excerpt, cover, categories; the single-book route adds editor, translator, publisher, ISBN, and a trimmed description. `Cache-Control: public, max-age=300` on both routes
- **`assets/js/webmcp.js`** — feature-detects `document.modelContext` and registers `search-publications` and `get-publication`, both `readOnlyHint: true`, `untrustedContentHint: false` (first-party content only). Results use the MCP `{ content: [{ type: "text", text }] }` shape with a one-line summary followed by JSON. Loaded in the footer on every front-end page while enabled; ~4 KiB, no dependencies
- **Declarative contact-form tool** — `page-contact.php` now emits `toolname="contact-publisher"`, `tooldescription`, and a `toolparamdescription` per field via two gated helpers (`langgam_fikir_webmcp_form_attrs()`, `langgam_fikir_webmcp_param_attr()`), which print nothing when the feature is off. **No `toolautosubmit`**: an agent can complete the form, the visitor presses Send
- **Honeypot made agent-aware** — the spam field carries `toolparamdescription="Leave this field empty."`, and `webmcp.js` clears it on submissions where `SubmitEvent.agentInvoked` is true, so a legitimate agent-assisted message is not silently discarded. Bots that fill it are still rejected
- **Theme Options → Social & SEO → Agent Tools (WebMCP)**: `webmcp_enabled` (default on), `webmcp_contact_form` (default on), `webmcp_origin_trial_token` (default empty; emitted as `<meta http-equiv="origin-trial">` in `wp_head` for Chrome 149 / Edge 150 trials)

### Security posture
- Both tools are honestly read-only; neither writes, authenticates, or touches session state. The endpoint returns only published books and only fields already on public pages
- Tool exposure stays at the WebMCP default (same-origin); `exposedTo` is not widened
- Sending mail remains a human action; the theme never registers a tool that submits the form
- WebMCP requires a secure context; the site is HTTPS

### Notes
- Browser support at time of release: Origin Trials in Chrome 149 and Edge 150, experimental in Brave Leo, supported in ChatGPT Desktop; no Firefox or Safari. The declarative form API is the least settled part of the draft (schema synthesis for `<select>`, `min`/`max`, and response delivery are open upstream), which is why the contact form is the only declarative use
- The REST endpoint is useful beyond WebMCP: any client can read the catalogue at `/wp-json/langgam-fikir/v1/publications`
- Translation catalog headers moved to 2.10.0 (minor release); new strings pending the next `.pot` regeneration
- "Pinned books" mode for the Showcase, previously pencilled in as the 2.10.0 candidate, moves to 2.11.0

## [2.9.9] - 2026-09-06

Book card content pass: the status no longer appears twice, and cards carry a short excerpt. Also corrects the record on 2.9.7.

### Changed
- **Status shown once** — the footer's `.book-card-price-label` (status text for non-orderable books) is removed from `book-card.php`. The cover badge already carries the status; the price slot now holds a price when the book is orderable and stays empty otherwise, which keeps the footer's flex row aligned
- **Excerpt on cards** — a 20-word excerpt (`wp_trim_words( get_the_excerpt(), 20 )`) renders between the subtitle and the author/year metadata. The book post type already supported excerpts; manual excerpts are used when set, otherwise the trimmed content
- **`main.css`** — new `.book-excerpt` rule; the dead `.book-card-price-label` rule removed from the canonical footer block

### Fixed
- **2.9.7 correction** — 2.9.7 stated the card footer had "no CSS at all" and added a block of footer rules near `.book-meta`. That diagnosis was wrong: `main.css` has carried footer rules (`.book-card-footer`, `.book-card-price`, `.book-card-price-amount`, `.book-card-price-label`, button overrides) since before 2.8.1, near the end of the file. The stacked, unstyled rendering that prompted 2.9.7 was almost certainly a stale `main.css` served from browser cache: with no `Cache-Control` headers the browser applies heuristic caching, so fresh markup met an old stylesheet. The 2.9.7 duplicate block is removed here; the original block, which sat later in the file and therefore won the cascade anyway, is the single definition again. Net visual change from 2.9.7 → 2.9.9 on a correctly loaded page: the status label disappears from the footer and the excerpt appears; footer layout is otherwise as it was

### Notes
- Applies wherever `book-card.php` renders: homepage Latest Publications, Publications archive, taxonomy archives, search
- If a book's content is empty and it has no manual excerpt, no excerpt paragraph is output

## [2.9.8] - 2026-09-06

Mobile fixes from the mobile half of the 6 September 2026 PageSpeed run (Performance 47, Accessibility 94). Two theme defects that only appear at phone widths.

### Fixed
- **Hamburger button had no accessible name** — `.menu-toggle` in `header.php` contained three empty `<span>`s and nothing else, so screen readers announced it as "button". It's hidden on desktop, which is why the desktop pass scored 100. Now carries `aria-label="Menu"`; `aria-expanded` already conveyed state. Also clears the Agentic Browsing "accessibility tree is not well-formed" audit, which pointed at the same element
- **About image was the mobile LCP and lazy-loaded** — on phones the layout stacks and the About-section image becomes the largest element. It shipped with `loading="lazy"` and no `fetchpriority`, and the hardcoded Unsplash origin had no preconnect: resource load delay 5.8 s, LCP 13.5 s. Both branches in `front-page.php` (featured image of the About page, or the Unsplash fallback) now render `loading="eager" fetchpriority="high"`. The fallback image itself is unchanged, per the standing rule

### Added
- **`langgam_fikir_resource_hints()`** in `inc/setup.php` — adds a `preconnect` to `images.unsplash.com` on the front page only while the fallback image is in use
- **`langgam_fikir_custom_logo_attributes()`** — sets the custom logo's `fetchpriority` to `auto`. WordPress had been giving the 60px header logo `fetchpriority="high"` as the first sizeable image on the page, competing with the real LCP element

### Notes
- The mobile run predates 2.9.6 and 2.9.7 (the Showcase still shows the inline background and the full-size PNG), so those fixes are not yet reflected in its numbers
- `crimson-text-v19-latin-600.woff2` timed out in this run alongside the document prerenders and `robots.txt`; same host-side throttling pattern already recorded in the baseline
- The best fix for the About image is still yours: set a featured image on the About page so the theme serves a local, sized, AVIF-converted file instead of the Unsplash fallback

## [2.9.7] - 2026-09-06

Bug fix: the book card footer had no styles.

### Fixed
- **Unstyled card footer** — `book-card.php` outputs a `.book-card-footer` holding a `.book-card-price` slot (either `.book-card-price-amount` or, for non-orderable statuses, `.book-card-price-label`) above the Learn More button, but `main.css` had no rules for any of those four classes. Prices and status labels rendered as plain body text, and because the footer was not pinned, buttons sat at different heights across a row depending on whether a card had a subtitle. Added: `margin-top: auto` on the footer so it sits at the card bottom; a fixed `min-height` price slot so status-only and priced cards stay level; the price in Crimson Text 600 in `--color-primary` (matching the single-book `.price-amount` voice at a smaller size); and the status label in the same small-caps treatment as the Showcase status line, in `--color-text-light` for AA contrast

### Notes
- The status label in the price slot duplicates the badge on the cover on purpose: it fills the slot so all cards in a row share a footer height. If you'd rather the slot stay empty for non-orderable books, that's a one-line template change (`book-card.php`, the `elseif` branch)
- CSS only; no markup or option changes

## [2.9.6] - 2026-09-06

Performance fix for the Showcase, prompted by the first PageSpeed run against a 2.9.x build (Accessibility 100, Performance 79). The Showcase had become the heaviest element on the homepage: a legacy cover without the `book-showcase` size fell back to its 1,720 KiB original upload, and all cover layers loaded eagerly for a section that sits below the fold.

### Fixed
- **Full-size fallback** — `langgam_fikir_get_showcase_items()` used `get_the_post_thumbnail_url( $id, 'book-showcase' )`, which silently returns the original upload when that size was never generated. New helper `langgam_fikir_showcase_image_url()` checks the `is_intermediate` flag and falls back `book-showcase` → `large` → original, so a cover that predates 2.9.0 is capped at the 1024px `large` file until thumbnails are regenerated. Regenerating remains the real fix and is the documented post-upgrade step

### Changed
- **Lazy background layers** — the template part now emits cover URLs as `data-showcase-src` instead of an inline `background-image`. `showcase.js` applies them when the section comes within 600px of the viewport (IntersectionObserver, immediate fallback for old browsers). A below-the-fold Showcase now adds zero image bytes to the initial load; hover and focus behaviour is unchanged. Without JavaScript the default tinted layer shows and the titles remain plain links, which was already the no-JS experience
- **Phase 8 roadmap** gains item 8.9, context-splitting `main.css`: the audit measured 64 KiB of its 84 KiB unused on the homepage. Baseline table updated with the second run

### Notes
- This also removes the last inline `style` attribute from the Showcase markup
- The remaining audit findings are unchanged in ownership: no compression and no cache headers (host), the 459 KiB PNG logo and the PNG cover source (content), duplicate Google tag loaders and Wikipedia Preview weight (plugins), and request timeouts consistent with speculative prerendering under bot throttling (Performance Lab / host)

## [2.9.5] - 2026-09-06

Bug fix: the Contact, About, and Privacy Policy pages were rendering without `additional.css`, which left the contact form's spam honeypot visible and the extra-page styles missing.

### Fixed
- **`additional.css` not loading on template-assigned pages** — the stylesheet was gated on `is_page( array( 'contact', 'about', 'privacy-policy' ) )`, i.e. literal slugs. The three templates are assigned by *Template Name*, so real-world slugs such as `contact-langgam-fikir` never matched. `inc/enqueue.php` now checks `is_page_template()` for the three template files first, with the slug check kept as a fallback for pages that pick up `page-{slug}.php` automatically
- **Honeypot visible** — the `.lf-hp` rule that hides the "Website" anti-spam field lived only in `additional.css`. It has moved to `main.css`, which loads on every page, so the honeypot is hidden regardless of which conditional stylesheets are present. Removed from `additional.css` to keep a single definition

### Notes
- Server-side honeypot handling (`website_url` must be empty) was never affected; only the visual hiding was
- Symptom to expect after deploying: the contact form regains its intended field spacing and textarea styling, and the "Website" row disappears
- The placeholder contact details and lorem ipsum visible on the live Contact page are Theme Options and page content, not theme code

## [2.9.4] - 2026-09-06

Accessibility patch, second pass over the 6 September 2026 Lighthouse report. Gives every repeated generic link a unique accessible name. Visible text is unchanged.

### Fixed
- **Identical link text, different destinations** — "Learn More" (hero → About page, and every book card → its book), "Read More" (every resource card → its post), and the two "VIEW MORE" buttons (→ publications archive, → blog) all read the same to a screen reader's link list. Each now carries a `.screen-reader-text` suffix, so the accessible names become "Learn More about {book title}", "Learn More about {site name}", "Read More about {post title}", "VIEW MORE publications", and "VIEW MORE resources". Visible text is untouched; the suffix is visually hidden by the existing utility class
- Same treatment applied to the "Read More" links in `index.php` and the "View Book"/"Read More" links in `search.php`, which the homepage audit could not see but which share the defect

### Notes
- The report's "Document does not have a main landmark" finding was already resolved by the `<main id="primary">` wrapper added to `front-page.php` in 2.9.3; the audit ran against the pre-2.9.3 live build
- One new translatable string, `about %s`, reused across all five templates. Translation catalog headers stay at 2.9 (patch release)

## [2.9.3] - 2026-09-06

Accessibility patch. Fixes the two failures from the 6 September 2026 Lighthouse accessibility audit (both theme defects) and records the audit's main-thread finding in the Phase 8 baseline.

### Fixed
- **Badge label contrast** — `.about-snippet-badge .badge-label` ("Years of Excellence") used `--color-text-muted` (#8C7F72) on the badge's #EDEAE4 background: 3.2:1, below the 4.5:1 AA floor for small text. Now `--color-text-light` (#6B6057), 5.1:1
- **Section subtitle contrast** — `.section-header-subtitle` used the same muted token at 1rem on cream (3.6:1). Lighthouse did not flag it only because the `reveal` animation holds those headers at opacity 0 until scrolled; the failure was real for readers. Now `--color-text-light`
- **Skip link had no target on the homepage** — `header.php` links to `#primary`, but `front-page.php` was the one template without a `<main id="primary">`. The homepage is now wrapped in `<main id="primary" class="site-main" tabindex="-1">` like every other template
- **Skip-link target focusability** — all 15 `<main id="primary">` wrappers now carry `tabindex="-1"`, which is what axe's `skip-link` rule tests for. A `.site-main[tabindex="-1"]:focus { outline: 0; }` rule suppresses the outline on this non-interactive target, matching the Twenty themes

### Changed
- **Phase 8 baseline** in `UPGRADING.md` gains the fourth audit finding: long main-thread tasks (Google tag 1,052 ms, Performance Lab View Transitions 286 ms, FAQ accordion plugin 122 ms). No theme script appears in that list
- **`ssot.md` §3.1** records the contrast ceiling of `--color-text-muted` (3.9:1 on white, 3.6:1 on cream): large text only. `--color-text-light` is the token for small secondary text

### Notes
- The 24 remaining `--color-text-muted` uses are unchanged. Any of them at small sizes on light backgrounds will surface in the Phase 8 Lighthouse pass and should move to `--color-text-light` as they do
- Wrapping the homepage in `.site-main` has no visual effect: the only `.site-main` rule forces serif headings, which every homepage heading already used

## [2.9.2] - 2026-09-06

Documentation patch following a PageSpeed Insights audit of the live site. No code changes. The audit's three findings (render-blocking requests, missing cache headers, oversized images) trace almost entirely to hosting configuration, two PNG uploads, and plugin payloads; the theme's front-end footprint was about 4% of first-party bytes. This release writes down what the theme guarantees, what the host must provide, and what Phase 8 will still do in-theme.

### Added
- **`README.md` → Server & Performance** — the theme's performance guarantees (footer JS, no front-end jQuery, conditional assets, self-hosted fonts, `filemtime()` versioning), an `.htaccess` block enabling compression and one-year browser caching, image sizing guidance, and an optional mu-plugin that drops `jquery-migrate`
- **`ssot.md` §5.4 Performance contract** — the same split, stated as rules: which optimisations belong to the theme and which are hosting responsibilities the theme must not paper over from PHP
- **`UPGRADING.md` Phase 8 baseline** — the 6 September 2026 audit figures with an owner per finding, plus two new roadmap items: 8.7 stylesheet consolidation (`fonts.css` + `style.css` into `main.css`) and 8.8 LCP image preload
- **`readme.txt` FAQ** — a short answer to "PageSpeed says my CSS is render-blocking and nothing is cached"

### Changed
- Phase 8 item 8.3 (WebP support) marked largely superseded: the live site's image pipeline already serves AVIF, so remaining theme work is limited to `<picture>` fallbacks if ever needed
- Phase 8 outline updated to include the two new items and to point server-side fixes at `README.md`

## [2.9.1] - 2026-06-12

Naming and separation patch for the two homepage book sections. The interactive section is now simply the **Showcase** (a spotlight: few titles, no prices, pure attention), and the card grid is now **Latest Publications** (the catalogue feed: covers, statuses, prices, buttons). The two never repeat a title.

### Changed
- **"Featured Showcase" renamed to "Showcase"** across the Theme Options card, checkbox label, template comments, and asset file headers. Option keys (`showcase_*`) were already generic and are unchanged, so saved settings carry over untouched
- **"Featured Publications" renamed to "Latest Publications"** on the homepage section heading and the Content Counts label. The subtitle now reads "Our newest releases…" to match (the old "editors' picks" line contradicted a latest-by-date query). The `featured_books_count` option key is unchanged
- **Showcase default count is now 3** (was 6) — a single hairline row, so the Showcase and the grid below it stay proportionate. The 2–6 range still allows the full 3×2 grid; existing saved values are respected
- **No-overlap rule:** the Latest Publications query now passes `post__not_in` with the showcased book IDs, so a title can never appear in both sections. With defaults that means the newest 3 in the Showcase and the next 6 in the grid — 9 distinct books. If the Showcase is disabled or doesn't render, the exclusion list is empty and the grid reverts to the plain latest feed
- **Showcase grid adapts to count:** a `--cols-N` modifier renders 2 books as a clean 2-column row instead of leaving an empty third cell

### Added
- **`langgam_fikir_get_showcase_items()`** in `inc/template-functions.php` — the Showcase query, moved out of the template part, statically cached per request, returning an empty array when the section won't render
- **`langgam_fikir_showcase_book_ids()`** — ID accessor used by the Latest Publications exclusion

### Notes
- `template-parts/content/featured-showcase.php` is renamed to `template-parts/content/showcase.php`; `front-page.php` consumes the new path. Asset filenames (`showcase.css`/`showcase.js`) already matched
- Translation catalog headers stay at 2.9 (patch release; per convention they track major/minor)

## [2.9.0] - 2026-06-12

Featured Showcase release. Adds a full-width interactive section to the homepage: a hairline grid of book titles over a dark background, where hovering (or keyboard-focusing) a title crossfades the background to that book's cover. The pattern is the "interactive link showcase" familiar from museum and gallery sites, rebuilt on the theme's own design tokens with no dependencies.

### Added
- **`template-parts/content/featured-showcase.php`** — self-guarding template part consumed by `front-page.php` between the About snippet and the Featured Publications grid. Renders nothing when the option is off or when fewer than 2 matching books carry featured images
- **`assets/css/showcase.css` + `assets/js/showcase.js`** — dedicated asset pair, enqueued from `inc/enqueue.php` on the front page only and only while the section is enabled. Vanilla JS, ~60 lines, cache-busted via `filemtime()` like every other theme asset
- **Featured Showcase card** on the Theme Options Homepage tab with 4 new options: `showcase_enabled` (default on), `showcase_heading` (optional, default empty for the heading-free look), `showcase_count` (2–6, default 6 to fill the 3×2 grid), and `showcase_scope` (latest / available only / pipeline only). Scope filters on `_book_status` using the existing lifecycle categories
- **`book-showcase` image size** (1600×900, cropped) registered in `inc/setup.php` for the full-bleed backgrounds

### Accessibility & Motion
- Keyboard focus mirrors hover: tabbing through the titles swaps backgrounds exactly as the pointer does, and leaving the section restores the default layer
- `prefers-reduced-motion: reduce` collapses the crossfade and scale settle to a near-instant opacity swap
- A fixed dark scrim sits between cover and titles, so contrast holds over any artwork
- On touch devices the links just navigate; the swap layers on top and nothing depends on it

### Notes
- Backgrounds use inline `background-image` URLs, the same dynamic-value exception the hero already uses (`ssot.md` §3.4)
- Covers uploaded before 2.9.0 serve their full-size file until thumbnails are regenerated (Regenerate Thumbnails plugin or `wp media regenerate`)
- The section is on by default. Switch it off under Appearance → Theme Options → Homepage if you don't want it

## [2.8.1] - 2026-04-29

Documentation patch — adds the Single Source of Truth document.

### Added
- **`ssot.md`** — new fifth documentation file at the theme root. Canonical reference for brand identity, design tokens, theme metadata, architecture, conventions, plugin integration contracts, hard rules, and authoritative ownership matrix. Where two docs disagree on a structural fact (file layout, design tokens, conventions, hard rules), `ssot.md` wins; per-version data continues to live in `CHANGELOG.md` and `UPGRADING.md`

### Notes
- The maintained documentation set is now exactly five files: `ssot.md`, `CHANGELOG.md`, `UPGRADING.md`, `readme.txt`, `README.md`. No other `.md` files (TODO.md, etc.) are permitted in the theme directory
- No code changes in this release — pure documentation addition

## [2.8.0] - 2026-04-29

Book Language Display + HrefMaster Independence release. Adds first-class support for declaring a book's language through the theme's own metadata (no plugin required) while still deferring to HrefMaster when present. The Book Language now appears as a visible row on book pages and in the Book schema's `inLanguage` property regardless of whether HrefMaster is installed.

### Added
- **Book Language meta box field** on the `book` CPT — language picker dropdown in the existing Book Details meta box, stored as `_book_language` post meta. 24 BCP 47 language choices covering Langgam Fikir's likely target languages (Malay, English variants, Arabic, Indonesian, Chinese variants, Japanese, Korean, French, German, Spanish, Portuguese, Turkish, Urdu, Persian, Bengali, Hindi, Thai, Russian, Latin, Hebrew, Greek), filterable via `langgam_fikir_book_language_choices`
- **Visible Language row** in the book-meta-grid on `single-book.php` — appears below ISBN, displays the human-readable language name with proper `lang=""` attribute for screen readers; renders only when an explicit language is set (HrefMaster meta or theme meta), so the row doesn't bloat every book on a single-language site
- **Three new helpers in `inc/template-functions.php`** (front-end available, work without HrefMaster):
  - `langgam_fikir_book_language_choices()` — returns the BCP 47 → display-name catalog
  - `langgam_fikir_format_language_name( $bcp47 )` — converts code to human-readable name with base-language fallback
  - `langgam_fikir_book_has_explicit_language( $post_id )` — boolean check used to gate the visible Language row

### Changed
- **`langgam_fikir_get_page_lang()` resolution chain extended** with a third tier: HrefMaster per-post → HrefMaster's resolver → theme's `_book_language` meta (book CPT only) → site locale. The theme is now fully functional without HrefMaster — books can declare their language through the native meta box and the Book schema, Open Graph locale, and visible Language row all pick it up
- **Book schema `inLanguage`** continues to flow through `langgam_fikir_get_page_lang()` — no change to the call site, but the resolver now serves a theme-meta value when HrefMaster is absent
- **Book Details meta box save list** in `inc/meta-boxes.php` extended with `book_language`

### HrefMaster Compatibility
- HrefMaster's per-post `_hrefmaster_html_lang` meta still takes precedence when set — the new theme meta is a fallback, not a replacement
- The Book Language meta box description explicitly notes that HrefMaster overrides it when active
- The `langgam_fikir_book_has_explicit_language()` check sees BOTH meta keys, so the Language row appears regardless of which mechanism set the language

### Notes on Theme Independence
- Removing the HrefMaster plugin no longer disables the Book Language feature — it just falls back to the theme's own meta
- Schema `inLanguage` is emitted on every Book page that has either meta value set
- The site-wide Schema `inLanguage` (WebSite, Article, WebPage) still resolves to `get_locale()` when HrefMaster is absent — there's no theme-side fallback for non-book content because there's no clean place to declare per-post language without a plugin

## [2.7.0] - 2026-04-29

HrefMaster Integration release. Recognises the [HrefMaster plugin](https://github.com/hrefmaster/hrefmaster) and delegates hreflang/HTML-lang concerns to it while aligning Schema.org and Open Graph language signals with its per-page settings.

### Added
- **`inc/hrefmaster-integration.php`** — dedicated integration module containing:
  - `langgam_fikir_is_hrefmaster_active()` — boolean detection via `HREFMASTER_VERSION` constant
  - `langgam_fikir_get_page_lang( $post_id )` — single source of truth for "what language is this page in"; resolution order is HrefMaster per-post override (`_hrefmaster_html_lang` meta) → HrefMaster's own resolver → site locale
  - `langgam_fikir_lang_to_og_locale()` — converts BCP 47 to underscored OG locale form
  - `langgam_fikir_hreflang_to_locale()` — legacy shim retained for compatibility, marked `@deprecated 2.7.0`
  - Locale switching on singular pages with HrefMaster lang override, when a matching `.mo` ships in `/languages/` — opt-in by file presence
  - Admin notice on the theme's Options screen acknowledging the delegation
- **Schema.org `inLanguage` properties** added to:
  - WebSite schema (`inc/schema.php` — site-wide signal)
  - Article schema (per-post signal via `langgam_fikir_get_page_lang(get_the_ID())`)
  - WebPage / AboutPage / ContactPage schema (per-page)
  - Book schema in `single-book.php` (per-book)
- **Open Graph `og:locale`** now respects HrefMaster per-page lang (via the new resolver)
- **Theme Options Social & SEO tab** — new status card at the top:
  - When HrefMaster is active: blue info card showing version + "Open HrefMaster" link
  - When HrefMaster is inactive: muted card suggesting installation for multilingual sites
- **CSS variants** for `.lf-card-notice-info` and `.lf-card-notice-muted` in `assets/css/theme-options.css`

### Changed
- **`inc/template-functions.php`** — removed the orphaned `langgam_fikir_hreflang_to_locale()` and locale-switcher functions; replaced with a docblock note pointing to the integration module. Both functions now live in `inc/hrefmaster-integration.php` with cleaner naming (`langgam_fikir_hrefmaster_switch_locale` / `langgam_fikir_hrefmaster_restore_locale`)
- **`functions.php`** — `inc/hrefmaster-integration.php` added to core loader; `@version` bumped to 2.7.0
- The locale-switcher now only fires when HrefMaster is active AND a matching `.mo` file exists — no behaviour change for sites without the plugin

### Notes
- HrefMaster handles `<link rel="alternate" hreflang>` tag emission on `wp_head` priority 1 — the theme deliberately does not synthesise hreflang tags
- HrefMaster handles `<html lang="">` overrides via its own `language_attributes` filter — the theme does not interfere
- The theme's role is purely supportive: align everything else (Schema, OG, theme strings) with whatever HrefMaster declares for the current page

### Roadmap shift
- Phase E (Polish & Edge Cases) shifted from v2.7.0 → v2.8.0
- Dark Mode — REMOVED from roadmap (explicitly out of scope per project owner)
- WooCommerce Deep Integration — DEFERRED INDEFINITELY (recorded but not implemented unless re-requested)
- Performance & Launch → v3.0.0 (will be major-version since it gates WordPress.org submission)

## [2.6.1] - 2026-04-29

i18n housekeeping patch.

### Fixed
- **`about_badge_label` default not translation-ready** — plain string `'Years of Excellence'` in `langgam_fikir_option_defaults()` now wrapped in `__()` for consistency with `hero_text` and proper translatability when option is unset
- **Stale `.pot` file** — was last regenerated at v2.1.0 with 260 strings; regenerated against the current source with 319 strings, picking up everything added in v2.4.0 (breadcrumbs, author bio, last-updated meta), v2.5.0 (FAQ system, ItemList), and v2.6.0 (reviews system, auto-TOC, robots toggle)
- **`.pot` extraction keyword spec** — previous regenerations had treated the textdomain argument as a `msgctxt` (gettext context), polluting the file with spurious context entries; now uses correct WordPress keyword spec where `_x()` and `_ex()` are the only context-bearing calls
- **`.po` and `.mo` files for ms_MY merged** — `msgmerge`'d the regenerated `.pot` into the existing Malay translation file, preserving any prior translations and adding placeholders for new strings; `Project-Id-Version` headers in both `.pot` and `.po` synced to 2.6.1

### Verified
- All theme source strings confirmed to be in en_US (English source language)
- All translatable copy properly wrapped in `__()`, `_e()`, `_n()`, `_x()`, or their `esc_*` variants
- Brand identifiers (Langgam Fikir Enterprise, the brand tagline) intentionally left untranslated as they are proper nouns

## [2.6.0] - 2026-04-29

Catalog Enrichment release. Phase D of the SEO/AEO audit roadmap. Score lift: 88 → 92.

### Added
- **Reviews meta box** on `book` post type — repeatable review entries with reviewer name, 1–5 star rating, optional headline, body, and date; drag-to-reorder, add/remove, sequential reindexing (`inc/admin/meta-boxes-reviews.php`)
- **`Review` and `aggregateRating` properties** on the existing Book JSON-LD — unlocks star-rating rich results in Google search; rating calculated from stored review entries with `bestRating: 5`, `worstRating: 1`
- **Visible reviews section** template part (`template-parts/content/reviews-section.php`) — aggregate score block with average + star widget + count, then individual review cards with stars, optional headline, body quote, reviewer cite, and date
- **Inline aggregate rating** below book subtitle on `single-book.php` — links to the full reviews section anchor; renders only when reviews exist
- **Star rating widget helper** `langgam_fikir_render_stars()` in `inc/template-functions.php` — fills, half-fills (for fractional aggregates), and empties stars; `aria-hidden="true"` on visual stars paired with screen-reader-text labels
- **Auto-TOC for long-form posts** — new `inc/toc.php` with `langgam_fikir_render_toc()` template tag; auto-generates anchor-link TOC from H2 headings on singular posts >1200 words (filterable via `langgam_fikir_toc_threshold`); injects `id` attributes into H2s via `the_content` filter at priority 9; rendered above content on `single.php`
- **Per-post robots meta override** — Search Engine Visibility side meta box on `post`, `page`, and `book` screens with "Discourage search engines from indexing this entry" checkbox; outputs `noindex`/`nofollow` via `wp_robots` filter when checked (`inc/admin/meta-boxes-robots.php` + filter in `template-functions.php`)
- **Reviews meta box JS module** in `assets/js/admin.js` — add/remove/reindex/sortable for review rows
- **`langgam_fikir_get_reviews()` and `langgam_fikir_get_aggregate_rating()` helpers** in `inc/template-functions.php` — sanitised front-end-available accessors for review data

### Changed
- **`single-book.php`** — visible reviews section inserted between book details and FAQ section; inline aggregate rating now rendered between subtitle and details card
- **`single.php`** — auto-TOC rendered above `the_content()` on long-form posts (silent on short posts)
- **`functions.php`** — three new core/admin includes registered; `@version` bumped to 2.6.0
- **`assets/css/main.css`** — new sections: Reviews Section, Star Rating Widget, Inline Aggregate Rating in Book Header, Auto Table of Contents; `scroll-behavior: smooth` and `scroll-margin-top: 80px` on entry-content H2 for TOC anchor jumps
- **`assets/css/admin.css`** — new section: Reviews Meta Box (with grid for reviewer/rating/date row)

### SEO/AEO Score Impact
- Layer 6 (Structured Data): 10/10 → 10/10 (perfect — Review + aggregateRating now present on books)
- Layer 5 (AEO Optimisation): 13/15 → 14/15 (auto-TOC adds long-form content navigation, gain marginal because FAQ already covered most AEO surface)
- Layer 7 (E-E-A-T Signals): 4/5 → 5/5 (review citations from real readers are the canonical trust signal)
- Layer 1 (Technical Surface): 13/15 → 14/15 (per-post robots control closes the audit's SEO-ROBOTS finding)
- **Overall: 88/100 → 92/100**

## [2.5.0] - 2026-04-29

AEO Layer release. Phase C of the SEO/AEO audit roadmap. Score lift: 80 → 88.

### Added
- **FAQ meta box** on `book` and `page` post types — repeatable Q/A pairs with drag-to-reorder, add/remove rows, sanitised on save (`inc/admin/meta-boxes-faq.php`)
- **`FAQPage` JSON-LD output** in `inc/schema.php` — `langgam_fikir_schema_faq()` renders `mainEntity` array of `Question` / `Answer` pairs when post has FAQ data set
- **Visible FAQ accordion** template part (`template-parts/content/faq-section.php`) — paired with the JSON-LD so search results and visible page show the same data; `aria-expanded`, `aria-controls`, focus styles, keyboard support
- **`ItemList` JSON-LD** on `/publications/` archive and book taxonomy archives — each book becomes a `ListItem` with position, name, url, and image; tells search engines this archive is a curated catalog
- **H2 sectioning on `single-book.php`** — "About the Book" promoted from H3 to H2; visually-hidden H2s added for "Book Details", "Order This Book", "Share This Book"; reinforces content-structure signal for search engines
- **`langgam_fikir_get_faqs()` helper** in `inc/template-functions.php` — returns sanitised FAQ array, available on both front-end and admin
- **`initFaqAccordion()` JS** in `assets/js/main.js` — click-to-toggle accordion with proper ARIA state management

### Changed
- **`inc/enqueue.php`** — admin JS now enqueues on both `page` and `book` screens (FAQ meta box needs JS on both); `jquery-ui-sortable` added to dependency list for drag-reordering FAQ rows; `media-upload` only loaded on page screens
- **`functions.php`** — `inc/admin/meta-boxes-faq.php` added to admin loader; `@version` bumped to 2.5.0
- **`assets/css/main.css`** — new sections: FAQ Section, FAQ Accordion (with `:has()` selector for open-state styling)
- **`assets/css/admin.css`** — new section: FAQ Meta Box (sortable handle, remove button, row body)
- **`assets/js/admin.js`** — FAQ row management module (add, remove, reindex, sortable integration)

### SEO/AEO Score Impact
- Layer 5 (AEO Optimisation): 7/15 → 13/15 (FAQPage schema + visible accordion = full Google rich-result eligibility)
- Layer 6 (Structured Data): 8/10 → 10/10 (FAQPage and ItemList both added)
- Layer 2 (Structural Integrity): 17/20 → 18/20 (book page H2 sectioning)
- **Overall: 80/100 → 88/100**

## [2.4.0] - 2026-04-29

Authorship & Navigation Layer release. Bundles Phase A (critical front-page H1 fix) with Phase B of the SEO/AEO audit roadmap. Score lift: 69 → 80.

### Fixed
- **CRITICAL (Phase A):** Double-H1 conflict on front page — `header.php` was emitting `<h1 class="site-title">` and `front-page.php` was also emitting `<h1>` for the hero, producing two top-level headings. Site-title is now always `<p class="site-title">`; the hero `<h1>` is the canonical primary heading on the front page

### Added
- **Visible breadcrumb component** — new `inc/breadcrumbs.php` with `langgam_fikir_breadcrumbs()` template tag mirroring the existing `BreadcrumbList` JSON-LD logic; rendered above H1 on `single.php`, `single-book.php`, `page.php`, `page-about.php`, `page-contact.php`, `page-privacy.php`, `archive.php`, `archive-book.php`, `taxonomy-book-category.php`, `taxonomy-book-tag.php`, `search.php`
- **Author bio template part** — `template-parts/content/author-bio.php` rendered after `the_content()` on `single.php`; pulls from `get_the_author_meta('description')`, shows avatar (96px), display name linked to author archive, bio, and "View all posts" link; renders nothing when bio is empty
- **Last-updated date** — `single.php` shows "Updated [date]" meta item when `dateModified` exceeds `datePublished` by more than a day; reinforces freshness signal for Google
- **`screen-reader-text` utility class** added to `main.css` (WordPress standard)
- **H1 added to `home.php`** (Resources blog index) — was previously inheriting H1 from header; now has its own visually-hidden H1 since header H1 was demoted in Phase A

### Changed
- **`template-parts/content/resource-card.php`** — category indicator changed from `<span>` to `<a href="<?php echo get_category_link() ?>">`; every resource card now adds an internal link to its category archive, improving topical clustering and link equity flow
- `header.php` — site-title now consistently `<p>` across all view types (was conditionally `<h1>` on front page)
- `functions.php` — `@version` bumped to 2.4.0; `breadcrumbs.php` added to core file loader

### SEO/AEO Score Impact
- Layer 2 (Structural Integrity): 13/20 → 17/20 (front-page H1 fixed, visible breadcrumbs added)
- Layer 7 (E-E-A-T Signals): 2/5 → 4/5 (author bio with archive link, last-updated date)
- **Overall: 69/100 → 80/100**

## [2.3.0] - 2026-04-11

### Added
- **Price on book cards** — `book-card.php` now shows the price (`RM X.XX`) for orderable books, or a muted status label (e.g. "Coming Soon") for non-priced statuses; card uses `flex-direction: column` so the price/button footer always aligns to the bottom
- **Related books section on single book page** — `single-book.php` queries up to 4 books from the same `book-category` taxonomy (random order, current excluded) and renders them below the book detail using the shared `book-card` template part
- **Social sharing bar on single book page** — WhatsApp, Facebook, X (Twitter) share links and a copy-link button with clipboard API (+ `execCommand` fallback) appear at the bottom of the book details column
- **Full social links on Contact page** — `page-contact.php` now reads all four social URLs from Theme Options (`social_facebook`, `social_instagram`, `social_twitter`, `social_whatsapp`); per-page Instagram meta still overrides the global setting; all configured platforms are shown as circular icon links
- **Configurable homepage badge** — `front-page.php` "Years of Excellence" badge now reads from Theme Options (`about_badge_number`, `about_badge_label`) instead of being hardcoded
- **Theme Options: About Badge card** — new card in the Homepage tab with "Badge Number" and "Badge Label" fields; both sanitised as text and included in the defaults array

### Changed
- `inc/template-functions.php` — `langgam_fikir_option_defaults()` extended with `about_badge_number` (`"2+"`) and `about_badge_label` (`"Years of Excellence"`)
- `inc/admin/theme-options.php` — `langgam_fikir_sanitize_options()` includes `about_badge_number` and `about_badge_label` in the text-field sanitisation loop
- `assets/js/main.js` — `initShareCopyLink()` function added, initialised on DOMContentLoaded
- `assets/css/main.css` — new sections: Book Card Footer, Book Share Bar, Related Books Section, Contact Social Icons Row

## [2.2.1] - 2026-04-10

### Fixed
- **BUG:** About page logo meta key mismatch — save function wrote `_about_logo_url`, template read `_about_logo_id`, admin JS targeted non-existent element. Entire chain now unified on `_about_logo_id` attachment ID pattern
- **BUG:** `fonts.css` `@font-face` paths resolved to `/assets/css/` instead of `/assets/fonts/` — corrected to `url('../fonts/...')`
- **BUG:** Hardcoded `/wp-content/uploads/` logo path in `page-about.php` — replaced with portable text fallback
- **BUG:** Duplicate `add_theme_support('woocommerce')` in both `inc/setup.php` and `inc/woocommerce.php` — removed from `setup.php`

### Improved
- Admin JS logo uploader handles first-time upload (creates `<img>` if none exists)
- `readme.txt` stable tag synced to 2.2.1

## [2.2] - 2026-04-10

### Removed
- `inc/customizer.php` — empty stub file removed; all settings managed via Theme Options
- `require_once` for customizer.php removed from `functions.php`
- `inc/customizer/` empty directory removed

### Changed
- Synced with upstream v2.1.0.1 and v2.1.1-patched releases
- `Tested up to` bumped from 6.7 to 6.9 in style.css and readme.txt

## [2.1.0] - 2026-04-09

### Added
- `inc/schema.php` — complete structured data and social meta system (~450 lines)
- BreadcrumbList JSON-LD on all non-front pages (books, posts, pages, archives, taxonomies, search, 404)
- Article schema on `single.php` with headline, datePublished, dateModified, author, image, publisher, wordCount
- Speakability schema on Book (synopsis) and Article (entry-content) — enables Google Assistant voice reading
- WebSite schema with SearchAction — enables Google sitelinks search box
- Organization schema on front page with logo, address, phone, email, sameAs social links
- WebPage / AboutPage / ContactPage schema auto-detected by page slug
- Open Graph meta tags — og:type=book with ISBN/author for books, article:published_time for posts
- Twitter Card meta (summary_large_image) across all content types
- Auto-generated meta descriptions — book pages use "Author — Subtitle" format, 160 char limit
- Font preload hints for critical WOFF2 files via wp_head priority 1
- Skip-to-content accessibility link as first element after `<body>`
- Skip-link CSS with focus reveal animation
- Social & SEO tab in Theme Options (Facebook, Instagram, Twitter/X, WhatsApp URLs)
- Default Meta Description field in Theme Options
- Social URL sanitisation via `esc_url_raw()`
- `langgam_fikir_book_speakable()` helper for Book schema Speakability
- `langgam_fikir_print_jsonld()` helper for consistent JSON-LD output

### Fixed
- header.php — site title is `<h1>` only on front page, `<p>` on all inner pages (SEO heading hierarchy)
- footer.php — site title changed from `<h2>` to `<p>` (removes heading noise from outline)

### Changed
- footer.php Organization microdata retained for backward compat, enriched Organization JSON-LD output separately via wp_head
- single-book.php Book schema now includes `speakable` property with CSS selectors

## [2.0.1] - 2026-04-09

### Added
- `template-parts/content/book-card.php` — reusable book card component
- `template-parts/content/resource-card.php` — reusable resource/blog card component with category badge, date footer, and read-more link
- `taxonomy-book-category.php` — dedicated archive template for book categories with staggered reveal grid
- `taxonomy-book-tag.php` — dedicated archive template for book tags
- `.resource-card-footer` CSS — flex layout with date and read-more link
- Resource cards now use `display: flex; flex-direction: column` for consistent footer alignment

### Changed
- `front-page.php` — book cards and resource cards replaced with `get_template_part()` calls
- `archive-book.php` — book card markup replaced with template part (eliminated ~45 lines)
- `404.php` — book suggestions now use book-card template part
- `home.php` — resource cards replaced with template part
- `single.php` — related articles section replaced with template part
- Resource cards are now consistent across all contexts (category badge, date, read-more everywhere)

### Removed
- ~200 lines of duplicated markup across 5 template files

### Technical
- Book card rendering is now a single source of truth (1 file instead of 4 copies)
- Resource card rendering is now a single source of truth (1 file instead of 3 copies)
- Template parts use WordPress `get_template_part()` for child-theme-safe overriding

## [2.0.0] - 2026-04-09

### Added
- `404.php` — branded 404 page with search form, book suggestions, scroll-reveal animations
- `search.php` — search results with content-type badges (Book/Article/Page), thumbnails, pagination, no-results state
- Contact form honeypot field (hidden via CSS, bots fill it → silent reject)
- Contact form timestamp check (reject if submitted < 3 seconds after page render)
- Contact form IP rate limiting (5 submissions/hour via WordPress transients)
- `/assets/css/fonts.css` — self-hosted @font-face declarations for Crimson Text and Inter
- `/assets/fonts/` directory with WOFF2 download instructions
- `languages/langgam-fikir.pot` — translation catalogue with 233 strings
- Recommended server security headers section in README.md (Apache + Nginx examples)
- CSS classes replacing inline styles: `.comments-area`, `.comment-list`, `.section-padded`, `.entry-meta`, `.lf-logo-preview-img`, `.lf-hp`

### Fixed
- **CRITICAL (SEC-01):** Contact form action name mismatch — form sent `langgam_fikir_contact_form` but handler registered `langgam_fikir_contact`, causing silent 404 on every submission
- **(SEC-02):** Email From header now uses site domain (`wordpress@{host}`) instead of user-supplied address — fixes SPF/DKIM failures
- **(SEC-04):** Unescaped `$description` in `header.php` (lines 32, 46) — now `esc_html()`
- **(SEC-04):** Raw `echo $content` in `page-privacy.php` — now `wp_kses_post()`
- **(SEC-04):** Unescaped `get_the_ID()` in `single-book.php` data attribute — now `absint()`
- **(SEC-04):** Unsanitised `$_GET['contact']` in `page-contact.php` — now `sanitize_key()`
- **(SEC-07):** `$active_tab` in theme-options.php now wrapped in `esc_attr()` on all outputs

### Changed
- Google Fonts CDN dependency removed — fonts now self-hosted from `/assets/fonts/` (GDPR compliant)
- Contact form handler fully rewritten with validation, spam protection, and proper email headers
- All inline `style=""` attributes removed from PHP templates (comments.php, page.php, index.php, admin meta-boxes)

### Security
- All Critical findings (SEC-01, SEC-02) resolved
- All High findings (SEC-03, SEC-04) resolved
- All Medium findings (SEC-05, SEC-07) resolved
- SEC-10 (server headers documentation) added
- Hardening checklist verified: nonces, capability checks, ABSPATH guards, escaping, sanitisation

## [1.9.9] - 2026-04-09

### Added
- **Announced status** (warm plum `#8E7193`) — 10th status for pipeline titles confirmed but with no timeline
- Scroll-reveal animation system using IntersectionObserver (`.reveal`, `.reveal-left`, `.reveal-right`, `.reveal-scale`)
- Staggered card entrance delays for books and resources grids
- Parallax hero background with `requestAnimationFrame` performance
- Hero background image setting in Theme Options (Homepage tab) with media uploader
- Hero text and button entrance animations (`@keyframes heroFadeUp`)
- Book card hover enhancements: deeper lift (`translateY(-8px)`), shadow bloom, cover image zoom (`scale(1.04)`)
- Resource card hover lift and shadow
- Badge shimmer animation on New Release and Pre-Order badges (`@keyframes badgeShimmer`)
- About snippet image hover zoom and badge scale effect
- Section header golden underline animation triggered on scroll reveal
- Button ripple hover effect via `::after` pseudo-element
- Footer fade-in animation on scroll
- Announced status message on single book page
- Announced status in Schema.org offers (PreOrder availability)
- Pipeline group in admin status selector fieldset
- `prefers-reduced-motion: reduce` — all animations disabled for accessibility

### Changed
- Hero section restructured with `.hero-bg`, `.hero-overlay`, `.hero-content` layers for parallax
- Homepage sections use reveal classes for scroll-triggered entrance
- Book and resource grids use `.stagger-children` for cascading reveals
- About snippet uses split reveal (left/right) for visual drama
- Footer treated as reveal element

### Technical
- `initScrollReveal()` — IntersectionObserver at 12% threshold with -40px root margin
- `initParallax()` — passive scroll listener with `requestAnimationFrame` throttling, 0.35 speed factor
- Parallax limited to 1.5x hero height for performance
- Animation CSS uses `cubic-bezier(0.25, 0.46, 0.45, 0.94)` for natural easing
- Media uploader integration via `wp.media` API on Theme Options page

## [1.9.8] - 2026-04-08

### Added
- **9-status book system** expanding from 5 to cover the full publishing lifecycle
- New Release status (crimson-coral `#C26B5A`) — launch window visibility
- Limited Edition status (deep bronze `#B07D3A`) — scarcity signalling
- Digital Only status (forest teal `#5B8A72`) — e-book/PDF availability
- Sold Out status (slate lavender `#7B7FA8`) — stock exhausted, reprint pending
- Helper functions: `langgam_fikir_status_badge()`, `langgam_fikir_get_status_label()`, `langgam_fikir_status_is_orderable()`, `langgam_fikir_status_has_price()`
- Sold Out status message callout on single book pages
- Schema.org `LimitedAvailability` and `OnlineOnly` offer types

### Changed
- Admin meta box status selector grouped by lifecycle category (Available, Upcoming, Unavailable)
- Status badge rendering refactored from per-template if/elseif chains to single helper function
- Schema.org offers expanded for all 9 statuses
- Price display and order buttons use helper functions for orderable status checks
- Admin CSS now loads on `book` post type edit screens (previously `page` only)
- All 9 badge colours use distinct hues — no two statuses can be confused at a glance

### Technical
- `langgam_fikir_status_is_orderable()` returns true for: published, new-release, limited-edition, digital-only, pre-order
- `langgam_fikir_status_has_price()` returns true for same set
- Status messages shown for: coming-soon, sold-out, reprinting, out-of-print

## [1.9.7] - 2026-04-08

### Added
- **NEW:** Tabbed Theme Options page under Appearance > Theme Options
- Three admin tabs: Homepage, Contact & Footer, Legal Entity
- `langgam_fikir_get_option()` helper function for unified option access
- `langgam_fikir_option_defaults()` centralised defaults definition
- Footer Contact column with address, phone, email, SVG icons
- Schema.org Organisation markup on footer contact column
- Live JavaScript preview for legal entity footer bar text in admin
- One-time automatic migration of old `theme_mod` values to new options
- Hero text is now editable from Theme Options
- Contact form recipient email configurable from Theme Options

### Changed
- **BREAKING:** All custom settings migrated from Customizer (`theme_mod`) to WordPress Options API (`get_option`)
- Customizer stripped to native WordPress controls only (Site Identity, Menus, etc.)
- `front-page.php` now reads from `langgam_fikir_get_option()` instead of `get_theme_mod()`
- `footer.php` rebuilt with three-column layout and Options API integration
- `inc/template-functions.php` now hosts option helper functions (available on all requests)
- Footer bottom bar reads legal entity name and registration number from Theme Options

### Added (Assets)
- `assets/css/theme-options.css` — admin page styling with earthy palette
- `assets/js/theme-options.js` — tab navigation and live preview

### Technical
- Settings stored as single `langgam_fikir_options` array via `register_setting()`
- Sanitisation via `langgam_fikir_sanitize_options()` with per-type handling
- Admin assets scoped to Theme Options page only via `$hook` check
- Helper functions in `inc/template-functions.php` (not admin-only) to avoid fatal errors on front-end

## [1.9.6] - 2026-04-01

### Changed
- **ARCHITECTURE:** Complete reorganization to WordPress coding standards
- Modularized `functions.php` from 890 lines to ~30 lines
- Created `/inc/` directory for modular includes
- Created `/inc/admin/` directory for admin-specific code
- Created `/template-parts/` directory for reusable components (optional)
- Created `/languages/` directory for translation files

### Added
- `inc/setup.php` - Theme configuration and setup functions
- `inc/enqueue.php` - All asset enqueuing functions
- `inc/post-types.php` - Custom post types and taxonomies
- `inc/meta-boxes.php` - Meta box registration
- `inc/admin/meta-boxes.php` - Admin meta box callbacks
- `inc/customizer.php` - Theme Customizer settings
- `inc/template-functions.php` - Template helper functions
- `inc/template-hooks.php` - Hooks and filters
- `inc/woocommerce.php` - WooCommerce support (conditional)

### Improved
- Better code organization and maintainability
- Easier to find and modify specific features
- WordPress Theme Handbook compliance
- Conditional loading (admin files only in admin)
- Conditional WooCommerce support
- Clear separation of concerns
- Developer-friendly architecture
- Scalability for future features

### Technical
- Defined theme constants: `LANGGAM_FIKIR_VERSION`, `LANGGAM_FIKIR_DIR`, `LANGGAM_FIKIR_URI`
- Implemented conditional file loading
- Organized functions by responsibility
- Separated display logic from business logic

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
