<?php
/**
 * Single Season Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main">
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
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <div class="season-meta">
                <?php if ($season_year): ?>
                    <span class="season-year"><strong>Año:</strong> <?php echo esc_html($season_year); ?></span>
                <?php endif; ?>
                <?php if ($start_date && $end_date): ?>
                    <span class="season-dates">
                        <strong>Período:</strong> 
                        <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                        <?php echo date('d/m/Y', strtotime($end_date)); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>

        <div class="entry-content">
            <?php the_content(); ?>
            
            <?php if ($tournaments): ?>
                <section class="season-tournaments">
                    <h2>Torneos de la Temporada</h2>
                    <div class="tournament-grid">
                        <?php foreach ($tournaments as $tournament): 
                            $tournament_start = get_post_meta($tournament->ID, '_tournament_start_date', true);
                            $tournament_end = get_post_meta($tournament->ID, '_tournament_end_date', true);
                            $thumbnail = get_the_post_thumbnail($tournament->ID, 'team-logo');
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
                </section>
            <?php endif; ?>
        </div>
    </article>

    <?php endwhile; ?>
</main>

<?php
get_footer();
