<?php
/**
 * Single Team Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); 
            $team_id = get_the_ID();
            $founded_year = get_post_meta($team_id, '_founded_year', true);
            $stadium = get_post_meta($team_id, '_stadium', true);
            $city = get_post_meta($team_id, '_city', true);
            
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
            
            $total_games = $wins + $losses;
            $win_pct = $total_games > 0 ? number_format(($wins / $total_games) * 100, 1) : '0.0';
        ?>
        
        <div class="team-header">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('team-logo', array('class' => 'team-logo')); ?>
            <?php endif; ?>
            
            <div class="team-info">
                <h1><?php the_title(); ?></h1>
                <div class="team-meta">
                    <?php if ($city) : ?>
                        <p><strong>Ciudad:</strong> <?php echo esc_html($city); ?></p>
                    <?php endif; ?>
                    <?php if ($stadium) : ?>
                        <p><strong>Estadio:</strong> <?php echo esc_html($stadium); ?></p>
                    <?php endif; ?>
                    <?php if ($founded_year) : ?>
                        <p><strong>Fundado:</strong> <?php echo esc_html($founded_year); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <h2>Labor de por Vida</h2>
            <div class="stat-boxes">
                <div class="stat-box">
                    <div class="stat-label">Victorias</div>
                    <div class="stat-value"><?php echo esc_html($wins); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Derrotas</div>
                    <div class="stat-value"><?php echo esc_html($losses); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Partidos Jugados</div>
                    <div class="stat-value"><?php echo esc_html($total_games); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">% Victorias</div>
                    <div class="stat-value"><?php echo esc_html($win_pct); ?>%</div>
                </div>
            </div>
        </div>

        <?php if (get_the_content()) : ?>
        <div class="stats-card">
            <h2>Acerca del Equipo</h2>
            <div class="team-description">
                <?php the_content(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Get players from this team
        $players = get_posts(array(
            'post_type' => 'player',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_player_team',
                    'value' => $team_id,
                ),
            ),
        ));

        if ($players) : ?>
        <div class="stats-card">
            <h2>Roster del Equipo</h2>
            <div class="player-grid">
                <?php foreach ($players as $player) : 
                    $player_number = get_post_meta($player->ID, '_player_number', true);
                    $batting_avg = get_post_meta($player->ID, '_batting_avg', true);
                    $home_runs = get_post_meta($player->ID, '_home_runs', true);
                    $positions = wp_get_post_terms($player->ID, 'position');
                    $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                ?>
                <div class="player-card">
                    <?php echo get_the_post_thumbnail($player->ID, 'player-thumbnail', array('class' => 'player-photo')); ?>
                    <div class="player-number">#<?php echo esc_html($player_number); ?></div>
                    <h3 class="player-name"><?php echo esc_html($player->post_title); ?></h3>
                    <div class="player-position"><?php echo esc_html($position_name); ?></div>
                    <div class="stat-boxes">
                        <div class="stat-box">
                            <div class="stat-label">AVG</div>
                            <div class="stat-value"><?php echo esc_html($batting_avg ?: '.000'); ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">HR</div>
                            <div class="stat-value"><?php echo esc_html($home_runs ?: '0'); ?></div>
                        </div>
                    </div>
                    <a href="<?php echo get_permalink($player->ID); ?>" class="btn" style="margin-top: 15px;">Ver Perfil</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Get team tournaments
        $team_tournaments = get_post_meta($team_id, '_team_tournaments', true);
        if (!empty($team_tournaments) && is_array($team_tournaments)) : ?>
        <div class="stats-card">
            <h2>Torneos</h2>
            <div class="tournaments-list">
                <?php foreach ($team_tournaments as $tournament_id): 
                    $tournament = get_post($tournament_id);
                    if ($tournament):
                        $season_id = get_post_meta($tournament_id, '_tournament_season', true);
                        $team_stats = baseball_get_team_stats($team_id, $tournament_id);
                ?>
                    <div class="tournament-item">
                        <h3>
                            <a href="<?php echo get_permalink($tournament_id); ?>">
                                <?php echo esc_html($tournament->post_title); ?>
                            </a>
                        </h3>
                        <?php if ($season_id): ?>
                            <p><strong>Temporada:</strong> <?php echo get_the_title($season_id); ?></p>
                        <?php endif; ?>
                        <div class="stat-boxes">
                            <div class="stat-box">
                                <div class="stat-label">PJ</div>
                                <div class="stat-value"><?php echo $team_stats['games']; ?></div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">G</div>
                                <div class="stat-value"><?php echo $team_stats['wins']; ?></div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">P</div>
                                <div class="stat-value"><?php echo $team_stats['losses']; ?></div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-label">%</div>
                                <div class="stat-value"><?php echo $team_stats['win_pct']; ?></div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif;
                endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Get recent games
        global $wpdb;
        $recent_games = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title,
                   pm1.meta_value as home_team_id,
                   pm2.meta_value as away_team_id,
                   pm3.meta_value as home_score,
                   pm4.meta_value as away_score,
                   pm5.meta_value as game_date
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = '_game_home_team')
            LEFT JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = '_game_away_team')
            LEFT JOIN {$wpdb->postmeta} pm3 ON (p.ID = pm3.post_id AND pm3.meta_key = '_game_home_score')
            LEFT JOIN {$wpdb->postmeta} pm4 ON (p.ID = pm4.post_id AND pm4.meta_key = '_game_away_score')
            LEFT JOIN {$wpdb->postmeta} pm5 ON (p.ID = pm5.post_id AND pm5.meta_key = '_game_date')
            WHERE p.post_type = 'game' 
            AND p.post_status = 'publish'
            AND (pm1.meta_value = %d OR pm2.meta_value = %d)
            ORDER BY pm5.meta_value DESC
            LIMIT 10
        ", $team_id, $team_id));
        
        if ($recent_games) : ?>
        <div class="stats-card">
            <h2>Partidos Recientes</h2>
            <div class="games-list">
                <?php foreach ($recent_games as $game): 
                    $is_home = ($game->home_team_id == $team_id);
                    $opponent_id = $is_home ? $game->away_team_id : $game->home_team_id;
                    $team_score = $is_home ? $game->home_score : $game->away_score;
                    $opponent_score = $is_home ? $game->away_score : $game->home_score;
                    $result = '';
                    if ($team_score !== '' && $opponent_score !== '') {
                        if ($team_score > $opponent_score) {
                            $result = 'win';
                        } else if ($team_score < $opponent_score) {
                            $result = 'loss';
                        } else {
                            $result = 'tie';
                        }
                    }
                ?>
                    <div class="game-item <?php echo $result; ?>">
                        <div class="game-date">
                            <?php echo $game->game_date ? date('d/m/Y', strtotime($game->game_date)) : '-'; ?>
                        </div>
                        <div class="game-info">
                            <span class="location"><?php echo $is_home ? 'vs' : '@'; ?></span>
                            <span class="opponent"><?php echo get_the_title($opponent_id); ?></span>
                        </div>
                        <div class="game-score">
                            <?php if ($team_score !== '' && $opponent_score !== ''): ?>
                                <span class="<?php echo $result; ?>">
                                    <?php echo $team_score; ?> - <?php echo $opponent_score; ?>
                                    <?php if ($result == 'win'): ?>
                                        <strong>G</strong>
                                    <?php elseif ($result == 'loss'): ?>
                                        <strong>P</strong>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo get_permalink($game->ID); ?>" class="btn btn-small">Ver</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
