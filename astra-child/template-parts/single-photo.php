<?php
/*
Template Name: Single Photo
*/
get_header(); // Inclut l'en-tête de votre thème

$image_id = 5938; // Remplacez 5944 par l'ID de l'image que vous souhaitez afficher

// Récupérer la catégorie de l'image principale
$categorie = get_field('categorie', $image_id);

// Requête personnalisée pour récupérer les images de la même catégorie
$args = array(
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'post_status' => 'inherit',
    'posts_per_page' => 2, // Nombre d'images à afficher
    'meta_query' => array(
        array(
            'key' => 'categorie', // Nom du champ ACF
            'value' => $categorie,
            'compare' => '=', // Comparaison pour matcher exactement la catégorie
        ),
    ),
    'post__not_in' => array($image_id) // Exclure l'image principale
);

$query = new WP_Query($args);

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
                <p class="photo-interest">cette photo vous intéresse ?</p>
                <div class="buttons">
                    <a href="#" class="btn_contact2">contact</a>
                </div>
            </div>
            <div class="right-column2">
                <img src="<?php echo esc_url(wp_get_attachment_url($image_id)); ?>" alt="">
                <!-- Container for arrows -->
                <div class="arrows">
                    <div class="arrow-left">&#8592;</div> <!-- Left arrow -->
                    <div class="arrow-right">&#8594;</div> <!-- Right arrow -->
                </div>
            </div>
        </div>

        <!-- Separator -->
        <hr class="separator">

        <!-- Section 3 -->
        <p class="uppercase">vous aimerez aussi</p>
        <div class="gallery">
            <?php
            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $image_url = wp_get_attachment_url(get_the_ID());
                    ?>
                    <div class="gallery-item">
                        <img src="<?php echo esc_url($image_url); ?>" alt="">
                    </div>
                    <?php
                endwhile;
            else :
                echo '<p>Aucune image trouvée dans cette catégorie.</p>';
            endif;

            // Réinitialiser les données du post global
            wp_reset_postdata();
            ?>
        </div>
    </div>

    
</body>
</html>

<?php
get_footer(); // Inclut le pied de page de votre thème
?>
