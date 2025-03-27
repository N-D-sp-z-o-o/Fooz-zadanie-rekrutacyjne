<?php
function most_recent_book_title() {
    $query = new WP_Query(['post_type' => 'library', 'posts_per_page' => 1]);
    return $query->have_posts() ? $query->posts[0]->post_title : 'No books found';
}
add_shortcode('recent_book', 'most_recent_book_title');

function books_by_genre($atts) {
    $atts = shortcode_atts(['id' => ''], $atts);
    $query = new WP_Query([
        'post_type' => 'library',
        'tax_query' => [['taxonomy' => 'book-genre', 'field' => 'term_id', 'terms' => $atts['id']]],
        'posts_per_page' => 5,
        'orderby' => 'title',
        'order' => 'ASC'
    ]);

    $output = '<ul>';
    while ($query->have_posts()) : $query->the_post();
        $output .= '<li>' . get_the_title() . '</li>';
    endwhile;
    wp_reset_postdata();
    return $output . '</ul>';
}
add_shortcode('books_by_genre', 'books_by_genre');
