<?php
/**
 * Single Post Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <!-- MLB Style Layout: Main Content + Sidebar -->
        <div class="mlb-layout">
            <!-- Main Content Area -->
            <div class="main-content-area">
                <?php while (have_posts()) : the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
                    <header class="single-post-header">
                        <h1 class="single-post-title"><?php the_title(); ?></h1>
                        
                        <div class="single-post-meta">
                            <span class="post-date">
                                <i class="dashicons dashicons-calendar"></i>
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php if (get_the_author()) : ?>
                                <span class="post-author">
                                    <i class="dashicons dashicons-admin-users"></i>
                                    <?php the_author(); ?>
                                </span>
                            <?php endif; ?>
                            <?php
                            $categories = get_the_category();
                            if ($categories) :
                                foreach ($categories as $category) :
                            ?>
                                <span class="post-category"><?php echo esc_html($category->name); ?></span>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="single-post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="single-post-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if (get_the_tags()) : ?>
                        <div class="post-tags">
                            <strong>Etiquetas:</strong>
                            <?php the_tags('', ', '); ?>
                        </div>
                    <?php endif; ?>
                </article>
                
                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
                
                <?php endwhile; ?>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar-area">
                <?php get_template_part('template-parts/sidebar-leaders-standings'); ?>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
