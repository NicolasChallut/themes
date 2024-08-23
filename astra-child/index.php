<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly. ?>

<?php get_header(); // Inclut l'en-tête de votre thème ?>

<?php
// Arguments pour WP_Query pour récupérer les images avec le champ 'display_in_gallery' activé
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => 8, // Récupérer 8 images par défaut
    'meta_query' => array(
        array(
            'key' => 'display_in_gallery', // Nom du champ ACF
            'value' => '1', // Valeur pour afficher l'image (true)
            'compare' => '='
        )
    )
);

$query = new WP_Query($args);

// Vérifier si des images ont été trouvées
if ($query->have_posts()) {
    // Récupérer toutes les images dans un tableau
    $images = $query->posts;

    // Sélectionner aléatoirement une image
    $random_image = $images[array_rand($images)];
    $header_image_url = wp_get_attachment_url($random_image->ID);

    // Récupérer l'image avec l'ID 6039
    $overlay_image_url = wp_get_attachment_url(6039);
}
?>

<?php if (!empty($header_image_url)) : ?>
    <div class="header-image">
        <!-- Image d'en-tête -->
        <img src="<?php echo esc_url($header_image_url); ?>" alt="Image d'en-tête">
        
        <?php if (!empty($overlay_image_url)) : ?>
            <!-- Image superposée -->
            <img class="background_logo" src="<?php echo esc_url($overlay_image_url); ?>" alt="Image superposée">
        <?php endif; ?>
    </div>
<?php else : ?>
    <p>Aucune image trouvée pour l'en-tête.</p>
<?php endif; ?>

<!-- Affichage du formulaire de tri -->
<div class="main-content">
    <div class="filter-controls">
        <form id="filter-form">
            <div class="filter-left">
            <select id="filter-category" name="category">
                <option value="">Catégorie</option>
                <?php
                // Récupérer dynamiquement les catégories ACF
                $categories = array();
                foreach ($query->posts as $post) {
                    $categorie = get_field('categorie', $post->ID);
                    if (!empty($categorie)) {
                        $categories[] = $categorie;
                    }
                }
                $categories = array_unique($categories); // Supprimer les doublons
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr(strtolower($category)) . '">' . esc_html($category) . '</option>';
                }
                ?>
            </select>

            <select id="filter-format" name="format">
                <option value="">Format</option>
                <?php
                // Récupérer dynamiquement les formats ACF
                $formats = array();
                foreach ($query->posts as $post) {
                    $format = get_field('format', $post->ID);
                    if (!empty($format)) {
                        $formats[] = $format;
                    }
                }
                $formats = array_unique($formats); // Supprimer les doublons
                foreach ($formats as $format) {
                    echo '<option value="' . esc_attr(strtolower($format)) . '">' . esc_html($format) . '</option>';
                }
                ?>
            </select>
            </div>
            
            <select id="filter-order" name="order">
                <option value="">Trier par</option>
                <option value="asc">Croissant</option>
                <option value="desc">Décroissant</option>
            </select>
        </form>
    </div>

    <?php // Affichage de la galerie ?>
<div class="gallery">
    <div id="photo-gallery" class="photo-gallery two-columns">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php
                $image_id = get_the_ID();
                $image_url = wp_get_attachment_url($image_id);
                $image_title = get_the_title($image_id);
                $image_category = strtolower(get_field('categorie', $image_id)); // Utiliser les champs ACF pour la catégorie
                $image_year = get_field('annee', $image_id); // Assurez-vous que les valeurs sont en minuscules
                $image_format = strtolower(get_field('format', $image_id)); // Utiliser les champs ACF pour le format

                // URL de la page "Single Photo" avec l'ID de l'image en paramètre
                $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;
                ?>
    
                <div class="gallery-item" data-category="<?php echo esc_attr($image_category); ?>" data-format="<?php echo esc_attr($image_format); ?>" data-year="<?php echo esc_attr($image_year); ?>">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>" width="100%">
                    
                    <!-- Overlay avec les icônes qui apparaissent au survol -->
                    <div class="overlay">
                        <!-- Icône d'œil pour accéder aux informations de la photo -->
                        <a href="<?php echo esc_url($single_photo_page_url); ?>" class="icon eye-icon" title="Voir les informations">
                            <i class="fa fa-eye"></i>
                        </a>
                        <!-- Icône de plein écran pour afficher dans une lightbox -->
                        <a href="<?php echo esc_url($image_url); ?>" class="icon fullscreen-icon" title="Voir en plein écran" data-lightbox="gallery">
                            <i class="fa fa-expand"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Aucune image trouvée dans la galerie.</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); // Réinitialiser les données du post global ?>
    </div>

    <!-- Bouton Charger plus -->
    <div class="load-more-wrapper">
        <button id="load-more" class="btn_contact2" data-page="1">Charger plus</button>
    </div>
        </div>
</div>

<?php get_footer(); // Inclut le pied de page de votre thème ?>
