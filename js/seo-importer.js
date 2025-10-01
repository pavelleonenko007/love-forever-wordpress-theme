jQuery(document).ready(function($) {
    'use strict';

    const SeoImporter = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $('#seo-import-form').on('submit', this.handleFormSubmit.bind(this));
            $('#seo-csv').on('change', this.handleFileSelect.bind(this));
        },

        handleFileSelect: function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const maxSize = 1;
                
                // Remove existing file info
                $('.file-info').remove();
                
                // Add file info
                const fileInfo = $(`
                    <div class="file-info" style="margin-top: 10px; padding: 10px; background: #f9f9f9; border-radius: 4px;">
                        <strong>Выбранный файл:</strong> ${file.name}<br>
                        <strong>Размер:</strong> ${fileSize} МБ ${fileSize > maxSize ? '<span style="color: red;">(превышает лимит!)</span>' : ''}
                    </div>
                `);
                
                $('#seo-csv').after(fileInfo);
            }
        },

        handleFormSubmit: function(e) {
            e.preventDefault();
            
            const fileInput = $('#seo-csv')[0];
            if (!fileInput.files.length) {
                alert('Пожалуйста, выберите CSV файл');
                return;
            }

            const file = fileInput.files[0];
            const maxSize = 1024 * 1024; // 1MB

            // Check file size
            if (file.size > maxSize) {
                alert('Размер файла превышает 1 МБ. Пожалуйста, выберите файл меньшего размера.');
                return;
            }

            // Check file type
            if (!file.name.toLowerCase().endsWith('.csv')) {
                alert('Пожалуйста, выберите CSV файл');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'import_seo');
            formData.append('nonce', loveforeverSeoImporter.nonce);
            formData.append('file', file);

            this.startImport(formData);
        },

        startImport: function(formData) {
            // Clear previous results
            $('.import-complete, .import-error').remove();
            
            // Show progress container
            $('#import-progress').show();
            $('.progress-bar-fill').css('width', '0%');
            $('.progress-status').text(loveforeverSeoImporter.importing);

            // Disable form
            $('#seo-import-form button').prop('disabled', true).addClass('loading');

            this.processImport(formData);
        },

        processImport: function(formData) {
            $.ajax({
                url: loveforeverSeoImporter.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleImportSuccess.bind(this),
                error: this.handleImportError.bind(this)
            });
        },

        handleImportSuccess: function(response) {
            if (response.success) {
                const data = response.data;
                
                if (data.complete) {
                    this.completeImport(data);
                } else {
                    this.updateProgress(data);
                    // Continue with next row
                    const formData = new FormData();
                    formData.append('action', 'import_seo');
                    formData.append('nonce', loveforeverSeoImporter.nonce);
                    formData.append('current_row', data.current_row);
                    formData.append('updated', data.updated);
                    formData.append('skipped', data.skipped);
                    formData.append('total_rows', data.total_rows);
                    
                    // Small delay to prevent overwhelming the server
                    setTimeout(() => {
                        this.processImport(formData);
                    }, 100);
                }
            } else {
                this.handleImportError(response);
            }
        },

        handleImportError: function(response) {
            console.error('Import error:', response);
            
            let errorMessage = loveforeverSeoImporter.error;
            if (response.data && response.data.message) {
                errorMessage = response.data.message;
            }

            this.showError(errorMessage);
            this.resetForm();
        },

        updateProgress: function(data) {
            $('.progress-bar-fill').css('width', data.progress + '%');
            $('.progress-status').text(data.message);
        },

        completeImport: function(data) {
            $('.progress-bar-fill').css('width', '100%');
            $('.progress-status').text(loveforeverSeoImporter.complete);
            
            // Show completion message with stats
            this.showCompletionMessage(data);
            this.resetForm();
        },

        showCompletionMessage: function(data) {
            const messageHtml = `
                <div class="import-complete">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Импорт завершен!</h3>
                        <button type="button" class="button button-secondary clear-results" style="font-size: 12px; padding: 5px 10px;">
                            Очистить результаты
                        </button>
                    </div>
                    <div class="import-stats">
                        <div class="stat-item stat-total">
                            <span class="stat-number">${data.total}</span>
                            <span class="stat-label">Всего</span>
                        </div>
                        <div class="stat-item stat-updated">
                            <span class="stat-number">${data.updated}</span>
                            <span class="stat-label">Обновлено</span>
                        </div>
                        <div class="stat-item stat-skipped">
                            <span class="stat-number">${data.skipped}</span>
                            <span class="stat-label">Пропущено</span>
                        </div>
                    </div>
                </div>
            `;
            
            $('#import-progress').after(messageHtml);
            
            // Bind clear results button
            $('.clear-results').on('click', () => {
                $('.import-complete, .import-error').remove();
            });
        },

        showError: function(message) {
            const errorHtml = `
                <div class="import-error">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Ошибка импорта</h3>
                        <button type="button" class="button button-secondary clear-results" style="font-size: 12px; padding: 5px 10px;">
                            Очистить
                        </button>
                    </div>
                    <p>${message}</p>
                </div>
            `;
            
            $('#import-progress').after(errorHtml);
            
            // Bind clear results button
            $('.clear-results').on('click', () => {
                $('.import-complete, .import-error').remove();
            });
        },

        resetForm: function() {
            // Re-enable form
            $('#seo-import-form button').prop('disabled', false).removeClass('loading');
            
            // Clear file input and file info
            $('#seo-csv').val('');
            $('.file-info').remove();
            
            // Hide progress but keep results visible
            setTimeout(() => {
                $('#import-progress').hide();
                // Don't remove completion/error messages - keep them visible
            }, 1000);
        }
    };

    // Initialize the importer
    SeoImporter.init();
});

