<?php
/**
 * Archive Teams Template
 *
 * @package Baseball_Stats
 */

get_header();

$teams = get_posts(array(
    'post_type' => 'team',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
));

$game_ids = get_posts(array(
    'post_type' => 'game',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'fields' => 'ids',
));

$team_batting_defaults = baseball_get_team_batting_defaults();
$team_batting_stats = baseball_get_team_batting_stats_for_games($game_ids);
$team_pitching_stats = baseball_get_team_pitching_stats_for_games($game_ids);
$comparison_batting_metrics = baseball_get_team_comparison_batting_metrics();
$comparison_pitching_metrics = baseball_get_team_comparison_pitching_metrics();
$standings = array();

foreach ($teams as $team) {
    $team_id = intval($team->ID);

    $standings[] = array(
        'team' => $team,
        'stats' => baseball_get_team_stats($team_id),
        'batting' => array_merge($team_batting_defaults, isset($team_batting_stats[$team_id]) ? $team_batting_stats[$team_id] : array()),
    );
}

usort($standings, function ($a, $b) {
    if ($a['stats']['wins'] == $b['stats']['wins']) {
        if ($a['stats']['runs_scored'] == $b['stats']['runs_scored']) {
            return strcasecmp($a['team']->post_title, $b['team']->post_title);
        }

        return $b['stats']['runs_scored'] - $a['stats']['runs_scored'];
    }

    return $b['stats']['wins'] - $a['stats']['wins'];
});

$comparison_teams = array_map(function ($standing) {
    return $standing['team'];
}, $standings);
$comparison_team_a = !empty($comparison_teams) ? intval($comparison_teams[0]->ID) : 0;
$comparison_team_b = isset($comparison_teams[1]) ? intval($comparison_teams[1]->ID) : $comparison_team_a;
$comparison_data = baseball_get_team_comparison_data($comparison_teams, $team_batting_stats, $team_pitching_stats);
?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Todos los Equipos</h1>
            <p>Tabla de posiciones y estad&iacute;sticas de equipos</p>
        </div>

        <?php if (!empty($standings)) : ?>
            <div class="stats-card tournament-section-card tournament-standings-card">
                <section class="tournament-standings">
                    <h2>Tabla de Posiciones</h2>
                    <div class="table-responsive">
                        <table class="stats-table teams-table">
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
                                $pos = 1;
                                foreach ($standings as $standing) :
                                    $team = $standing['team'];
                                    $stats = $standing['stats'];
                                    $batting_stats = $standing['batting'];
                                    $team_id = intval($team->ID);
                                    $team_name = $team->post_title;
                                    $team_abbr = strtoupper(substr($team_name, 0, 3));
                                ?>
                                <tr>
                                    <td data-value="<?php echo esc_attr($pos); ?>"><?php echo esc_html($pos++); ?></td>
                                    <td data-value="<?php echo esc_attr($team_name); ?>">
                                        <div class="team-name-cell">
                                            <?php if (has_post_thumbnail($team_id)) : ?>
                                                <div class="team-mini-logo">
                                                    <?php echo get_the_post_thumbnail($team_id, 'thumbnail'); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="team-name-text">
                                                <a href="<?php echo esc_url(get_permalink($team_id)); ?>">
                                                    <span class="team-full-name"><?php echo esc_html($team_name); ?></span>
                                                    <span class="team-abbr-name"><?php echo esc_html($team_abbr); ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-value="<?php echo esc_attr($stats['games']); ?>"><?php echo esc_html($stats['games']); ?></td>
                                    <td data-value="<?php echo esc_attr($stats['wins']); ?>"><?php echo esc_html($stats['wins']); ?></td>
                                    <td data-value="<?php echo esc_attr($stats['losses']); ?>"><?php echo esc_html($stats['losses']); ?></td>
                                    <td data-value="<?php echo esc_attr($stats['win_pct']); ?>"><?php echo esc_html($stats['win_pct']); ?></td>
                                    <td data-value="<?php echo esc_attr($stats['runs_scored']); ?>"><?php echo esc_html($stats['runs_scored']); ?></td>
                                    <td data-value="<?php echo esc_attr($stats['runs_allowed']); ?>"><?php echo esc_html($stats['runs_allowed']); ?></td>
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

            <?php if (count($comparison_teams) >= 2) : ?>
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
                                        <?php foreach ($comparison_teams as $team) : ?>
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
                                        <?php foreach ($comparison_teams as $team) : ?>
                                            <option value="<?php echo esc_attr($team->ID); ?>" <?php selected($comparison_team_b, intval($team->ID)); ?>>
                                                <?php echo esc_html($team->post_title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                            </div>

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
                                            <?php foreach ($comparison_batting_metrics as $metric) : ?>
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
                                            <?php foreach ($comparison_pitching_metrics as $metric) : ?>
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
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay equipos registrados</h2>
                <p>A&uacute;n no se han a&ntilde;adido equipos al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var teamComparisonData = <?php echo wp_json_encode($comparison_data); ?>;
    var comparisonTeamA = document.getElementById('comparison-team-a');
    var comparisonTeamB = document.getElementById('comparison-team-b');

    function getComparisonValue(teamId, group, metric) {
        if (!teamComparisonData[teamId] || !teamComparisonData[teamId][group]) {
            return '0';
        }

        return teamComparisonData[teamId][group][metric] !== undefined
            ? String(teamComparisonData[teamId][group][metric])
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
                var valueA = getComparisonValue(teamA, group, metric);
                var valueB = getComparisonValue(teamB, group, metric);
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
});
</script>

<?php get_footer(); ?>
