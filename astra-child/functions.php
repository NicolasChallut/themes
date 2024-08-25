<?php
// Enqueue Styles and Scripts
function enqueue_theme_assets() {
    // Enqueue Parent Theme Styles (RTL support)
    if (is_rtl() && file_exists(get_template_directory() . '/rtl.css')) {
        wp_enqueue_style('parent-theme-rtl', get_template_directory_uri() . '/rtl.css');
    }

    // Enqueue Child Theme Styles
    wp_enqueue_style('child-theme-style', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css', 'astra-contact-form-7'));

    // Enqueue Custom Scripts
    wp_enqueue_script('popup-contact-script', get_stylesheet_directory_uri() . '/assets/js/popup-contact.js', array('jquery'), null, true);
    wp_enqueue_script('custom-gallery-script', get_stylesheet_directory_uri() . '/assets/js/custom-gallery.js', array('jquery'), null, true);
    wp_enqueue_script('load-more', get_stylesheet_directory_uri() . '/assets/js/load-more.js', array('jquery'), null, true);
    wp_localize_script('load-more', 'load_more_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('load_more_images_nonce')
    ));

    // Enqueue Font Awesome and Lightbox2
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2');
    wp_enqueue_style('lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3');
    wp_enqueue_script('lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), '2.11.3', true);

    // Assurez-vous que nous sommes sur la page du template concerné pour les données popup
    if (is_page_template('single-photo.php')) {
        $image_id = isset($_GET['image_id']) ? intval($_GET['image_id']) : 0;
        if ($image_id) {
            $reference = get_field('reference', $image_id);
            wp_localize_script('popup-contact-script', 'popup_contact_data', array(
                'photoReference' => $reference,
            ));
        }
    }
}
add_action('wp_enqueue_scripts', 'enqueue_theme_assets');


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
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
        'meta_query' => array('relation' => 'AND')
    );

    $params = array('category', 'format', 'date');
    foreach ($params as $param) {
        $value = ucfirst($request->get_param($param));
        if ($value && $value !== 'tous') {
            $args['meta_query'][] = array('key' => "image_$param", 'value' => $value, 'compare' => '=');
        }
    }

    if ($request->get_param('date') === 'les-plus-recentes') {
        $args['order'] = 'DESC';
        $args['orderby'] = 'date';
    } elseif ($request->get_param('date') === 'les-plus-anciennes') {
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
    $paged = (int) (isset($_POST['page']) ? $_POST['page'] : 1);
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
        }
    } else {
        echo 'no_more_images';
    }

    wp_reset_postdata();
    die();
}
add_action('wp_ajax_load_more_images', 'load_more_images');
add_action('wp_ajax_nopriv_load_more_images', 'load_more_images');

// Add AJAX URL to the head for JS use
function add_ajax_url() {
    ?>
    <script type="text/javascript">
        var wp_vars = {
            ajax_url: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
        };
    </script>
    <?php
}
add_action('wp_head', 'add_ajax_url');

// Get Adjacent Image Data
function get_adjacent_image_data($current_id, $direction = 'previous') {
    $adjacent_image = ($direction === 'next') ? get_next_post_thumbnail_id($current_id) : get_previous_post_thumbnail_id($current_id);
    return array(
        'url' => $adjacent_image ? wp_get_attachment_url($adjacent_image) : '',
        'id' => $adjacent_image
    );
}

// Get Thumbnail URL
function get_thumbnail_url($image_id) {
    $thumbnail = wp_get_attachment_image_src($image_id, 'thumbnail');
    return $thumbnail[0] ?? '';
}
