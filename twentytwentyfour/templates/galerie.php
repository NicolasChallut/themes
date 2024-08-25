<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
get_header(); // Inclut l'en-tête de votre thème

// Arguments pour WP_Query pour récupérer les images avec le champ 'display_in_gallery' activé
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => -1, // Récupérer toutes les images
    'meta_query' => array(
        array(
            'key' => 'display_in_gallery', // Nom du champ ACF
            'value' => '1', // Valeur pour afficher l'image (true)
            'compare' => '='
        )
    )
);

$query = new WP_Query($args);

// URL de l'image à afficher au-dessus de la galerie
$header_image_url = 'http://nathalie.local/wp-content/uploads/2024/08/nathalie-11-scaled.jpeg'; // Remplacez par l'URL de l'image que vous souhaitez afficher

// Afficher l'image au-dessus de la galerie
if (!empty($header_image_url)) {
    echo '<div class="header-image">';
    echo '<img src="' . esc_url($header_image_url) . '" alt="Image d\'en-tête">';
    echo '</div>';
}

// Affichage du formulaire de tri
?>
<div class="filter-controls">
    <form id="filter-form">
        <label for="filter-category">Catégorie :</label>
        <select id="filter-category" name="category">
            <option value=""></option>
            <option value="réception">Réception</option>
            <option value="concert">Concert</option>
            <option value="mariage">Mariage</option>
            <option value="télévision">Télévision</option>
        </select>

        <label for="filter-format">Format :</label>
        <select id="filter-format" name="format">
            <option value=""></option>
            <option value="paysage">Paysage</option>
            <option value="portrait">Portrait</option>
        </select>

        <label for="filter-year">Année :</label>
        <select id="filter-year" name="year">
            <option value=""></option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
            <option value="2020">2020</option>
            <option value="2019">2019</option>
        </select>
    </form>
</div>

<?php
// Affichage de la galerie
?>
<div id="photo-gallery" class="photo-gallery two-columns">
    <?php if ($query->have_posts()) : ?>
        <?php while ($query->have_posts()) : $query->the_post(); ?>
            <?php
            $image_id = get_the_ID();
            $image_url = wp_get_attachment_url($image_id);
            $image_title = get_the_title($image_id);
            $image_category = strtolower(get_post_meta($image_id, 'categorie', true)); // Assurez-vous que les valeurs sont en minuscules
            $image_year = get_post_meta($image_id, 'annee', true);
            $image_format = strtolower(get_post_meta($image_id, 'format', true)); // Assurez-vous que les valeurs sont en minuscules
            $image_type = strtolower(get_post_meta($image_id, 'type', true)); // Assurez-vous que les valeurs sont en minuscules
            ?>
            <div class="gallery-item" data-category="<?php echo esc_attr($image_category); ?>" data-format="<?php echo esc_attr($image_format); ?>" data-year="<?php echo esc_attr($image_year); ?>">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>" width="100%">
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <p>Aucune image trouvée dans la galerie.</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); // Réinitialiser les données du post global ?>
</div>

<?php
get_footer(); // Inclut le pied de page de votre thème
?>
