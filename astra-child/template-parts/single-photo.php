<?php
/*
Template Name: Single Photo
*/
get_header(); // Inclut l'en-tête de votre thème

// Récupérer l'ID de l'image depuis l'URL
$image_id = isset($_GET['image_id']) ? intval($_GET['image_id']) : 0;

if (!$image_id) {
    echo 'Aucune image spécifiée.';
    get_footer();
    exit;
}

// Récupérer les informations de l'image principale
                    $image_reference=get_field('reference',$image_id);
                    $image_url = wp_get_attachment_url($image_id);
                    $image_title = get_the_title($image_id);
                    $image_category = get_field('category', $image_id);
                    $image_year = get_field('annee', $image_id);
                    $image_format = get_field('format', $image_id);
                    $image_id_from_url = attachment_url_to_postid($image_url);
                    $single_photo_page_url = get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id;

// Définir les variables pour les images précédentes et suivantes
$prev_image_id = null;
$next_image_id = null;

// Obtenir les ID des images de la même catégorie, y compris l'image actuelle
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'categorie',
            'value' => $image_category,
            'compare' => '=',
        ),
    ),
    'orderby' => 'menu_order',
    'order' => 'ASC'
);

$query = new WP_Query($args);

$images = $query->posts;

if ($images) {
    // Trouver les indices de l'image actuelle
    $image_ids = wp_list_pluck($images, 'ID');
    $current_index = array_search($image_id, $image_ids);

    // Définir l'image précédente et suivante
    if ($current_index > 0) {
        $prev_image_id = $image_ids[$current_index - 1];
    }
    if ($current_index < count($image_ids) - 1) {
        $next_image_id = $image_ids[$current_index + 1];
    }
}

wp_reset_postdata();
?>

<body>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Section 1 -->
        <div class="section">
            <div class="left-column">
                <h2 class="heading">
                    <?php echo esc_html(get_the_title($image_id)); ?>
                </h2>
                <ul class="info-photo">
                    <li>référence: <?php echo esc_html($image_reference); ?></li>
                    <li>catégorie: <?php echo esc_html($image_category); ?></li>
                    <li>format: <?php echo esc_html($image_format); ?></li>
                    <li>type: <?php echo esc_html(get_field('type', $image_id)); ?></li>
                    <li>année: <?php echo esc_html($image_year); ?></li>
                </ul>
            </div>

            <div class="right-column">
                <img src="<?php echo esc_url(wp_get_attachment_url($image_id)); ?>" alt="" style="width: 563px; height: 844px; object-fit: cover;">
            </div>
        </div>

        <hr class="separator1">

        <!-- Section 2 -->
        <div class="section2">
            <div class="interest">
                <p class="photo-interest">Cette photo vous intéresse ?</p>
                <div class="contact-link">
                    <a href="#" class="btn_contact2">Contact</a>
                </div>
            </div>
            <div class="right-column2">
                <img src="<?php echo esc_url(wp_get_attachment_url($image_id)); ?>">
                <!-- Container for arrows -->
                <div class="arrows">
                    <?php if ($prev_image_id): ?>
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $prev_image_id); ?>" class="arrow-left" title="Précédent">&#8592;</a>
                    <?php endif; ?>
                    <?php if ($next_image_id): ?>
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $next_image_id); ?>" class="arrow-right" title="Suivant">&#8594;</a>
                    <?php endif; ?>
                </div>
                
                <!-- Container for thumbnail previews -->
                <div class="thumbnails">
                    <?php if ($prev_image_id): ?>
                        <div class="thumbnail preview-prev">
                            <img src="<?php echo esc_url(wp_get_attachment_url($prev_image_id)); ?>" alt="Image précédente">
                        </div>
                    <?php endif; ?>
                    <?php if ($next_image_id): ?>
                        <div class="thumbnail preview-next">
                            <img src="<?php echo esc_url(wp_get_attachment_url($next_image_id)); ?>" alt="Image suivante">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr class="separator">

        <!-- Section 3 -->
        <p class="uppercase">Vous aimerez aussi</p>
        <div class="gallery_singlephoto">
            <?php
            // Requête personnalisée pour récupérer les images de la même catégorie
            $args = array(
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'post_status' => 'inherit',
                'posts_per_page' => 2,
                'meta_query' => array(
                    array(
                        'key' => 'category',
                        'value' => $image_category,
                        'compare' => '=',
                    ),
                ),
                'post__not_in' => array($image_id),
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $image_url = wp_get_attachment_url(get_the_ID());
                    ?>
                         <div class="gallery-item" data-category="<?php echo esc_attr($image_category); ?>" data-format="<?php echo esc_attr($image_format); ?>" data-year="<?php echo esc_attr($image_year); ?>">
    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_title); ?>" width="100%">
    <div class="overlay">
        <!-- Icone plein écran en haut à gauche -->
        <a href="<?php echo esc_url($image_url); ?>" class="icon fullscreen-icon" data-lightbox="gallery" title="Voir en plein écran">
            <i class="fa fa-expand"></i>
        </a>
        <!-- Obtenir l'ID de l'image en utilisant l'URL -->
        <?php 
            // Obtenir l'ID de l'image en utilisant son URL
            $image_id_from_url = attachment_url_to_postid($image_url);
            // Construire l'URL de la page dynamique en utilisant cet ID
            $dynamic_single_photo_page_url = $image_id_from_url ? get_permalink(get_page_by_path('single-photo')) . '?image_id=' . $image_id_from_url : '';
            // Utiliser cet ID pour obtenir le titre de l'image
            $overlay_image_title = $image_id_from_url ? get_the_title($image_id_from_url) : '';
        ?>
        <!-- Icone d'information au centre -->
        <a href="<?php echo esc_url($dynamic_single_photo_page_url); ?>" class="icon eye-icon" title="Voir les informations">
            <i class="fa fa-eye"></i>
        </a>
        <!-- Référence en bas à gauche -->
        <div class="photo-title"><?php echo esc_html($overlay_image_title); ?></div>
        <!-- Catégorie en bas à droite -->
        <div class="photo-category"><?php echo esc_html($image_category); ?></div>
    </div>
</div>



                    <?php
                endwhile;
            else :
                echo '<p>Aucune image trouvée dans cette catégorie.</p>';
            endif;

            wp_reset_postdata();
            ?>
        </div>
    </div>

    <!-- Script JavaScript inclus dans le template PHP -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer la référence de la photo depuis PHP
            var photoReference = <?php echo json_encode($image_reference); ?>;

            // Vérifiez que la variable est correctement définie
            console.log("Référence de la photo:", photoReference);

            var contactLinks = document.querySelectorAll('.contact-link');
            var popup = document.querySelector('.popup-overlay');
            var closeBtn = document.querySelector('.popup-close');
            var photoRefInput = document.querySelector('#photo-ref');

            function openPopup() {
                if (photoRefInput) {
                    photoRefInput.value = photoReference;
                }
                popup.style.display = 'block';
            }

            function closePopup() {
                popup.style.display = 'none';
            }

            contactLinks.forEach(function(contactLink) {
                contactLink.addEventListener('click', function(event) {
                    event.preventDefault();
                    openPopup();
                });
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    closePopup();
                });
            }

            window.addEventListener('click', function(event) {
                if (event.target === popup) {
                    closePopup();
                }
            });
        });
    </script>
</body>
</html>

<?php
get_footer(); // Inclut le pied de page de votre thème
?>
