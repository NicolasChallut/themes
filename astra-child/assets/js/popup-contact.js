jQuery(document).ready(function($) {
    // Afficher le popup
    $('.popup-overlay').fadeIn();

    // Fermer le popup en cliquant sur l'arrière-plan (popup-overlay)
    $('.popup-overlay').click(function(e) {
        // Vérifier que le clic n'est pas à l'intérieur du popup-salon
        if ($(e.target).closest('.popup-salon').length === 0) {
            $(this).fadeOut();
        }
    });
});

