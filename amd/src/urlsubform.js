define(['jquery'], function($) {
    return {
        init: function() {
            console.log("URL submission script loaded.");

            $(document).ready(function() {
                var container = $('#urlsub_container'); // Define container here

                // Target the add button by its ID
                $('#add_url_button').on('click', function(e) {
                    e.preventDefault();
                    addUrlTitlePair();
                });

                // Function to add a new URL-title pair
                function addUrlTitlePair() {
                    var index = $('.url-title-pair').length;

                    // Create the URL and title inputs, and removal button
                    var urlInput = $('<input>').attr({
                        type: 'text',
                        name: 'urls[' + index + '][url]',
                        placeholder: 'URL',
                        class: 'url-input'
                    });

                    var titleInput = $('<input>').attr({
                        type: 'text',
                        name: 'urls[' + index + '][title]',
                        placeholder: 'Title',
                        class: 'title-input'
                    });

                    var removeButton = $('<button>').attr({
                        type: 'button',
                    }).text('Remove').on('click', function() {
                        $(this).parent().remove();
                    });

                    // Container for the pair
                    var pairContainer = $('<div>').addClass('url-title-pair');
                    pairContainer.append(urlInput, titleInput, removeButton);

                    container.append(pairContainer); // This now correctly refers to the defined container variable
                }

                // Initial pair
                addUrlTitlePair();
            });
        }
    };
});
