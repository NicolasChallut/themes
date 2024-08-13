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

// Custom Script for Photo Gallery Filtering
function enqueue_custom_gallery_script() {
    wp_enqueue_script('custom-gallery-script', get_stylesheet_directory_uri() . '/js/custom-gallery.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_gallery_script');

// END ENQUEUE PARENT ACTION
