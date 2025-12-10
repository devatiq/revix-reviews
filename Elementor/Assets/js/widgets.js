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
            // Masonry initialization can be added here if needed for Elementor widgets
            if (typeof $.fn.masonry !== 'undefined') {
                $('.revixreviews-elementor-masonry').masonry({
                    itemSelector: '.revixreviews-elementor-review-card',
                    columnWidth: '.revixreviews-elementor-review-card',
                    percentPosition: true
                });
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
