# Revix Reviews - Troubleshooting Guide

## Shortcodes Not Displaying Reviews

If your Trustpilot or Google reviews shortcodes are not showing reviews, follow this guide.

---

## 🔍 Debug Mode

Both shortcodes now support debug mode to help you identify issues:

### Trustpilot Debug
```
[revix_trustpilot_reviews debug="true"]
```

### Google Reviews Debug
```
[revix_google_reviews debug="true"]
```

When debug mode is enabled, you'll see error messages displayed on the page instead of hidden HTML comments.

---

## Common Issues & Solutions

### 1. Trustpilot Reviews Not Showing

#### ✅ Checklist:
- [ ] Trustpilot URL is configured in WordPress Admin → Revix Reviews → Trustpilot tab
- [ ] URL format is correct: `https://www.trustpilot.com/review/yourdomain.com`
- [ ] The business actually has reviews on Trustpilot
- [ ] Your server can access Trustpilot's website (not blocked)

#### 🛠️ Solutions:

**Problem: "Trustpilot URL not configured"**
- Go to WordPress Admin → Revix Reviews → Trustpilot tab
- Enter your Trustpilot business review page URL
- Click "Save Changes"

**Problem: URL is set but no reviews appear**
- Verify the URL in your browser - make sure it loads reviews
- Check if Trustpilot has changed their page structure (this can break scraping)
- Enable debug mode in TrustpilotFetcher.php: change `const DEBUG = false;` to `const DEBUG = true;`
- Check your WordPress debug.log file for error messages
- Try clearing the Trustpilot reviews cache (it caches for 12 hours)

**Problem: JavaScript not hiding the loader**
- Check browser console for JavaScript errors
- Verify that `trustpilot.js` is loading correctly
- Make sure there are no CSS conflicts hiding the reviews

---

### 2. Google Reviews Not Showing

#### ✅ Checklist:
- [ ] Google API Key is configured
- [ ] Google Place ID is configured  
- [ ] Places API is enabled in Google Cloud Console
- [ ] API Key has correct permissions and no restrictions blocking it
- [ ] Place ID is correct for your business

#### 🛠️ Solutions:

**Problem: "Google API Key or Place ID not configured"**
- Go to WordPress Admin → Revix Reviews → Google tab
- Enter your Google API Key
- Enter your Google Place ID
- Click "Save Changes"

**Problem: Settings are configured but no reviews appear**

1. **Verify Places API is enabled:**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Navigate to "APIs & Services" → "Library"
   - Search for "Places API"
   - Make sure it's enabled

2. **Check API Key restrictions:**
   - Go to Google Cloud Console → "APIs & Services" → "Credentials"
   - Click on your API Key
   - Under "API restrictions", ensure "Places API" is allowed
   - Under "Website restrictions", add your domain or use "None" for testing

3. **Verify Place ID:**
   - Use [Google's Place ID Finder](https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder)
   - Make sure the Place ID matches your business

4. **Check API Response:**
   - Use the debug script (see below) to see the actual API response
   - Look for error messages in the response

---

## 🧪 Using the Debug Script

A debug script has been created at `/debug-reviews.php` in the plugin folder.

**To use it:**

1. Make sure you're logged in as an admin
2. Visit: `https://yoursite.com/wp-content/plugins/revix-reviews/debug-reviews.php`
3. Review the information displayed:
   - Configuration status
   - API connection tests
   - Sample data from APIs
   - Actual shortcode output

**⚠️ Important:** Delete this file after debugging for security!

---

## 📋 Testing Shortcodes

### Test Trustpilot:
```
[revix_trustpilot_reviews count="5" min_rating="4" debug="true"]
```

Attributes:
- `count` - Number of reviews to display (default: 15)
- `min_rating` - Minimum star rating to show (default: 0)
- `max_rating` - Maximum star rating to show (default: 5)
- `debug` - Show error messages on page (default: false)

### Test Google Reviews:
```
[revix_google_reviews masonry="false" words="500" debug="true"]
```

Attributes:
- `masonry` - Enable masonry layout (default: false)
- `words` - Max words per review (default: 500)
- `debug` - Show error messages on page (default: false)

---

## 🔧 Advanced Debugging

### Enable WordPress Debug Mode

Edit `wp-config.php` and add:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `/wp-content/debug.log` for error messages.

### Check Browser Console

1. Open your page with the shortcode
2. Press F12 to open browser developer tools
3. Go to the Console tab
4. Look for JavaScript errors

### Inspect Network Requests

1. Open browser developer tools (F12)
2. Go to Network tab
3. Reload the page
4. Check if CSS and JS files are loading (look for 404 errors)

---

## 🆘 Still Not Working?

If you've tried everything above and it's still not working:

1. **Check that shortcodes are registered:**
   - Temporarily add this to your theme's `functions.php`:
   ```php
   add_action('init', function() {
       global $shortcode_tags;
       error_log('Available shortcodes: ' . print_r(array_keys($shortcode_tags), true));
   });
   ```
   - Check debug.log to confirm shortcodes are registered

2. **Test with default WordPress theme:**
   - Switch to Twenty Twenty-Four or another default theme
   - This helps identify theme conflicts

3. **Disable other plugins:**
   - Temporarily deactivate other plugins one by one
   - This helps identify plugin conflicts

4. **Server requirements:**
   - PHP 7.4 or higher
   - WordPress 5.0 or higher
   - cURL or allow_url_fopen enabled
   - DOMDocument PHP extension enabled

---

## 📝 Logging Review Fetches

To log review fetch attempts, check these locations:

### Trustpilot Logs
When `DEBUG` is enabled in `TrustpilotFetcher.php`, HTML comments are added to the page output.
View page source to see debug comments.

### Google Reviews Logs
When `debug="true"` is added to shortcode, messages are logged to `error_log()`.
Check your PHP error log or WordPress debug.log.

---

## 🔄 Cache Clearing

### Trustpilot Cache
Reviews are cached for 12 hours. To clear:
- Delete transients starting with `revix_trustpilot_reviews_cache_`
- Use a plugin like "Transients Manager"
- Or wait 12 hours for automatic expiration

### Google Reviews Cache
Google reviews are fetched fresh each time (no caching implemented by default).

---

## Version Information

Make sure you're running the latest version with debugging support:
- Trustpilot shortcode: Now includes debug mode and validation
- Google Reviews shortcode: Now includes debug mode and validation
- Debug script: Available in plugin root folder

