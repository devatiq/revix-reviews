# Revix Reviews - Architecture Documentation

## Overview

Revix Reviews is a WordPress plugin that provides a comprehensive review management system with support for Google Reviews, Trustpilot integration, custom testimonials, and user-submitted reviews. The plugin features both shortcode and Elementor widget implementations for flexible content display.

**Version:** 1.2.6  
**Minimum Requirements:**
- WordPress: 6.9+
- PHP: 8.0+
- Elementor: 3.19.0+ (for widget functionality)

---

## Architecture Principles

### 1. **Separation of Concerns**
- Clear separation between Admin, Public, and Core functionality
- Dedicated namespaces for different feature areas
- Single Responsibility Principle applied to classes

### 2. **DRY (Don't Repeat Yourself)**
- Shared AJAX handlers between shortcodes and Elementor widgets
- Reusable CSS and JavaScript assets
- Common rendering logic for review displays

### 3. **Security First**
- Nonce validation on all AJAX requests
- Capability checks for admin operations
- Input sanitization and output escaping
- Prepared SQL statements for database queries

### 4. **Extensibility**
- Hook-based architecture
- Settings API for configuration
- Custom post types for data flexibility
- Elementor widget framework integration

---

## Directory Structure

```
revix-reviews/
├── Admin/                          # WordPress admin functionality
│   ├── Assets/                     # Admin CSS, JS, images
│   │   ├── css/
│   │   │   └── admin-style.css    # Admin UI styles
│   │   ├── js/
│   │   │   └── settings.js        # AJAX toggle handling
│   │   └── img/
│   ├── Core/
│   │   └── Core.php               # Admin initialization
│   ├── Inc/
│   │   ├── Core/                  # Admin core components
│   │   ├── Dashboard/
│   │   │   ├── Settings/
│   │   │   │   └── Settings.php   # Main settings page
│   │   │   └── Tabs/
│   │   │       ├── Tabs.php       # Settings tab system
│   │   │       ├── Google/
│   │   │       │   └── GoogleSettings.php
│   │   │       └── Trustpilot/
│   │   │           └── TrustpilotSettings.php
│   │   └── Reviews/
│   │       ├── MetaBox/
│   │       │   └── ReviewsMetaBox.php
│   │       └── PostTypes/
│   │           └── Reviews.php    # Custom post type registration
│   └── AdminManager.php           # Admin coordinator
│
├── Inc/                           # Plugin activation/deactivation
│   ├── Activate.php
│   ├── Deactivate.php
│   └── Manager.php                # Plugin initialization
│
├── Public/                        # Frontend functionality
│   ├── Assets/                    # Public CSS, JS, images
│   │   ├── css/
│   │   │   ├── style.css         # Main frontend styles
│   │   │   └── sweetalert2.min.css
│   │   ├── js/
│   │   │   └── sweetalert2.all.min.js
│   │   └── Library/
│   │       └── Icons/
│   │           └── SVG.php       # SVG icon renderer
│   │
│   ├── Elementor/                # Elementor integration
│   │   ├── Assets/
│   │   │   ├── css/
│   │   │   └── js/
│   │   │       └── widgets.js    # Widget initialization
│   │   ├── Widgets/
│   │   │   ├── GoogleReviews/
│   │   │   │   └── Main.php
│   │   │   ├── GoogleSummary/
│   │   │   │   └── Main.php
│   │   │   ├── TrustpilotReviews/
│   │   │   │   └── Main.php
│   │   │   ├── TrustpilotSummary/
│   │   │   │   └── Main.php
│   │   │   ├── TestimonialReviews/
│   │   │   │   └── Main.php
│   │   │   └── ReviewSubmitForm/
│   │   │       └── Main.php
│   │   └── Configuration.php     # Elementor setup
│   │
│   ├── Inc/
│   │   └── Integrations/
│   │       ├── Google/
│   │       │   └── GoogleReviewFetcher.php
│   │       └── Trustpilot/
│   │           └── TrustpilotFetcher.php
│   │
│   ├── Shortcodes/
│   │   ├── Assets/
│   │   │   ├── css/
│   │   │   │   ├── general.css
│   │   │   │   ├── google-review.css
│   │   │   │   └── trustpilot.css
│   │   │   └── js/
│   │   │       ├── masonry.pkgd.min.js
│   │   │       ├── revixreviews-ajax.js
│   │   │       ├── revixreviews-form.js
│   │   │       └── trustpilot.js
│   │   ├── General/
│   │   │   ├── ReviewsShortcode.php
│   │   │   └── ReviewsSubmitForm.php
│   │   ├── Google/
│   │   │   └── GoogleReviews.php
│   │   ├── Trustpilot/
│   │   │   ├── TrustpilotShortcode.php
│   │   │   └── TrustpilotSummaryShortcode.php
│   │   └── Shortcodes.php
│   │
│   └── Frontend.php              # Public coordinator
│
├── languages/
│   └── revix-reviews.pot         # Translation template
│
├── vendor/                        # Composer autoloader
│
├── revix-reviews.php             # Main plugin file
├── composer.json                 # Composer configuration
└── phpcs.xml.dist               # PHP CodeSniffer rules
```

