# 🚀 IMMEDIATE FIX - Trustpilot Review Text Not Showing

## Problem
Reviews display name, avatar, rating, and date - but the review text/content is empty.

## Root Cause
1. **Cache contains old data** with empty text fields (cached for 12 hours)
2. **Trustpilot HTML structure** may have changed, making text extraction fail

## ✅ SOLUTION - Follow These Steps EXACTLY

### Step 1: Clear the Cache (REQUIRED!)

**Choose ONE method:**

#### Method A: Direct Database Query (Recommended - Fastest)
Run this in phpMyAdmin, Adminer, or database tool:

```sql
DELETE FROM wp_options WHERE option_name LIKE '_transient_revix_trustpilot_%';
DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_revix_trustpilot_%';
```

#### Method B: Via URL (Easiest)
1. Copy the code from `clear-cache-snippet.php`
2. Paste it at the END of your theme's `functions.php` file
3. Visit: `yoursite.com/?clear_trustpilot_cache=1`
4. You'll see "Cache Cleared!" message
5. Remove the code from `functions.php`

#### Method C: Via PHP Code
Add this to `wp-config.php` temporarily (above the "Stop editing" line):

```php
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_revix_trustpilot_%'");
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_revix_trustpilot_%'");
```

Visit any page once, then remove the code.

---

### Step 2: Refresh Your Page

After clearing the cache:
1. Go to the page with `[revix_trustpilot_reviews]`
2. **Hard refresh** (Ctrl+Shift+R or Cmd+Shift+R)
3. Review text should now appear!

---

### Step 3: Check Debug Log

If text still doesn't appear, check `wp-content/debug.log` for messages like:

```
Revix Trustpilot: Processing review by [Author Name]
Revix Trustpilot: Text length = 0 chars
Revix Trustpilot: WARNING - No text found for this review!
```

This tells us if the new extraction strategies are working.

---

## 🔧 What I Changed

I've updated `TrustpilotFetcher.php` with **6 different strategies** to extract review text:

1. ✅ Standard data attribute: `data-service-review-text-typography`
2. ✅ Review content div: `data-review-content`
3. ✅ Class-based selector: `styles_reviewContent`
4. ✅ Alternative data attribute search
5. ✅ **Smart paragraph detection** - finds the longest paragraph (likely the review)
6. ✅ **Fallback div search** - looks for text patterns

Also:
- ✅ Disabled cache temporarily (set `ENABLE_CACHE = false`)
- ✅ Added detailed debug logging
- ✅ Added cache clearing method

---

## 🎯 Expected Result

After clearing cache and refreshing:
- ✅ Author name (already working)
- ✅ Avatar (already working)
- ✅ Rating stars (already working)
- ✅ Date (already working)
- ✅ **Review text/content** ← Should work now!

---

## 🆘 If Still Not Working

### Diagnose with Inspector Tool

Visit: `yoursite.com/wp-content/plugins/revix-reviews/inspect-trustpilot.php`

This will show:
- Which HTML selectors are finding data
- The actual paragraph contents in reviews
- Whether reviews have text at all

### Check if Reviews Actually Have Text

Some Trustpilot reviews are **rating-only** (no written review). This is normal!

To verify:
1. Visit your Trustpilot page manually: `https://www.trustpilot.com/review/www.payoneer.com`
2. Check if the reviews have written text
3. If they don't, our plugin is working correctly - those reviews just don't have text

### Force Fresh Data

To ensure you're getting fresh data every time (for testing):
1. The cache is already disabled (`ENABLE_CACHE = false`)
2. Each page load will fetch new data
3. After testing, change it back to `ENABLE_CACHE = true` for performance

---

## 📋 Quick Checklist

- [ ] Cache cleared using one of the methods above
- [ ] Page hard-refreshed (Ctrl+Shift+R)
- [ ] Checked actual Trustpilot page to verify reviews have text
- [ ] Checked debug log for extraction messages
- [ ] Visited inspector tool to see HTML structure

---

## 🎉 Re-Enable Cache Later

Once everything works:

1. Edit `TrustpilotFetcher.php`
2. Change line 5 from:
   ```php
   const ENABLE_CACHE = false;
   ```
   to:
   ```php
   const ENABLE_CACHE = true;
   ```

This improves performance by not fetching Trustpilot on every page load.

---

## ⚠️ Important Note

If Trustpilot loads reviews via JavaScript (after page load), PHP scraping won't work. The inspector tool will reveal this. In that case, we'd need to use Trustpilot's official API instead.

---

**Clear that cache and let me know what happens!** 🚀
