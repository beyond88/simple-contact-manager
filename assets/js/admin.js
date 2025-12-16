/**
 * Simple Contact Manager - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Delete submission
        $('.scm-delete').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $row = $button.closest('tr');
            var id = $button.data('id');
            
            if (!confirm(scmAdmin.confirmDelete)) {
                return;
            }
            
            $button.prop('disabled', true).text('Deleting...');
            
            $.ajax({
                url: scmAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'scm_delete_submission',
                    id: id,
                    nonce: scmAdmin.deleteNonce
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is empty
                            if ($('.scm-submissions-table tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(response.data.message || 'Error deleting submission.');
                        $button.prop('disabled', false).text('Delete');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Delete');
                }
            });
        });
        
        // Mark as read
        $('.scm-mark-read').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $row = $button.closest('tr');
            var id = $button.data('id');
            
            $button.prop('disabled', true).text('Updating...');
            
            $.ajax({
                url: scmAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'scm_mark_as_read',
                    id: id,
                    nonce: scmAdmin.deleteNonce
                },
                success: function(response) {
                    if (response.success) {
                        $row.removeClass('scm-unread');
                        $row.find('.scm-status')
                            .removeClass('scm-status-unread')
                            .addClass('scm-status-read')
                            .text('Read');
                        $button.remove();
                    } else {
                        alert(response.data.message || 'Error updating submission.');
                        $button.prop('disabled', false).text('Mark Read');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Mark Read');
                }
            });
        });
        
        // View full message
        $('.scm-view-message').on('click', function(e) {
            e.preventDefault();
            
            var message = $(this).data('message');
            $('#scm-full-message').text(message);
            $('#scm-message-modal').fadeIn(200);
        });
        
        // Close modal
        $('.scm-modal-close, .scm-modal').on('click', function(e) {
            if (e.target === this) {
                $('#scm-message-modal').fadeOut(200);
            }
        });
        
        // Close modal with Escape key
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') {
                $('#scm-message-modal').fadeOut(200);
            }
        });
        
        // Copy shortcode
        $('.scm-copy-shortcode').on('click', function(e) {
            e.preventDefault();
            
            var shortcode = $(this).data('shortcode');
            var $button = $(this);
            
            // Create temporary input
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            
            // Show feedback
            var originalText = $button.text();
            $button.text('Copied!');
            
            setTimeout(function() {
                $button.text(originalText);
            }, 2000);
        });
        
    });
    
})(jQuery);