---

## Core Components

### 1. Plugin Manager (`Inc/Manager.php`)

**Responsibilities:**
- Plugin initialization
- Autoloader setup
- Admin and Public component coordination
- Hook registration

**Key Methods:**
```php
public function run()               // Initialize plugin
private function load_dependencies() // Load required files
private function init_hooks()       // Register WordPress hooks
```

### 2. Admin Manager (`Admin/AdminManager.php`)

**Responsibilities:**
- Admin interface initialization
- Settings page registration
- Meta box registration
- Admin assets enqueueing

**Features:**
- AJAX-powered toggle switches for settings
- Tab-based settings interface (General, Google, Trustpilot)
- Master toggle for Elementor widgets with dependency management
- Security: nonce validation, capability checks, sanitization

### 3. Frontend Manager (`Public/Frontend.php`)

**Responsibilities:**
- Public assets enqueueing
- Shortcode registration
- Elementor integration initialization

**Asset Loading Strategy:**
- Conditional loading based on shortcode presence
- Prevention of duplicate script loading
- Proper dependency management

### 4. Custom Post Type (`Admin/Inc/Reviews/PostTypes/Reviews.php`)

**Post Type:** `revixreviews`

**Meta Fields:**
- `revixreviews_name` - Reviewer name
- `revixreviews_email` - Reviewer email
- `revixreviews_rating` - Star rating (0-5)

**Features:**
- Custom meta boxes for review data
- Admin columns customization
- Support for title, editor, custom-fields

---

## Feature Modules

### 1. Settings System

**Architecture Pattern:** Tab-based interface with AJAX toggles

**Files:**
- `Admin/Inc/Dashboard/Settings/Settings.php` - Main settings coordinator
- `Admin/Inc/Dashboard/Tabs/Tabs.php` - Tab registration system
- `Admin/Assets/js/settings.js` - AJAX toggle logic

**Settings Structure:**

#### General Tab
- Redirect URL after review submission
- Default review status (Publish/Pending)
- Master Elementor toggle
- Individual widget toggles (6 widgets)
- "Enable All Widgets" bulk toggle

#### Google Tab
- Business ID
- API Key
- Cache duration
- Cache clearing functionality

#### Trustpilot Tab
- Business Unit URL
- Cache duration
- Cache clearing functionality

**AJAX Implementation:**

```javascript
// Toggle handling with dependency management
handleToggleChange(e) {
    // Security: nonce validation
    // Master toggle disables all child widgets
    // Individual toggles update immediately
    // Select all toggle syncs with individual states
}

// Widget arrays synchronized across 4 functions:
const widgetToggles = [
    'revixreviews_google_summary',
    'revixreviews_trustpilot_summary',
    'revixreviews_trustpilot_reviews',
    'revixreviews_google_reviews',
    'revixreviews_testimonial_reviews',
    'revixreviews_submit_form'
];
```

