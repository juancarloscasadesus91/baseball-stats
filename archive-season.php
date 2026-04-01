<?php
/**
 * Archive Seasons Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Temporadas</h1>
            <p>Historial de temporadas de baseball</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="stats-grid">
                <?php while (have_posts()) : the_post(); 
                    $season_id = get_the_ID();
                    $year = get_post_meta($season_id, '_season_year', true);
                    $start_date = get_post_meta($season_id, '_season_start_date', true);
                    $end_date = get_post_meta($season_id, '_season_end_date', true);
                ?>
                <div class="stats-card">
                    <h2><?php the_title(); ?></h2>
                    <?php if ($year) : ?>
                        <p><strong>Año:</strong> <?php echo esc_html($year); ?></p>
                    <?php endif; ?>
                    <?php if ($start_date) : ?>
                        <p><strong>Inicio:</strong> <?php echo date('d/m/Y', strtotime($start_date)); ?></p>
                    <?php endif; ?>
                    <?php if ($end_date) : ?>
                        <p><strong>Fin:</strong> <?php echo date('d/m/Y', strtotime($end_date)); ?></p>
                    <?php endif; ?>
                    <?php if (get_the_excerpt()) : ?>
                        <p><?php the_excerpt(); ?></p>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="btn">Ver Detalles</a>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay temporadas registradas</h2>
                <p>Aún no se han añadido temporadas al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
