<?php
/**
 * Archive Games Template
 *
 * @package Baseball_Stats
 */

get_header();

$selected_season = isset($_GET['game_season']) ? absint($_GET['game_season']) : 0;
$selected_tournament = isset($_GET['game_tournament']) ? absint($_GET['game_tournament']) : 0;
$selected_team = isset($_GET['game_team']) ? absint($_GET['game_team']) : 0;
$selected_vs_team = isset($_GET['game_vs_team']) ? absint($_GET['game_vs_team']) : 0;

$seasons = get_posts(array(
    'post_type' => 'season',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'DESC',
));

$tournament_args = array(
    'post_type' => 'tournament',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
);

if ($selected_season) {
    $tournament_args['meta_key'] = '_tournament_season';
    $tournament_args['meta_value'] = $selected_season;
}

$tournaments = get_posts($tournament_args);
$teams = get_posts(array(
    'post_type' => 'team',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
));

$paged = max(1, get_query_var('paged'), get_query_var('page'));
$meta_query = array('relation' => 'AND');
$scope_meta_query = array('relation' => 'AND');

if ($selected_tournament) {
    $scope_meta_query[] = array(
        'key' => '_game_tournament',
        'value' => $selected_tournament,
        'compare' => '=',
    );
} elseif ($selected_season) {
    $season_tournament_ids = wp_list_pluck($tournaments, 'ID');
    $scope_meta_query[] = !empty($season_tournament_ids)
        ? array(
            'key' => '_game_tournament',
            'value' => $season_tournament_ids,
            'compare' => 'IN',
        )
        : array(
            'key' => '_game_tournament',
            'value' => 0,
            'compare' => '=',
        );
}

if (count($scope_meta_query) > 1) {
    foreach (array_slice($scope_meta_query, 1) as $scope_filter) {
        $meta_query[] = $scope_filter;
    }
}

$vs_teams = array();
$vs_options_by_team = array();
$vs_scope_query_args = array(
    'post_type' => 'game',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
);

if (count($scope_meta_query) > 1) {
    $vs_scope_query_args['meta_query'] = $scope_meta_query;
}

$vs_scope_game_ids = get_posts($vs_scope_query_args);

foreach ($vs_scope_game_ids as $vs_scope_game_id) {
    $home_id = absint(get_post_meta($vs_scope_game_id, '_game_home_team', true));
    $away_id = absint(get_post_meta($vs_scope_game_id, '_game_away_team', true));

    if (!$home_id || !$away_id) {
        continue;
    }

    if (!isset($vs_options_by_team[$home_id])) {
        $vs_options_by_team[$home_id] = array();
    }
    if (!isset($vs_options_by_team[$away_id])) {
        $vs_options_by_team[$away_id] = array();
    }

    $vs_options_by_team[$home_id][$away_id] = get_the_title($away_id);
    $vs_options_by_team[$away_id][$home_id] = get_the_title($home_id);
}

foreach ($vs_options_by_team as $team_id => $opponents) {
    asort($opponents, SORT_NATURAL | SORT_FLAG_CASE);
    $vs_options_by_team[$team_id] = $opponents;
}

if ($selected_team) {
    foreach ($vs_options_by_team[$selected_team] ?? array() as $vs_team_id => $vs_team_title) {
        $vs_teams[] = (object) array(
            'ID' => $vs_team_id,
            'post_title' => $vs_team_title,
        );
    }
}

if ($selected_team) {
    $team_filter = array(
        'relation' => 'OR',
        array(
            'key' => '_game_home_team',
            'value' => $selected_team,
            'compare' => '=',
        ),
        array(
            'key' => '_game_away_team',
            'value' => $selected_team,
            'compare' => '=',
        ),
    );

    if ($selected_vs_team) {
        $team_filter = array(
            'relation' => 'OR',
            array(
                'relation' => 'AND',
                array(
                    'key' => '_game_home_team',
                    'value' => $selected_team,
                    'compare' => '=',
                ),
                array(
                    'key' => '_game_away_team',
                    'value' => $selected_vs_team,
                    'compare' => '=',
                ),
            ),
            array(
                'relation' => 'AND',
                array(
                    'key' => '_game_away_team',
                    'value' => $selected_team,
                    'compare' => '=',
                ),
                array(
                    'key' => '_game_home_team',
                    'value' => $selected_vs_team,
                    'compare' => '=',
                ),
            ),
        );
    }

    $meta_query[] = $team_filter;
}

