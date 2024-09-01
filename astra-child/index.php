<?php
/*
Template Name: Home
*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly

get_header(); // Inclut l'en-tête de votre thème

// Récupération des images avec le champ 'display_in_gallery' activé
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => 8,
    'meta_query' => array(
        array(
            'key' => 'display_in_gallery',
            'value' => '1',
            'compare' => '='
        )
    )
);

$query = new WP_Query($args);

// Récupération de l'image aléatoire pour l'en-tête
$header_image_url = '';
$overlay_image_url = '';

if ($query->have_posts()) {
    $images = $query->posts;
    $random_image = $images[array_rand($images)];
    $header_image_url = wp_get_attachment_url($random_image->ID);
    $overlay_image_url = wp_get_attachment_url(6039);
}
?>

<?php if ($header_image_url) : ?>
    <div class="header-image">
        <img src="<?php echo esc_url($header_image_url); ?>" alt="Image d'en-tête">
        <?php if ($overlay_image_url) : ?>
            <img class="background_logo" src="<?php echo esc_url($overlay_image_url); ?>" alt="Image superposée">
        <?php endif; ?>
    </div>
<?php else : ?>
    <p>Aucune image trouvée pour l'en-tête.</p>
<?php endif; ?>

<!-- Formulaire de tri -->


<?php
// Fonction pour traiter le filtrage AJAX
function filter_gallery_images()
{
    check_ajax_referer('load_more_images_nonce', 'nonce');

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $format = isset($_POST['format']) ? sanitize_text_field($_POST['format']) : '';

    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => 8,
        'meta_query' => array(
            array(
                'key' => 'display_in_gallery',
                'value' => '1',
                'compare' => '='
            )
        ),
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category', // Remplacez par la taxonomie appropriée
                'field' => 'slug',
                'terms' => $category,
                'operator' => !empty($category) ? 'IN' : 'EXISTS'
            ),
            array(
                'taxonomy' => 'format', // Remplacez par la taxonomie appropriée
                'field' => 'slug',
                'terms' => $format,
                'operator' => !empty($format) ? 'IN' : 'EXISTS'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // Générer le HTML pour chaque image
        }
    } else {
        wp_send_json_error('no_more_images');
    }

    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_filter_gallery_images', 'filter_gallery_images');
add_action('wp_ajax_nopriv_filter_gallery_images', 'filter_gallery_images');

?>

<div class="main-content">
    <div class="filter-controls">
        <form id="filter-form">
            <div class="gallery">

                <div class="filters">
                    <div class="filter-left">
                        <select id="filter-category" name="category">
                            <option value="">Catégorie</option>
                            <?php generate_filter_options('category'); ?>
                        </select>

                        <select id="filter-format" name="format">
                            <option value="">Format</option>
                            <?php generate_filter_options('format'); ?>
                        </select>
                    </div>

                    <div class="filter-right">
                        <select id="filter-order" name="order">
                            <option value="">Trier par</option>
                            <option value="asc">Croissant</option>
                            <option value="desc">Décroissant</option>
                        </select>
                    </div>
                </div>
        </form>
        <div id="photo-gallery" class="photo-gallery">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $image_id = get_the_ID();
                    $image_url = wp_get_attachment_url($image_id);
                    $image_title = get_the_title($image_id);
                    $image_category = get_field('category', $image_id);
                    $image_year = get_field('annee', $image_id);
                    $image_format = get_field('format', $image_id);
                    $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;
                    ?>

<div class="gallery-item" data-category="<?php echo esc_attr($image_category); ?>" data-format="<?php echo esc_attr($image_format); ?>" data-year="<?php echo esc_attr($image_year); ?>">
    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>" width="100%">
    <div class="overlay">
        <!-- Icone plein écran en haut à gauche -->
        <a href="<?php echo esc_url($image_url); ?>" class="icon fullscreen-icon" data-fancybox="gallery" title="Voir en plein écran" data-caption="<span class='fancybox-title'><?php echo esc_html($image_title); ?></span> <span class='fancybox-category'><?php echo esc_html($image_category); ?></span>">
    <i class="fa fa-expand"></i>
</a>

        <!-- Icone d'information au centre -->
        <a href="<?php echo esc_url($single_photo_page_url); ?>" class="icon eye-icon" title="Voir les informations">
            <i class="fa fa-eye"></i>
        </a>
        <!-- Référence en bas à gauche -->
        <div class="photo-title"><?php echo esc_html($image_title); ?></div>
        <!-- Catégorie en bas à droite -->
        <div class="photo-category"><?php echo esc_html($image_category); ?></div>
    </div>
</div>

                <?php endwhile; ?>
            <?php else : ?>
                <p>Aucune image trouvée dans la galerie.</p>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>

        <!-- Bouton Charger plus -->

        <div class="load-more-wrapper">
            <button id="load-more" class="btn_contact2" data-page="1">Charger plus</button>
        </div>
    </div>
</div>
<?php get_footer(); ?>