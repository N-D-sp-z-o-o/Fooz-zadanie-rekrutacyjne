<?php
function enqueue_custom_scripts() {
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/assets/js/scripts.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

require_once get_stylesheet_directory() . '/includes/custom-post-type.php';
require_once get_stylesheet_directory() . '/includes/shortcodes.php';
require_once get_stylesheet_directory() . '/includes/ajax-handler.php';