**Security Whitelist:**
```php
$allowed_options = array(
    'revixreviews_elementor_active',
    'revixreviews_google_summary',
    'revixreviews_google_reviews',
    'revixreviews_trustpilot_summary',
    'revixreviews_trustpilot_reviews',
    'revixreviews_testimonial_reviews',
    'revixreviews_submit_form',
);
```

### 2. Elementor Integration

**Architecture Pattern:** Widget-based with centralized configuration

**Configuration File:** `Public/Elementor/Configuration.php`

**Initialization Flow:**
```
1. Check Elementor compatibility (version, PHP)
2. Display admin notices if incompatible
3. Register 'revix-reviews' widget category
4. Register widgets based on settings
5. Enqueue shared assets
```

**Widget Registration:**
```php
// Conditional registration based on user settings
foreach ($widgets as $option_name => $widget_class) {
    $is_enabled = get_option($option_name, 1);
    if ($is_enabled) {
        $widgets_manager->register(new $full_class());
    }
}
```

**Shared Assets:**
- Masonry library (masonry.pkgd.min.js)
- Trustpilot integration script
- Form handling scripts (conditional)
- Widget initialization script

**Widget Types:**

1. **Google Summary** - Displays aggregated Google rating
2. **Google Reviews** - Lists individual Google reviews
3. **Trustpilot Summary** - Shows Trustpilot rating overview
4. **Trustpilot Reviews** - Displays Trustpilot review list
5. **Testimonial Reviews** - Custom testimonial display from post type
6. **Review Submit Form** - User review submission interface

**Common Widget Features:**
- Responsive column controls
- Masonry layout option (CSS columns for testimonials, JS for Google/Trustpilot)
- Typography controls
- Color controls
- Border/Box Shadow controls
- Spacing controls

### 3. Review Submission System

**Flow Diagram:**
```
User fills form → JavaScript validation → AJAX submit
                                              ↓
                              Security: nonce verification
                                              ↓
                              Sanitize inputs (name, email, subject, rating)
                                              ↓
                              Create post (type: revixreviews)
                                              ↓
                              Status: publish/pending (admin setting)
                                              ↓
                              JSON response → SweetAlert popup → Redirect
```

**Files:**
- `Public/Shortcodes/General/ReviewsSubmitForm.php` - AJAX handler
- `Public/Shortcodes/Assets/js/revixreviews-form.js` - Star rating UI
- `Public/Shortcodes/Assets/js/revixreviews-ajax.js` - AJAX submission

**Security Layers:**

1. **Nonce Validation:**
   ```php
   check_ajax_referer('revixreviews_feedback_nonce_action', 'nonce');
   ```

2. **Required Field Validation:**
   ```php
   $required_fields = ['revixreviews_name', 'revixreviews_email', 
                       'revixreviews_subject', 'revixreviews_comments', 
                       'revixreviews_rating'];
   ```

3. **Input Sanitization:**
   ```php
   $name = sanitize_text_field(wp_unslash($_POST['revixreviews_name']));
   $email = sanitize_email(wp_unslash($_POST['revixreviews_email']));
   $comments = sanitize_textarea_field(wp_unslash($_POST['revixreviews_comments']));
   $rating = intval(wp_unslash($_POST['revixreviews_rating']));
   ```

4. **Email Validation:**
   ```php
   if (!is_email($email)) {
       wp_send_json_error(['message' => 'Invalid email address.']);
   }
   ```

**Duplicate Prevention:**
- Single event handler binding with `wp_script_is()` check
- Conditional script loading (shortcode vs Elementor)
- Explicit `wp_die()` after JSON responses

### 4. External API Integration

**Google Reviews Integration:**

**File:** `Public/Inc/Integrations/Google/GoogleReviewFetcher.php`

**API Endpoint:** Google Places API
**Cache Key:** `revixreviews_google_reviews_{business_id}`
**Cache Duration:** Admin configurable (default: 1 hour)

**Features:**
- API key authentication
- Response caching with transients
- Error handling and validation
- Rating filtering (min/max)
- Review count limiting

**Trustpilot Integration:**

**File:** `Public/Inc/Integrations/Trustpilot/TrustpilotFetcher.php`

