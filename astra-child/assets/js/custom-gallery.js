jQuery(document).ready(function($) {
    $('#filter-form').on('change', function() {
        var selectedCategory = $('#filter-category').val().toLowerCase();
        var selectedFormat = $('#filter-format').val().toLowerCase();
        var selectedOrder = $('#filter-order').val(); // Récupère l'ordre choisi (asc/desc)

        // Récupérer les éléments de la galerie
        var galleryItems = $('.gallery-item');

        // Filtrer les éléments de la galerie
        galleryItems.each(function() {
            var itemCategory = $(this).data('categorie_acf'); // Correction: utiliser 'category' au lieu de 'categorie_acf'
            var itemFormat = $(this).data('format');     // Utilisation correcte de 'format'
            var itemYear = $(this).data('year');         // Utilisation correcte de 'year'

            if ((selectedCategory === '' || itemCategory === selectedCategory) &&
                (selectedFormat === '' || itemFormat === selectedFormat)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Trier les éléments de la galerie selon l'année
        if (selectedOrder) {
            galleryItems.sort(function(a, b) {
                var yearA = parseInt($(a).data('year'));
                var yearB = parseInt($(b).data('year'));

                if (selectedOrder === 'asc') {
                    return yearA - yearB;
                } else if (selectedOrder === 'desc') {
                    return yearB - yearA;
                }
            });

            // Réorganiser les éléments dans le conteneur
            $('#photo-gallery').html(galleryItems);
        }
    });
});
