# Upgrading to 2.1.0

- About and Contact text moved from per-page meta boxes to **Appearance > Theme Settings**. Values you saved in the old meta boxes keep working until you save the new settings.
- Open **Footer and pages** and choose your About, Contact and Terms pages. Until then the theme still finds them by their old slugs.
- Text in the Contact page editor now shows above the form. Clear it if you do not want it there.

---

# Upgrading to 2.0.0

- **Homepage counts and hero text** moved from the Customizer to Appearance > Theme Settings > Homepage. Values you saved in the Customizer keep working until you save the new settings.
- **Headings** previously ignored inline styles set in the block editor; they now respect them. Review pages with custom heading styles.
- **Fonts**: the theme no longer calls Google Fonts. Sabon Next LT, Kagnue and Ferments are bundled in `assets/fonts/` (licensed to the site owner). If the files are removed, the theme falls back to Crimson Text and Inter.
- No action is needed to keep the original look: the default scheme is Earth.

---

# Upgrading Langgam Fikir Theme

This guide provides instructions for upgrading the Langgam Fikir WordPress theme between versions.

## Table of Contents

1. [Before You Upgrade](#before-you-upgrade)
2. [Upgrade Methods](#upgrade-methods)
3. [Version-Specific Upgrade Notes](#version-specific-upgrade-notes)
4. [Post-Upgrade Steps](#post-upgrade-steps)
5. [Troubleshooting](#troubleshooting)
6. [Rolling Back](#rolling-back)

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

3. **Customizer Settings**
   - Screenshot your Customizer settings
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

### Upgrading to 1.9.3 (Latest)

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
   - [ ] Check Customizer settings

5. **Verify Navigation**
   - [ ] Primary menu works
   - [ ] Footer menu works
   - [ ] Mobile menu toggles correctly
   - [ ] All links work

### Optional Steps

1. **Review Customizer Settings**
   - Go to **Appearance > Customize**
   - Check **Homepage Settings**
   - Verify featured publications count
   - Verify resources count

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

**Last Updated:** February 9, 2026  
**Current Version:** 1.9.3  
**Compatibility:** WordPress 6.7+ | PHP 7.4+ | Twenty Twenty-Five 1.0+
