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
            <div class="stats-card">
                <section class="tournament-standings">
                    <h2>Tabla de Posiciones</h2>
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
                            ?>
                            <tr>
                                <td><?php echo $pos++; ?></td>
                                <td>
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
                                <td><?php echo $stats['games']; ?></td>
                                <td><?php echo $stats['wins']; ?></td>
                                <td><?php echo $stats['losses']; ?></td>
                                <td><?php echo $stats['win_pct']; ?></td>
                                <td><?php echo $stats['runs_scored']; ?></td>
                                <td><?php echo $stats['runs_allowed']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p><em>PJ = Partidos Jugados, G = Ganados, P = Perdidos, % = Porcentaje, CF = Carreras a Favor, CC = Carreras en Contra</em></p>
                </section>
            </div>
        <?php endif; ?>

        <?php
        $tournament_batting = baseball_get_batting_stats_for_games($tournament_game_ids);
        $tournament_pitching = baseball_get_pitching_stats_for_games($tournament_game_ids);

        // Ordenar bateo por AVG desc por defecto
        usort($tournament_batting, function ($a, $b) {
            $avg_a = intval($a->ab) > 0 ? intval($a->h) / intval($a->ab) : 0;
            $avg_b = intval($b->ab) > 0 ? intval($b->h) / intval($b->ab) : 0;
            return $avg_b <=> $avg_a;
        });
        ?>

        <?php if (!empty($tournament_batting) || !empty($tournament_pitching) || $games): ?>
        <div class="stats-card tournament-stats-card">
            <h2>Estadísticas del Torneo</h2>
            <div class="players-tabs tournament-tabs">
                <button class="players-tab active" data-tab="tournament-batting">Bateo</button>
                <button class="players-tab" data-tab="tournament-pitching">Pitcheo</button>
                <button class="players-tab" data-tab="tournament-games">Partidos <?php if ($games): ?>(<?php echo count($games); ?>)<?php endif; ?></button>
            </div>

            <!-- Bateo -->
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
                                <th class="sortable">AVG <span class="sort-arrow">↕</span></th>
                                <th class="sortable">J <span class="sort-arrow">↕</span></th>
                                <th class="sortable">AB <span class="sort-arrow">↕</span></th>
                                <th class="sortable">H <span class="sort-arrow">↕</span></th>
                                <th class="sortable">HR <span class="sort-arrow">↕</span></th>
                                <th class="sortable">RBI <span class="sort-arrow">↕</span></th>
                                <th class="sortable">R <span class="sort-arrow">↕</span></th>
                                <th class="sortable">BB <span class="sort-arrow">↕</span></th>
                                <th class="sortable">SO <span class="sort-arrow">↕</span></th>
                                <th class="sortable">2B <span class="sort-arrow">↕</span></th>
                                <th class="sortable">3B <span class="sort-arrow">↕</span></th>
                                <th class="sortable">E <span class="sort-arrow">↕</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tournament_batting as $b):
                                $player_id = intval($b->player_id);
                                $player = get_post($player_id);
                                if (!$player) continue;
                                $player_number = get_post_meta($player_id, '_player_number', true);
                                $avg = intval($b->ab) > 0 ? number_format(intval($b->h) / intval($b->ab), 3) : '.000';
                                $avg_val = intval($b->ab) > 0 ? intval($b->h) / intval($b->ab) : 0;
                                $positions = wp_get_post_terms($player_id, 'position');
                                $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                                $team_id = get_post_meta($player_id, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'FA';
                                $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'FA';
                            ?>
                            <tr>
                                <td data-value="<?php echo esc_attr($player_number ?: 0); ?>"><?php echo esc_html($player_number ?: '-'); ?></td>
                                <td data-value="<?php echo esc_attr($player->post_title); ?>">
                                    <div class="player-name-cell">
                                        <div class="player-mini-photo">
                                            <?php if (has_post_thumbnail($player_id)): ?>
                                                <?php echo get_the_post_thumbnail($player_id, 'thumbnail'); ?>
                                            <?php else: ?>
                                                <div class="player-placeholder"><span class="dashicons dashicons-admin-users"></span></div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </div>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_abbr); ?></td>
                                <td data-value="<?php echo esc_attr($position_name); ?>"><?php echo esc_html($position_name); ?></td>
                                <td data-value="<?php echo esc_attr($avg_val); ?>" class="stat-highlight"><?php echo esc_html($avg); ?></td>
                                <td data-value="<?php echo esc_attr($b->games); ?>"><?php echo esc_html($b->games); ?></td>
                                <td data-value="<?php echo esc_attr($b->ab); ?>"><?php echo esc_html($b->ab); ?></td>
                                <td data-value="<?php echo esc_attr($b->h); ?>"><?php echo esc_html($b->h); ?></td>
                                <td data-value="<?php echo esc_attr($b->hr); ?>" class="stat-highlight"><?php echo esc_html($b->hr); ?></td>
                                <td data-value="<?php echo esc_attr($b->rbi); ?>" class="stat-highlight"><?php echo esc_html($b->rbi); ?></td>
                                <td data-value="<?php echo esc_attr($b->r); ?>"><?php echo esc_html($b->r); ?></td>
                                <td data-value="<?php echo esc_attr($b->bb); ?>"><?php echo esc_html($b->bb); ?></td>
                                <td data-value="<?php echo esc_attr($b->so); ?>"><?php echo esc_html($b->so); ?></td>
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
                    <p><strong>Leyenda:</strong> # = Número, Pos = Posición, AVG = Promedio de Bateo, J = Juegos, AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, R = Carreras, BB = Bases por Bolas, SO = Ponches, 2B = Dobles, 3B = Triples, E = Errores</p>
                </div>
                <?php else: ?>
                    <p class="no-content"><em>No hay estadísticas de bateo registradas en este torneo.</em></p>
                <?php endif; ?>
            </div>

            <!-- Pitcheo -->
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
                                <th class="sortable">ERA <span class="sort-arrow">↕</span></th>
                                <th class="sortable">W <span class="sort-arrow">↕</span></th>
                                <th class="sortable">L <span class="sort-arrow">↕</span></th>
                                <th class="sortable">SV <span class="sort-arrow">↕</span></th>
                                <th class="sortable">IP <span class="sort-arrow">↕</span></th>
                                <th class="sortable">H <span class="sort-arrow">↕</span></th>
                                <th class="sortable">R <span class="sort-arrow">↕</span></th>
                                <th class="sortable">ER <span class="sort-arrow">↕</span></th>
                                <th class="sortable">BB <span class="sort-arrow">↕</span></th>
                                <th class="sortable">SO <span class="sort-arrow">↕</span></th>
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
                                    <div class="player-name-cell">
                                        <div class="player-mini-photo">
                                            <?php if (has_post_thumbnail($pid)): ?>
                                                <?php echo get_the_post_thumbnail($pid, 'thumbnail'); ?>
                                            <?php else: ?>
                                                <div class="player-placeholder"><span class="dashicons dashicons-admin-users"></span></div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </div>
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
                    <p class="no-content"><em>No hay estadísticas de pitcheo registradas en este torneo.</em></p>
                <?php endif; ?>
            </div>

            <!-- Partidos -->
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
                                        <a href="<?php echo get_permalink($game->ID); ?>" class="btn-view-game">
                                            Ver Detalles
                                        </a>
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
    // Tabs del bloque de torneo
    document.querySelectorAll('.players-tabs').forEach(function (tabGroup) {
        var tabs = tabGroup.querySelectorAll('.players-tab');
        var scope = tabGroup.closest('.stats-card') || document;

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var name = this.getAttribute('data-tab');
                tabs.forEach(function (t) { t.classList.remove('active'); });
                scope.querySelectorAll('.players-tab-content').forEach(function (c) { c.classList.remove('active'); });
                this.classList.add('active');
                var content = scope.querySelector('#' + name + '-stats');
                if (content) { content.classList.add('active'); }
            });
        });
    });

    // Orden por columnas (igual que en la seccion de jugadores)
    document.querySelectorAll('.sortable-table').forEach(function (table) {
        var headers = table.querySelectorAll('th.sortable');
        var dir = 'desc';
        var lastCol = null;
        headers.forEach(function (header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function () {
                var idx = Array.prototype.indexOf.call(this.parentNode.children, this);
                if (lastCol === idx) { dir = dir === 'asc' ? 'desc' : 'asc'; } else { dir = 'desc'; lastCol = idx; }
                headers.forEach(function (h) { var a = h.querySelector('.sort-arrow'); if (a) a.textContent = '↕'; });
                var arrow = this.querySelector('.sort-arrow'); if (arrow) arrow.textContent = dir === 'asc' ? '↑' : '↓';
                var tbody = table.querySelector('tbody');
                var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    var av = a.children[idx].getAttribute('data-value');
                    var bv = b.children[idx].getAttribute('data-value');
                    if (av !== null && bv !== null && !isNaN(av) && !isNaN(bv)) { av = parseFloat(av); bv = parseFloat(bv); }
                    else { av = (av || '').toLowerCase(); bv = (bv || '').toLowerCase(); }
                    if (dir === 'asc') { return av > bv ? 1 : (av < bv ? -1 : 0); }
                    return av < bv ? 1 : (av > bv ? -1 : 0);
                });
                rows.forEach(function (r) { tbody.appendChild(r); });
            });
        });
    });
});
</script>

<?php
get_footer();
