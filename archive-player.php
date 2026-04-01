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
            <div class="stats-card">
                <div class="table-responsive">
                    <table class="players-table sortable-table" id="players-stats-table">
                        <thead>
                            <tr>
                                <th data-sort="number">#</th>
                                <th data-sort="name">Jugador</th>
                                <th data-sort="team">Equipo</th>
                                <th data-sort="position">Pos</th>
                                <th data-sort="avg" class="sortable">AVG <span class="sort-arrow">↕</span></th>
                                <th data-sort="games" class="sortable">J <span class="sort-arrow">↕</span></th>
                                <th data-sort="ab" class="sortable">AB <span class="sort-arrow">↕</span></th>
                                <th data-sort="hits" class="sortable">H <span class="sort-arrow">↕</span></th>
                                <th data-sort="hr" class="sortable">HR <span class="sort-arrow">↕</span></th>
                                <th data-sort="rbi" class="sortable">RBI <span class="sort-arrow">↕</span></th>
                                <th data-sort="runs" class="sortable">R <span class="sort-arrow">↕</span></th>
                                <th data-sort="bb" class="sortable">BB <span class="sort-arrow">↕</span></th>
                                <th data-sort="so" class="sortable">SO <span class="sort-arrow">↕</span></th>
                                <th data-sort="sb" class="sortable">SB <span class="sort-arrow">↕</span></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player) : 
                                $player_id = $player->ID;
                                $player_number = get_post_meta($player_id, '_player_number', true);
                                $batting_avg = get_post_meta($player_id, '_batting_avg', true);
                                $games = get_post_meta($player_id, '_games_played', true);
                                $at_bats = get_post_meta($player_id, '_at_bats', true);
                                $hits = get_post_meta($player_id, '_hits', true);
                                $home_runs = get_post_meta($player_id, '_home_runs', true);
                                $rbis = get_post_meta($player_id, '_rbis', true);
                                $runs = get_post_meta($player_id, '_runs', true);
                                $walks = get_post_meta($player_id, '_walks', true);
                                $strikeouts = get_post_meta($player_id, '_strikeouts', true);
                                $stolen_bases = get_post_meta($player_id, '_stolen_bases', true);
                                
                                $positions = wp_get_post_terms($player_id, 'position');
                                $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                                $team_id = get_post_meta($player_id, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'FA';
                            ?>
                            <tr>
                                <td data-value="<?php echo esc_attr($player_number ?: 0); ?>"><?php echo esc_html($player_number ?: '-'); ?></td>
                                <td data-value="<?php echo esc_attr($player->post_title); ?>">
                                    <div class="player-name-cell">
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
                                    </div>
                                </td>
                                <td data-value="<?php echo esc_attr($team_name); ?>"><?php echo esc_html($team_name); ?></td>
                                <td data-value="<?php echo esc_attr($position_name); ?>"><?php echo esc_html($position_name); ?></td>
                                <td data-value="<?php echo esc_attr($batting_avg ?: 0); ?>" class="stat-highlight"><?php echo esc_html($batting_avg ?: '.000'); ?></td>
                                <td data-value="<?php echo esc_attr($games ?: 0); ?>"><?php echo esc_html($games ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($at_bats ?: 0); ?>"><?php echo esc_html($at_bats ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($hits ?: 0); ?>"><?php echo esc_html($hits ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($home_runs ?: 0); ?>" class="stat-highlight"><?php echo esc_html($home_runs ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($rbis ?: 0); ?>" class="stat-highlight"><?php echo esc_html($rbis ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($runs ?: 0); ?>"><?php echo esc_html($runs ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($walks ?: 0); ?>"><?php echo esc_html($walks ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($strikeouts ?: 0); ?>"><?php echo esc_html($strikeouts ?: '0'); ?></td>
                                <td data-value="<?php echo esc_attr($stolen_bases ?: 0); ?>" class="stat-highlight"><?php echo esc_html($stolen_bases ?: '0'); ?></td>
                                <td>
                                    <a href="<?php echo get_permalink($player_id); ?>" class="btn-small">Ver</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="table-legend">
                    <p><strong>Leyenda:</strong> # = Número, Pos = Posición, AVG = Promedio de Bateo, J = Juegos, AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, R = Carreras, BB = Bases por Bolas, SO = Ponches, SB = Bases Robadas</p>
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
    const table = document.getElementById('players-stats-table');
    if (!table) return;
    
    const headers = table.querySelectorAll('th.sortable');
    let currentSort = { column: null, direction: 'asc' };
    
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const sortType = this.getAttribute('data-sort');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Toggle direction
            if (currentSort.column === sortType) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'desc'; // Default to descending for stats
                currentSort.column = sortType;
            }
            
            // Remove all sort indicators
            headers.forEach(h => {
                h.querySelector('.sort-arrow').textContent = '↕';
                h.classList.remove('sorted-asc', 'sorted-desc');
            });
            
            // Add sort indicator
            const arrow = this.querySelector('.sort-arrow');
            arrow.textContent = currentSort.direction === 'asc' ? '↑' : '↓';
            this.classList.add(currentSort.direction === 'asc' ? 'sorted-asc' : 'sorted-desc');
            
            // Sort rows
            rows.sort((a, b) => {
                const aCell = a.querySelector(`td[data-value]:nth-child(${getColumnIndex(sortType)})`);
                const bCell = b.querySelector(`td[data-value]:nth-child(${getColumnIndex(sortType)})`);
                
                let aVal = aCell.getAttribute('data-value');
                let bVal = bCell.getAttribute('data-value');
                
                // Convert to numbers if numeric
                if (!isNaN(aVal) && !isNaN(bVal)) {
                    aVal = parseFloat(aVal);
                    bVal = parseFloat(bVal);
                } else {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }
                
                if (currentSort.direction === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        });
    });
    
    function getColumnIndex(sortType) {
        const mapping = {
            'avg': 5,
            'games': 6,
            'ab': 7,
            'hits': 8,
            'hr': 9,
            'rbi': 10,
            'runs': 11,
            'bb': 12,
            'so': 13,
            'sb': 14
        };
        return mapping[sortType] || 1;
    }
});
</script>

<?php get_footer(); ?>
