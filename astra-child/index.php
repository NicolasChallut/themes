<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header(); // Inclut l'en-tête de votre thème

// URL de l'image à afficher au-dessus de la galerie
$header_image_url = 'http://nathalie.local/wp-content/uploads/2024/08/nathalie-11-scaled.jpeg'; // Remplacez par l'URL de l'image que vous souhaitez afficher

// Afficher l'image au-dessus de la galerie
if (!empty($header_image_url)) {
    echo '<div class="header-image">';
    echo '<img src="' . esc_url($header_image_url) . '" alt="Image d\'en-tête">';
    echo '</div>';
}

// Menus déroulants pour le tri
$categories = array('', 'Réception', 'Concert', 'Mariage', 'Télévision'); // Les catégories de votre choix
$formats = array('', 'portrait', 'paysage'); // Les formats de votre choix
$dates = array('', 'les plus récentes', 'Les plus anciennes'); // Options de date

echo '<div class="filter-controls">';
echo '<form id="filter-form">';
echo '<label for="category-filter">Catégorie :</label>';
echo '<select id="category-filter" name="category">';
foreach ($categories as $category) {
    echo '<option value="' . strtolower($category) . '">' . $category . '</option>';
}
echo '</select>';

echo '<label for="format-filter">Format :</label>';
echo '<select id="format-filter" name="format">';
foreach ($formats as $format) {
    echo '<option value="' . strtolower($format) . '">' . $format . '</option>';
}
echo '</select>';

echo '<label for="date-filter">Date :</label>';
echo '<select id="date-filter" name="date">';
foreach ($dates as $date) {
    echo '<option value="' . strtolower(str_replace(' ', '-', $date)) . '">' . $date . '</option>';
}
echo '</select>';
echo '</form>';
echo '</div>';

echo '<div id="photo-gallery" class="photo-gallery two-columns">';
echo '</div>';

get_footer(); // Inclut le pied de page de votre thème
?>
