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
$categorie = get_field('categorie', $image_id);
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
                    <li>référence: <?php echo esc_html(get_field('reference', $image_id)); ?></li>
                    <li>catégorie: <?php echo esc_html($categorie); ?></li>
                    <li>format: <?php echo esc_html(get_field('format', $image_id)); ?></li>
                    <li>type: <?php echo esc_html(get_field('type', $image_id)); ?></li>
                    <li>année: <?php echo esc_html(get_field('annee', $image_id)); ?></li>
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
                <div class="buttons">
                    <a href="#" id="contact-link" class="btn_contact2">Contact</a>
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
        <div class="gallery">
            <?php
            // Requête personnalisée pour récupérer les images de la même catégorie
            $args = array(
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'post_status' => 'inherit',
                'posts_per_page' => 2,
                'meta_query' => array(
                    array(
                        'key' => 'categorie',
                        'value' => $categorie,
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
                    <div class="gallery-item">
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('single-photo')) . '?image_id=' . get_the_ID()); ?>">
                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                        </a>
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

</body>
</html>

<?php
get_footer(); // Inclut le pied de page de votre thème
?>
