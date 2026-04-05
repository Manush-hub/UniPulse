
/* Extracted from payhere_redirect.view.php */

        // Auto-submit after a brief moment so the user sees the loading screen
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('payhere-checkout').submit();
            }, 600);
        });
    
