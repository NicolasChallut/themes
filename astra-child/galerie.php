<?php
/*
Template Name: Galerie Simple avec Tri
*/

get_header(); // Inclut l'en-tête de votre thème

// Tableau de données pour les images
$gallery_images = array(
    array('file' => 'nathalie-0.jpeg', 'title' => 'Santé !', 'reference' => 'bf2385', 'category' => 'Réception', 'year' => 2019, 'format' => 'paysage', 'type' => 'Argentique'),
    array('file' => 'nathalie-1.jpeg', 'title' => 'Et bon anniversaire !', 'reference' => 'bf2386', 'category' => 'Réception', 'year' => 2020, 'format' => 'paysage', 'type' => 'Argentique'),
    array('file' => 'nathalie-2.jpeg', 'title' => "Let's party!", 'reference' => 'bf2387', 'category' => 'Concert', 'year' => 2021, 'format' => 'paysage', 'type' => 'Numérique'),
    array('file' => 'nathalie-3.jpeg', 'title' => 'Tout est installé', 'reference' => 'bf2388', 'category' => 'Mariage', 'year' => 2019, 'format' => 'portrait', 'type' => 'Argentique'),
    array('file' => 'nathalie-4.jpeg', 'title' => "Vers l'éternité", 'reference' => 'bf2389', 'category' => 'Mariage', 'year' => 2020, 'format' => 'portrait', 'type' => 'Numérique'),
    // Ajoutez ici les autres images...
);

// Affichage du formulaire de tri
echo '<div class="filter-controls">';
echo '<form id="filter-form">';
echo '<label for="category-filter">Catégorie :</label>';
echo '<select id="category-filter" name="category">';
echo '<option value="">Toutes</option>';
echo '<option value="Réception">Réception</option>';
echo '<option value="Concert">Concert</option>';
echo '<option value="Mariage">Mariage</option>';
echo '<option value="Télévision">Télévision</option>';
echo '</select>';

echo '<label for="format-filter">Format :</label>';
echo '<select id="format-filter" name="format">';
echo '<option value="">Tous</option>';
echo '<option value="paysage">Paysage</option>';
echo '<option value="portrait">Portrait</option>';
echo '</select>';

echo '<label for="year-filter">Année :</label>';
echo '<select id="year-filter" name="year">';
echo '<option value="">Toutes</option>';
echo '<option value="2022">2022</option>';
echo '<option value="2021">2021</option>';
echo '<option value="2020">2020</option>';
echo '<option value="2019">2019</option>';
echo '</select>';
echo '</form>';
echo '</div>';

// Affichage de la galerie
echo '<div id="photo-gallery" class="photo-gallery two-columns">';
foreach ($gallery_images as $image) {
    echo '<div class="gallery-item" data-category="' . strtolower($image['category']) . '" data-format="' . strtolower($image['format']) . '" data-year="' . $image['year'] . '">';
    echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/images/' . $image['file']) . '" alt="' . esc_attr($image['title']) . '" width="100%">';
    echo '<div class="image-details">';
    echo '<h3>' . esc_html($image['title']) . '</h3>';
    echo '<p>Référence: ' . esc_html($image['reference']) . '</p>';
    echo '<p>Catégorie: ' . esc_html($image['category']) . '</p>';
    echo '<p>Année: ' . esc_html($image['year']) . '</p>';
    echo '<p>Format: ' . esc_html($image['format']) . '</p>';
    echo '<p>Type: ' . esc_html($image['type']) . '</p>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';

get_footer(); // Inclut le pied de page de votre thème
?>
