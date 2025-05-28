=== Revix Reviews – All-in-One Business Review Manager ===
Plugin Name: Revix Reviews
Version: 1.1.1
Author: supreoxltd
Author URI: https://supreox.com/
Contributors: abcplugins, atiqbd4ever, supreoxltd
Tags: reviews, business reviews, trustpilot, testimonials, google reviews
Requires at least: 5.4
Tested up to: 6.8
Stable tag: 1.1.1
Requires PHP: 8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Revix Reviews helps you collect, import, and display reviews—including Trustpilot—with more platforms coming soon.

== Description ==

Revix Reviews allows you to manage, showcase, and collect customer feedback directly on your WordPress site, with built-in Trustpilot integration and flexible display options. 

Whether you're a business owner, freelancer, or eCommerce site, this plugin helps boost credibility and conversion by showing authentic testimonials.

**Key Features:**

- 📝 Add and manage reviews using a built-in submission form.
- 🌐 Fetch and display public reviews from Trustpilot (with pagination & filters).
- 🌟 Star-rating SVG icons to visually match the review score.
- 🎯 Filter reviews by minimum and maximum rating via shortcode.
- 📊 Show company-wide review summary (average rating + total count).
- 📎 Custom post type support for native review management.
- 🔒 Review moderation and approval settings.
- ⚙️ Shortcode support to embed forms, display grids, or summaries anywhere.

**Coming Soon:**
- ⭐ Google Reviews integration
- 🛠️ Yelp, Facebook, and more third-party sources
- 🎨 Gutenberg & Elementor widgets
- 📈 Analytics dashboard for review insights

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/revix-reviews` directory, or install the plugin via the WordPress plugin repository.
2. Activate the plugin from the 'Plugins' menu in WordPress.
3. Go to **Settings → Revix Reviews** to configure plugin options.
4. Use the shortcodes below to add review forms and displays to your pages.

== Shortcodes ==

**1. `[revixreviews_form]`**  
Displays the native customer review submission form.

**2. `[revixreviews]`**  
Displays native reviews in a customizable grid layout.  
Now supports the following attributes:
- `count` – Limit how many reviews are shown (e.g., `count="6"`)
- `min_rating` – Minimum rating to display (e.g., `min_rating="4"`)
- `max_rating` – Maximum rating to display (e.g., `max_rating="5"`)

**3. `[revix_trustpilot_reviews]`**  
Displays Trustpilot reviews pulled from the public business profile.  
Supports:
- `count`
- `min_rating`
- `max_rating`

**4. `[revix_trustpilot_summary]`**  
Displays Trustpilot summary: star rating and total number of reviews.


== Frequently Asked Questions ==

= Can I customize the form fields? =
Yes. You can customize the native review form fields from the settings page.

= Can I show only 5-star Trustpilot reviews? =
Absolutely. Use `[revix_trustpilot_reviews min_rating="5"]` to filter.

= Will it support Google reviews? =
Yes, Google Reviews integration is planned for future updates.

= Can I show average rating and total count separately? =
Yes, use `[revix_trustpilot_summary]` to show the business summary pulled directly from Trustpilot.

= Can I moderate reviews before they appear? =
Yes, the plugin allows you to manually approve or auto-publish native reviews.

== Screenshots ==

1. Admin settings panel for Revix Reviews.
2. General Review post type.
3. Trustpilot admin settings
4. Display of reviews using Trustpilot shortcode.
5. Company summary with average and total reviews.

== Changelog ==
= 1.1.1 =
* Count issue fix for [revix_trustpilot_reviews] shortcode

= 1.1.0 =
* NEW: Trustpilot review integration
* NEW: Filter reviews by rating (`min_rating`, `max_rating`) for both Trustpilot and native `[revixreviews]`
* NEW: Limit number of reviews with `count` attribute for `[revixreviews]`
* NEW: Trustpilot summary shortcode `[revix_trustpilot_summary]`
* NEW: Pagination support for up to 10 pages of Trustpilot reviews
* NEW: Star-rating SVG icons
* Tweak: Improved shortcode flexibility
* Fix: Better fallback for missing review content


= 1.0.0 =
* Initial release

== Upgrade Notice ==
= 1.1.1 = 
- Count issue fix for [revix_trustpilot_reviews] shortcode

= 1.1.0 =
This update adds Trustpilot integration, filtering, summary features, and SVG star icons. Future updates will include Google Reviews support and more.