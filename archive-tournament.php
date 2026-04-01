<?php
/**
 * Archive Tournaments Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Torneos</h1>
            <p>Todos los torneos de baseball</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="stats-grid">
                <?php while (have_posts()) : the_post(); ?>
                <div class="stats-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h2><?php the_title(); ?></h2>
                    
                    <?php if (get_the_excerpt()) : ?>
                        <p><?php the_excerpt(); ?></p>
                    <?php endif; ?>
                    
                    <a href="<?php the_permalink(); ?>" class="btn">Ver Torneo</a>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay torneos registrados</h2>
                <p>Aún no se han añadido torneos al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
