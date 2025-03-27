<?php
function fetch_books_ajax() {
    $query = new WP_Query(['post_type' => 'library', 'posts_per_page' => 20]);
    $books = [];

    while ($query->have_posts()) {
        $query->the_post();
        $books[] = [
            'name' => get_the_title(),
            'date' => get_the_date(),
            'genre' => wp_get_post_terms(get_the_ID(), 'book-genre', ['fields' => 'names']),
            'excerpt' => get_the_excerpt(),
        ];
    }
    wp_send_json($books);
}
add_action('wp_ajax_fetch_books', 'fetch_books_ajax');
add_action('wp_ajax_nopriv_fetch_books', 'fetch_books_ajax');
