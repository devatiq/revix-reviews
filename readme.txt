=== Revix Reviews – All-in-One Business Review Manager ===
Plugin Name: Revix Reviews
Version: 1.2.2
Author: supreoxltd
Author URI: https://supreox.com/
Contributors: abcplugins, atiqbd4ever, supreoxltd
Tags: Tags: reviews, business reviews, testimonials, trustpilot, google reviews
Requires at least: 5.4
Tested up to: 6.8
Stable tag: 1.2.2
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Revix Reviews helps you collect, import, and display reviews—including Trustpilot and Google—with more platforms coming soon.

== Description ==

Revix Reviews allows you to manage, showcase, and collect customer feedback directly on your WordPress site, with built-in Trustpilot and Google Reviews integration.

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
- `count`
- `min_rating`
- `max_rating`

**4. `[revix_trustpilot_summary]`**  
Displays Trustpilot summary: star rating and total number of reviews.

**5. `[revix_google_reviews]`**  
Displays reviews from a public Google Maps place using your API key.  
Displays:
- Author, rating stars, time, review text, profile image, and Google logo.
Supports:
- `words`
- `masonry`

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

== Screenshots ==

1. Admin settings panel for Revix Reviews
2. General Review post type
3. Trustpilot admin integration panel
4. Google Reviews settings panel
5. Google and Trustpilot review displays with SVG icons
6. Google Reviews
7. Google Review Settings

== Changelog ==

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
= 1.2.0 =  
Major update: Adds Google Reviews support and display shortcodes. Please configure your Google API Key and Place ID in settings to enable.