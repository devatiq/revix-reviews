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
            
            // Handle select all toggle
            $(document).on('change', '.revixreviews-select-all-toggle-input', this.handleSelectAllToggle);
            
            // Handle form submission
            $('.revixreviews-settings-form').on('submit', this.handleFormSubmit);
            
            // Initial check on page load
            this.handleMasterToggleState();
            this.updateSelectAllState();
        },

        handleToggleChange: function(e) {
            const $toggle = $(this);
            const $wrapper = $toggle.closest('.revixreviews-toggle-switch');
            const optionName = $toggle.data('option');
            const value = $toggle.is(':checked') ? 1 : 0;

            // Add loading state
            $wrapper.addClass('loading');
            $toggle.prop('disabled', true);
            
            // If this is the master Elementor toggle being disabled
            if (optionName === 'revixreviews_elementor_active' && value === 0) {
                RevixSettings.disableAllWidgets($toggle, $wrapper);
                return;
            }

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
                        
                        // If master toggle enabled, re-enable widget controls
                        if (optionName === 'revixreviews_elementor_active') {
                            RevixSettings.handleMasterToggleState();
                        }
                        
                        // Update select all state when individual widget changes
                        RevixSettings.updateSelectAllState();
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
        
        disableAllWidgets: function($masterToggle, $masterWrapper) {
            const widgetToggles = [
                'revixreviews_google_summary',
                'revixreviews_trustpilot_summary',
                'revixreviews_trustpilot_reviews',
                'revixreviews_google_reviews',
                'revixreviews_testimonial_reviews',
                'revixreviews_submit_form'
            ];
            
            let completedRequests = 0;
            const totalToggles = widgetToggles.length;
            
            // Disable master toggle first
            $.ajax({
                url: revixReviewsSettings.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'revixreviews_toggle_setting',
                    nonce: revixReviewsSettings.nonce,
                    option: 'revixreviews_elementor_active',
                    value: 0
                },
                success: function(response) {
                    if (response.success) {
                        // Now disable all widget toggles
                        widgetToggles.forEach(function(widgetOption) {
                            const $widgetToggle = $('.revixreviews-ajax-toggle[data-option="' + widgetOption + '"]');
                            
                            if ($widgetToggle.length && $widgetToggle.is(':checked')) {
                                $.ajax({
                                    url: revixReviewsSettings.ajaxUrl,
                                    type: 'POST',
                                    data: {
                                        action: 'revixreviews_toggle_setting',
                                        nonce: revixReviewsSettings.nonce,
                                        option: widgetOption,
                                        value: 0
                                    },
                                    success: function() {
                                        $widgetToggle.prop('checked', false);
                                    },
                                    complete: function() {
                                        completedRequests++;
                                        
                                        if (completedRequests === totalToggles) {
                                            RevixSettings.showNotice('success', 'Elementor widgets disabled successfully');
                                            RevixSettings.handleMasterToggleState();
                                        }
                                    }
                                });
                            } else {
                                completedRequests++;
                                
                                if (completedRequests === totalToggles) {
                                    RevixSettings.showNotice('success', 'Elementor widgets disabled successfully');
                                    RevixSettings.handleMasterToggleState();
                                }
                            }
                        });
                    } else {
                        // If failed, revert the master toggle
                        $masterToggle.prop('checked', true);
                    }
                },
                complete: function() {
                    $masterWrapper.removeClass('loading');
                    $masterToggle.prop('disabled', false);
                }
            });
        },
        
        handleMasterToggleState: function() {
            const $masterToggle = $('.revixreviews-ajax-toggle[data-option="revixreviews_elementor_active"]');
            const isMasterEnabled = $masterToggle.is(':checked');
            
            const widgetToggles = [
                'revixreviews_google_summary',
                'revixreviews_trustpilot_summary',
                'revixreviews_trustpilot_reviews',
                'revixreviews_google_reviews',
                'revixreviews_testimonial_reviews',
                'revixreviews_submit_form'
            ];
            
            // Handle individual widget toggles
            widgetToggles.forEach(function(widgetOption) {
                const $widgetToggle = $('.revixreviews-ajax-toggle[data-option="' + widgetOption + '"]');
                const $widgetField = $widgetToggle.closest('.revixreviews-toggle-field');
                
                if (isMasterEnabled) {
                    $widgetToggle.prop('disabled', false);
                    $widgetField.removeClass('disabled');
                } else {
                    $widgetToggle.prop('disabled', true);
                    $widgetField.addClass('disabled');
                }
            });
            
            // Handle select all toggle
            const $selectAllToggle = $('.revixreviews-select-all-toggle-input');
            const $selectAllContainer = $('.revixreviews-select-all-toggle');
            
            if (isMasterEnabled) {
                $selectAllToggle.prop('disabled', false);
                $selectAllContainer.removeClass('disabled');
            } else {
                $selectAllToggle.prop('disabled', true);
                $selectAllContainer.addClass('disabled');
            }
        },
        
        handleSelectAllToggle: function(e) {
            const $selectAllToggle = $(this);
            const isChecked = $selectAllToggle.is(':checked');
            
            const widgetToggles = [
                'revixreviews_google_summary',
                'revixreviews_trustpilot_summary',
                'revixreviews_trustpilot_reviews',
                'revixreviews_google_reviews',
                'revixreviews_testimonial_reviews',
                'revixreviews_submit_form'
            ];
            
            let completedRequests = 0;
            const totalToggles = widgetToggles.length;
            
            widgetToggles.forEach(function(widgetOption) {
                const $widgetToggle = $('.revixreviews-ajax-toggle[data-option="' + widgetOption + '"]');
                
                if ($widgetToggle.length && !$widgetToggle.prop('disabled')) {
                    const currentValue = $widgetToggle.is(':checked') ? 1 : 0;
                    const newValue = isChecked ? 1 : 0;
                    
                    // Only update if value is different
                    if (currentValue !== newValue) {
                        $.ajax({
                            url: revixReviewsSettings.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'revixreviews_toggle_setting',
                                nonce: revixReviewsSettings.nonce,
                                option: widgetOption,
                                value: newValue
                            },
                            success: function() {
                                $widgetToggle.prop('checked', isChecked);
                            },
                            complete: function() {
                                completedRequests++;
                                
                                if (completedRequests === totalToggles) {
                                    const message = isChecked ? 'All widgets enabled' : 'All widgets disabled';
                                    RevixSettings.showNotice('success', message);
                                }
                            }
                        });
                    } else {
                        completedRequests++;
                    }
                } else {
                    completedRequests++;
                }
            });
        },
        
        updateSelectAllState: function() {
            const widgetToggles = [
                'revixreviews_google_summary',
                'revixreviews_trustpilot_summary',
                'revixreviews_trustpilot_reviews',
                'revixreviews_google_reviews',
                'revixreviews_testimonial_reviews',
                'revixreviews_submit_form'
            ];
            
            let allChecked = true;
            
            widgetToggles.forEach(function(widgetOption) {
                const $widgetToggle = $('.revixreviews-ajax-toggle[data-option="' + widgetOption + '"]');
                if ($widgetToggle.length && !$widgetToggle.is(':checked')) {
                    allChecked = false;
                }
            });
            
            $('.revixreviews-select-all-toggle-input').prop('checked', allChecked);
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
        
        // Import/Export UI Enhancements
        
        // File upload display
        $('#import_file').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('.revixreviews-file-name .file-name-text').text(fileName);
                $('.revixreviews-file-name').addClass('active');
            }
        });
        
        // Drag and drop functionality
        const $uploadLabel = $('.revixreviews-file-upload-label');
        const fileInput = document.getElementById('import_file');
        
        $uploadLabel.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });
        
        $uploadLabel.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });
        
        $uploadLabel.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                
                // Check if it's a JSON file
                if (file.name.endsWith('.json')) {
                    // Set the file to the input
                    fileInput.files = files;
                    
                    // Display the file name
                    $('.revixreviews-file-name .file-name-text').text(file.name);
                    $('.revixreviews-file-name').addClass('active');
                } else {
                    alert('Please upload a JSON file.');
                }
            }
        });
        
        // Button hover effects
        $('.revixreviews-export-btn').hover(
            function() {
                $(this).css({
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 6px 20px rgba(16, 185, 129, 0.4)'
                });
            },
            function() {
                $(this).css({
                    'transform': 'translateY(0)',
                    'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                });
            }
        );
        
        $('.revixreviews-import-btn').hover(
            function() {
                $(this).css({
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 6px 20px rgba(59, 130, 246, 0.4)'
                });
            },
            function() {
                $(this).css({
                    'transform': 'translateY(0)',
                    'box-shadow': '0 4px 12px rgba(59, 130, 246, 0.3)'
                });
            }
        );

        // Handle AJAX import form submission
        $('.revixreviews-import-form').on('submit', function(e) {
            e.preventDefault();
            
            // Check if Swal is available
            if (typeof Swal === 'undefined') {
                alert('Error: SweetAlert2 library not loaded. Please refresh the page.');
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'revixreviews_import');
            formData.append('nonce', $('#revixreviews_import_nonce').val());
            
            // Show loading alert
            Swal.fire({
                title: 'Importing Reviews...',
                html: 'Please wait while we process your file.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send AJAX request
            $.ajax({
                url: revixReviewsSettings.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        let message = `Successfully imported ${response.data.imported} review(s).`;
                        let icon = 'success';
                        
                        if (response.data.skipped > 0) {
                            icon = 'warning';
                            message += `<br><br><strong>${response.data.skipped} item(s) were skipped:</strong><br>`;
                            message += '<div style="text-align: left; margin-top: 10px; max-height: 200px; overflow-y: auto;">';
                            message += '<ul style="padding-left: 20px; margin: 0;">';
                            response.data.skipped_items.forEach(function(item) {
                                message += `<li style="margin: 5px 0;">${item}</li>`;
                            });
                            message += '</ul></div>';
                        }
                        
                        Swal.fire({
                            icon: icon,
                            title: 'Import Completed!',
                            html: message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3b82f6',
                            width: '600px'
                        }).then(() => {
                            // Reset form and file display
                            $('.revixreviews-import-form')[0].reset();
                            $('.revixreviews-file-name').removeClass('active');
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Import Failed!',
                            html: response.data.message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed!',
                        html: 'An unexpected error occurred. Please try again.<br>Check browser console for details.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        });
