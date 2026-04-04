<?php
/**
 * Single Season Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); 
            $season_year = get_post_meta(get_the_ID(), '_season_year', true);
            $start_date = get_post_meta(get_the_ID(), '_season_start_date', true);
            $end_date = get_post_meta(get_the_ID(), '_season_end_date', true);
            
            // Get tournaments for this season
            $tournaments = get_posts(array(
                'post_type' => 'tournament',
                'posts_per_page' => -1,
                'meta_key' => '_tournament_season',
                'meta_value' => get_the_ID(),
                'orderby' => 'date',
                'order' => 'DESC'
            ));
        ?>
        
        <div class="team-header">
            <div class="team-info">
                <h1><?php the_title(); ?></h1>
                <div class="team-meta">
                    <?php if ($season_year): ?>
                        <p><strong>Año:</strong> <?php echo esc_html($season_year); ?></p>
                    <?php endif; ?>
                    <?php if ($start_date && $end_date): ?>
                        <p>
                            <strong>Período:</strong> 
                            <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                            <?php echo date('d/m/Y', strtotime($end_date)); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (get_the_content()) : ?>
        <div class="stats-card">
            <h2>Descripción</h2>
            <?php the_content(); ?>
        </div>
        <?php endif; ?>
        
        <div class="stats-card">
            <h2>Torneos de la Temporada</h2>
            <?php if ($tournaments && count($tournaments) > 0): ?>
                <div class="tournament-grid">
                    <?php foreach ($tournaments as $tournament): 
                        $tournament_start = get_post_meta($tournament->ID, '_tournament_start_date', true);
                        $tournament_end = get_post_meta($tournament->ID, '_tournament_end_date', true);
                        $thumbnail = get_the_post_thumbnail($tournament->ID, 'medium');
                    ?>
                        <div class="tournament-card">
                            <?php if ($thumbnail): ?>
                                <div class="tournament-logo">
                                    <a href="<?php echo get_permalink($tournament->ID); ?>">
                                        <?php echo $thumbnail; ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <h3>
                                <a href="<?php echo get_permalink($tournament->ID); ?>">
                                    <?php echo esc_html($tournament->post_title); ?>
                                </a>
                            </h3>
                            <?php if ($tournament_start && $tournament_end): ?>
                                <p class="tournament-dates">
                                    <?php echo date('d/m/Y', strtotime($tournament_start)); ?> - 
                                    <?php echo date('d/m/Y', strtotime($tournament_end)); ?>
                                </p>
                            <?php endif; ?>
                            <a href="<?php echo get_permalink($tournament->ID); ?>" class="btn">Ver Torneo</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No hay torneos registrados para esta temporada.</p>
            <?php endif; ?>
        </div>

        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
