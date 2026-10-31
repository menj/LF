Bundled fonts
=============

In the repository
  Crimson Text, Inter  - SIL Open Font License 1.1 (free to redistribute).

NOT in the repository (git-ignored, upload to this folder on the server yourself)
  Sabon Next LT   - (c) Monotype GmbH. Commercial licence.
  Kagnue          - (c) Youthlabs Studio (Creative Market). Commercial licence.
  Ferments        - (c) Haksen Studio. Commercial licence.

Expected files:
  sabon-next-lt-regular.woff2, sabon-next-lt-italic.woff2,
  sabon-next-lt-bold.woff2, sabon-next-lt-bolditalic.woff2,
  kagnue-regular.woff2, ferments-regular.woff2

The theme loads assets/css/fonts-premium.css only when sabon-next-lt-regular.woff2 exists.
Without the files it falls back to Crimson Text and Inter, so nothing breaks.
Check that each licence covers web/self-hosting use for your domain.
