<?php
/**
 * Archive Players Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Todos los Jugadores</h1>
            <p>Explora las estadísticas de todos nuestros jugadores</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="player-grid">
                <?php while (have_posts()) : the_post(); 
                    $player_id = get_the_ID();
                    $player_number = get_post_meta($player_id, '_player_number', true);
                    $batting_avg = get_post_meta($player_id, '_batting_avg', true);
                    $home_runs = get_post_meta($player_id, '_home_runs', true);
                    $rbis = get_post_meta($player_id, '_rbis', true);
                    $positions = wp_get_post_terms($player_id, 'position');
                    $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                    $team_id = get_post_meta($player_id, '_player_team', true);
                    $team_name = $team_id ? get_the_title($team_id) : 'Agente Libre';
                ?>
                <div class="player-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('player-thumbnail', array('class' => 'player-photo')); ?>
                    <?php endif; ?>
                    <div class="player-number">#<?php echo esc_html($player_number); ?></div>
                    <h3 class="player-name"><?php the_title(); ?></h3>
                    <div class="player-position"><?php echo esc_html($position_name); ?></div>
                    <p style="font-size: 0.9rem; color: #666;"><?php echo esc_html($team_name); ?></p>
                    <div class="stat-boxes">
                        <div class="stat-box">
                            <div class="stat-label">AVG</div>
                            <div class="stat-value"><?php echo esc_html($batting_avg ?: '.000'); ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">HR</div>
                            <div class="stat-value"><?php echo esc_html($home_runs ?: '0'); ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">RBI</div>
                            <div class="stat-value"><?php echo esc_html($rbis ?: '0'); ?></div>
                        </div>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="btn" style="margin-top: 15px;">Ver Detalles</a>
                </div>
                <?php endwhile; ?>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay jugadores registrados</h2>
                <p>Aún no se han añadido jugadores al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
