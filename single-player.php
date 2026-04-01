<?php
/**
 * Single Player Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); 
            $player_id = get_the_ID();
            $player_number = get_post_meta($player_id, '_player_number', true);
            $team_id = get_post_meta($player_id, '_player_team', true);
            $batting_avg = get_post_meta($player_id, '_batting_avg', true);
            $home_runs = get_post_meta($player_id, '_home_runs', true);
            $rbis = get_post_meta($player_id, '_rbis', true);
            $hits = get_post_meta($player_id, '_hits', true);
            $at_bats = get_post_meta($player_id, '_at_bats', true);
            $stolen_bases = get_post_meta($player_id, '_stolen_bases', true);
            $era = get_post_meta($player_id, '_era', true);
            $wins = get_post_meta($player_id, '_wins', true);
            $losses = get_post_meta($player_id, '_losses', true);
            $strikeouts = get_post_meta($player_id, '_strikeouts', true);
            $positions = wp_get_post_terms($player_id, 'position');
            $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
            $team_name = $team_id ? get_the_title($team_id) : 'Agente Libre';
        ?>
        
        <div class="team-header">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('large', array('class' => 'team-logo')); ?>
            <?php endif; ?>
            
            <div class="team-info">
                <h1><?php the_title(); ?></h1>
                <div class="team-meta">
                    <span class="badge">#<?php echo esc_html($player_number); ?></span>
                    <span class="badge badge-success"><?php echo esc_html($position_name); ?></span>
                    <span class="badge badge-warning"><?php echo esc_html($team_name); ?></span>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <h2>Estadísticas de Bateo</h2>
            <div class="stat-boxes">
                <div class="stat-box">
                    <div class="stat-label">Promedio (AVG)</div>
                    <div class="stat-value"><?php echo esc_html($batting_avg ?: '.000'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Turnos al Bate (AB)</div>
                    <div class="stat-value"><?php echo esc_html($at_bats ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Hits (H)</div>
                    <div class="stat-value"><?php echo esc_html($hits ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Home Runs (HR)</div>
                    <div class="stat-value"><?php echo esc_html($home_runs ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Carreras Impulsadas (RBI)</div>
                    <div class="stat-value"><?php echo esc_html($rbis ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Bases Robadas (SB)</div>
                    <div class="stat-value"><?php echo esc_html($stolen_bases ?: '0'); ?></div>
                </div>
            </div>
        </div>

        <?php if ($wins || $losses || $era || $strikeouts) : ?>
        <div class="stats-card">
            <h2>Estadísticas de Pitcheo</h2>
            <div class="stat-boxes">
                <div class="stat-box">
                    <div class="stat-label">Victorias (W)</div>
                    <div class="stat-value"><?php echo esc_html($wins ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Derrotas (L)</div>
                    <div class="stat-value"><?php echo esc_html($losses ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Efectividad (ERA)</div>
                    <div class="stat-value"><?php echo esc_html($era ?: '0.00'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Ponches (K)</div>
                    <div class="stat-value"><?php echo esc_html($strikeouts ?: '0'); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (get_the_content()) : ?>
        <div class="stats-card">
            <h2>Biografía</h2>
            <div class="player-bio">
                <?php the_content(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Get player game-by-game stats
        $game_stats = baseball_get_player_game_stats($player_id);
        
        if ($game_stats) : ?>
        <div class="stats-card">
            <h2>Estadísticas por Partido</h2>
            <div style="overflow-x: auto;">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Equipo</th>
                            <th>Partido</th>
                            <th>AB</th>
                            <th>H</th>
                            <th>AVG</th>
                            <th>HR</th>
                            <th>RBI</th>
                            <th>BB</th>
                            <th>SO</th>
                            <th>SB</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($game_stats as $stat): 
                            $game_avg = $stat->at_bats > 0 ? number_format($stat->hits / $stat->at_bats, 3) : '.000';
                            $opponent_team_id = ($stat->home_team_id == $stat->team_id) ? $stat->away_team_id : $stat->home_team_id;
                            $vs_label = ($stat->home_team_id == $stat->team_id) ? 'vs' : '@';
                        ?>
                        <tr>
                            <td><?php echo $stat->game_date ? date('d/m/Y', strtotime($stat->game_date)) : '-'; ?></td>
                            <td><strong><?php echo esc_html($stat->team_name); ?></strong></td>
                            <td>
                                <a href="<?php echo get_permalink($stat->game_id); ?>">
                                    <?php echo $vs_label; ?> <?php echo get_the_title($opponent_team_id); ?>
                                </a>
                            </td>
                            <td><?php echo $stat->at_bats; ?></td>
                            <td><?php echo $stat->hits; ?></td>
                            <td><?php echo $game_avg; ?></td>
                            <td><?php echo $stat->home_runs; ?></td>
                            <td><?php echo $stat->rbis; ?></td>
                            <td><?php echo $stat->walks; ?></td>
                            <td><?php echo $stat->strikeouts; ?></td>
                            <td><?php echo $stat->stolen_bases; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p><em>AB = Turnos al Bate, H = Hits, AVG = Promedio, HR = Home Runs, RBI = Carreras Impulsadas, BB = Bases por Bolas, SO = Ponches, SB = Bases Robadas</em></p>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
