/**
 * Revix Reviews Import/Export JavaScript
 * Handles import/export functionality with AJAX and drag-drop
 */
(function($) {
    'use strict';

    const RevixImportExport = {
        _exportCheckPassed: false, // Prevents double submit loop

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // File upload display
            $('#import_file').on('change', this.handleFileSelect);
            
            // Drag and drop functionality
            this.setupDragDrop();
            
            // Button hover effects
            this.setupButtonEffects();
            
            // Handle export button clicks with validation
            $(document).on('submit', '.revixreviews-export-form, .revixreviews-export-csv-form', this.handleExportSubmit);
            
            // Handle AJAX import form submission
            $(document).on('submit', '.revixreviews-import-form', this.handleImportSubmit);
        },

        handleFileSelect: function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('.revixreviews-file-name .file-name-text').text(fileName);
                $('.revixreviews-file-name').addClass('active');
            }
        },

        setupDragDrop: function() {
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
                    
                    // Check if it's a JSON or CSV file
                    if (file.name.endsWith('.json') || file.name.endsWith('.csv')) {
                        // Set the file to the input
                        fileInput.files = files;
                        
                        // Display the file name
                        $('.revixreviews-file-name .file-name-text').text(file.name);
                        $('.revixreviews-file-name').addClass('active');
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Please upload a JSON or CSV file.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#ef4444'
                            });
                        } else {
                            alert('Please upload a JSON or CSV file.');
                        }
                    }
                }
            });
        },

        setupButtonEffects: function() {
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
        },

        handleExportSubmit: function(e) {
            if (RevixImportExport._exportCheckPassed) {
                RevixImportExport._exportCheckPassed = false; // reset for next time
                return true; // allow native submit
            }
            e.preventDefault();
            
            const $form = $(this);
            const isCSV = $form.hasClass('revixreviews-export-csv-form');
            const exportType = isCSV ? 'CSV' : 'JSON';
            
            // Check if Swal is available
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 not loaded');
                // Continue with normal form submission
                e.target.submit();
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Checking reviews...',
                html: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Check if reviews exist
            $.ajax({
                url: revixReviewsSettings.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'revixreviews_check_reviews',
                    nonce: revixReviewsSettings.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Reviews exist, close modal and submit form natively
                        Swal.close();
                        RevixImportExport._exportCheckPassed = true;
                        $form[0].submit();
                    } else {
                        // No reviews found
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Reviews Found',
                            html: response.data.message || 'There are no reviews to export.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#f59e0b'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: 'Failed to check reviews. Please try again.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        },

        handleImportSubmit: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Import form submitted');
            
            // Check if Swal is available
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 not loaded');
                alert('Error: Required library not loaded. Please refresh the page and try again.');
                return false;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'revixreviews_import');
            formData.append('nonce', $('#revixreviews_import_nonce').val());
            
            console.log('Sending AJAX request...');
            
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
                    console.log('AJAX Success:', response);
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
            
            return false;
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        RevixImportExport.init();
    });

})(jQuery);
