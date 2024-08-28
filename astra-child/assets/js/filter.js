jQuery(document).ready(function($) {
    $('#filter-form select').on('change', function() {
        var category = $('#filter-category').val();
        var format = $('#filter-format').val();


        console.log('Sending AJAX request with data:', {
            action: 'filter_gallery_images',
            category: category,
            format: format,
            order: order,
            nonce: load_more_params.nonce
        });

        $.ajax({
            url: load_more_params.ajax_url,  // URL AJAX localisée
            type: 'POST',
            data: {
                action: 'filter_gallery_images',  // Action définie pour le traitement
                category: category,
                format: format,
                nonce: load_more_params.nonce  // Nonce pour la sécurité
            },
            beforeSend: function() {
                $('#photo-gallery').html('<p>Chargement...</p>');
            },
            success: function(response) {
                if (response === 'no_more_images') {
                    $('#photo-gallery').html('<p>Aucune image trouvée.</p>');
                } else {
                    $('#photo-gallery').html(response);
                }
            },
            error: function(xhr, status, error) {
                console.log('Erreur AJAX: ', error);
                $('#photo-gallery').html('<p>Erreur lors du chargement des images.</p>');
            }
        });
    });
});