**Data Source:** Trustpilot Business Unit page scraping
**Cache Key:** `revixreviews_trustpilot_reviews_{business_unit}`
**Cache Duration:** Admin configurable (default: 1 hour)

**Features:**
- HTML parsing for review data
- Transient caching
- Manual cache clearing
- Review filtering

---

## Data Flow

### Review Display (Testimonials)

```
User visits page with widget/shortcode
           ↓
WP_Query: post_type = 'revixreviews'
           ↓
Filter by rating (min_rating, max_rating)
           ↓
Limit by count
           ↓
Apply column layout (grid/masonry)
           ↓
Truncate content by word limit
           ↓
Render: title, rating stars, content, quote icon
```

### Google Reviews Display

```
User visits page
      ↓
Check transient cache
      ↓
If cached: return data
      ↓
If not: API request to Google Places
      ↓
Parse response
      ↓
Cache for X hours (transient)
      ↓
Filter reviews (rating, count)
      ↓
Render: avatar, name, rating, text, date, Google logo
```

### Settings Update

```
User clicks toggle in admin
         ↓
JavaScript: AJAX POST to admin-ajax.php
         ↓
PHP: revixreviews_toggle_setting action
         ↓
Security: check_ajax_referer()
         ↓
Validate: option in whitelist
         ↓
Validate: value is 0 or 1
         ↓
Update: update_option()
         ↓
Response: wp_send_json_success()
         ↓
JavaScript: update UI, show notification
```

---

## Design Patterns

### 1. **Singleton Pattern**
Used in Elementor Configuration:
```php
private static $_instance = null;

public static function instance() {
    if (is_null(self::$_instance)) {
        self::$_instance = new self();
    }
    return self::$_instance;
}
```

### 2. **Factory Pattern**
Elementor widget registration:
```php
foreach ($widgets as $option_name => $widget_class) {
    $full_class = $namespace_base . $widget_class;
    if (class_exists($full_class)) {
        $widgets_manager->register(new $full_class());
    }
}
```

### 3. **Strategy Pattern**
Layout rendering (Grid vs Masonry):
```php
// CSS-based strategy selection
$classes = ['revix-testimonial-grids'];
if ($settings['masonry'] === 'true') {
    $classes[] = 'revix-testimonial-masonry';
}
```

### 4. **Observer Pattern**
WordPress hooks and actions:
```php
add_action('elementor/widgets/register', [$this, 'register_widgets']);
add_action('wp_ajax_submit_revixreviews_feedback_ajax', [$this, 'handle_submission_ajax']);
```

---

## Performance Optimization

### 1. **Caching Strategy**

**Transient Cache:**
- Google Reviews: `revixreviews_google_reviews_{id}`
- Trustpilot Reviews: `revixreviews_trustpilot_reviews_{unit}`
- Default duration: 1 hour (3600 seconds)
- Manual cache clearing via admin settings

**Asset Loading:**
- Conditional enqueuing based on shortcode presence
- Script dependency management
- Minified libraries for production

### 2. **Database Optimization**

**Prepared Statements:**
```php
$wpdb->prepare(
    "DELETE FROM {$wpdb->postmeta} 
     WHERE meta_key LIKE %s",
    $wpdb->esc_like('_transient_revixreviews_google_reviews_') . '%'
);
```

**Indexed Queries:**
- WP_Query with meta_query for rating filters
- Custom post type indexes

### 3. **Frontend Optimization**

**CSS Columns for Masonry:**
- Pure CSS implementation for testimonials
- No JavaScript overhead for layout
- Better performance than JS masonry

**Lazy Loading:**
- Images in review cards
- SweetAlert loaded on-demand

---

## Security Implementation

### 1. **Input Validation**

**Nonce Verification:**
```php
check_ajax_referer('revixreviews_feedback_nonce_action', 'nonce');
wp_verify_nonce($_POST['revixreviews_feedback_nonce'], 'revixreviews_feedback_nonce_action');
```

**Capability Checks:**
```php
if (!current_user_can('manage_options')) {
    wp_send_json_error(['message' => 'Unauthorized']);
}
```

