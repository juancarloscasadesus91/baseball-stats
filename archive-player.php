<?php
/**
 * Archive Players Template - Table View
 *
 * @package Baseball_Stats
 */

get_header(); 

// Get all players with their stats
$players = get_posts(array(
    'post_type' => 'player',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC'
));
?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Todos los Jugadores</h1>
            <p>Haz clic en las columnas para ordenar las estadísticas</p>
        </div>

        <?php if ($players) : ?>
            <!-- Tabs -->
            <div class="players-tabs">
                <button class="players-tab active" data-tab="batting">Bateo</button>
                <button class="players-tab" data-tab="pitching">Pitcheo</button>
            </div>
            
            <!-- Batting Stats -->
            <div class="stats-card players-tab-content active" id="batting-stats">
                <div class="table-scroll-top" aria-hidden="true">
                    <div class="table-scroll-top-inner"></div>
                </div>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="players-stats-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name">Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th data-sort="position">Pos</th>
                                <th data-sort="avg" class="sortable">AVG <span class="sort-arrow">↕</span></th>
                                <th data-sort="obp" class="sortable">OBP <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="slg" class="sortable">SLG <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="ops" class="sortable">OPS <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="games" class="sortable">J <span class="sort-arrow">↕</span></th>
                                <th data-sort="ab" class="sortable">AB <span class="sort-arrow">↕</span></th>
                                <th data-sort="hits" class="sortable">H <span class="sort-arrow">↕</span></th>
                                <th data-sort="hr" class="sortable">HR <span class="sort-arrow">↕</span></th>
                                <th data-sort="rbi" class="sortable">RBI <span class="sort-arrow">↕</span></th>
                                <th data-sort="runs" class="sortable">R <span class="sort-arrow">↕</span></th>
                                <th data-sort="bb" class="sortable">BB <span class="sort-arrow">↕</span></th>
                                <th data-sort="hbp" class="sortable">HBP <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="so" class="sortable">SO <span class="sort-arrow">↕</span></th>
                                <th data-sort="gidp" class="sortable">GIDP <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="sf" class="sortable">SF <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="roe" class="sortable">ROE <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="fc" class="sortable">FC <span class="sort-arrow">&#8597;</span></th>
                                <th data-sort="doubles" class="sortable">2B <span class="sort-arrow">↕</span></th>
                                <th data-sort="triples" class="sortable">3B <span class="sort-arrow">↕</span></th>
                                <th data-sort="errors" class="sortable">E <span class="sort-arrow">↕</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player) : 
                                $player_id = $player->ID;
                                $player_number = get_post_meta($player_id, '_player_number', true);
                                $batting_avg = get_post_meta($player_id, '_batting_avg', true);
                                $on_base_percentage = get_post_meta($player_id, '_on_base_percentage', true);
                                $slugging_percentage = get_post_meta($player_id, '_slugging_percentage', true);
                                $on_base_plus_slugging = get_post_meta($player_id, '_on_base_plus_slugging', true);
                                $games = get_post_meta($player_id, '_games_played', true);
                                $at_bats = get_post_meta($player_id, '_at_bats', true);
                                $hits = get_post_meta($player_id, '_hits', true);
                                $home_runs = get_post_meta($player_id, '_home_runs', true);
                                $rbis = get_post_meta($player_id, '_rbis', true);
                                $runs = get_post_meta($player_id, '_runs', true);
                                $walks = get_post_meta($player_id, '_walks', true);
                                $hit_by_pitch = get_post_meta($player_id, '_hit_by_pitch', true);
                                $grounded_into_dp = get_post_meta($player_id, '_grounded_into_dp', true);
                                $sacrifice_flies = get_post_meta($player_id, '_sacrifice_flies', true);
                                $reached_on_error = get_post_meta($player_id, '_reached_on_error', true);
                                $fielders_choice = get_post_meta($player_id, '_fielders_choice', true);
                                $strikeouts = get_post_meta($player_id, '_strikeouts', true);
                                $doubles = get_post_meta($player_id, '_doubles', true);
                                $triples = get_post_meta($player_id, '_triples', true);
                                $errors = get_post_meta($player_id, '_errors', true);
                                
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
                                            <?php if (has_post_thumbnail($player_id)) : ?>
                                                <?php echo get_the_post_thumbnail($player_id, 'thumbnail'); ?>
                                            <?php else : ?>
                                                <div class="player-placeholder">
                                                    <span class="dashicons dashicons-admin-users"></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </a>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_abbr); ?></td>
                                <td data-value="<?php echo esc_attr($position_name); ?>"><?php echo esc_html($position_name); ?></td>
                                <td data-value="<?php echo esc_attr($batting_avg ?: 0); ?>" class="stat-highlight"><?php echo esc_html($batting_avg ?: '.000'); ?></td>
                                <td data-value="<?php echo esc_attr($on_base_percentage ?: 0); ?>" class="stat-highlight"><?php echo esc_html($on_base_percentage ?: '.000'); ?></td>
                                <td data-value="<?php echo esc_attr($slugging_percentage ?: 0); ?>" class="stat-highlight"><?php echo esc_html($slugging_percentage ?: '.000'); ?></td>
                                <td data-value="<?php echo esc_attr($on_base_plus_slugging ?: 0); ?>" class="stat-highlight"><?php echo esc_html($on_base_plus_slugging ?: '.000'); ?></td>
                                <td data-value="<?php echo esc_attr($games ?: 0); ?>"><?php echo esc_html($games ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($at_bats ?: 0); ?>"><?php echo esc_html($at_bats ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($hits ?: 0); ?>"><?php echo esc_html($hits ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($home_runs ?: 0); ?>" class="stat-highlight"><?php echo esc_html($home_runs ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($rbis ?: 0); ?>" class="stat-highlight"><?php echo esc_html($rbis ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($runs ?: 0); ?>"><?php echo esc_html($runs ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($walks ?: 0); ?>"><?php echo esc_html($walks ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($hit_by_pitch ?: 0); ?>"><?php echo esc_html($hit_by_pitch ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($strikeouts ?: 0); ?>"><?php echo esc_html($strikeouts ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($grounded_into_dp ?: 0); ?>"><?php echo esc_html($grounded_into_dp ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($sacrifice_flies ?: 0); ?>"><?php echo esc_html($sacrifice_flies ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($reached_on_error ?: 0); ?>"><?php echo esc_html($reached_on_error ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($fielders_choice ?: 0); ?>"><?php echo esc_html($fielders_choice ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($doubles ?: 0); ?>" class="stat-highlight"><?php echo esc_html($doubles ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($triples ?: 0); ?>" class="stat-highlight"><?php echo esc_html($triples ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($errors ?: 0); ?>" class="stat-highlight"><?php echo esc_html($errors ?: '0'); ?></td>
                                <td>
                                    <a href="<?php echo get_permalink($player_id); ?>" class="btn-small">Ver</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-legend">
                    <p><strong>Leyenda:</strong> # = Número, Pos = Posición, AVG = Promedio de Bateo, OBP = Porcentaje de Embasado, SLG = Slugging, OPS = OBP + SLG, J = Juegos, AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, R = Carreras, BB = Bases por Bolas, HBP = Golpeado por Lanzamiento, SO = Ponches, GIDP = Batea para Doble Play, SF = Fly de Sacrificio, ROE = Embasado por Error, FC = Bola Ocupada, 2B = Dobles, 3B = Triples, E = Errores</p>
                </div>
            </div>
            
            <!-- Pitching Stats -->
            <div class="stats-card players-tab-content" id="pitching-stats">
                <div class="table-scroll-top" aria-hidden="true">
                    <div class="table-scroll-top-inner"></div>
                </div>
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="pitchers-stats-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name"> Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th data-sort="era" class="sortable">ERA <span class="sort-arrow">↕</span></th>
                                <th data-sort="wins" class="sortable">W <span class="sort-arrow">↕</span></th>
                                <th data-sort="losses" class="sortable">L <span class="sort-arrow">↕</span></th>
                                <th data-sort="saves" class="sortable">SV <span class="sort-arrow">↕</span></th>
                                <th data-sort="ip" class="sortable">IP <span class="sort-arrow">↕</span></th>
                                <th data-sort="hits" class="sortable">H <span class="sort-arrow">↕</span></th>
                                <th data-sort="runs" class="sortable">R <span class="sort-arrow">↕</span></th>
                                <th data-sort="er" class="sortable">ER <span class="sort-arrow">↕</span></th>
                                <th data-sort="bb" class="sortable">BB <span class="sort-arrow">↕</span></th>
<th data-sort="so" class="sortable">SO <span class="sort-arrow">↕</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player) : 
                                $player_id = $player->ID;
                                $player_number = get_post_meta($player_id, '_player_number', true);
                                $ip = get_post_meta($player_id, '_innings_pitched', true);
                                
                                // Only show players with pitching stats
                                if (!$ip || floatval($ip) == 0) continue;
                                
                                $era = get_post_meta($player_id, '_era', true);
                                $wins = get_post_meta($player_id, '_pitching_wins', true);
                                $losses = get_post_meta($player_id, '_pitching_losses', true);
                                $saves = get_post_meta($player_id, '_pitching_saves', true);
                                $hits = get_post_meta($player_id, '_pitching_hits', true);
                                $runs = get_post_meta($player_id, '_pitching_runs', true);
                                $er = get_post_meta($player_id, '_pitching_earned_runs', true);
                                $bb = get_post_meta($player_id, '_pitching_walks', true);
                                $so = get_post_meta($player_id, '_pitching_strikeouts', true);
                                
                                // Calculate ERA if not set
                                if (empty($era) || floatval($era) == 0) {
                                    if (floatval($ip) > 0) {
                                        $era = number_format((floatval($er) * 9) / floatval($ip), 2);
                                    } else {
                                        $era = '0.00';
                                    }
                                } else {
                                    $era = number_format(floatval($era), 2);
                                }
                                
                                $team_id = get_post_meta($player_id, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'FA';
                                $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'FA';
                            ?>
                            <tr>
                                <td data-value="<?php echo esc_attr($player_number ?: 0); ?>"><?php echo esc_html($player_number ?: '-'); ?></td>
                                <td data-value="<?php echo esc_attr($player->post_title); ?>">
                                    <a href="<?php echo esc_url(get_permalink($player_id)); ?>" class="player-name-cell player-name-link">
                                        <div class="player-mini-photo">
                                            <?php if (has_post_thumbnail($player_id)) : ?>
                                                <?php echo get_the_post_thumbnail($player_id, 'thumbnail'); ?>
                                            <?php else : ?>
                                                <div class="player-placeholder">
                                                    <span class="dashicons dashicons-admin-users"></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <strong><?php echo esc_html($player->post_title); ?></strong>
                                    </a>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_abbr); ?></td>
                                <td data-value="<?php echo esc_attr($era); ?>" class="stat-highlight"><?php echo esc_html($era); ?></td>
                                <td data-value="<?php echo esc_attr($wins ?: 0); ?>" class="stat-highlight"><?php echo esc_html($wins ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($losses ?: 0); ?>"><?php echo esc_html($losses ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($saves ?: 0); ?>" class="stat-highlight"><?php echo esc_html($saves ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($ip ?: 0); ?>"><?php echo number_format(floatval($ip), 1); ?></td>
                                <td data-value="<?php echo esc_attr($hits ?: 0); ?>"><?php echo esc_html($hits ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($runs ?: 0); ?>"><?php echo esc_html($runs ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($er ?: 0); ?>"><?php echo esc_html($er ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($bb ?: 0); ?>"><?php echo esc_html($bb ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($so ?: 0); ?>" class="stat-highlight"><?php echo esc_html($so ?: '0'); ?></td>
                                <td>
                                    <a href="<?php echo get_permalink($player_id); ?>" class="btn-small">Ver</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-legend">
                    <p><strong>Leyenda:</strong> ERA = Efectividad, W = Victorias, L = Derrotas, SV = Salvados, IP = Innings Lanzados, H = Hits Permitidos, R = Carreras Permitidas, ER = Carreras Limpias, BB = Bases por Bolas, SO = Ponches</p>
                </div>
            </div>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay jugadores registrados</h2>
                <p>Aún no se han añadido jugadores al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Top horizontal scrollbars synced with each table.
    function setupTopTableScrollbars() {
        document.querySelectorAll('.table-scroll-top').forEach(topScroll => {
            const tableWrap = topScroll.nextElementSibling;
            const inner = topScroll.querySelector('.table-scroll-top-inner');

            if (!tableWrap || !tableWrap.classList.contains('table-responsive') || !inner) {
                return;
            }

            const updateWidth = () => {
                inner.style.width = tableWrap.scrollWidth + 'px';
                topScroll.scrollLeft = tableWrap.scrollLeft;
            };

            updateWidth();

            if (topScroll.dataset.scrollSynced === 'true') {
                return;
            }

            topScroll.dataset.scrollSynced = 'true';

            topScroll.addEventListener('scroll', () => {
                tableWrap.scrollLeft = topScroll.scrollLeft;
            });

            tableWrap.addEventListener('scroll', () => {
                topScroll.scrollLeft = tableWrap.scrollLeft;
            });

            window.addEventListener('resize', updateWidth);
        });
    }

    setupTopTableScrollbars();

    // Tab switching
    const tabs = document.querySelectorAll('.players-tab');
    const tabContents = document.querySelectorAll('.players-tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(tabName + '-stats').classList.add('active');
            setupTopTableScrollbars();
        });
    });
    
    // Sorting for batting and pitching tables
    document.querySelectorAll('.sortable-table').forEach(table => {
        const headers = table.querySelectorAll('th.sortable');
        let currentSort = { column: null, direction: 'asc' };

        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const columnIndex = Array.prototype.indexOf.call(this.parentNode.children, this);
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                if (currentSort.column === columnIndex) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.direction = 'desc';
                    currentSort.column = columnIndex;
                }

                headers.forEach(h => {
                    const arrow = h.querySelector('.sort-arrow');
                    if (arrow) arrow.textContent = '\u2195';
                    h.classList.remove('sorted-asc', 'sorted-desc');
                });

                const arrow = this.querySelector('.sort-arrow');
                if (arrow) arrow.textContent = currentSort.direction === 'asc' ? '\u2191' : '\u2193';
                this.classList.add(currentSort.direction === 'asc' ? 'sorted-asc' : 'sorted-desc');

                rows.sort((a, b) => {
                    const aCell = a.children[columnIndex];
                    const bCell = b.children[columnIndex];
                    let aVal = aCell ? (aCell.getAttribute('data-value') || aCell.textContent.trim()) : '';
                    let bVal = bCell ? (bCell.getAttribute('data-value') || bCell.textContent.trim()) : '';

                    if (aVal !== '' && bVal !== '' && !isNaN(aVal) && !isNaN(bVal)) {
                        aVal = parseFloat(aVal);
                        bVal = parseFloat(bVal);
                    } else {
                        aVal = aVal.toLowerCase();
                        bVal = bVal.toLowerCase();
                    }

                    if (currentSort.direction === 'asc') {
                        return aVal > bVal ? 1 : (aVal < bVal ? -1 : 0);
                    }
                    return aVal < bVal ? 1 : (aVal > bVal ? -1 : 0);
                });

                rows.forEach(row => tbody.appendChild(row));
            });
        });
    });
});
</script>

<?php get_footer(); ?>
