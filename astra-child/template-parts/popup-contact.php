<div class="popup-overlay">
    <div class="popup-salon">
        <div class="popup-header">
            <!-- Récupérer et afficher l'image par son titre -->
            <?php 
$image_title = 'contact header'; // Le titre de l'image

// Utiliser WP_Query pour récupérer l'image
$query_args = array(
    'post_type'      => 'attachment',
    'post_status'    => 'inherit',
    'title'          => $image_title,
    'posts_per_page' => 1,
);

$image_query = new WP_Query( $query_args );

if ( $image_query->have_posts() ) {
    while ( $image_query->have_posts() ) {
        $image_query->the_post();
        $image_id = get_the_ID();
        echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'popup-image' ) );
    }
    wp_reset_postdata();
} else {
    echo 'Image non trouvée';
}
?>

        </div>
        <div class="popup-content">
            <!-- Formulaire de contact -->
            <?php echo do_shortcode('[contact-form-7 id="cb4793d" title="Formulaire de contact 1"]'); ?>
        </div>
    </div>
</div>
