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

            $season_game_ids = baseball_get_season_game_ids(get_the_ID());
            $season_batting = baseball_get_batting_stats_for_games($season_game_ids);
            $season_pitching = baseball_get_pitching_stats_for_games($season_game_ids);

            usort($season_batting, function ($a, $b) {
                $avg_a = intval($a->ab) > 0 ? intval($a->h) / intval($a->ab) : 0;
                $avg_b = intval($b->ab) > 0 ? intval($b->h) / intval($b->ab) : 0;
                return $avg_b <=> $avg_a;
            });
        ?>
        
        <div class="team-header">
            <div class="team-info">
                <h1><?php the_title(); ?></h1>
                <div class="team-meta">
                    <?php if ($season_year): ?>
                        <p><strong>AÃ±o:</strong> <?php echo esc_html($season_year); ?></p>
                    <?php endif; ?>
                    <?php if ($start_date && $end_date): ?>
                        <p>
                            <strong>PerÃ­odo:</strong> 
                            <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                            <?php echo date('d/m/Y', strtotime($end_date)); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (get_the_content()) : ?>
        <div class="stats-card">
            <h2>DescripciÃ³n</h2>
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

        <?php if (!empty($season_batting) || !empty($season_pitching)) : ?>
        <div class="stats-card tournament-stats-card season-stats-card">
            <h2>Estadisticas de la Temporada</h2>
            <div class="players-tabs tournament-tabs">
                <button class="players-tab active" data-tab="season-batting">Bateo</button>
                <button class="players-tab" data-tab="season-pitching">Pitcheo</button>
            </div>

            <div class="players-tab-content active" id="season-batting-stats">
                <?php if (!empty($season_batting)) : ?>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="season-batting-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name">Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th data-sort="position">Pos</th>
                                <th class="sortable">AVG <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">J <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">AB <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">H <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">HR <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">RBI <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">R <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">BB <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">SO <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">2B <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">3B <span class="sort-arrow">&#8597;</span></th>
                                <th class="sortable">E <span class="sort-arrow">&#8597;</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($season_batting as $b) :
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
                                            <?php if (has_post_thumbnail($player_id)) : ?>
                                                <?php echo get_the_post_thumbnail($player_id, 'thumbnail'); ?>
                                            <?php else : ?>
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
                    <p><strong>Leyenda:</strong> # = Numero, Pos = Posicion, AVG = Promedio de Bateo, J = Juegos, AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, R = Carreras, BB = Bases por Bolas, SO = Ponches, 2B = Dobles, 3B = Triples, E = Errores</p>
                </div>
                <?php else : ?>
                    <p class="no-content"><em>No hay estadisticas de bateo registradas en esta temporada.</em></p>
                <?php endif; ?>
            </div>

            <div class="players-tab-content" id="season-pitching-stats">
                <?php if (!empty($season_pitching)) :
                    uasort($season_pitching, function ($a, $b) {
                        $era_a = $a['ip'] > 0 ? ($a['er'] * 9) / $a['ip'] : 9999;
                        $era_b = $b['ip'] > 0 ? ($b['er'] * 9) / $b['ip'] : 9999;
                        return $era_a <=> $era_b;
                    });
                ?>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="season-pitching-table">
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
                            <?php foreach ($season_pitching as $pid => $p) :
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
                                            <?php if (has_post_thumbnail($pid)) : ?>
                                                <?php echo get_the_post_thumbnail($pid, 'thumbnail'); ?>
                                            <?php else : ?>
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
                <?php else : ?>
                    <p class="no-content"><em>No hay estadisticas de pitcheo registradas en esta temporada.</em></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        

        <?php endwhile; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

    document.querySelectorAll('.sortable-table').forEach(function (table) {
        var headers = table.querySelectorAll('th.sortable');
        var dir = 'desc';
        var lastCol = null;
        headers.forEach(function (header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function () {
                var idx = Array.prototype.indexOf.call(this.parentNode.children, this);
                if (lastCol === idx) { dir = dir === 'asc' ? 'desc' : 'asc'; } else { dir = 'desc'; lastCol = idx; }
                headers.forEach(function (h) { var a = h.querySelector('.sort-arrow'); if (a) a.textContent = '\u2195'; });
                var arrow = this.querySelector('.sort-arrow'); if (arrow) arrow.textContent = dir === 'asc' ? '\u2191' : '\u2193';
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
