<?php
/**
 * Single Post Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('stats-card'); ?>>
            <h1><?php the_title(); ?></h1>
            
            <div class="post-meta" style="margin-bottom: 20px; color: #666;">
                <span>Publicado el <?php echo get_the_date(); ?></span>
                <?php if (get_the_author()) : ?>
                    <span> por <?php the_author(); ?></span>
                <?php endif; ?>
            </div>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="post-thumbnail" style="margin-bottom: 30px;">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <div class="post-content">
                <?php the_content(); ?>
            </div>
            
            <?php if (get_the_tags()) : ?>
                <div class="post-tags" style="margin-top: 30px;">
                    <?php the_tags('<strong>Tags:</strong> ', ', '); ?>
                </div>
            <?php endif; ?>
        </article>
        
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
