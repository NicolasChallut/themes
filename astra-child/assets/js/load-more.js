jQuery(document).ready(function($) {
    $('#load-more').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var page = button.data('page');

        // Envoyer la requête AJAX
        $.ajax({
            url: load_more_params.ajax_url,
            type: 'POST',
            data: {
                action: 'chld_thm_cfg_load_more_images',  // Nom de l'action corrigé
                page: page,
                nonce: load_more_params.nonce
            },
            beforeSend: function() {
                button.text('Chargement...');
            },
            success: function(response) {
                if (response === 'no_more_images') {
                    button.text('Plus d\'images à charger');
                    button.prop('disabled', true);
                } else {
                    $('#photo-gallery').append(response);
                    button.data('page', page + 1);
                    button.text('Charger plus');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error: ', error);
                button.text('Erreur lors du chargement');
            }
        });
    });
});
