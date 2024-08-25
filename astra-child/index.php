<?php
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
<div class="main-content">
    <div class="filter-controls">
        <form id="filter-form">
            <div class="filter-left">
                <?php
                // Fonction pour générer des options de filtre
                function generate_filter_options($meta_key) {
                    $posts = get_posts(array(
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'post_status' => 'inherit',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            array(
                                'key' => 'display_in_gallery',
                                'value' => '1',
                                'compare' => '='
                            )
                        ),
                        'fields' => 'ids'
                    ));

                    $values = array();
                    foreach ($posts as $post_id) {
                        $value = get_field($meta_key, $post_id);
                        if ($value && !in_array($value, $values)) {
                            $values[] = $value;
                        }
                    }

                    foreach ($values as $value) {
                        echo '<option value="' . esc_attr(strtolower($value)) . '">' . esc_html($value) . '</option>';
                    }
                }
                ?>

                <select id="filter-category" name="category">
                    <option value="">Catégorie</option>
                    <?php generate_filter_options('categorie'); ?>
                </select>

                <select id="filter-format" name="format">
                    <option value="">Format</option>
                    <?php generate_filter_options('format'); ?>
                </select>
            </div>
            
            <select id="filter-order" name="order">
                <option value="">Trier par</option>
                <option value="asc">Croissant</option>
                <option value="desc">Décroissant</option>
            </select>
        </form>
    </div>

    <!-- Galerie -->
    <div class="gallery">
        <div id="photo-gallery" class="photo-gallery two-columns">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $image_id = get_the_ID();
                    $image_url = wp_get_attachment_url($image_id);
                    $image_title = get_the_title($image_id);
                    $image_category = strtolower(get_field('categorie', $image_id));
                    $image_year = get_field('annee', $image_id);
                    $image_format = strtolower(get_field('format', $image_id));
                    $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;
                    ?>
        
                    <div class="gallery-item" data-category="<?php echo esc_attr($image_category); ?>" data-format="<?php echo esc_attr($image_format); ?>" data-year="<?php echo esc_attr($image_year); ?>">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>" width="100%">
                        <div class="overlay">
                            <a href="<?php echo esc_url($single_photo_page_url); ?>" class="icon eye-icon" title="Voir les informations">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo esc_url($image_url); ?>" class="icon fullscreen-icon" title="Voir en plein écran" data-lightbox="gallery">
                                <i class="fa fa-expand"></i>
                            </a>
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
