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
            <div class="tournament-grid">
                <?php while (have_posts()) : the_post();
                    $season_id = get_post_meta(get_the_ID(), '_tournament_season', true);
                    $start_date = get_post_meta(get_the_ID(), '_tournament_start_date', true);
                    $end_date = get_post_meta(get_the_ID(), '_tournament_end_date', true);
                ?>
                <div class="tournament-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="tournament-logo">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <h3>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <?php if ($season_id) : ?>
                        <p><strong>Temporada:</strong> <?php echo esc_html(get_the_title($season_id)); ?></p>
                    <?php endif; ?>

                    <?php if ($start_date && $end_date) : ?>
                        <p class="tournament-dates">
                            <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                            <?php echo date('d/m/Y', strtotime($end_date)); ?>
                        </p>
                    <?php endif; ?>
                    
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
