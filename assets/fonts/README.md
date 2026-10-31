# Self-Hosted Fonts

This theme uses self-hosted fonts for GDPR compliance (no requests to Google servers).

## Required Font Files

Download these WOFF2 files and place them in this directory:

### Crimson Text (Serif — headings)
- `crimson-text-v19-latin-regular.woff2`
- `crimson-text-v19-latin-italic.woff2`
- `crimson-text-v19-latin-600.woff2`

### Inter (Sans-serif — body)
- `inter-v18-latin-regular.woff2`
- `inter-v18-latin-500.woff2`
- `inter-v18-latin-600.woff2`

## How to Download

1. Visit https://gwfh.mranftl.com/fonts (Google Webfonts Helper)
2. Search for "Crimson Text" → select 400, 400italic, 600 → download WOFF2
3. Search for "Inter" → select 400, 500, 600 → download WOFF2
4. Place all `.woff2` files in this directory

## Fallback

If font files are missing, the theme gracefully falls back to:
- **Serif:** Georgia
- **Sans-serif:** Helvetica Neue, system sans-serif

## Licensed Fonts (2.11.0)

Three further families are part of the default design. They are licensed to the site owner and
declared in `/assets/css/fonts-premium.css`, which loads only when the files are present:

- `sabon-next-lt-regular.woff2`, `sabon-next-lt-italic.woff2`, `sabon-next-lt-bold.woff2`, `sabon-next-lt-bolditalic.woff2` (reading text)
- `kagnue-regular.woff2` (site title, page and section titles)
- `ferments-regular.woff2` (tagline and script accents)

Without them the theme falls back to Crimson Text and Georgia.
