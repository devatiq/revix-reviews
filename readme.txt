=== Revix Reviews – All-in-One Business Review Manager ===
Plugin Name: Revix Reviews
Author: nexibyllc
Author URI: https://nexiby.com/
Contributors: abcplugins, atiqbd4ever, nexibyllc, supreoxltd
Tags: reviews, business reviews, testimonials, trustpilot, google reviews
Requires at least: 5.4
Tested up to: 6.9
Stable tag: 1.2.7
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Revix Reviews helps you collect, import, and display reviews—including Trustpilot and Google—with more platforms coming soon.

== Description ==

Revix Reviews allows you to manage, showcase, and collect customer feedback directly on your WordPress site, with built-in Trustpilot and Google Reviews integration.

[Demo](https://revixreviews.com/demo/)

https://youtu.be/yB0dJ70jS2Y

Whether you're a business owner, freelancer, or eCommerce site, this plugin helps boost credibility and conversions by showing authentic testimonials from real customers.

**Key Features:**

- 📝 Add and manage reviews using a built-in submission form.
- 🌐 Fetch and display public reviews from **Trustpilot** and **Google Maps**.
- 🌟 Star-rating SVG icons that visually match the review score.
- 🎯 Filter reviews by rating range (`min_rating`, `max_rating`).
- 📊 Show company-wide review summary (average + total) with logo and stars.
- 🔎 Display business name dynamically (or customize label).
- 📎 Custom post type support for native review management.
- 🔒 Manual review moderation and approval settings.
- ⚙️ Shortcodes to embed forms, grids, summaries, or third-party reviews.

**Coming Soon:**
- 🛠️ Yelp, Facebook, and other third-party sources
- 🎨 Gutenberg & Elementor widgets
- 📈 Analytics dashboard for review insights

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/revix-reviews` directory, or install via the WordPress plugin repository.
2. Activate the plugin from the 'Plugins' menu in WordPress.
3. Go to **Settings → Revix Reviews** to configure plugin options.
4. Use the shortcodes below to add review forms and displays to your pages.

== Shortcodes ==

**1. `[revixreviews_form]`**  
Displays the native customer review submission form.

**2. `[revixreviews]`**  
Displays native reviews in a customizable grid layout.  
Supports:
- `count` – Limit how many reviews are shown (`count="6"`)
- `min_rating` – Minimum rating to display (`min_rating="4"`)
- `max_rating` – Maximum rating to display (`max_rating="5"`)

**3. `[revix_trustpilot_reviews]`**  
Displays Trustpilot reviews pulled from your business profile.  
Supports:
- `count` – Limit the number of reviews displayed (default: all reviews, example: `count="6"`)
- `min_rating` – Show only reviews with this rating or higher (1-5 scale, example: `min_rating="4"`)
- `max_rating` – Show only reviews with this rating or lower (1-5 scale, example: `max_rating="5"`)

**4. `[revix_trustpilot_summary]`**  
Displays Trustpilot summary: star rating and total number of reviews.

**5. `[revix_google_reviews]`**  
Displays reviews from a public Google Maps place using your API key.  
Displays:
- Author, rating stars, time, review text, profile image, and Google logo.
Supports:
- `words` – Limit review text length by word count (default: `100`, example: `words="30"`)
- `masonry` – Enable Pinterest-style masonry layout for varying review heights (`masonry="true"` or `masonry="false"`)

**6. `[revix_google_summary]`**  
Displays summary of Google reviews (place name, average, total count).  
Supports:
- `name` – `true`, `false`, or custom label (`name="Rated by"`)
- `average` – Show average rating (`average="true"`)
- `label` – Custom text after total count (`label="votes"`)

== Frequently Asked Questions ==

= Can I customize the review form fields? =
Yes, native review form fields are fully editable from the plugin settings.

= Can I filter Trustpilot or Google reviews by star rating? =
Yes. Use `min_rating` and `max_rating` attributes in the shortcodes.

= Can I display Google reviews for any place ID? =
Yes, as long as you have a valid Google Maps API key and set the correct place ID.

= Can I disable the Google place name or customize the label? =
Yes. Use the `name` attribute in `[revix_google_summary]` to hide or replace it.

= Do I need to approve reviews before they appear? =
Yes, moderation can be turned on/off for native reviews via settings.

= How do I create a Google Maps API key? =
1. Visit the Google Cloud Console: https://console.cloud.google.com/
2. Create a new project or select an existing one
3. Go to "APIs & Services" → "Library"
4. Search for "Places API (New)" and click "ENABLE"
5. Go to "Credentials" and click "Create Credentials" → "API Key"
6. Copy your API key and paste it in the Revix Reviews → Google tab
7. (Optional) Restrict your API key to your domain for security
Watch this helpful video tutorial: https://www.youtube.com/watch?v=hsNlz7-abd0

= I've created an API key but reviews still don't show up? =
As of December 2025, Google has migrated to the new Places API. Make sure you:
1. Enable "Places API (New)" in Google Cloud Console (NOT the legacy "Places API")
2. Your API key must have access to the new Places API
3. Billing must be enabled on your Google Cloud account (Google requires this even for free tier usage)
4. Wait a few minutes after enabling the API for it to take effect
Watch this tutorial for step-by-step guidance: https://www.youtube.com/watch?v=eycjk3APuoI

= What's the difference between Places API and Places API (New)? =
Google has deprecated the old Places API. This plugin now uses the new "Places API (New)" which offers:
- Better performance and reliability
- More accurate review data
- Enhanced features and fields
- Modern API structure
Make sure to enable "Places API (New)" in your Google Cloud Console, not the legacy version.


= Can I customize the review form fields? =
Yes, native review form fields are fully editable from the plugin settings.

= Can I filter Trustpilot or Google reviews by star rating? =
Yes. Use `min_rating` and `max_rating` attributes in the shortcodes.

= Can I display Google reviews for any place ID? =
Yes, as long as you have a valid Google Maps API key and set the correct place ID.

= Can I disable the Google place name or customize the label? =
Yes. Use the `name` attribute in `[revix_google_summary]` to hide or replace it.

= Do I need to approve reviews before they appear? =
Yes, moderation can be turned on/off for native reviews via settings.

= How do I enable Elementor widgets? =
1. Go to your WordPress admin panel
2. Navigate to Revix Reviews → Settings
3. Look for the "Elementor Integration" section
4. Toggle the "Enable Elementor Widgets" switch to ON
5. Save your settings

Once enabled, you'll have access to Revix Reviews widgets in the Elementor page builder, including:
- Review Submit Form widget with customizable star ratings
- Google Reviews display widget
- Google Summary widget
- Trustpilot Reviews widget
- Trustpilot Summary widget
- Testimonial Reviews widget

= Can I customize the star rating colors in Elementor? =
Yes! When using the Review Submit Form widget in Elementor:
1. Add the widget to your page
2. Go to the "Style" tab
3. Look for the "Rating Stars" section
4. You can customize:
   - Empty Star Color (for unselected stars)
   - Filled Star Color (for selected/hovered stars)
   - Star Size (adjust the size in pixels)

= Do I need Elementor Pro to use Revix Reviews widgets? =
No, the free version of Elementor is sufficient. All Revix Reviews widgets work with both Elementor Free and Elementor Pro.

== Screenshots ==

1. Admin settings panel for Revix Reviews
2. General Review post type
3. Trustpilot admin integration panel
4. Google Reviews settings panel
5. Google and Trustpilot review displays with SVG icons
6. Google Reviews
7. Google Review Settings
8. General User Feedback submission form

== Changelog ==
= 1.2.7 =
- NEW: Modern admin panel UI with improved user experience
- NEW: Elementor widget styling controls for better visual customization
- NEW: Review Submit Form Elementor widget - Enhanced star rating controls
- NEW: Added separate "Empty Star Color" and "Filled Star Color" controls in Elementor
- IMPROVED: Testimonial Reviews Elementor widget - Renamed "Author Name" to "Heading Name" for clarity
- FIXED: Rating star color and size controls now work properly in Elementor editor
- IMPROVED: Star styling controls now properly override default CSS with !important flags

= 1.2.6 =
- IMPORTANT: Migrated to Google Places API (New) - Legacy API no longer supported
- NEW: Automatic cache clearing when Google or Trustpilot settings are saved
- NEW: Smart caching system (12-hour expiration) for better performance
- NEW: Enhanced text extraction for Trustpilot reviews with 6 fallback strategies
- IMPROVED: Better error logging and debugging for API issues
- FIXED: Google Reviews now work with the new Places API format
- FIXED: Trustpilot review text display issues
- Note: You must enable "Places API (New)" in Google Cloud Console for Google Reviews to work

= 1.2.5 =
- bug fixes
- Form styling improvements to Modern
- Using Ajax for form submission


= 1.2.4 =
- performance improvements
- bug fixes

= 1.2.3 =
* Bug fix and improvement

= 1.2.2 = 
* Bug Fix

= 1.2.1 =
* Add attributes 'words' and masonry for google review shortcode.

= 1.2.0 =
* NEW: Google Reviews integration with `[revix_google_reviews]` shortcode
* NEW: Google summary display with `[revix_google_summary]` shortcode
* NEW: SVG-based star icons for Google ratings
* NEW: Customizable name and label controls for summary
* ENHANCED: Improved shortcode rendering and visual structure

= 1.1.1 =
* Count issue fix for `[revix_trustpilot_reviews]` shortcode

= 1.1.0 =
* NEW: Trustpilot integration
* NEW: Shortcode filters for review count and rating
* NEW: Trustpilot summary support
* Tweak: Improved display and fallback

= 1.0.0 =
* Initial release

== Upgrade Notice ==
= 1.2.6 =
IMPORTANT: Google has deprecated the old Places API. This version migrates to the new Places API (New). You MUST enable "Places API (New)" in Google Cloud Console for Google Reviews to continue working. Legacy API users will need to update their settings.

= 1.2.0 =  
Major update: Adds Google Reviews support and display shortcodes. Please configure your Google API Key and Place ID in settings to enable.