**Whitelist Validation:**
```php
if (!in_array($option_name, $allowed_options, true)) {
    wp_send_json_error(['message' => 'Invalid option']);
}
```

### 2. **Output Escaping**

**Context-aware escaping:**
```php
esc_html__()      // Translatable text
esc_attr()        // HTML attributes
esc_url()         // URLs
wp_kses_post()    // Post content
sanitize_text_field()  // User input
sanitize_email()  // Email addresses
```

### 3. **SQL Injection Prevention**

**Prepared Statements:**
```php
$wpdb->prepare(
    "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
    'revixreviews_rating'
);
```

**Escaped LIKE Patterns:**
```php
$wpdb->esc_like('_transient_prefix_') . '%'
```

---

## Extension Points

### 1. **WordPress Hooks**

**Actions:**
```php
do_action('revixreviews_before_submit');
do_action('revixreviews_after_submit', $post_id);
do_action('revixreviews_cache_cleared', $type);
```

**Filters:**
```php
apply_filters('revixreviews_default_status', 'pending');
apply_filters('revixreviews_google_reviews', $reviews);
apply_filters('revixreviews_widget_output', $html);
```

### 2. **Elementor Widget Extension**

Developers can extend base widgets:
```php
class CustomReviewWidget extends \RevixReviews\Public\Elementor\Widgets\TestimonialReviews\Main {
    // Override methods
}
```

### 3. **Custom Post Type Fields**

Add custom meta fields:
```php
add_action('revixreviews_meta_box_fields', function() {
    // Add custom fields
});
```

---

## Testing Considerations

### 1. **Unit Testing**

**Recommended Coverage:**
- API integration classes
- Data sanitization methods
- Cache management
- Security validation

### 2. **Integration Testing**

**Critical Paths:**
- Form submission flow
- AJAX toggle updates
- Widget registration
- Settings persistence

### 3. **Security Testing**

**Test Cases:**
- SQL injection attempts
- XSS vulnerability checks
- CSRF token validation
- Capability bypasses

---

## Maintenance & Debugging

### 1. **Error Logging**

Enable WordPress debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### 2. **Common Issues**

**Duplicate Submissions:**
- Check for multiple script enqueues
- Verify event handler binding once
- Confirm `wp_die()` after AJAX responses

**Widget Not Appearing:**
- Verify option `revixreviews_[widget]` is enabled
- Check Elementor version compatibility
- Ensure class files exist and are autoloaded

**Styles Not Loading:**
- Check asset enqueue conditions
- Verify file paths in constants
- Clear browser and plugin caches

### 3. **Performance Monitoring**

**Key Metrics:**
- API request duration
- Cache hit rate
- Database query count
- Page load time with widgets

---

## Future Considerations

### Potential Enhancements

1. **REST API Endpoints**
   - Public API for review retrieval
   - Webhook support for real-time updates

2. **Advanced Filtering**
   - Date range filters
   - Keyword search in reviews
   - Tag-based categorization

3. **Analytics Dashboard**
   - Review trends
   - Rating distribution charts
   - Response rate tracking

4. **Multi-language Support**
   - Full i18n implementation
   - RTL support
   - Translation management

5. **Review Moderation**
   - Flagging system
   - Automated spam detection
   - Review editing interface

---

## Changelog

**Version 1.2.6**
- Added Testimonial Reviews Elementor widget
- Added Review Submit Form Elementor widget
- Implemented masonry layout support
- Fixed duplicate submission issue
- Enhanced security with explicit wp_die() calls
- Improved script loading to prevent duplicates
- Added word limit control for testimonials
- Professional UI/UX redesign for admin settings
- Master toggle dependency system for Elementor widgets
- Bulk "Enable All Widgets" toggle

---

## License

This plugin is proprietary software. All rights reserved.

---

## Support & Documentation

For issues, feature requests, or questions:
- GitHub: https://github.com/devatiq/revix-reviews
- Documentation: [Link to documentation]
- Support: [Support contact]

---

**Last Updated:** December 15, 2025
