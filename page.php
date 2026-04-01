<?php
/**
 * Page Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
        
        <article id="page-<?php the_ID(); ?>" <?php post_class('stats-card'); ?>>
            <h1><?php the_title(); ?></h1>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="page-thumbnail" style="margin-bottom: 30px;">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <div class="page-content">
                <?php the_content(); ?>
            </div>
        </article>
        
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
