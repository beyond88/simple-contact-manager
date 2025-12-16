/**
 * Simple Contact Manager - Frontend JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Form validation enhancement
        $('#scm-contact-form').on('submit', function(e) {
            var $form = $(this);
            var $submitBtn = $form.find('.scm-submit-btn');
            var originalText = $submitBtn.text();
            
            // Simple client-side validation
            var isValid = true;
            
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    isValid = false;
                    $field.addClass('scm-error');
                } else {
                    $field.removeClass('scm-error');
                }
            });
            
            // Email validation
            var $email = $form.find('#scm_email');
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if ($email.val() && !emailRegex.test($email.val())) {
                isValid = false;
                $email.addClass('scm-error');
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            $submitBtn.prop('disabled', true).text('Sending...');
        });
        
        // Remove error class on focus
        $('.scm-contact-form input, .scm-contact-form textarea').on('focus', function() {
            $(this).removeClass('scm-error');
        });
        
    });
    
})(jQuery);
