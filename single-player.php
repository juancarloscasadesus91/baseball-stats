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
            $on_base_percentage = get_post_meta($player_id, '_on_base_percentage', true);
            $home_runs = get_post_meta($player_id, '_home_runs', true);
            $runs = get_post_meta($player_id, '_runs', true);
            $rbis = get_post_meta($player_id, '_rbis', true);
            $hits = get_post_meta($player_id, '_hits', true);
            $at_bats = get_post_meta($player_id, '_at_bats', true);
            $doubles = get_post_meta($player_id, '_doubles', true);
            $triples = get_post_meta($player_id, '_triples', true);
            $errors = get_post_meta($player_id, '_errors', true);
            $walks = get_post_meta($player_id, '_walks', true);
            $hit_by_pitch = get_post_meta($player_id, '_hit_by_pitch', true);
            $grounded_into_dp = get_post_meta($player_id, '_grounded_into_dp', true);
            $sacrifice_flies = get_post_meta($player_id, '_sacrifice_flies', true);
            $reached_on_error = get_post_meta($player_id, '_reached_on_error', true);
            $fielders_choice = get_post_meta($player_id, '_fielders_choice', true);
            $strikeouts = get_post_meta($player_id, '_strikeouts', true);
            
            // Pitching stats
            $ip = get_post_meta($player_id, '_innings_pitched', true);
            $era = get_post_meta($player_id, '_era', true);
            $wins = get_post_meta($player_id, '_pitching_wins', true);
            $losses = get_post_meta($player_id, '_pitching_losses', true);
            $saves = get_post_meta($player_id, '_pitching_saves', true);
            $pitching_hits = get_post_meta($player_id, '_pitching_hits', true);
            $pitching_er = get_post_meta($player_id, '_pitching_earned_runs', true);
            $pitching_bb = get_post_meta($player_id, '_pitching_walks', true);
            $pitching_so = get_post_meta($player_id, '_pitching_strikeouts', true);
            
            // Calculate ERA if not set
            if ((empty($era) || floatval($era) == 0) && floatval($ip) > 0) {
                $era = number_format((floatval($pitching_er) * 9) / floatval($ip), 2);
            } else {
                $era = $era ? number_format(floatval($era), 2) : '0.00';
            }
            
            $positions = wp_get_post_terms($player_id, 'position');
            $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
            $team_name = $team_id ? get_the_title($team_id) : 'Agente Libre';

            $get_tournament_game_ids = function ($tournament_id) {
                if (!$tournament_id) {
                    return array();
                }

                return array_map('intval', get_posts(array(
                    'post_type'      => 'game',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'meta_query'     => array(
                        array(
                            'key'   => '_game_tournament',
                            'value' => intval($tournament_id),
                        ),
                    ),
                )));
            };

            $get_player_batting_totals = function ($game_ids) use ($player_id) {
                global $wpdb;

                $totals = (object) array(
                    'games' => 0,
                    'ab'    => 0,
                    'h'     => 0,
                    'hr'    => 0,
                    'r'     => 0,
                    'rbi'   => 0,
                    'bb'    => 0,
                    'hbp'   => 0,
                    'so'    => 0,
                    'd'     => 0,
                    't'     => 0,
                    'gidp'  => 0,
                    'sf'    => 0,
                    'roe'   => 0,
                    'fc'    => 0,
                    'e'     => 0,
                );

                $game_ids = array_values(array_unique(array_map('intval', $game_ids)));
                if (empty($game_ids)) {
                    return $totals;
                }

                $table = $wpdb->prefix . 'baseball_game_stats';
                $placeholders = implode(',', array_fill(0, count($game_ids), '%d'));
                $sql = "SELECT
                        COUNT(DISTINCT game_id) AS games,
                        SUM(at_bats) AS ab,
                        SUM(hits) AS h,
                        SUM(home_runs) AS hr,
                        SUM(runs) AS r,
                        SUM(rbis) AS rbi,
                        SUM(walks) AS bb,
                        SUM(hit_by_pitch) AS hbp,
                        SUM(strikeouts) AS so,
                        SUM(doubles) AS d,
                        SUM(triples) AS t,
                        SUM(grounded_into_dp) AS gidp,
                        SUM(sacrifice_flies) AS sf,
                        SUM(reached_on_error) AS roe,
                        SUM(fielders_choice) AS fc,
                        SUM(errors) AS e
                    FROM $table
                    WHERE player_id = %d AND game_id IN ($placeholders)";

                $prepared = $wpdb->prepare($sql, array_merge(array($player_id), $game_ids));
                $row = $wpdb->get_row($prepared);

                return $row ?: $totals;
            };

            $get_player_pitching_totals = function ($game_ids) use ($player_id) {
                $totals = array(
                    'ip'     => 0,
                    'h'      => 0,
                    'r'      => 0,
                    'er'     => 0,
                    'bb'     => 0,
                    'so'     => 0,
                    'wins'   => 0,
                    'losses' => 0,
                    'saves'  => 0,
                );

                foreach (array_unique(array_map('intval', $game_ids)) as $gid) {
                    $home = get_post_meta($gid, '_game_home_pitchers', true) ?: array();
                    $away = get_post_meta($gid, '_game_away_pitchers', true) ?: array();

                    foreach (array_merge($home, $away) as $pitcher) {
                        if (empty($pitcher['player_id']) || intval($pitcher['player_id']) !== intval($player_id)) {
                            continue;
                        }

                        $totals['ip'] += floatval($pitcher['ip'] ?? 0);
                        $totals['h'] += intval($pitcher['h'] ?? 0);
                        $totals['r'] += intval($pitcher['r'] ?? 0);
                        $totals['er'] += intval($pitcher['er'] ?? 0);
                        $totals['bb'] += intval($pitcher['bb'] ?? 0);
                        $totals['so'] += intval($pitcher['so'] ?? 0);

                        $decision = $pitcher['decision'] ?? '';
                        if ($decision === 'W') { $totals['wins']++; }
                        if ($decision === 'L') { $totals['losses']++; }
                        if ($decision === 'SV') { $totals['saves']++; }
                    }
                }

                return $totals;
            };

            $render_batting_boxes = function ($totals) {
                $ab = intval($totals->ab ?? 0);
                $hits_total = intval($totals->h ?? 0);
                $avg = baseball_format_rate($hits_total, $ab);
                $obp = baseball_calculate_obp($hits_total, intval($totals->bb ?? 0), intval($totals->hbp ?? 0), $ab, intval($totals->sf ?? 0));
                ?>
                <div class="stat-boxes">
                    <div class="stat-box"><div class="stat-label">Promedio (AVG)</div><div class="stat-value"><?php echo esc_html($avg); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Embasado (OBP)</div><div class="stat-value"><?php echo esc_html($obp); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Juegos (J)</div><div class="stat-value"><?php echo esc_html(intval($totals->games ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Turnos al Bate (AB)</div><div class="stat-value"><?php echo esc_html($ab); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Hits (H)</div><div class="stat-value"><?php echo esc_html($hits_total); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Home Runs (HR)</div><div class="stat-value"><?php echo esc_html(intval($totals->hr ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Carreras Anotadas (R)</div><div class="stat-value"><?php echo esc_html(intval($totals->r ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Carreras Impulsadas (RBI)</div><div class="stat-value"><?php echo esc_html(intval($totals->rbi ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Dobles (2B)</div><div class="stat-value"><?php echo esc_html(intval($totals->d ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Triples (3B)</div><div class="stat-value"><?php echo esc_html(intval($totals->t ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Bases por Bolas (BB)</div><div class="stat-value"><?php echo esc_html(intval($totals->bb ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Golpeado (HBP)</div><div class="stat-value"><?php echo esc_html(intval($totals->hbp ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Ponches (SO)</div><div class="stat-value"><?php echo esc_html(intval($totals->so ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Doble Play (GIDP)</div><div class="stat-value"><?php echo esc_html(intval($totals->gidp ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Fly Sacrificio (SF)</div><div class="stat-value"><?php echo esc_html(intval($totals->sf ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Embasado por Error (ROE)</div><div class="stat-value"><?php echo esc_html(intval($totals->roe ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Bola Ocupada (FC)</div><div class="stat-value"><?php echo esc_html(intval($totals->fc ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Errores (E)</div><div class="stat-value"><?php echo esc_html(intval($totals->e ?? 0)); ?></div></div>
                </div>
                <?php
            };

            $render_pitching_boxes = function ($totals) {
                $ip_total = floatval($totals['ip'] ?? 0);
                $era_total = $ip_total > 0 ? number_format((floatval($totals['er'] ?? 0) * 9) / $ip_total, 2) : '0.00';
                ?>
                <div class="stat-boxes">
                    <div class="stat-box"><div class="stat-label">Efectividad (ERA)</div><div class="stat-value"><?php echo esc_html($era_total); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Victorias (W)</div><div class="stat-value"><?php echo esc_html(intval($totals['wins'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Derrotas (L)</div><div class="stat-value"><?php echo esc_html(intval($totals['losses'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Salvados (SV)</div><div class="stat-value"><?php echo esc_html(intval($totals['saves'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Innings Lanzados (IP)</div><div class="stat-value"><?php echo esc_html(number_format($ip_total, 1)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Hits Permitidos (H)</div><div class="stat-value"><?php echo esc_html(intval($totals['h'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Carreras Permitidas (R)</div><div class="stat-value"><?php echo esc_html(intval($totals['r'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Carreras Limpias (ER)</div><div class="stat-value"><?php echo esc_html(intval($totals['er'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Bases por Bolas (BB)</div><div class="stat-value"><?php echo esc_html(intval($totals['bb'] ?? 0)); ?></div></div>
                    <div class="stat-box"><div class="stat-label">Ponches (SO)</div><div class="stat-value"><?php echo esc_html(intval($totals['so'] ?? 0)); ?></div></div>
                </div>
                <?php
            };
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
                    <div class="stat-label">Embasado (OBP)</div>
                    <div class="stat-value"><?php echo esc_html($on_base_percentage ?: '.000'); ?></div>
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
                    <div class="stat-label">Carreras Anotadas (R)</div>
                    <div class="stat-value"><?php echo esc_html($runs ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Carreras Impulsadas (RBI)</div>
                    <div class="stat-value"><?php echo esc_html($rbis ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Dobles (2B)</div>
                    <div class="stat-value"><?php echo esc_html($doubles ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Triples (3B)</div>
                    <div class="stat-value"><?php echo esc_html($triples ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Errores (E)</div>
                    <div class="stat-value"><?php echo esc_html($errors ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Bases por Bolas (BB)</div>
                    <div class="stat-value"><?php echo esc_html($walks ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Golpeado (HBP)</div>
                    <div class="stat-value"><?php echo esc_html($hit_by_pitch ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Doble Play (GIDP)</div>
                    <div class="stat-value"><?php echo esc_html($grounded_into_dp ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Fly Sacrificio (SF)</div>
                    <div class="stat-value"><?php echo esc_html($sacrifice_flies ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Embasado por Error (ROE)</div>
                    <div class="stat-value"><?php echo esc_html($reached_on_error ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Bola Ocupada (FC)</div>
                    <div class="stat-value"><?php echo esc_html($fielders_choice ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Ponches (SO)</div>
                    <div class="stat-value"><?php echo esc_html($strikeouts ?: '0'); ?></div>
                </div>
            </div>
        </div>

        <?php if ($ip && floatval($ip) > 0) : ?>
        <div class="stats-card">
            <h2>Estadísticas de Pitcheo</h2>
            <div class="stat-boxes">
                <div class="stat-box">
                    <div class="stat-label">Efectividad (ERA)</div>
                    <div class="stat-value"><?php echo esc_html($era); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Victorias (W)</div>
                    <div class="stat-value"><?php echo esc_html($wins ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Derrotas (L)</div>
                    <div class="stat-value"><?php echo esc_html($losses ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Salvados (SV)</div>
                    <div class="stat-value"><?php echo esc_html($saves ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Innings Lanzados (IP)</div>
                    <div class="stat-value"><?php echo number_format(floatval($ip), 1); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Hits Permitidos (H)</div>
                    <div class="stat-value"><?php echo esc_html($pitching_hits ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Carreras Limpias (ER)</div>
                    <div class="stat-value"><?php echo esc_html($pitching_er ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Bases por Bolas (BB)</div>
                    <div class="stat-value"><?php echo esc_html($pitching_bb ?: '0'); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Ponches (SO)</div>
                    <div class="stat-value"><?php echo esc_html($pitching_so ?: '0'); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $game_stats = baseball_get_player_game_stats($player_id);
        $player_game_ids = array();
        $player_tournaments = array();
        $player_seasons = array();
        $player_opponents = array();

        foreach ($game_stats as $stat) {
            $gid = intval($stat->game_id);
            $player_game_ids[] = $gid;

            $tournament_id = intval(get_post_meta($gid, '_game_tournament', true));
            if ($tournament_id) {
                $player_tournaments[$tournament_id] = get_the_title($tournament_id);
                $season_for_tournament = intval(get_post_meta($tournament_id, '_tournament_season', true));
                if ($season_for_tournament) {
                    $player_seasons[$season_for_tournament] = get_the_title($season_for_tournament);
                }
            }

            $opponent_team_id = intval($stat->home_team_id) === intval($stat->team_id) ? intval($stat->away_team_id) : intval($stat->home_team_id);
            if ($opponent_team_id) {
                $player_opponents[$opponent_team_id] = get_the_title($opponent_team_id);
            }
        }

        asort($player_tournaments);
        asort($player_seasons);
        asort($player_opponents);

        $selected_stats_tournament = isset($_GET['player_stats_tournament']) ? intval($_GET['player_stats_tournament']) : intval(array_key_first($player_tournaments));
        $selected_stats_season = isset($_GET['player_stats_season']) ? intval($_GET['player_stats_season']) : intval(array_key_first($player_seasons));
        $active_scope_tab = isset($_GET['player_stats_season']) ? 'season' : 'tournament';
        $tournament_stat_game_ids = $selected_stats_tournament ? array_intersect($player_game_ids, $get_tournament_game_ids($selected_stats_tournament)) : array();
        $season_stat_game_ids = $selected_stats_season && function_exists('baseball_get_season_game_ids') ? array_intersect($player_game_ids, baseball_get_season_game_ids($selected_stats_season)) : array();
        ?>

        <?php if (!empty($player_tournaments) || !empty($player_seasons)) : ?>
        <div class="stats-card tournament-stats-card player-scope-stats-card">
            <h2>Estad&iacute;sticas por Torneo y Temporada</h2>
            <div class="players-tabs tournament-tabs">
                <button class="players-tab <?php echo $active_scope_tab === 'tournament' ? 'active' : ''; ?>" data-tab="player-tournament">Por Torneo</button>
                <button class="players-tab <?php echo $active_scope_tab === 'season' ? 'active' : ''; ?>" data-tab="player-season">Por Temporada</button>
            </div>

            <div class="players-tab-content <?php echo $active_scope_tab === 'tournament' ? 'active' : ''; ?>" id="player-tournament-stats">
                <form class="game-filters player-scope-filter" method="get">
                    <div class="game-filter-field">
                        <label for="player-stats-tournament">Torneo</label>
                        <select id="player-stats-tournament" name="player_stats_tournament" onchange="this.form.submit()">
                            <?php foreach ($player_tournaments as $tid => $title) : ?>
                                <option value="<?php echo esc_attr($tid); ?>" <?php selected($selected_stats_tournament, $tid); ?>>
                                    <?php echo esc_html($title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <h3>Bateo</h3>
                <?php $render_batting_boxes($get_player_batting_totals($tournament_stat_game_ids)); ?>
                <h3>Pitcheo</h3>
                <?php $render_pitching_boxes($get_player_pitching_totals($tournament_stat_game_ids)); ?>
            </div>

            <div class="players-tab-content <?php echo $active_scope_tab === 'season' ? 'active' : ''; ?>" id="player-season-stats">
                <form class="game-filters player-scope-filter" method="get">
                    <div class="game-filter-field">
                        <label for="player-stats-season">Temporada</label>
                        <select id="player-stats-season" name="player_stats_season" onchange="this.form.submit()">
                            <?php foreach ($player_seasons as $sid => $title) : ?>
                                <option value="<?php echo esc_attr($sid); ?>" <?php selected($selected_stats_season, $sid); ?>>
                                    <?php echo esc_html($title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>

                <h3>Bateo</h3>
                <?php $render_batting_boxes($get_player_batting_totals($season_stat_game_ids)); ?>
                <h3>Pitcheo</h3>
                <?php $render_pitching_boxes($get_player_pitching_totals($season_stat_game_ids)); ?>
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
        $selected_game_season = isset($_GET['player_game_season']) ? intval($_GET['player_game_season']) : 0;
        $selected_game_tournament = isset($_GET['player_game_tournament']) ? intval($_GET['player_game_tournament']) : 0;
        $selected_game_opponent = isset($_GET['player_game_opponent']) ? intval($_GET['player_game_opponent']) : 0;

        $filtered_game_stats = array_values(array_filter($game_stats, function ($stat) use ($selected_game_season, $selected_game_tournament, $selected_game_opponent) {
            $gid = intval($stat->game_id);
            $game_tournament_id = intval(get_post_meta($gid, '_game_tournament', true));
            $game_season_id = $game_tournament_id ? intval(get_post_meta($game_tournament_id, '_tournament_season', true)) : 0;
            $opponent_team_id = intval($stat->home_team_id) === intval($stat->team_id) ? intval($stat->away_team_id) : intval($stat->home_team_id);

            if ($selected_game_season && $game_season_id !== $selected_game_season) {
                return false;
            }

            if ($selected_game_tournament && $game_tournament_id !== $selected_game_tournament) {
                return false;
            }

            if ($selected_game_opponent && $opponent_team_id !== $selected_game_opponent) {
                return false;
            }

            return true;
        }));

        $filtered_game_ids = array_map(function ($stat) {
            return intval($stat->game_id);
        }, $filtered_game_stats);
        $filtered_batting_summary = $get_player_batting_totals($filtered_game_ids);
        $filtered_pitching_summary = $get_player_pitching_totals($filtered_game_ids);
        
        if ($game_stats) : ?>
        <div class="stats-card">
            <h2>Estad&iacute;sticas por Partido</h2>
            <form class="game-filters player-game-filters" method="get">
                <div class="game-filter-field">
                    <label for="player-game-season">Temporada</label>
                    <select id="player-game-season" name="player_game_season">
                        <option value="">Todas</option>
                        <?php foreach ($player_seasons as $sid => $title) : ?>
                            <option value="<?php echo esc_attr($sid); ?>" <?php selected($selected_game_season, $sid); ?>>
                                <?php echo esc_html($title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="game-filter-field">
                    <label for="player-game-tournament">Torneo</label>
                    <select id="player-game-tournament" name="player_game_tournament">
                        <option value="">Todos</option>
                        <?php foreach ($player_tournaments as $tid => $title) : ?>
                            <option value="<?php echo esc_attr($tid); ?>" <?php selected($selected_game_tournament, $tid); ?>>
                                <?php echo esc_html($title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="game-filter-field">
                    <label for="player-game-opponent">Equipo en contra</label>
                    <select id="player-game-opponent" name="player_game_opponent">
                        <option value="">Todos</option>
                        <?php foreach ($player_opponents as $oid => $title) : ?>
                            <option value="<?php echo esc_attr($oid); ?>" <?php selected($selected_game_opponent, $oid); ?>>
                                <?php echo esc_html($title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="game-filter-actions">
                    <button type="submit" class="btn">Filtrar</button>
                    <a href="<?php echo esc_url(get_permalink($player_id)); ?>" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>

            <div class="game-vs-summary player-game-summary">
                <div class="game-vs-summary-title">Resumen filtrado</div>
                <?php $render_batting_boxes($filtered_batting_summary); ?>
                <?php if (floatval($filtered_pitching_summary['ip']) > 0) : ?>
                    <h3>Pitcheo</h3>
                    <?php $render_pitching_boxes($filtered_pitching_summary); ?>
                <?php endif; ?>
            </div>

            <?php if (empty($filtered_game_stats)) : ?>
                <p class="no-content"><em>No hay partidos para los filtros seleccionados.</em></p>
            <?php else : ?>
            <div class="table-responsive">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Equipo</th>
                            <th>Partido</th>
                            <th>AB</th>
                            <th>H</th>
                            <th>AVG</th>
                            <th>OBP</th>
                            <th>HR</th>
                            <th>R</th>
                            <th>RBI</th>
                            <th>BB</th>
                            <th>HBP</th>
                            <th>SO</th>
                            <th>GIDP</th>
                            <th>SF</th>
                            <th>ROE</th>
                            <th>FC</th>
                            <th>2B</th>
                            <th>3B</th>
                            <th>E</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filtered_game_stats as $stat): 
                            $game_avg = baseball_format_rate($stat->hits, $stat->at_bats);
                            $game_obp = baseball_calculate_obp($stat->hits, $stat->walks, $stat->hit_by_pitch ?? 0, $stat->at_bats, $stat->sacrifice_flies ?? 0);
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
                            <td><?php echo $game_obp; ?></td>
                            <td><?php echo $stat->home_runs; ?></td>
                            <td><?php echo $stat->runs; ?></td>
                            <td><?php echo $stat->rbis; ?></td>
                            <td><?php echo $stat->walks; ?></td>
                            <td><?php echo intval($stat->hit_by_pitch ?? 0); ?></td>
                            <td><?php echo $stat->strikeouts; ?></td>
                            <td><?php echo intval($stat->grounded_into_dp ?? 0); ?></td>
                            <td><?php echo intval($stat->sacrifice_flies ?? 0); ?></td>
                            <td><?php echo intval($stat->reached_on_error ?? 0); ?></td>
                            <td><?php echo intval($stat->fielders_choice ?? 0); ?></td>
                            <td><?php echo $stat->doubles; ?></td>
                            <td><?php echo $stat->triples; ?></td>
                            <td><?php echo $stat->errors; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <p><em>AB = Turnos al Bate, H = Hits, AVG = Promedio, OBP = Porcentaje de Embasado, HR = Home Runs, R = Carreras Anotadas, RBI = Carreras Impulsadas, BB = Bases por Bolas, HBP = Golpeado por Lanzamiento, SO = Ponches, GIDP = Batea para Doble Play, SF = Fly de Sacrificio, ROE = Embasado por Error, FC = Bola Ocupada, 2B = Dobles, 3B = Triples, E = Errores</em></p>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.player-scope-stats-card .players-tabs').forEach(function (tabGroup) {
        var tabs = tabGroup.querySelectorAll('.players-tab');
        var scope = tabGroup.closest('.player-scope-stats-card');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var name = this.getAttribute('data-tab');

                tabs.forEach(function (item) { item.classList.remove('active'); });
                scope.querySelectorAll('.players-tab-content').forEach(function (content) {
                    content.classList.remove('active');
                });

                this.classList.add('active');
                var panel = scope.querySelector('#' + name + '-stats');
                if (panel) {
                    panel.classList.add('active');
                }
            });
        });
    });
});
</script>

<?php get_footer(); ?>
