jQuery(document).ready(function($) {
    $('.get-projects').on('click', function(){
        $.ajax({
            url: js_data.ajax_url,
            type: 'POST',
            data: {
                action: 'get_project_posts'
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    })
});
