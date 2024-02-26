define(['jquery'], function($) {
    return {
        init: function() {
            $(document).ready(function() {
                var container = $('#urlsub_container');
                var addButton = $('#add_url_button');

                // Function to add a new URL-title pair
                function addUrlTitlePair() {
                    var index = $('.url-title-pair').length;

                    // Create the URL input
                    var urlInput = $('<input>').attr({
                        type: 'text',
                        name: 'urls[' + index + '][url]',
                        placeholder: 'URL',
                        class: 'url-input'
                    });

                    // Create the title input
                    var titleInput = $('<input>').attr({
                        type: 'text',
                        name: 'urls[' + index + '][title]',
                        placeholder: 'Title',
                        class: 'title-input'
                    });

                    // Create a removal button for the pair
                    var removeButton = $('<button>').attr({
                        type: 'button',
                    }).text('Remove').on('click', function() {
                        $(this).parent().remove();
                    });

                    // Container for the pair
                    var pairContainer = $('<div>').addClass('url-title-pair');
                    pairContainer.append(urlInput, titleInput, removeButton);

                    container.append(pairContainer);
                }

                // Initial pair
                addUrlTitlePair();

                // Event listener for the add button
                addButton.on('click', function(e) {
                    e.preventDefault();
                    addUrlTitlePair();
                });
            });
        }
    };
});
