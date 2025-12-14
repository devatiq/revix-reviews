/**
 * Revix Reviews Elementor Widgets Scripts
 * 
 * @package RevixReviews
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Initialize Elementor widgets
     */
    var RevixReviewsElementor = {
        init: function() {
            // Add any widget-specific JavaScript initialization here
            this.initMasonryLayout();
        },

        /**
         * Initialize masonry layout if needed
         */
        initMasonryLayout: function() {
            // Initialize masonry for Google reviews
            if (typeof Masonry !== 'undefined') {
                $('.revix-google-masonry').each(function() {
                    new Masonry(this, {
                        itemSelector: '.revix-google-review-item',
                        columnWidth: '.revix-google-review-item',
                        percentPosition: true,
                        gutter: 25
                    });
                });

                // Initialize masonry for Trustpilot reviews
                $('.revix-trustpilot-masonry').each(function() {
                    new Masonry(this, {
                        itemSelector: '.revix-trustpilot-review-item',
                        columnWidth: '.revix-trustpilot-review-item',
                        percentPosition: true,
                        gutter: 25
                    });
                });

                // Note: Testimonial reviews use CSS columns (column-count) for masonry layout
                // No JS initialization needed
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        RevixReviewsElementor.init();
    });

    // Re-initialize on Elementor frontend load
    $(window).on('elementor/frontend/init', function() {
        RevixReviewsElementor.init();
    });

})(jQuery);
