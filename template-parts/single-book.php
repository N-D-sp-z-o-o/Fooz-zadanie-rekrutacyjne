<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php the_post_thumbnail(); ?>
    <p>Genre: <?php echo get_the_term_list(get_the_ID(), 'book-genre', '', ', '); ?></p>
    <p>Published on: <?php echo get_the_date(); ?></p>
<?php endwhile; ?>
<?php get_footer(); ?>