$vs_summary = null;
if ($selected_team && $selected_vs_team) {
    $summary_query_args = array(
        'post_type' => 'game',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
    );

    if (count($meta_query) > 1) {
        $summary_query_args['meta_query'] = $meta_query;
    }

    $summary_game_ids = get_posts($summary_query_args);

    $vs_summary = array(
        'games' => count($summary_game_ids),
        'wins' => 0,
        'losses' => 0,
        'run_diff' => 0,
        'avg' => '.000',
        'h' => 0,
        'doubles' => 0,
        'triples' => 0,
        'hr' => 0,
        'bb' => 0,
        'e' => 0,
    );

    foreach ($summary_game_ids as $summary_game_id) {
        $home_id = absint(get_post_meta($summary_game_id, '_game_home_team', true));
        $away_id = absint(get_post_meta($summary_game_id, '_game_away_team', true));
        $home_score = intval(get_post_meta($summary_game_id, '_game_home_score', true));
        $away_score = intval(get_post_meta($summary_game_id, '_game_away_score', true));

        if ($home_id === $selected_team) {
            $team_score = $home_score;
            $opponent_score = $away_score;
        } elseif ($away_id === $selected_team) {
            $team_score = $away_score;
            $opponent_score = $home_score;
        } else {
            continue;
        }

        $vs_summary['run_diff'] += ($team_score - $opponent_score);

        if ($team_score > $opponent_score) {
            $vs_summary['wins']++;
        } elseif ($team_score < $opponent_score) {
            $vs_summary['losses']++;
        }
    }

    if (!empty($summary_game_ids)) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'baseball_game_stats';
        $placeholders = implode(',', array_fill(0, count($summary_game_ids), '%d'));
        $summary_sql = "SELECT
                SUM(at_bats) AS ab,
                SUM(hits) AS h,
                SUM(doubles) AS doubles,
                SUM(triples) AS triples,
                SUM(home_runs) AS hr,
                SUM(walks) AS bb,
                SUM(errors) AS e
            FROM $stats_table
            WHERE team_id = %d
            AND game_id IN ($placeholders)";

        $summary_stats = $wpdb->get_row($wpdb->prepare($summary_sql, array_merge(array($selected_team), $summary_game_ids)));

        if ($summary_stats) {
            $ab = intval($summary_stats->ab);
            $hits = intval($summary_stats->h);
            $vs_summary['avg'] = $ab > 0 ? number_format($hits / $ab, 3) : '.000';
            $vs_summary['h'] = $hits;
            $vs_summary['doubles'] = intval($summary_stats->doubles);
            $vs_summary['triples'] = intval($summary_stats->triples);
            $vs_summary['hr'] = intval($summary_stats->hr);
            $vs_summary['bb'] = intval($summary_stats->bb);
            $vs_summary['e'] = intval($summary_stats->e);
        }
    }
}

$games_query_args = array(
    'post_type' => 'game',
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => $paged,
    'meta_key' => '_game_date',
    'orderby' => 'meta_value',
    'order' => 'DESC',
);

if (count($meta_query) > 1) {
    $games_query_args['meta_query'] = $meta_query;
}

$games_query = new WP_Query($games_query_args);
?>

