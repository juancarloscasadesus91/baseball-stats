<?php
/**
 * Archive Teams Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Todos los Equipos</h1>
            <p>Tabla de posiciones y estadísticas de equipos</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="stats-card">
                <div class="table-responsive">
                    <table class="teams-table">
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Ciudad</th>
                                <th>Estadio</th>
                                <th>V</th>
                                <th>D</th>
                                <th>PCT</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while (have_posts()) : the_post(); 
                                $team_id = get_the_ID();
                                $city = get_post_meta($team_id, '_city', true);
                                $stadium = get_post_meta($team_id, '_stadium', true);
                                
                                // Calculate wins and losses from ALL games
                                $all_games = get_posts(array(
                                    'post_type' => 'game',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                        'relation' => 'OR',
                                        array(
                                            'key' => '_game_home_team',
                                            'value' => $team_id,
                                            'compare' => '='
                                        ),
                                        array(
                                            'key' => '_game_away_team',
                                            'value' => $team_id,
                                            'compare' => '='
                                        )
                                    )
                                ));
                                
                                $wins = 0;
                                $losses = 0;
                                
                                foreach ($all_games as $game) {
                                    $home_team = get_post_meta($game->ID, '_game_home_team', true);
                                    $away_team = get_post_meta($game->ID, '_game_away_team', true);
                                    $home_score = get_post_meta($game->ID, '_game_home_score', true);
                                    $away_score = get_post_meta($game->ID, '_game_away_score', true);
                                    
                                    // Only count if scores are set
                                    if ($home_score !== '' && $away_score !== '') {
                                        if ($home_team == $team_id) {
                                            // Team is home
                                            if ($home_score > $away_score) {
                                                $wins++;
                                            } else if ($home_score < $away_score) {
                                                $losses++;
                                            }
                                        } else if ($away_team == $team_id) {
                                            // Team is away
                                            if ($away_score > $home_score) {
                                                $wins++;
                                            } else if ($away_score < $home_score) {
                                                $losses++;
                                            }
                                        }
                                    }
                                }
                                
                                $total = $wins + $losses;
                                $pct = $total > 0 ? number_format(($wins / $total), 3) : '.000';
                                $team_name = get_the_title();
                                $team_abbr = strtoupper(substr($team_name, 0, 3));
                            ?>
                            <tr>
                                <td>
                                    <div class="team-name-cell">
                                        <?php if (has_post_thumbnail($team_id)): ?>
                                            <div class="team-mini-logo">
                                                <?php echo get_the_post_thumbnail($team_id, 'thumbnail'); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="team-name-text">
                                            <span class="team-full-name"><?php echo esc_html($team_name); ?></span>
                                            <span class="team-abbr-name"><?php echo esc_html($team_abbr); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo esc_html($city ?: 'N/A'); ?></td>
                                <td><?php echo esc_html($stadium ?: 'N/A'); ?></td>
                                <td class="stat-highlight"><?php echo esc_html($wins); ?></td>
                                <td><?php echo esc_html($losses); ?></td>
                                <td class="stat-highlight"><?php echo esc_html($pct); ?></td>
                                <td>
                                    <a href="<?php the_permalink(); ?>" class="btn-small">Ver</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay equipos registrados</h2>
                <p>Aún no se han añadido equipos al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
