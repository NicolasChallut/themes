<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/* ==========================================
   THEME STYLES & SCRIPTS
   ========================================== */

// Enqueue Parent Theme Styles (RTL support)
if (!function_exists('chld_thm_cfg_locale_css')) :
    function chld_thm_cfg_locale_css($uri) {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css')) {
            $uri = get_template_directory_uri() . '/rtl.css';
        }
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

// Enqueue Child Theme Styles
if (!function_exists('child_theme_configurator_css')) :
    function child_theme_configurator_css() {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('astra-theme-css', 'astra-contact-form-7'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// Enqueue Custom Scripts
function my_theme_enqueue_scripts() {
    wp_enqueue_script('popup-contact-script', get_stylesheet_directory_uri() . '/assets/js/popup-contact.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Enqueue Font Awesome and Lightbox2
function enqueue_custom_styles_and_scripts() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2');
    wp_enqueue_style('lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3');
    wp_enqueue_script('lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), '2.11.3', true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles_and_scripts');

// Enqueue Custom Gallery Scripts
function enqueue_custom_gallery_scripts() {
    wp_enqueue_script('custom-gallery-script', get_stylesheet_directory_uri() . '/assets/js/custom-gallery.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_gallery_scripts');

// Enqueue Load More Script and Localize Variables
function enqueue_load_more_script() {
    // Utiliser le répertoire du thème enfant pour le script JavaScript
    wp_enqueue_script('load-more', get_stylesheet_directory_uri() . '/assets/js/load-more.js', array('jquery'), null, true);
    wp_localize_script('load-more', 'load_more_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_images_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_load_more_script');


/* ==========================================
   AJAX & REST API HANDLERS
   ========================================== */

// Register Custom API Endpoints
function register_custom_api_endpoints() {
    register_rest_route('my-plugin/v1', '/filtered-images', array(
        'methods' => 'GET',
        'callback' => 'get_filtered_images',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_custom_api_endpoints');

// Get Filtered Images Callback
function get_filtered_images(WP_REST_Request $request) {
    $category = $request->get_param('category');
    $format = $request->get_param('format');
    $date = $request->get_param('date');

    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
        'meta_query' => array('relation' => 'AND')
    );

    if ($category && $category !== 'tous') {
        $args['meta_query'][] = array('key' => 'image_category', 'value' => ucfirst($category), 'compare' => '=');
    }
    if ($format && $format !== 'tous') {
        $args['meta_query'][] = array('key' => 'image_format', 'value' => ucfirst($format), 'compare' => '=');
    }
    if ($date === 'les-plus-recentes') {
        $args['order'] = 'DESC';
        $args['orderby'] = 'date';
    } elseif ($date === 'les-plus-anciennes') {
        $args['order'] = 'ASC';
        $args['orderby'] = 'date';
    }

    $query = new WP_Query($args);
    $images = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $image_id = get_the_ID();
            $images[] = array('url' => wp_get_attachment_url($image_id), 'title' => get_the_title());
        }
    }
    wp_reset_postdata();

    if (empty($images)) {
        return new WP_Error('no_images', 'No images found', array('status' => 404));
    }

    ob_start();
    foreach ($images as $image) {
        echo '<div class="photo-card">';
        echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['title']) . '">';
        echo '</div>';
    }
    return ob_get_clean();
}

// Load More Images via AJAX
function load_more_images() {
    check_ajax_referer('load_more_images_nonce', 'nonce');
    $paged = isset($_POST['page']) ? $_POST['page'] : 1;
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
        while ($query->have_posts()) : $query->the_post();
            $image_id = get_the_ID();
            $image_url = wp_get_attachment_url($image_id);
            $image_title = get_the_title($image_id);
            $image_category = strtolower(get_field('categorie_acf', $image_id));
            $image_format = strtolower(get_field('format_acf', $image_id));
            $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;

            echo '<div class="gallery-item" data-category="' . esc_attr($image_category) . '" data-format="' . esc_attr($image_format) . '">';
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_title) . '" width="100%">';
            echo '<div class="overlay">';
            echo '<a href="' . esc_url($single_photo_page_url) . '" class="icon eye-icon" title="Voir les informations"><i class="fa fa-eye"></i></a>';
            echo '<a href="' . esc_url($image_url) . '" class="icon fullscreen-icon" title="Voir en plein écran" data-lightbox="gallery"><i class="fa fa-expand"></i></a>';
            echo '</div>';
            echo '</div>';
        endwhile;
    } else {
        echo 'no_more_images';
    }

    wp_reset_postdata();
    die();
}
add_action('wp_ajax_load_more_images', 'load_more_images');
add_action('wp_ajax_nopriv_load_more_images', 'load_more_images');

/* ==========================================
   MISCELLANEOUS FUNCTIONS
   ========================================== */

// Add AJAX URL to the head for JS use
function add_ajax_url() {
    ?>
    <script type="text/javascript">
        var wp_vars = {
            ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>"
        };
    </script>
    <?php
}
add_action('wp_head', 'add_ajax_url');

// Get Previous Image URL by ID
function get_previous_image_url($current_id) {
    $prev_image = get_previous_post_thumbnail_id($current_id);
    return wp_get_attachment_url($prev_image);
}

// Get Next Image URL by ID
function get_next_image_url($current_id) {
    $next_image = get_next_post_thumbnail_id($current_id);
    return wp_get_attachment_url($next_image);
}

// Get Previous Image ID
function get_previous_image_id($current_id) {
    $prev_image = get_previous_post_thumbnail_id($current_id);
    return $prev_image;
}

// Get Next Image ID
function get_next_image_id($current_id) {
    $next_image = get_next_post_thumbnail_id($current_id);
    return $next_image;
}

// Get Thumbnail URL
function get_thumbnail_url($image_id) {
    $thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');
    return $thumbnail[0];
}