<main class="site-main games-archive-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">Partidos</h1>
        </header>

        <form class="game-filters" method="get" action="<?php echo esc_url(get_post_type_archive_link('game')); ?>">
            <div class="game-filter-field">
                <label for="game-season-filter">Temporada</label>
                <select id="game-season-filter" name="game_season">
                    <option value="">Todas</option>
                    <?php foreach ($seasons as $season): ?>
                        <option value="<?php echo esc_attr($season->ID); ?>" <?php selected($selected_season, $season->ID); ?>>
                            <?php echo esc_html($season->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="game-filter-field">
                <label for="game-tournament-filter">Torneo</label>
                <select id="game-tournament-filter" name="game_tournament">
                    <option value="">Todos</option>
                    <?php foreach ($tournaments as $tournament): ?>
                        <option value="<?php echo esc_attr($tournament->ID); ?>" <?php selected($selected_tournament, $tournament->ID); ?>>
                            <?php echo esc_html($tournament->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="game-filter-field">
                <label for="game-team-filter">Equipo</label>
                <select id="game-team-filter" name="game_team">
                    <option value="">Todos</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo esc_attr($team->ID); ?>" <?php selected($selected_team, $team->ID); ?>>
                            <?php echo esc_html($team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="game-filter-field">
                <label for="game-vs-team-filter">VS</label>
                <select id="game-vs-team-filter" name="game_vs_team" <?php disabled(!$selected_team); ?>>
                    <option value=""><?php echo $selected_team ? 'Todos' : 'Selecciona un equipo'; ?></option>
                    <?php foreach ($vs_teams as $vs_team): ?>
                        <option value="<?php echo esc_attr($vs_team->ID); ?>" <?php selected($selected_vs_team, $vs_team->ID); ?>>
                            <?php echo esc_html($vs_team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="game-filter-actions">
                <button type="submit" class="btn">Filtrar</button>
                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        <?php if ($vs_summary): ?>
            <div class="game-vs-summary">
                <div class="game-vs-summary-title">
                    <?php echo esc_html(get_the_title($selected_team)); ?> vs <?php echo esc_html(get_the_title($selected_vs_team)); ?>
                </div>
                <div class="game-vs-summary-grid">
                    <div class="summary-stat"><span>V</span><strong><?php echo esc_html($vs_summary['wins']); ?></strong></div>
                    <div class="summary-stat"><span>D</span><strong><?php echo esc_html($vs_summary['losses']); ?></strong></div>
                    <div class="summary-stat"><span>DIF</span><strong><?php echo esc_html($vs_summary['run_diff'] > 0 ? '+' . $vs_summary['run_diff'] : $vs_summary['run_diff']); ?></strong></div>
                    <div class="summary-stat"><span>AVG</span><strong><?php echo esc_html($vs_summary['avg']); ?></strong></div>
                    <div class="summary-stat"><span>H</span><strong><?php echo esc_html($vs_summary['h']); ?></strong></div>
                    <div class="summary-stat"><span>2B</span><strong><?php echo esc_html($vs_summary['doubles']); ?></strong></div>
                    <div class="summary-stat"><span>3B</span><strong><?php echo esc_html($vs_summary['triples']); ?></strong></div>
                    <div class="summary-stat"><span>HR</span><strong><?php echo esc_html($vs_summary['hr']); ?></strong></div>
                    <div class="summary-stat"><span>BB</span><strong><?php echo esc_html($vs_summary['bb']); ?></strong></div>
                    <div class="summary-stat"><span>E</span><strong><?php echo esc_html($vs_summary['e']); ?></strong></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($games_query->have_posts()): ?>
            <div class="games-grid">
                <?php while ($games_query->have_posts()): $games_query->the_post(); 
                    $tournament_id = get_post_meta(get_the_ID(), '_game_tournament', true);
                    $home_team_id = get_post_meta(get_the_ID(), '_game_home_team', true);
                    $away_team_id = get_post_meta(get_the_ID(), '_game_away_team', true);
                    $home_score = get_post_meta(get_the_ID(), '_game_home_score', true);
                    $away_score = get_post_meta(get_the_ID(), '_game_away_score', true);
                    $game_date = get_post_meta(get_the_ID(), '_game_date', true);
                    $game_time = get_post_meta(get_the_ID(), '_game_time', true);
                    $location = get_post_meta(get_the_ID(), '_game_location', true);
                    
                    // Get R-H-E data
                    $away_hits = get_post_meta(get_the_ID(), '_game_away_hits', true);
                    $home_hits = get_post_meta(get_the_ID(), '_game_home_hits', true);
                    $away_errors = get_post_meta(get_the_ID(), '_game_away_errors', true);
                    $home_errors = get_post_meta(get_the_ID(), '_game_home_errors', true);
                    
                    $is_final = true; // Puedes agregar lógica para determinar si el partido finalizó
                ?>
                    <?php
                    // Calculate team records
                    $away_record = baseball_get_team_record($away_team_id);
                    $home_record = baseball_get_team_record($home_team_id);
                    ?>
                    <a href="<?php the_permalink(); ?>" class="game-box">
                        <div class="game-header">
                            <div class="game-status">F</div>
                            <div class="game-rhe-labels">
                                <span>R</span>
                                <span>H</span>
                                <span>E</span>
                            </div>
                        </div>
                        
                        <div class="game-teams">
                            <div class="team-row">
                                <div class="team-info">
                                    <?php if (has_post_thumbnail($away_team_id)): ?>
                                        <div class="team-logo-mini">
                                            <?php echo get_the_post_thumbnail($away_team_id, 'thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="team-details">
                                        <span class="team-name-short"><?php echo get_the_title($away_team_id); ?></span>
                                        <span class="team-record">(<?php echo $away_record; ?>)</span>
                                    </div>
                                </div>
                                <div class="team-scores">
                                    <span class="score-r"><?php echo $away_score !== '' ? $away_score : '0'; ?></span>
                                    <span class="score-h"><?php echo $away_hits !== '' ? $away_hits : '0'; ?></span>
                                    <span class="score-e"><?php echo $away_errors !== '' ? $away_errors : '0'; ?></span>
                                </div>
                            </div>
                            
                            <div class="team-row">
                                <div class="team-info">
                                    <?php if (has_post_thumbnail($home_team_id)): ?>
                                        <div class="team-logo-mini">
                                            <?php echo get_the_post_thumbnail($home_team_id, 'thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="team-details">
                                        <span class="team-name-short"><?php echo get_the_title($home_team_id); ?></span>
                                        <span class="team-record">(<?php echo $home_record; ?>)</span>
                                    </div>
                                </div>
                                <div class="team-scores">
                                    <span class="score-r"><?php echo $home_score !== '' ? $home_score : '0'; ?></span>
                                    <span class="score-h"><?php echo $home_hits !== '' ? $home_hits : '0'; ?></span>
                                    <span class="score-e"><?php echo $home_errors !== '' ? $home_errors : '0'; ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'total' => $games_query->max_num_pages,
                    'current' => $paged,
                    'mid_size' => 2,
                    'prev_text' => '&larr; Anterior',
                    'next_text' => 'Siguiente &rarr;',
                    'add_args' => array_filter(array(
                        'game_season' => $selected_season,
                        'game_tournament' => $selected_tournament,
                        'game_team' => $selected_team,
                        'game_vs_team' => $selected_vs_team,
                    )),
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="no-content">
                <p>No se encontraron partidos.</p>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var teamFilter = document.getElementById('game-team-filter');
    var vsFilter = document.getElementById('game-vs-team-filter');
    var vsOptionsByTeam = <?php echo wp_json_encode($vs_options_by_team); ?>;
    var selectedVsTeam = '<?php echo esc_js((string) $selected_vs_team); ?>';

    if (!teamFilter || !vsFilter) {
        return;
    }

    function rebuildVsOptions(teamId, selectedValue) {
        var opponents = vsOptionsByTeam[teamId] || {};
        var opponentIds = Object.keys(opponents);

        vsFilter.innerHTML = '';

        var defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = teamId ? 'Todos' : 'Selecciona un equipo';
        vsFilter.appendChild(defaultOption);

        opponentIds.forEach(function (opponentId) {
            var option = document.createElement('option');
            option.value = opponentId;
            option.textContent = opponents[opponentId];
            if (String(selectedValue) === String(opponentId)) {
                option.selected = true;
            }
            vsFilter.appendChild(option);
        });

        vsFilter.disabled = !teamId || opponentIds.length === 0;

        if (vsFilter.disabled || !opponents[selectedValue]) {
            vsFilter.value = '';
        }
    }

    rebuildVsOptions(teamFilter.value, selectedVsTeam);

    teamFilter.addEventListener('change', function () {
        selectedVsTeam = '';
        rebuildVsOptions(this.value, '');
    });
});
</script>

<?php
get_footer();
