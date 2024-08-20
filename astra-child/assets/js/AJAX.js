jQuery(document).ready(function($) {
    $('#filter-form').on('change', function() {
        // Récupérer les valeurs des filtres
        var category = $('#category-filter').val();
        var format = $('#format-filter').val();
        var date = $('#date-filter').val();

        // Construire la requête AJAX
        $.ajax({
            url: wp_vars.ajax_url, // L'URL pour les requêtes AJAX
            method: 'GET',
            data: {
                action: 'filter_images',
                category: category,
                format: format,
                date: date
            },
            success: function(response) {
                $('#photo-gallery').html(response); // Mettre à jour la galerie
            }
        });
    });

    // Charger les images initiales
    $('#filter-form').trigger('change');
});
