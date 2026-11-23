---
name: testing-langgam-fikir-theme
description: How to stand up a local WordPress install to runtime-test the Langgam Fikir (LF) Twenty Twenty-Five child theme — contact form, book CPT, templates — including capturing wp_mail output.
---

# Testing the Langgam Fikir (LF) WordPress child theme locally

The repo is only a theme; there is no WP install in it. Build one from scratch (~5 min).

## One-time environment setup

```bash
sudo apt-get install -y php-mysql php-xml php-mbstring php-curl php-gd mariadb-server
sudo service mariadb start          # systemd is unavailable in containers; `service` works
curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
  && chmod +x wp-cli.phar && sudo mv wp-cli.phar /usr/local/bin/wp
sudo mysql -e "CREATE DATABASE wp; CREATE USER 'wp'@'localhost' IDENTIFIED BY 'wp'; GRANT ALL ON wp.* TO 'wp'@'localhost';"
mkdir -p ~/wp && cd ~/wp
wp core download
wp config create --dbname=wp --dbuser=wp --dbpass=wp --dbhost=127.0.0.1
wp core install --url=http://localhost:8080 --title="Langgam Fikir" \
  --admin_user=admin --admin_password=admin123 --admin_email=admin@example.com --skip-email
wp theme install twentytwentyfive            # parent theme (required)
ln -sfn /path/to/repos/LF ~/wp/wp-content/themes/LF
wp theme activate LF
wp rewrite structure '/%postname%/' && wp rewrite flush --hard
nohup php -S 0.0.0.0:8080 -t ~/wp >/tmp/php-server.log 2>&1 &
```
PHP's built-in server handles WP pretty permalinks fine (no router script needed).

## Capturing outgoing mail (wp_mail never delivers here)

mu-plugin `~/wp/wp-content/mu-plugins/mail-log.php`:
```php
<?php
add_filter( 'wp_mail', function ( $args ) {
    file_put_contents( '/tmp/wp-mail.log', print_r( $args, true ), FILE_APPEND );
    return $args;
} );
add_filter( 'pre_wp_mail', '__return_true' ); // short-circuit real delivery, keeps $sent === true
```
Without `pre_wp_mail` the handler takes the failure branch and redirects with `contact=error`.

## Reaching the features

- Contact form: create a page whose **slug** is `contact`, `contact-us` or `contact-langgam-fikir`
  (functions.php routes those slugs to `page-contact.php`; there is no page-template picker).
  Form posts to `admin-post.php` (`langgam_fikir_contact_form`, registered for nopriv).
  Result banners come from `?contact=success|throttled|error` on the contact page.
- Rate limit: transient `lf_contact_<md5(REMOTE_ADDR)>`, 1/min. Clear between tests with
  `wp transient delete --all`, or wait 60s. Honeypot check runs **before** the rate limit,
  so a honeypot POST inside the throttle window still returns `contact=success`.
- Honeypot field `contact_website` is off-screen (`left:-9999px`); to simulate a bot, set its
  value once from the devtools console and then click "Send message" in the UI.
- Book CPT: post type `book`, permalink base `/publications/`, meta keys `_book_author`,
  `_book_isbn`, `_book_price`, `_book_status` (`published`/`pre-order`), `_book_publisher`,
  `_book_year`. Create with `wp post create --post_type=book ... && wp post meta set ...`.

## Newer features (2.13.0–2.16.0)

- **Theme Options** live in one array option, `langgam_fikir_options`; set values with
  `wp option patch update langgam_fikir_options <key> <value>`.
- **Private login address:** `login_hide=1`, `login_slug=masuk`. Then `/wp-login.php` and
  logged-out `/wp-admin/` must show the 404 page, and `/masuk/` the login form. If you lock
  yourself out, `wp option patch update langgam_fikir_options login_hide 0`.
- **Print:** use Chrome's print preview, or Playwright `page.emulateMedia({ media: 'print' })`.
  A Print button shows on single posts and books.
- **Server PDF:** Chromium is at `/opt/pw-browsers/chromium` in cloud sessions. Set
  `pdf_engine=chrome` and `pdf_no_sandbox=1` (needed as root), open a post with `?lf-pdf=1`,
  and check `pdfinfo` on the download. Files land in `wp-content/uploads/langgam-fikir-pdf/`.
- **Citations:** `[cite]` in a post, or the Citation block (`langgam-fikir/citation`).
- **Plugin detection:** activating Auto Justify Content, CitePress or WPS Hide Login is
  reversed on the next admin page load, with a notice.
- **Preferred sources:** `gps_enabled=1`. Google's script only loads near the button, and
  `gps_script=link` keeps it from loading at all.

## Gotchas worth knowing

- Chrome's omnibox autocompletes to previously visited URLs; type the URL then press `Delete`
  before `Enter`, or you will land on the wrong page.
- `sanitize_text_field()`/`sanitize_textarea_field()` already strip tags, so a `<script>` payload
  in the contact form is removed before `esc_html()`; the visible proof of escaping is `&amp;`,
  `&quot;`, `&#039;` entities in the mail log, plus `<br />` from `nl2br`.
- Post **titles** are echoed raw by `front-page.php` / `single-book.php` (`the_title()`), so a
  script payload in a book title executes on page load. If an unexpected `alert()` appears,
  check whether it comes from the title, not from the field you are testing.
- `$_POST` is not `wp_unslash()`'d in the contact handler, so quotes arrive in the email as
  `O\&#039;Brien` (stray backslashes). Expect this in captured mail bodies.

## Devin Secrets Needed

None — everything runs locally with throwaway credentials.
