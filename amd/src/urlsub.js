document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('urlsub_container');
    var addButton = document.getElementById('add_url_button');

    // Function to add a new URL-title pair
    function addUrlTitlePair() {
        var index = container.querySelectorAll('.url-title-pair').length;

        // Create the URL input
        var urlInput = document.createElement('input');
        urlInput.type = 'text';
        urlInput.name = 'urls[' + index + '][url]';
        urlInput.placeholder = 'URL';

        // Create the title input
        var titleInput = document.createElement('input');
        titleInput.type = 'text';
        titleInput.name = 'urls[' + index + '][title]';
        titleInput.placeholder = 'Title';

        // Create a removal button for the pair
        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = 'Remove';
        removeButton.onclick = function() {
            this.parentNode.remove();
        };

        // Container for the pair
        var pairContainer = document.createElement('div');
        pairContainer.className = 'url-title-pair';
        pairContainer.appendChild(urlInput);
        pairContainer.appendChild(titleInput);
        pairContainer.appendChild(removeButton);

        container.appendChild(pairContainer);
    }

    // Initial pair
    addUrlTitlePair();

    // Event listener for the add button
    addButton.addEventListener('click', function (e) {
        e.preventDefault();
        addUrlTitlePair();
    });
});
