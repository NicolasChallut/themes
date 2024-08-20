<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css()
    {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('astra-theme-css', 'astra-contact-form-7'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

function my_theme_enqueue_scripts() {
    // Enregistrer le fichier JavaScript
    wp_enqueue_script( 'popup-contact-script', get_stylesheet_directory_uri() . '/assets/js/popup-contact.js', array('jquery'), null, true );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_scripts' );


// Custom Scripts for Photo Gallery Filtering
function enqueue_custom_gallery_scripts() {
    // Enregistrer et inclure le script custom-gallery.js
    wp_enqueue_script('custom-gallery-script', get_stylesheet_directory_uri() . '/assets/js/custom-gallery.js', array('jquery'), null, true);

    // Enregistrer et inclure le script filter-button.js
    wp_enqueue_script('filter-button-script', get_stylesheet_directory_uri() . '/assets/js/filter-button.js', array('jquery'), null, true);

    wp_enqueue_script('JQUERY-script', get_stylesheet_directory_uri() . '/assets/js/JQUERY.js', array('jquery'), null, true);

}

add_action('wp_enqueue_scripts', 'enqueue_custom_gallery_scripts');


//REQUET AJAX
function register_custom_api_endpoints() {
    register_rest_route('my-plugin/v1', '/filtered-images', array(
        'methods' => 'GET',
        'callback' => 'get_filtered_images',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'register_custom_api_endpoints');

function get_filtered_images(WP_REST_Request $request) {
    $category = $request->get_param('category');
    $format = $request->get_param('format');
    $date = $request->get_param('date');

    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
        ),
    );

    // Ajouter des filtres par catégorie
    if ($category && $category !== 'tous') {
        $args['meta_query'][] = array(
            'key' => 'image_category',
            'value' => ucfirst($category),
            'compare' => '=',
        );
    }

    // Ajouter des filtres par format
    if ($format && $format !== 'tous') {
        $args['meta_query'][] = array(
            'key' => 'image_format',
            'value' => ucfirst($format),
            'compare' => '=',
        );
    }

    // Ajouter des filtres par date
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
            $images[] = array(
                'url' => wp_get_attachment_url($image_id),
                'title' => get_the_title(),
            );
        }
    }
    wp_reset_postdata();

    if (empty($images)) {
        return new WP_Error('no_images', 'No images found', array('status' => 404));
    }

    // Générer le HTML des images pour affichage
    ob_start();
    foreach ($images as $image) {
        echo '<div class="photo-card">';
        echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['title']) . '">';
        echo '</div>';
    }
    return ob_get_clean();
}

//VERIFICATION REQUETE AJAX

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
