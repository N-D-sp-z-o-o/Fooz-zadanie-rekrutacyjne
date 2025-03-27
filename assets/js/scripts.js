jQuery(document).ready(function($) {
    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: { action: 'fetch_books' },
        success: function(response) {
            console.log(response);
        }
    });
});