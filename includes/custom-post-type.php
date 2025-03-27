<?php
function register_custom_post_type() {
    register_post_type('library', [
        'labels' => [
            'name' => __('Books', 'text-domain'),
            'singular_name' => __('Book', 'text-domain')
        ],
        'public' => true,
        'menu_position' => 5,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'library'],
    ]);
    register_taxonomy('book-genre', 'library', [
        'labels' => [
            'name' => __('Genres', 'text-domain'),
            'singular_name' => __('Genre', 'text-domain')
        ],
        'public' => true,
        'rewrite' => ['slug' => 'book-genre'],
    ]);
}
add_action('init', 'register_custom_post_type');