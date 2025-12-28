/**
 * Revix Reviews Settings Save JavaScript
 * Handles AJAX form submission with SweetAlert2 confirmation
 */
(function($) {
    'use strict';

    const RevixSettingsSave = {
        /**
         * Initialize the settings save handler
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Handle form submission
            $('#revixreviews-settings-form').on('submit', this.handleFormSubmit.bind(this));
        },

        /**
         * Handle form submission
         * @param {Event} e - Form submit event
         */
        handleFormSubmit: function(e) {
            e.preventDefault();

            const $form = $(e.currentTarget);
            const formData = this.collectFormData($form);
            const activeTab = this.getActiveTab();

            // Add active tab to form data
            formData.append('active_tab', activeTab);

            // Save settings via AJAX
            this.saveSettings(formData);
        },

        /**
         * Collect all form data
         * @param {jQuery} $form - Form element
         * @returns {FormData} - Form data object
         */
        collectFormData: function($form) {
            const formData = new FormData($form[0]);
            
            // Add AJAX action and nonce
            formData.append('action', 'revixreviews_save_settings');
            formData.append('nonce', revixReviewsSettings.nonce);

            return formData;
        },

        /**
         * Get the currently active tab
         * @returns {string} - Active tab name
         */
        getActiveTab: function() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('tab') || 'general';
        },

        /**
         * Save settings via AJAX
         * @param {FormData} formData - Form data to send
         */
        saveSettings: function(formData) {
            const self = this;

            // Set loading state
            self.setLoadingState(true);

            $.ajax({
                url: revixReviewsSettings.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        self.showSuccessMessage(response.data.message);
                    } else {
                        self.showErrorMessage(response.data.message || 'Failed to save settings.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    self.showErrorMessage('An error occurred while saving settings. Please try again.');
                },
                complete: function() {
                    self.setLoadingState(false);
                }
            });
        },

        /**
         * Show success message using SweetAlert2
         * @param {string} message - Success message
         */
        showSuccessMessage: function(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: message || 'Settings saved successfully!',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                alert(message || 'Settings saved successfully!');
            }
        },

        /**
         * Show error message using SweetAlert2
         * @param {string} message - Error message
         */
        showErrorMessage: function(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message || 'Failed to save settings.',
                    confirmButtonText: 'OK'
                });
            } else {
                alert(message || 'Failed to save settings.');
            }
        },

        /**
         * Toggle loading state on submit button
         * @param {boolean} isLoading - Loading state
         */
        setLoadingState: function(isLoading) {
            const $submitButton = $('#revixreviews-submit-btn');
            
            if (isLoading) {
                $submitButton.prop('disabled', true);
                $submitButton.data('original-text', $submitButton.text());
                $submitButton.text(revixReviewsSettings.savingText || 'Saving...');
            } else {
                $submitButton.prop('disabled', false);
                const originalText = $submitButton.data('original-text');
                if (originalText) {
                    $submitButton.text(originalText);
                }
            }
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RevixSettingsSave.init();
    });

    // Export to global scope for potential external access
    window.RevixSettingsSave = RevixSettingsSave;

})(jQuery);
