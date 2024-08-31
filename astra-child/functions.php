<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('chld_thm_cfg_child_theme_configurator_css')) :
    function chld_thm_cfg_child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('astra-theme-css', 'astra-contact-form-7'));
    }
endif;
add_action('wp_enqueue_scripts', 'chld_thm_cfg_child_theme_configurator_css', 10);


// Enqueue Custom Scripts
function chld_thm_cfg_enqueue_theme_assets() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('filter-script', get_stylesheet_directory_uri() . '/assets/js/filter.js', array('jquery'), null, true);
    wp_enqueue_script('popup-contact-script', get_stylesheet_directory_uri() . '/assets/js/popup-contact.js', array('jquery'), null, true);
    wp_enqueue_script('custom-gallery-script', get_stylesheet_directory_uri() . '/assets/js/custom-gallery.js', array('jquery'), null, true);
    wp_enqueue_script('load-more', get_stylesheet_directory_uri() . '/assets/js/load-more.js', array('jquery'), null, true);
    wp_enqueue_script('arrows', get_stylesheet_directory_uri() . '/assets/js/arrows.js', array('jquery'), null, true);


    // Localiser les scripts pour passer des variables PHP à JavaScript
    wp_localize_script('filter-script', 'load_more_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_images_nonce')
    ));
    wp_localize_script('load-more', 'load_more_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_images_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'chld_thm_cfg_enqueue_theme_assets');

//chargement de FontAwesome pour light box
function enqueue_fancybox_assets() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
    wp_enqueue_style('fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), '3.5.7');
    wp_enqueue_script('fancybox-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array('jquery'), '3.5.7', true);
}
add_action('wp_enqueue_scripts', 'enqueue_fancybox_assets');




// Add AJAX URL to the head for JS use
function chld_thm_cfg_add_ajax_url() {
    ?>
    <script type="text/javascript">
        var wp_vars = {
            ajax_url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
        };
    </script>
    <?php
}
add_action('wp_head', 'chld_thm_cfg_add_ajax_url');

// Load More Images via AJAX
function chld_thm_cfg_load_more_images() {
    check_ajax_referer('load_more_images_nonce', 'nonce');

    if (!isset($_POST['page'])) {
        wp_send_json_error('Invalid request.');
    }

    $paged = (int) $_POST['page'];
    $paged++;

    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => 8,
        'paged' => $paged,
        'meta_query' => array(
            array('key' => 'display_in_gallery', 'value' => '1', 'compare' => '=')
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $image_id = get_the_ID();
            $image_url = wp_get_attachment_url($image_id);
            $image_title = get_the_title($image_id);

            $image_category = strtolower(get_field('category', $image_id)); // Récupère la catégorie
            $image_format = strtolower(get_field('format', $image_id)); // Récupère le format
            $image_reference = get_field('reference', $image_id); // Récupère la référence

            $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;

            echo '<div class="gallery-item" data-category="' . esc_attr($image_category) . '" data-format="' . esc_attr($image_format) . '">';
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_title) . '" width="100%">';
            echo '<div class="overlay">';
            echo '<div class="photo-category"> ' . esc_html($image_category) . '</div>';
            echo '<div class="photo-title"> ' . esc_html($image_title) . '</div>';
            echo '<a href="' . esc_url($single_photo_page_url) . '" class="icon eye-icon" title="Voir les informations"><i class="fa fa-eye"></i></a>';
            echo '<a href="' . esc_url($image_url) . '" class="icon fullscreen-icon" title="Voir en plein écran" data-lightbox="gallery"><i class="fa fa-expand"></i></a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        wp_send_json_error('no_more_images');
    }

    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_chld_thm_cfg_load_more_images', 'chld_thm_cfg_load_more_images');
add_action('wp_ajax_nopriv_chld_thm_cfg_load_more_images', 'chld_thm_cfg_load_more_images');

// Fonction pour générer des options de filtre à partir des champs ACF
function generate_filter_options($field_name) {
    global $wpdb;

    // Requête pour obtenir les valeurs distinctes du champ ACF spécifié
    $query = $wpdb->prepare(
        "
        SELECT DISTINCT meta_value 
        FROM $wpdb->postmeta 
        WHERE meta_key = %s 
        AND meta_value != ''
        ",
        $field_name
    );

    $results = $wpdb->get_col($query);

    // Vérifier si des résultats ont été trouvés et générer les options
    if ($results) {
        foreach ($results as $value) {
            echo '<option value="' . esc_attr($value) . '">' . esc_html(ucfirst($value)) . '</option>';
        }
    }
}



