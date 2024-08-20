<div class="popup-overlay" style="display:none;">
    <div class="popup-salon">
        <div class="popup-header">
            <!-- Récupérer et afficher l'image par son titre -->
            <?php 
            $image_title = 'contact header'; // Le titre de l'image
            $image = get_page_by_title( $image_title, OBJECT, 'attachment' );

            if ( $image ) {
                $image_id = $image->ID;
                echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'popup-image' ) );
            } else {
                echo 'Image non trouvée';
            }
            ?>
            <!-- Bouton de fermeture -->
            <span class="popup-close"><i class="fa fa-times"></i></span>
        </div>
        <div class="popup-content">
            <!-- Formulaire de contact -->
            <?php echo do_shortcode('[contact-form-7 id="cb4793d" title="Formulaire de contact 1"]'); ?>
        </div>
    </div>
</div>
