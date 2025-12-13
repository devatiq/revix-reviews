/**
 * Revix Reviews Settings JavaScript
 * Handles AJAX toggle switches and enhanced UX
 */
(function($) {
    'use strict';

    const RevixSettings = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Handle toggle switches with AJAX
            $(document).on('change', '.revixreviews-ajax-toggle', this.handleToggleChange);
            
            // Handle form submission
            $('.revixreviews-settings-form').on('submit', this.handleFormSubmit);
        },

        handleToggleChange: function(e) {
            const $toggle = $(this);
            const $wrapper = $toggle.closest('.revixreviews-toggle-switch');
            const optionName = $toggle.data('option');
            const value = $toggle.is(':checked') ? 1 : 0;

            // Add loading state
            $wrapper.addClass('loading');
            $toggle.prop('disabled', true);

            // Send AJAX request
            $.ajax({
                url: revixReviewsSettings.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'revixreviews_toggle_setting',
                    nonce: revixReviewsSettings.nonce,
                    option: optionName,
                    value: value
                },
                success: function(response) {
                    if (response.success) {
                        RevixSettings.showNotice('success', response.data.message);
                    } else {
                        RevixSettings.showNotice('error', response.data.message || 'Failed to update setting');
                        // Revert toggle state
                        $toggle.prop('checked', !value);
                    }
                },
                error: function() {
                    RevixSettings.showNotice('error', 'An error occurred. Please try again.');
                    // Revert toggle state
                    $toggle.prop('checked', !value);
                },
                complete: function() {
                    $wrapper.removeClass('loading');
                    $toggle.prop('disabled', false);
                }
            });
        },

        handleFormSubmit: function(e) {
            const $form = $(this);
            const $submitButton = $form.find('.revixreviews-save-button');
            const originalText = $submitButton.text();

            // Show loading state
            $submitButton.prop('disabled', true);
            $submitButton.html('<span class="revixreviews-spinner"></span> ' + revixReviewsSettings.savingText);

            // Form will submit normally, but we enhance the UX
            setTimeout(function() {
                $submitButton.text(originalText);
                $submitButton.prop('disabled', false);
            }, 2000);
        },

        showNotice: function(type, message) {
            const noticeClass = 'revixreviews-notice-' + type;
            const icons = {
                success: '✓',
                error: '✕',
                info: 'ℹ'
            };

            const $notice = $('<div>', {
                class: 'revixreviews-notice ' + noticeClass,
                html: '<span class="revixreviews-notice-icon">' + icons[type] + '</span><span>' + message + '</span>'
            });

            // Remove existing notices
            $('.revixreviews-notice').remove();

            // Add notice to the top of settings body
            $('.revixreviews-settings-body').prepend($notice);

            // Auto-hide after 4 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RevixSettings.init();
    });

})(jQuery);
