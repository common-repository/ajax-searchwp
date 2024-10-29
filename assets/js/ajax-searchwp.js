jQuery(document).ready(function($) {
    $('#s').on('keyup', function() {
        var query = $(this).val().trim();
        if (query.length > 2) {
            searchwp_ajax_search(query);
        }
    });

    $('#searchform').on('submit', function(e) {
        if ($('#s').val().trim() === '') {
            e.preventDefault();
        }
    });

    $('#searchsubmit').on('click', function(e) {
        if ($('#s').val().trim() === '') {
            e.preventDefault();
        } else {
            $('#searchform').submit();
        }
    });

    function searchwp_ajax_search(query) {
        $.ajax({
            url: ajax_searchwp_object.ajax_url,
            type: 'POST',
            data: {
                action: 'ajax_searchwp_handle_search',
                query: query,
                nonce: ajax_searchwp_object.ajax_nonce // Include the nonce here
            },
            success: function(response) {
                var resultsContainer = $('#ajax_searchwp_results');
                resultsContainer.empty(); // Clear previous results

                if (response.success && response.data.length > 0) {
                    // If results are found, loop through and display them
                    response.data.forEach(function(result) {
                        resultsContainer.append('<div><a href="' + result.url + '">' + result.title + '</a></div>');
                    });
                } else {
                    // If no results, display the "No results found" message
                    resultsContainer.append('<div>' + ajax_searchwp_object.no_results_text + '</div>');
                }
            }
        });
    }
});
