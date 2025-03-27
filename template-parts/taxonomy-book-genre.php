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