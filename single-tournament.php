<?php
/**
 * Single Tournament Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main">
    <div class="container">
    <?php while (have_posts()) : the_post(); 
        $season_id = get_post_meta(get_the_ID(), '_tournament_season', true);
        $start_date = get_post_meta(get_the_ID(), '_tournament_start_date', true);
        $end_date = get_post_meta(get_the_ID(), '_tournament_end_date', true);
        
        // Get games in this tournament
        $games = get_posts(array(
            'post_type' => 'game',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_game_tournament',
                    'value' => get_the_ID(),
                    'compare' => '='
                )
            ),
            'orderby' => 'meta_value',
            'meta_key' => '_game_date',
            'order' => 'DESC'
        ));
        
        $tournament_game_ids = wp_list_pluck($games, 'ID');
        
        // Get teams from games in this tournament
        $team_ids = array();
        foreach ($games as $game) {
            $home_team = get_post_meta($game->ID, '_game_home_team', true);
            $away_team = get_post_meta($game->ID, '_game_away_team', true);
            if ($home_team) $team_ids[] = $home_team;
            if ($away_team) $team_ids[] = $away_team;
        }
        $team_ids = array_unique($team_ids);
        
        // Get team objects
        $teams = array();
        if (!empty($team_ids)) {
            $teams = get_posts(array(
                'post_type' => 'team',
                'posts_per_page' => -1,
                'post__in' => $team_ids,
                'orderby' => 'title',
                'order' => 'ASC'
            ));
        }

        $team_batting_defaults = array(
            'games' => 0,
            'ab' => 0,
            'avg' => '.000',
            'h' => 0,
            'hr' => 0,
            'rbi' => 0,
            'r' => 0,
            'd' => 0,
            't' => 0,
            'bb' => 0,
            'hbp' => 0,
            'obp' => '.000',
            'slg' => '.000',
            'ops' => '.000',
            'so' => 0,
            'gidp' => 0,
            'sf' => 0,
            'roe' => 0,
            'fc' => 0,
            'e' => 0,
        );
        $team_batting_stats = array();
        $team_pitching_defaults = array(
            'era' => '0.00',
            'wins' => 0,
            'losses' => 0,
            'saves' => 0,
            'ip' => 0,
            'h' => 0,
            'r' => 0,
            'er' => 0,
            'bb' => 0,
            'so' => 0,
        );
        $team_pitching_stats = array();

        if (!empty($tournament_game_ids)) {
            global $wpdb;

            $stats_table = $wpdb->prefix . 'baseball_game_stats';
            $game_placeholders = implode(',', array_fill(0, count($tournament_game_ids), '%d'));
            $team_batting_rows = $wpdb->get_results($wpdb->prepare(
                "SELECT team_id,
                    COUNT(DISTINCT game_id) AS games,
                    SUM(at_bats) AS ab,
                    SUM(hits) AS h,
                    SUM(home_runs) AS hr,
                    SUM(rbis) AS rbi,
                    SUM(runs) AS r,
                    SUM(doubles) AS d,
                    SUM(triples) AS t,
                    SUM(walks) AS bb,
                    SUM(hit_by_pitch) AS hbp,
                    SUM(strikeouts) AS so,
                    SUM(grounded_into_dp) AS gidp,
                    SUM(sacrifice_flies) AS sf,
                    SUM(reached_on_error) AS roe,
                    SUM(fielders_choice) AS fc,
                    SUM(errors) AS e
                FROM $stats_table
                WHERE game_id IN ($game_placeholders)
                GROUP BY team_id",
                array_map('intval', $tournament_game_ids)
            ));

            foreach ($team_batting_rows as $row) {
                $team_batting_stats[intval($row->team_id)] = array(
                    'games' => intval($row->games),
                    'ab' => intval($row->ab),
                    'avg' => baseball_format_rate(intval($row->h), intval($row->ab)),
                    'h' => intval($row->h),
                    'hr' => intval($row->hr),
                    'rbi' => intval($row->rbi),
                    'r' => intval($row->r),
                    'd' => intval($row->d),
                    't' => intval($row->t),
                    'bb' => intval($row->bb),
                    'hbp' => intval($row->hbp),
                    'obp' => baseball_calculate_obp($row->h, $row->bb, $row->hbp, $row->ab, $row->sf),
                    'slg' => baseball_calculate_slg($row->h, $row->d, $row->t, $row->hr, $row->ab),
                    'ops' => baseball_calculate_ops($row->h, $row->d, $row->t, $row->hr, $row->bb, $row->hbp, $row->ab, $row->sf),
                    'so' => intval($row->so),
                    'gidp' => intval($row->gidp),
                    'sf' => intval($row->sf),
                    'roe' => intval($row->roe),
                    'fc' => intval($row->fc),
                    'e' => intval($row->e),
                );
            }

            foreach ($games as $game) {
                $home_team_id = intval(get_post_meta($game->ID, '_game_home_team', true));
                $away_team_id = intval(get_post_meta($game->ID, '_game_away_team', true));
                $home_pitchers = get_post_meta($game->ID, '_game_home_pitchers', true) ?: array();
                $away_pitchers = get_post_meta($game->ID, '_game_away_pitchers', true) ?: array();
                $pitching_groups = array(
                    $home_team_id => $home_pitchers,
                    $away_team_id => $away_pitchers,
                );

                foreach ($pitching_groups as $team_id => $pitchers) {
                    if (!$team_id) {
                        continue;
                    }

                    if (!isset($team_pitching_stats[$team_id])) {
                        $team_pitching_stats[$team_id] = $team_pitching_defaults;
                    }

                    foreach ($pitchers as $pitcher) {
                        $team_pitching_stats[$team_id]['ip'] += floatval($pitcher['ip'] ?? 0);
                        $team_pitching_stats[$team_id]['h'] += intval($pitcher['h'] ?? 0);
                        $team_pitching_stats[$team_id]['r'] += intval($pitcher['r'] ?? 0);
                        $team_pitching_stats[$team_id]['er'] += intval($pitcher['er'] ?? 0);
                        $team_pitching_stats[$team_id]['bb'] += intval($pitcher['bb'] ?? 0);
                        $team_pitching_stats[$team_id]['so'] += intval($pitcher['so'] ?? 0);

                        $decision = isset($pitcher['decision']) ? $pitcher['decision'] : '';
                        if ($decision === 'W') {
                            $team_pitching_stats[$team_id]['wins']++;
                        } elseif ($decision === 'L') {
                            $team_pitching_stats[$team_id]['losses']++;
                        } elseif ($decision === 'S') {
                            $team_pitching_stats[$team_id]['saves']++;
                        }
                    }
                }
            }

            foreach ($team_pitching_stats as $team_id => $pitching_stats) {
                $team_pitching_stats[$team_id]['era'] = $pitching_stats['ip'] > 0
                    ? number_format(($pitching_stats['er'] * 9) / $pitching_stats['ip'], 2)
                    : '0.00';
                $team_pitching_stats[$team_id]['ip'] = number_format($pitching_stats['ip'], 1);
            }
        }

        $comparison_teams = array_values($teams);
        $comparison_team_a = !empty($comparison_teams) ? intval($comparison_teams[0]->ID) : 0;
        $comparison_team_b = isset($comparison_teams[1]) ? intval($comparison_teams[1]->ID) : $comparison_team_a;
        $comparison_batting_metrics = baseball_get_team_comparison_batting_metrics();
        $comparison_pitching_metrics = baseball_get_team_comparison_pitching_metrics();
        $team_record_stats = baseball_get_team_records_for_games($tournament_game_ids);
        $comparison_data = baseball_get_team_comparison_data($comparison_teams, $team_batting_stats, $team_pitching_stats, $team_record_stats);
        $comparison_matchup_data = baseball_get_team_matchup_comparison_data($comparison_teams, $tournament_game_ids);
        
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <?php if (has_post_thumbnail()): ?>
                <div class="tournament-logo-large">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php endif; ?>
            
            <h1 class="entry-title"><?php the_title(); ?></h1>
            
            <div class="game-info">
                <?php if ($season_id): ?>
                    <div class="game-tournament">
                        <strong>Temporada:</strong> 
                        <a href="<?php echo get_permalink($season_id); ?>">
                            <?php echo get_the_title($season_id); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($start_date && $end_date): ?>
                    <div class="game-datetime">
                        <strong>Período:</strong> 
                        <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                        <?php echo date('d/m/Y', strtotime($end_date)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <?php if (get_the_content()): ?>
            <div class="stats-card">
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($teams): ?>
            <div class="stats-card tournament-section-card tournament-standings-card">
                <section class="tournament-standings">
                    <h2>Tabla de Posiciones</h2>
                    <div class="table-responsive">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Pos</th>
                                    <th>Equipo</th>
                                    <th>PJ</th>
                                    <th>G</th>
                                    <th>P</th>
                                    <th>%</th>
                                    <th>CF</th>
                                    <th>CC</th>
                                    <th>AVG</th>
                                    <th>H</th>
                                    <th>HR</th>
                                    <th>2B</th>
                                    <th>3B</th>
                                    <th>BB</th>
                                    <th>SLG</th>
                                    <th>SO</th>
                                    <th>E</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $standings = array();
                                foreach ($teams as $team) {
                                    $stats = baseball_get_team_stats($team->ID, get_the_ID());
                                    $standings[] = array(
                                        'team' => $team,
                                        'stats' => $stats
                                    );
                                }
                                
                                // Sort by wins
                                usort($standings, function($a, $b) {
                                    if ($a['stats']['wins'] == $b['stats']['wins']) {
                                        return $b['stats']['runs_scored'] - $a['stats']['runs_scored'];
                                    }
                                    return $b['stats']['wins'] - $a['stats']['wins'];
                                });
                                
                                $pos = 1;
                                foreach ($standings as $standing): 
                                    $team = $standing['team'];
                                    $stats = $standing['stats'];
                                    $team_name = $team->post_title;
                                    $team_abbr = strtoupper(substr($team_name, 0, 3));
                                    $batting_stats = isset($team_batting_stats[$team->ID]) ? $team_batting_stats[$team->ID] : $team_batting_defaults;
                                ?>
                                <tr>
                                    <td data-value="<?php echo esc_attr($pos); ?>"><?php echo $pos++; ?></td>
                                    <td data-value="<?php echo esc_attr($team_name); ?>">
                                        <div class="team-name-cell">
                                            <?php if (has_post_thumbnail($team->ID)): ?>
                                                <div class="team-mini-logo">
                                                    <?php echo get_the_post_thumbnail($team->ID, 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="team-name-text">
                                                <a href="<?php echo get_permalink($team->ID); ?>">
                                                    <span class="team-full-name"><?php echo esc_html($team_name); ?></span>
                                                    <span class="team-abbr-name"><?php echo esc_html($team_abbr); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-value="<?php echo esc_attr($stats['games']); ?>"><?php echo $stats['games']; ?></td>
                                    <td data-value="<?php echo esc_attr($stats['wins']); ?>"><?php echo $stats['wins']; ?></td>
                                    <td data-value="<?php echo esc_attr($stats['losses']); ?>"><?php echo $stats['losses']; ?></td>
                                    <td data-value="<?php echo esc_attr($stats['win_pct']); ?>"><?php echo $stats['win_pct']; ?></td>
                                    <td data-value="<?php echo esc_attr($stats['runs_scored']); ?>"><?php echo $stats['runs_scored']; ?></td>
                                    <td data-value="<?php echo esc_attr($stats['runs_allowed']); ?>"><?php echo $stats['runs_allowed']; ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['avg']); ?>"><?php echo esc_html($batting_stats['avg']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['h']); ?>"><?php echo esc_html($batting_stats['h']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['hr']); ?>"><?php echo esc_html($batting_stats['hr']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['d']); ?>"><?php echo esc_html($batting_stats['d']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['t']); ?>"><?php echo esc_html($batting_stats['t']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['bb']); ?>"><?php echo esc_html($batting_stats['bb']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['slg']); ?>"><?php echo esc_html($batting_stats['slg']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['so']); ?>"><?php echo esc_html($batting_stats['so']); ?></td>
                                    <td data-value="<?php echo esc_attr($batting_stats['e']); ?>"><?php echo esc_html($batting_stats['e']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p><em>PJ = Partidos Jugados, G = Ganados, P = Perdidos, % = Porcentaje, CF = Carreras a Favor, CC = Carreras en Contra, AVG = Promedio de Bateo, H = Hits, HR = Jonrones, 2B = Dobles, 3B = Triples, BB = Bases por Bolas, SLG = Slugging, SO = Ponches, E = Errores</em></p>
                </section>
            </div>
        <?php endif; ?>

        <?php if (count($comparison_teams) >= 2): ?>
            <div class="stats-card tournament-section-card team-comparison-card">
                <details class="team-comparison-details">
                    <summary>
                        <span>Comparativa de Equipos</span>
                    </summary>

                    <section class="team-comparison">
                        <div class="team-comparison-controls">
                            <label>
                                <span>Equipo 1</span>
                                <select id="comparison-team-a" class="team-comparison-select">
                                    <?php foreach ($comparison_teams as $team): ?>
                                        <option value="<?php echo esc_attr($team->ID); ?>" <?php selected($comparison_team_a, intval($team->ID)); ?>>
                                            <?php echo esc_html($team->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>

                            <span class="team-comparison-vs">VS</span>

                            <label>
                                <span>Equipo 2</span>
                                <select id="comparison-team-b" class="team-comparison-select">
                                    <?php foreach ($comparison_teams as $team): ?>
                                        <option value="<?php echo esc_attr($team->ID); ?>" <?php selected($comparison_team_b, intval($team->ID)); ?>>
                                            <?php echo esc_html($team->post_title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>

                        <label class="team-comparison-toggle">
                            <input type="checkbox" id="comparison-head-to-head">
                            <span>Enfrentamiento directo</span>
                        </label>

                        <div class="players-tabs team-comparison-tabs">
                            <button class="players-tab active" data-comparison-tab="comparison-batting">Bateo</button>
                            <button class="players-tab" data-comparison-tab="comparison-pitching">Pitcheo</button>
                        </div>

                        <div class="team-comparison-tab-content active" id="comparison-batting-panel">
                            <div class="table-responsive">
                                <table class="stats-table team-comparison-table" data-comparison-group="batting">
                                    <thead>
                                        <tr>
                                            <th>Estad&iacute;stica</th>
                                            <th data-side-heading="a"><?php echo esc_html(get_the_title($comparison_team_a)); ?></th>
                                            <th data-side-heading="b"><?php echo esc_html(get_the_title($comparison_team_b)); ?></th>
                                            <th>Ganador</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($comparison_batting_metrics as $metric): ?>
                                            <tr data-metric="<?php echo esc_attr($metric['key']); ?>" data-higher="<?php echo $metric['higher'] ? '1' : '0'; ?>">
                                                <td>
                                                    <strong><?php echo esc_html($metric['label']); ?></strong>
                                                    <span><?php echo esc_html($metric['description']); ?></span>
                                                </td>
                                                <td data-side="a"></td>
                                                <td data-side="b"></td>
                                                <td data-side="winner"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="team-comparison-tab-content" id="comparison-pitching-panel">
                            <div class="table-responsive">
                                <table class="stats-table team-comparison-table" data-comparison-group="pitching">
                                    <thead>
                                        <tr>
                                            <th>Estad&iacute;stica</th>
                                            <th data-side-heading="a"><?php echo esc_html(get_the_title($comparison_team_a)); ?></th>
                                            <th data-side-heading="b"><?php echo esc_html(get_the_title($comparison_team_b)); ?></th>
                                            <th>Ganador</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($comparison_pitching_metrics as $metric): ?>
                                            <tr data-metric="<?php echo esc_attr($metric['key']); ?>" data-higher="<?php echo $metric['higher'] ? '1' : '0'; ?>">
                                                <td>
                                                    <strong><?php echo esc_html($metric['label']); ?></strong>
                                                    <span><?php echo esc_html($metric['description']); ?></span>
                                                </td>
                                                <td data-side="a"></td>
                                                <td data-side="b"></td>
                                                <td data-side="winner"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </details>
            </div>
        <?php endif; ?>

        <?php
        $tournament_batting = baseball_get_batting_stats_for_games($tournament_game_ids);
        $tournament_pitching = baseball_get_pitching_stats_for_games($tournament_game_ids);

        usort($tournament_batting, function ($a, $b) {
            $avg_a = intval($a->ab) > 0 ? intval($a->h) / intval($a->ab) : 0;
            $avg_b = intval($b->ab) > 0 ? intval($b->h) / intval($b->ab) : 0;
            return $avg_b <=> $avg_a;
        });
        ?>

        <?php if (!empty($tournament_batting) || !empty($tournament_pitching) || $games): ?>
        <div class="stats-card tournament-stats-card">
            <h2>Estad&iacute;sticas del Torneo</h2>

            <div class="players-tabs tournament-tabs">
                <button class="players-tab active" data-tab="tournament-batting">Bateo</button>
                <button class="players-tab" data-tab="tournament-pitching">Pitcheo</button>
                <button class="players-tab" data-tab="tournament-games">Partidos <?php if ($games): ?>(<?php echo count($games); ?>)<?php endif; ?></button>
            </div>

            <div class="players-tab-content active" id="tournament-batting-stats">
                <?php if (!empty($tournament_batting)): ?>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="tournament-batting-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name">Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th data-sort="position">Pos</th>
                                <th class="sortable">AVG <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">OBP <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SLG <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">OPS <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">J <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">AB <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">H <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">HR <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">RBI <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">R <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">BB <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">HBP <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SO <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">GIDP <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SF <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">ROE <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">FC <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">2B <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">3B <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">E <span class="sort-arrow">&#8597;</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tournament_batting as $b):
                                $player_id = intval($b->player_id);
                                $player = get_post($player_id);
                                if (!$player) continue;
                                $player_number = get_post_meta($player_id, '_player_number', true);
                                $avg = baseball_format_rate($b->h, $b->ab);
                                $avg_val = intval($b->ab) > 0 ? intval($b->h) / intval($b->ab) : 0;
                                $obp = baseball_calculate_obp($b->h, $b->bb, $b->hbp, $b->ab, $b->sf);
                                $obp_denominator = intval($b->ab) + intval($b->bb) + intval($b->hbp) + intval($b->sf);
                                $obp_val = $obp_denominator > 0 ? (intval($b->h) + intval($b->bb) + intval($b->hbp)) / $obp_denominator : 0;
                                $slg = baseball_calculate_slg($b->h, $b->d, $b->t, $b->hr, $b->ab);
                                $slg_val = baseball_calculate_slg_value($b->h, $b->d, $b->t, $b->hr, $b->ab);
                                $ops = baseball_calculate_ops($b->h, $b->d, $b->t, $b->hr, $b->bb, $b->hbp, $b->ab, $b->sf);
                                $ops_val = baseball_calculate_ops_value($b->h, $b->d, $b->t, $b->hr, $b->bb, $b->hbp, $b->ab, $b->sf);
                                $positions = wp_get_post_terms($player_id, 'position');
                                $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                                $team_id = get_post_meta($player_id, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'FA';
                                $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'FA';
                            ?>
                            <tr>
                                <td data-value="<?php echo esc_attr($player_number ?: 0); ?>"><?php echo esc_html($player_number ?: '-'); ?></td>
                                <td data-value="<?php echo esc_attr($player->post_title); ?>">
                                    <a href="<?php echo esc_url(get_permalink($player_id)); ?>" class="player-name-cell player-name-link">
                                        <div class="player-mini-photo">
                                            <?php if (has_post_thumbnail($player_id)): ?>
                                                <?php echo get_the_post_thumbnail($player_id, 'thumbnail'); ?>
                                            <?php else: ?>
                                                <div class="player-placeholder"><span class="dashicons dashicons-admin-users"></span></div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </a>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_abbr); ?></td>
                                <td data-value="<?php echo esc_attr($position_name); ?>"><?php echo esc_html($position_name); ?></td>
                                <td data-value="<?php echo esc_attr($avg_val); ?>" class="stat-highlight"><?php echo esc_html($avg); ?></td>
                                <td data-value="<?php echo esc_attr($obp_val); ?>" class="stat-highlight"><?php echo esc_html($obp); ?></td>
                                <td data-value="<?php echo esc_attr($slg_val); ?>" class="stat-highlight"><?php echo esc_html($slg); ?></td>
                                <td data-value="<?php echo esc_attr($ops_val); ?>" class="stat-highlight"><?php echo esc_html($ops); ?></td>
                                <td data-value="<?php echo esc_attr($b->games); ?>"><?php echo esc_html($b->games); ?></td>
                                <td data-value="<?php echo esc_attr($b->ab); ?>"><?php echo esc_html($b->ab); ?></td>
                                <td data-value="<?php echo esc_attr($b->h); ?>"><?php echo esc_html($b->h); ?></td>
                                <td data-value="<?php echo esc_attr($b->hr); ?>" class="stat-highlight"><?php echo esc_html($b->hr); ?></td>
                                <td data-value="<?php echo esc_attr($b->rbi); ?>" class="stat-highlight"><?php echo esc_html($b->rbi); ?></td>
                                <td data-value="<?php echo esc_attr($b->r); ?>"><?php echo esc_html($b->r); ?></td>
                                <td data-value="<?php echo esc_attr($b->bb); ?>"><?php echo esc_html($b->bb); ?></td>
                                <td data-value="<?php echo esc_attr($b->hbp); ?>"><?php echo esc_html($b->hbp); ?></td>
                                <td data-value="<?php echo esc_attr($b->so); ?>"><?php echo esc_html($b->so); ?></td>
                                <td data-value="<?php echo esc_attr($b->gidp); ?>"><?php echo esc_html($b->gidp); ?></td>
                                <td data-value="<?php echo esc_attr($b->sf); ?>"><?php echo esc_html($b->sf); ?></td>
                                <td data-value="<?php echo esc_attr($b->roe); ?>"><?php echo esc_html($b->roe); ?></td>
                                <td data-value="<?php echo esc_attr($b->fc); ?>"><?php echo esc_html($b->fc); ?></td>
                                <td data-value="<?php echo esc_attr($b->d); ?>" class="stat-highlight"><?php echo esc_html($b->d); ?></td>
                                <td data-value="<?php echo esc_attr($b->t); ?>" class="stat-highlight"><?php echo esc_html($b->t); ?></td>
                                <td data-value="<?php echo esc_attr($b->e); ?>" class="stat-highlight"><?php echo esc_html($b->e); ?></td>
                                <td><a href="<?php echo get_permalink($player_id); ?>" class="btn-small">Ver</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-legend">
                    <p><strong>Leyenda:</strong> # = N&uacute;mero, Pos = Posici&oacute;n, AVG = Promedio de Bateo, OBP = Porcentaje de Embasado, SLG = Slugging, OPS = OBP + SLG, J = Juegos, AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, R = Carreras, BB = Bases por Bolas, HBP = Golpeado por Lanzamiento, SO = Ponches, GIDP = Batea para Doble Play, SF = Fly de Sacrificio, ROE = Embasado por Error, FC = Bola Ocupada, 2B = Dobles, 3B = Triples, E = Errores</p>
                </div>
                <?php else: ?>
                    <p class="no-content"><em>No hay estad&iacute;sticas de bateo registradas en este torneo.</em></p>
                <?php endif; ?>
            </div>

            <div class="players-tab-content" id="tournament-pitching-stats">
                <?php if (!empty($tournament_pitching)):
                    uasort($tournament_pitching, function ($a, $b) {
                        $era_a = $a['ip'] > 0 ? ($a['er'] * 9) / $a['ip'] : 9999;
                        $era_b = $b['ip'] > 0 ? ($b['er'] * 9) / $b['ip'] : 9999;
                        return $era_a <=> $era_b;
                    });
                ?>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="tournament-pitching-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name">Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th class="sortable">ERA <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">W <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">L <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SV <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">IP <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">H <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">R <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">ER <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">BB <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SO <span class="sort-arrow">&#8597;</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tournament_pitching as $pid => $p):
                                $player = get_post($pid);
                                if (!$player) continue;
                                $player_number = get_post_meta($pid, '_player_number', true);
                                $era = $p['ip'] > 0 ? number_format(($p['er'] * 9) / $p['ip'], 2) : '0.00';
                                $era_val = $p['ip'] > 0 ? ($p['er'] * 9) / $p['ip'] : 0;
                                $team_id = get_post_meta($pid, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'FA';
                                $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'FA';
                            ?>
                            <tr>
                                <td data-value="<?php echo esc_attr($player_number ?: 0); ?>"><?php echo esc_html($player_number ?: '-'); ?></td>
                                <td data-value="<?php echo esc_attr($player->post_title); ?>">
                                    <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="player-name-cell player-name-link">
                                        <div class="player-mini-photo">
                                            <?php if (has_post_thumbnail($pid)): ?>
                                                <?php echo get_the_post_thumbnail($pid, 'thumbnail'); ?>
                                            <?php else: ?>
                                                <div class="player-placeholder"><span class="dashicons dashicons-admin-users"></span></div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </a>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_abbr); ?></td>
                                <td data-value="<?php echo esc_attr($era_val); ?>" class="stat-highlight"><?php echo esc_html($era); ?></td>
                                <td data-value="<?php echo esc_attr($p['wins']); ?>" class="stat-highlight"><?php echo esc_html($p['wins']); ?></td>
                                <td data-value="<?php echo esc_attr($p['losses']); ?>"><?php echo esc_html($p['losses']); ?></td>
                                <td data-value="<?php echo esc_attr($p['saves']); ?>" class="stat-highlight"><?php echo esc_html($p['saves']); ?></td>
                                <td data-value="<?php echo esc_attr($p['ip']); ?>"><?php echo number_format($p['ip'], 1); ?></td>
                                <td data-value="<?php echo esc_attr($p['h']); ?>"><?php echo esc_html($p['h']); ?></td>
                                <td data-value="<?php echo esc_attr($p['r']); ?>"><?php echo esc_html($p['r']); ?></td>
                                <td data-value="<?php echo esc_attr($p['er']); ?>"><?php echo esc_html($p['er']); ?></td>
                                <td data-value="<?php echo esc_attr($p['bb']); ?>"><?php echo esc_html($p['bb']); ?></td>
                                <td data-value="<?php echo esc_attr($p['so']); ?>" class="stat-highlight"><?php echo esc_html($p['so']); ?></td>
                                <td><a href="<?php echo get_permalink($pid); ?>" class="btn-small">Ver</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-legend">
                    <p><strong>Leyenda:</strong> ERA = Efectividad, W = Victorias, L = Derrotas, SV = Salvados, IP = Innings Lanzados, H = Hits Permitidos, R = Carreras Permitidas, ER = Carreras Limpias, BB = Bases por Bolas, SO = Ponches</p>
                </div>
                <?php else: ?>
                    <p class="no-content"><em>No hay estad&iacute;sticas de pitcheo registradas en este torneo.</em></p>
                <?php endif; ?>
            </div>

            <div class="players-tab-content" id="tournament-games-stats">
                <?php if ($games): ?>
                    <section class="tournament-games tournament-games-tab">
                        <div class="tournament-games-grid">
                            <?php foreach ($games as $game):
                                $home_team_id = get_post_meta($game->ID, '_game_home_team', true);
                                $away_team_id = get_post_meta($game->ID, '_game_away_team', true);
                                $home_score = get_post_meta($game->ID, '_game_home_score', true);
                                $away_score = get_post_meta($game->ID, '_game_away_score', true);
                                $game_date = get_post_meta($game->ID, '_game_date', true);
                                $game_time = get_post_meta($game->ID, '_game_time', true);
                                $location = get_post_meta($game->ID, '_game_location', true);
                                $away_team_name = get_the_title($away_team_id);
                                $home_team_name = get_the_title($home_team_id);
                                $away_abbr = strtoupper(substr($away_team_name, 0, 3));
                                $home_abbr = strtoupper(substr($home_team_name, 0, 3));
                            ?>
                                <div class="tournament-game-card">
                                    <div class="game-card-header">
                                        <?php if ($game_date): ?>
                                            <span class="game-date-badge">
                                                <?php echo date('d/m/Y', strtotime($game_date)); ?>
                                                <?php if ($game_time): ?>
                                                    - <?php echo date('H:i', strtotime($game_time)); ?>
                                                <?php endif; ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($location): ?>
                                            <span class="game-location-badge"><?php echo esc_html($location); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="game-card-teams">
                                        <div class="game-team away">
                                            <?php if (has_post_thumbnail($away_team_id)): ?>
                                                <div class="team-logo-small">
                                                    <?php echo get_the_post_thumbnail($away_team_id, 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="team-info">
                                                <span class="team-name-full"><?php echo esc_html($away_team_name); ?></span>
                                                <span class="team-name-abbr"><?php echo esc_html($away_abbr); ?></span>
                                                <span class="team-label">Visitante</span>
                                            </div>
                                            <div class="team-score-large">
                                                <?php echo $away_score !== '' ? $away_score : '-'; ?>
                                            </div>
                                        </div>

                                        <div class="vs-divider">VS</div>

                                        <div class="game-team home">
                                            <?php if (has_post_thumbnail($home_team_id)): ?>
                                                <div class="team-logo-small">
                                                    <?php echo get_the_post_thumbnail($home_team_id, 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="team-info">
                                                <span class="team-name-full"><?php echo esc_html($home_team_name); ?></span>
                                                <span class="team-name-abbr"><?php echo esc_html($home_abbr); ?></span>
                                                <span class="team-label">Local</span>
                                            </div>
                                            <div class="team-score-large">
                                                <?php echo $home_score !== '' ? $home_score : '-'; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="game-card-footer">
                                        <a href="<?php echo get_permalink($game->ID); ?>" class="btn-view-game">Ver Detalles</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php else: ?>
                    <p class="no-content"><em>No hay partidos registrados en este torneo.</em></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </article>

    <?php endwhile; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var teamComparisonData = <?php echo wp_json_encode($comparison_data); ?>;
    var teamComparisonMatchups = <?php echo wp_json_encode($comparison_matchup_data); ?>;
    var comparisonTeamA = document.getElementById('comparison-team-a');
    var comparisonTeamB = document.getElementById('comparison-team-b');
    var comparisonHeadToHead = document.getElementById('comparison-head-to-head');

    function getComparisonGroup(teamId, opponentId, group) {
        if (
            comparisonHeadToHead &&
            comparisonHeadToHead.checked &&
            teamComparisonMatchups[teamId] &&
            teamComparisonMatchups[teamId][opponentId] &&
            teamComparisonMatchups[teamId][opponentId][group]
        ) {
            return teamComparisonMatchups[teamId][opponentId][group];
        }

        return teamComparisonData[teamId] && teamComparisonData[teamId][group]
            ? teamComparisonData[teamId][group]
            : null;
    }

    function getComparisonValue(teamId, opponentId, group, metric) {
        var stats = getComparisonGroup(teamId, opponentId, group);

        if (!stats) {
            return '0';
        }

        return stats[metric] !== undefined
            ? String(stats[metric])
            : '0';
    }

    function getComparisonNumber(value) {
        var numeric = parseFloat(String(value).replace(/,/g, ''));
        return isNaN(numeric) ? 0 : numeric;
    }

    function setComparisonCellState(cell, state) {
        cell.classList.remove('is-better', 'is-worse', 'is-even');
        if (state) {
            cell.classList.add(state);
        }
    }

    function updateTeamComparison() {
        if (!comparisonTeamA || !comparisonTeamB) {
            return;
        }

        var teamA = comparisonTeamA.value;
        var teamB = comparisonTeamB.value;
        var nameA = teamComparisonData[teamA] ? teamComparisonData[teamA].name : '';
        var nameB = teamComparisonData[teamB] ? teamComparisonData[teamB].name : '';

        document.querySelectorAll('.team-comparison-table').forEach(function (table) {
            var group = table.getAttribute('data-comparison-group');
            var headingA = table.querySelector('[data-side-heading="a"]');
            var headingB = table.querySelector('[data-side-heading="b"]');

            if (headingA) { headingA.textContent = nameA; }
            if (headingB) { headingB.textContent = nameB; }

            table.querySelectorAll('tbody tr[data-metric]').forEach(function (row) {
                var metric = row.getAttribute('data-metric');
                var higherIsBetter = row.getAttribute('data-higher') === '1';
                var valueA = getComparisonValue(teamA, teamB, group, metric);
                var valueB = getComparisonValue(teamB, teamA, group, metric);
                var numberA = getComparisonNumber(valueA);
                var numberB = getComparisonNumber(valueB);
                var cellA = row.querySelector('[data-side="a"]');
                var cellB = row.querySelector('[data-side="b"]');
                var winnerCell = row.querySelector('[data-side="winner"]');
                var stateA = 'is-even';
                var stateB = 'is-even';
                var winner = 'Empate';

                if (teamA === teamB) {
                    winner = 'Mismo equipo';
                } else if (numberA !== numberB) {
                    var teamAWins = higherIsBetter ? numberA > numberB : numberA < numberB;
                    stateA = teamAWins ? 'is-better' : 'is-worse';
                    stateB = teamAWins ? 'is-worse' : 'is-better';
                    winner = teamAWins ? nameA : nameB;
                }

                if (cellA) {
                    cellA.textContent = valueA;
                    setComparisonCellState(cellA, stateA);
                }
                if (cellB) {
                    cellB.textContent = valueB;
                    setComparisonCellState(cellB, stateB);
                }
                if (winnerCell) {
                    winnerCell.textContent = winner;
                    setComparisonCellState(winnerCell, winner === 'Empate' || winner === 'Mismo equipo' ? 'is-even' : 'is-better');
                }
            });
        });
    }

    if (comparisonTeamA && comparisonTeamB) {
        comparisonTeamA.addEventListener('change', updateTeamComparison);
        comparisonTeamB.addEventListener('change', updateTeamComparison);
        if (comparisonHeadToHead) {
            comparisonHeadToHead.addEventListener('change', updateTeamComparison);
        }
        updateTeamComparison();
    }

    document.querySelectorAll('.team-comparison-tabs').forEach(function (tabGroup) {
        var tabs = tabGroup.querySelectorAll('[data-comparison-tab]');
        var scope = tabGroup.closest('.team-comparison');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var target = this.getAttribute('data-comparison-tab');
                tabs.forEach(function (item) { item.classList.remove('active'); });
                scope.querySelectorAll('.team-comparison-tab-content').forEach(function (content) {
                    content.classList.remove('active');
                });
                this.classList.add('active');
                var panel = scope.querySelector('#' + target + '-panel');
                if (panel) {
                    panel.classList.add('active');
                }
            });
        });
    });

    document.querySelectorAll('.tournament-stats-card .players-tabs').forEach(function (tabGroup) {
        var tabs = tabGroup.querySelectorAll('.players-tab');
        var scope = tabGroup.closest('.tournament-stats-card');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var name = this.getAttribute('data-tab');
                tabs.forEach(function (item) { item.classList.remove('active'); });
                scope.querySelectorAll('.players-tab-content').forEach(function (content) {
                    content.classList.remove('active');
                });
                this.classList.add('active');
                var content = scope.querySelector('#' + name + '-stats');
                if (content) {
                    content.classList.add('active');
                }
            });
        });
    });

    document.querySelectorAll('.tournament-stats-card .sortable-table').forEach(function (table) {
        var headers = table.querySelectorAll('th.sortable');
        var direction = 'desc';
        var lastColumn = null;

        headers.forEach(function (header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function () {
                var index = Array.prototype.indexOf.call(this.parentNode.children, this);
                direction = lastColumn === index && direction === 'desc' ? 'asc' : 'desc';
                lastColumn = index;

                headers.forEach(function (item) {
                    var arrow = item.querySelector('.sort-arrow');
                    if (arrow) {
                        arrow.textContent = '\u2195';
                    }
                });

                var activeArrow = this.querySelector('.sort-arrow');
                if (activeArrow) {
                    activeArrow.textContent = direction === 'asc' ? '\u2191' : '\u2193';
                }

                var tbody = table.querySelector('tbody');
                var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                rows.sort(function (rowA, rowB) {
                    var valueA = rowA.children[index].getAttribute('data-value') || rowA.children[index].textContent.trim();
                    var valueB = rowB.children[index].getAttribute('data-value') || rowB.children[index].textContent.trim();
                    var numberA = parseFloat(valueA);
                    var numberB = parseFloat(valueB);

                    if (!isNaN(numberA) && !isNaN(numberB)) {
                        return direction === 'asc' ? numberA - numberB : numberB - numberA;
                    }

                    return direction === 'asc'
                        ? valueA.localeCompare(valueB, undefined, { numeric: true, sensitivity: 'base' })
                        : valueB.localeCompare(valueA, undefined, { numeric: true, sensitivity: 'base' });
                });

                rows.forEach(function (row) {
                    tbody.appendChild(row);
                });
            });
        });
    });
});
</script>

<?php
get_footer();
