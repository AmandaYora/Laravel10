<!-- resources/views/snackbar.blade.php -->
<div id="snackbar" style="display:none !important; position:fixed; z-index:9999; top:30px; right:30px; background-color:#333; color:#fff; padding:16px 24px; border-radius:8px; min-width:300px; max-width:400px; display:flex; align-items:center; box-shadow:0 4px 6px rgba(0,0,0,0.1); font-family:'Roboto', sans-serif;">
    <div id="snackbar-icon" style="margin-right:12px;"></div>
    <span id="snackbar-text" style="flex:1;"></span>
</div>

<script>
    function snackbar(type, message, timeout = 3000) {
        const $snackbar = $('#snackbar');
        const $snackbarText = $('#snackbar-text');
        const $snackbarIcon = $('#snackbar-icon');

        // Set message and style based on type
        $snackbarText.text(message);
        switch(type) {
            case 'success':
                $snackbar.css('background-color', '#4CAF50');
                $snackbarIcon.html('<i class="fas fa-check-circle" style="color:white;"></i>');
                break;
            case 'error':
                $snackbar.css('background-color', '#f44336');
                $snackbarIcon.html('<i class="fas fa-exclamation-circle" style="color:white;"></i>');
                break;
            case 'warning':
                $snackbar.css('background-color', '#ff9800');
                $snackbarIcon.html('<i class="fas fa-exclamation-triangle" style="color:white;"></i>');
                break;
            case 'info':
                $snackbar.css('background-color', '#2196F3');
                $snackbarIcon.html('<i class="fas fa-info-circle" style="color:white;"></i>');
                break;
            default:
                $snackbar.css('background-color', '#333');
                $snackbarIcon.html('');
        }

        // Animate snackbar
        $snackbar.stop(true, true).fadeIn(300).css({top: '30px', right: '30px'});
        
        // Automatically hide the snackbar after the specified timeout
        setTimeout(function() {
            $snackbar.fadeOut(300, function() {
                $snackbar.css({top: '30px', right: '30px'}); // Reset position
            });
        }, timeout);
    }
</script>
