<?php
/**
 * Template Name: Página de Noticias
 * Template for displaying all blog posts
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="archive-header">
            <h1 class="archive-title">Todas las Noticias</h1>
            <p class="archive-description">Mantente al día con todas las últimas noticias del béisbol</p>
        </div>

        <!-- MLB Style Layout: Main Content + Sidebar -->
        <div class="mlb-layout">
            <!-- Main Content Area -->
            <div class="main-content-area">
                <?php
                // Query all posts
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 10,
                    'paged' => $paged,
                    'post_status' => 'publish'
                );
                $news_query = new WP_Query($args);
                
                if ($news_query->have_posts()) : ?>
                    <div class="news-archive-grid">
                        <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                            <article class="news-archive-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="news-archive-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="news-archive-content">
                                    <h2 class="news-archive-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="news-archive-meta">
                                        <span class="news-date"><?php echo get_the_date(); ?></span>
                                        <?php
                                        $categories = get_the_category();
                                        if ($categories) :
                                            foreach ($categories as $category) :
                                        ?>
                                            <span class="news-category"><?php echo esc_html($category->name); ?></span>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </div>
                                    <p class="news-archive-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="read-more-link">Leer más →</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="archive-pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $news_query->max_num_pages,
                            'current' => $paged,
                            'mid_size' => 2,
                            'prev_text' => '← Anterior',
                            'next_text' => 'Siguiente →',
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <div class="no-posts">
                        <p>No hay noticias disponibles en este momento.</p>
                    </div>
                <?php endif; 
                wp_reset_postdata();
                ?>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar-area">
                <?php get_template_part('template-parts/sidebar-leaders-standings'); ?>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
