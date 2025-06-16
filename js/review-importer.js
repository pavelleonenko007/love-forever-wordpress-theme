jQuery(document).ready(function($) {
    $('#review-import-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData();
        var fileInput = $('#review-csv')[0];
        
        if (!fileInput.files.length) {
            alert('Пожалуйста, выберите файл.');
            return;
        }
        
        formData.append('action', 'import_reviews');
        formData.append('nonce', loveforeverReviewImporter.nonce);
        formData.append('file', fileInput.files[0]);
        
        // Show progress bar
        $('#import-progress').show();
        $('.progress-bar-fill').css('width', '0%');
        $('.progress-status').text(loveforeverReviewImporter.importing);
        
        // Disable form
        $('button[type="submit"]').prop('disabled', true);
        
        // Start import process
        importReviews(formData);
    });
    
    function importReviews(formData, currentRow, imported, skipped, total) {
        // Add progress data to formData if available
        if (typeof currentRow !== 'undefined') {
            formData.append('current_row', currentRow);
            formData.append('imported', imported);
            formData.append('skipped', skipped);
            formData.append('total_rows', total);
        }
        
        $.ajax({
            url: loveforeverReviewImporter.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    if (response.data.complete) {
                        // Import completed
                        $('.progress-bar-fill').css('width', '100%');
                        $('.progress-status').html(
                            loveforeverReviewImporter.complete + '<br>' +
                            'Импортировано: ' + response.data.imported + '<br>' +
                            'Пропущено: ' + response.data.skipped + '<br>' +
                            'Всего: ' + response.data.total
                        );
                        $('button[type="submit"]').prop('disabled', false);
                    } else {
                        // Update progress
                        $('.progress-bar-fill').css('width', response.data.progress + '%');
                        $('.progress-status').text(response.data.message);
                        
                        // Continue import with current progress
                        importReviews(
                            formData,
                            response.data.current_row,
                            response.data.imported,
                            response.data.skipped,
                            response.data.total
                        );
                    }
                } else {
                    $('.progress-status').text(response.data || loveforeverReviewImporter.error);
                    $('button[type="submit"]').prop('disabled', false);
                }
            },
            error: function() {
                $('.progress-status').text(loveforeverReviewImporter.error);
                $('button[type="submit"]').prop('disabled', false);
            }
        });
    }
}); 