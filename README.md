## Introduction

To keep the project organized and maintainable, I will use the following directory structure for TwentyTwenty child theme:

![Zrzut ekranu 2025-03-26 135342](https://github.com/user-attachments/assets/e846ee09-0d92-4358-8ff0-05192192277f)

## Task 1: Styling

Since future styling changes are expected, the best approach is to place custom CSS rules in the child theme's style.css file. This ensures updates to the parent theme won't override our styles. Alternatively, CSS can be enqueued via wp_enqueue_style in functions.php.

style.css
```CSS
/*
Theme Name: Fooz Child Theme
Template: twentytwenty
*/
```

## Task 2: Loading Custom JavaScript

The best way to do this is by enqueuing it in functions.php:

functions.php
```PHP
function enqueue_custom_scripts() {
    wp_enqueue_script('custom-js', get_template_directory_uri().'/js/scripts.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
```

## Task 3: Custom Post Type & Taxonomy

custom-post-type.php
```PHP
function register_custom_post_type() {
    register_post_type('library', [
        'labels' => [
            'name' => __('Books', 'text-domain'),
            'singular_name' => __('Book', 'text-domain'),
        ],
        'public' => true,
        'menu_position' => 5,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'library'],
    ]);

    register_taxonomy('book-genre', 'library', [
        'labels' => [
            'name' => __('Genres', 'text-domain'),
            'singular_name' => __('Genre', 'text-domain'),
        ],
        'public' => true,
        'rewrite' => ['slug' => 'book-genre'],
    ]);
}
add_action('init', 'register_custom_post_type');
```

functions.php
```PHP
require_once get_stylesheet_directory() . '/includes/custom-post-type.php';
```

## Task 4: Custom Templates

### 4.1. Single Book Page

single-book.php
```PHP
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_post_thumbnail(); ?>
    <p>Genre: <?php echo get_the_term_list(get_the_ID(), 'book-genre', '', ', '); ?></p>
    <p>Published on: <?php echo get_the_date(); ?></p>
<?php endwhile; ?>
<?php get_footer(); ?>
```

### 4.2. Genre Page (Taxonomy Archive)

taxonomy-book-genre.php
```PHP
<?php
get_header(); 

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$query = new WP_Query([
    'post_type'      => 'library',
    'posts_per_page' => 5, // Limit books to 5 per page
    'paged'          => $paged,
    'tax_query'      => [
        [
            'taxonomy' => 'book-genre',
            'field'    => 'slug',
            'terms'    => get_queried_object()->slug,
        ]
    ],
]);

if ($query->have_posts()) : ?>
    <h1><?php single_term_title(); ?></h1>
    <?php while ($query->have_posts()) : $query->the_post(); ?>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php endwhile; ?>

    <div class="pagination">
        <?php
        echo paginate_links([
            'total'   => $query->max_num_pages,
            'current' => max(1, get_query_var('paged')),
        ]);
        ?>
    </div>

    <?php wp_reset_postdata(); ?>
<?php endif; 
get_footer();
?>
```

## Task 5: Shortcodes

### 5.1. Display the Title of the Most Recent Book

shortcodes.php
```PHP
function most_recent_book_title() {
    $query = new WP_Query(['post_type' => 'library', 'posts_per_page' => 1]);
    return $query->have_posts() ? $query->posts[0]->post_title : 'No books found';
}
add_shortcode('recent_book', 'most_recent_book_title');
```

### 5.2. Display 5 Books from a Given Genre

shortcodes.php
```PHP
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
```

functions.php
```PHP
require_once get_stylesheet_directory() . '/includes/shortcodes.php';
```

## Task 6 (Bonus): AJAX Callback Returning Books in JSON

### 6.1. PHP: Handle AJAX Request

ajax-handler.php
```PHP
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
```

functions.php
```PHP
require_once get_stylesheet_directory() . '/includes/ajax-handler.php';
```

### 6.2. JavaScript: Make AJAX Request

scripts.js
```JS
jQuery(document).ready(function($) {
    $.ajax({
        url: ajaxurl,
        method: 'POST',
        data: { action: 'fetch_books' },
        success: function(response) {
            console.log(response);
        }
    });
});
```


