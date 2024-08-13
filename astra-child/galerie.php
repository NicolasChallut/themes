<?php
/*
Template Name: Galerie Interactif en Deux Colonnes
*/
get_header(); // Inclut l'en-tête de votre thème

// URL de l'image à afficher au-dessus de la galerie
$header_image_url = 'http://nathalie.local/wp-content/uploads/2024/08/nathalie-11-scaled.jpeg'; // Remplacez par l'URL de l'image que vous souhaitez afficher

// Afficher l'image au-dessus de la galerie
if (!empty($header_image_url)) {
    echo '<div class="header-image">';
    echo '<img src="' . esc_url($header_image_url) . '" alt="Image d\'en-tête">';
    echo '</div>';
}

// Configuration de la requête pour récupérer les images sélectionnées de la médiathèque
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => -1, // Récupérer toutes les images
    'meta_query' => array(
        array(
            'key' => 'display_in_gallery', // Nom du champ personnalisé
            'value' => '1', // Afficher seulement les images sélectionnées
            'compare' => '='
        )
    )
);

$query_images = new WP_Query($args);

if ($query_images->have_posts()) :
?>

    <div class="photo-gallery two-columns">
        <?php
        $counter = 0; // Compteur pour gérer les colonnes

        while ($query_images->have_posts()) : $query_images->the_post();
            // Récupérer les métadonnées des images
            $image_id = get_the_ID();
            $image_url = wp_get_attachment_url($image_id);
            $image_title = get_the_title();
            
            // Les champs personnalisés pour chaque image
            $image_reference = get_post_meta($image_id, 'image_reference', true);
            $image_category = get_post_meta($image_id, 'image_category', true);
            $image_year = get_post_meta($image_id, 'image_year', true);
            $image_format = get_post_meta($image_id, 'image_format', true);
            $image_type = get_post_meta($image_id, 'image_type', true);

            // Ouvrir une nouvelle ligne si nécessaire
            if ($counter % 2 == 0) {
                echo '<div class="row">';
            }
        ?>
            <div class="photo-card">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>">
            </div>

        <?php
            $counter++;

            // Fermer la ligne après deux colonnes
            if ($counter % 2 == 0) {
                echo '</div>'; // Fermeture de la ligne
            }

        endwhile;

        // Si le dernier item ne ferme pas la ligne, fermez-la ici
        if ($counter % 2 != 0) {
            echo '</div>'; // Fermeture de la ligne incomplète
        }
        ?>
    </div>
<?php
endif;
wp_reset_postdata();

get_footer(); // Inclut le pied de page de votre thème
?>
