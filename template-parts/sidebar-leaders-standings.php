<?php
/**
 * Shared sidebar widgets for leaders and standings.
 *
 * @package Baseball_Stats
 */

$sidebar_active_season_id = function_exists('baseball_get_active_season_id') ? baseball_get_active_season_id() : 0;
$sidebar_active_season_name = $sidebar_active_season_id ? get_the_title($sidebar_active_season_id) : '';
?>

<!-- Leaders Widget with Tabs -->
<div class="sidebar-widget leaders-widget">
    <h3 class="widget-title">L&iacute;deres</h3>
    <?php if ($sidebar_active_season_name) : ?>
        <p class="widget-subtitle"><?php echo esc_html($sidebar_active_season_name); ?></p>
    <?php endif; ?>

    <div class="leaders-main-tabs">
        <button class="main-tab active" data-tab="bateo">BATEO</button>
        <button class="main-tab" data-tab="pitcheo">PITCHEO</button>
    </div>

    <div class="tab-content active" id="bateo-content">
        <div class="leaders-filter-tabs">
            <button class="filter-tab active" data-stat="avg">AVG</button>
            <button class="filter-tab" data-stat="obp">OBP</button>
            <button class="filter-tab" data-stat="hr">HR</button>
            <button class="filter-tab" data-stat="runs">R</button>
            <button class="filter-tab" data-stat="rbis">RBI</button>
            <button class="filter-tab" data-stat="hits">H</button>
            <button class="filter-tab" data-stat="doubles">2B</button>
            <button class="filter-tab" data-stat="triples">3B</button>
            <button class="filter-tab" data-stat="bb">BB</button>
            <button class="filter-tab" data-stat="strikeouts">SO</button>
            <button class="filter-tab" data-stat="errors">E</button>
        </div>

        <div id="bateo-stats-container" class="stats-list-container"></div>
    </div>

    <div class="tab-content" id="pitcheo-content">
        <div class="leaders-filter-tabs">
            <button class="filter-tab active" data-stat="era">ERA</button>
            <button class="filter-tab" data-stat="wins">W</button>
            <button class="filter-tab" data-stat="so">K</button>
            <button class="filter-tab" data-stat="ip">IP</button>
        </div>

        <div id="pitcheo-stats-container" class="stats-list-container"></div>
    </div>
</div>

<!-- Team Standings -->
<div class="sidebar-widget">
    <h3 class="widget-title">Tabla de Posiciones</h3>
    <?php if ($sidebar_active_season_name) : ?>
        <p class="widget-subtitle"><?php echo esc_html($sidebar_active_season_name); ?></p>
    <?php endif; ?>
    <?php
    $standings = $sidebar_active_season_id && function_exists('baseball_get_season_standings')
        ? baseball_get_season_standings($sidebar_active_season_id)
        : array();

    if (!$sidebar_active_season_id) : ?>
        <p class="no-active-season"><em>No hay temporada activa.</em></p>
    <?php elseif (empty($standings)) : ?>
        <p class="no-active-season"><em>La temporada activa a&uacute;n no tiene partidos jugados.</em></p>
    <?php else : ?>
        <div class="standings-table">
            <table>
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>V</th>
                        <th>D</th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $row) :
                        $pct = number_format($row->pct, 3);
                    ?>
                    <tr>
                        <td class="team-name">
                            <a href="<?php echo get_permalink($row->team_id); ?>">
                                <?php echo esc_html($row->title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($row->wins); ?></td>
                        <td><?php echo esc_html($row->losses); ?></td>
                        <td><strong><?php echo esc_html($pct); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    const leaderStats = {
        bateo: {
            avg: { meta: '_batting_avg', order: 'DESC' },
            obp: { meta: '_on_base_percentage', order: 'DESC' },
            hr: { meta: '_home_runs', order: 'DESC' },
            runs: { meta: '_runs', order: 'DESC' },
            rbis: { meta: '_rbis', order: 'DESC' },
            hits: { meta: '_hits', order: 'DESC' },
            doubles: { meta: '_doubles', order: 'DESC' },
            triples: { meta: '_triples', order: 'DESC' },
            bb: { meta: '_walks', order: 'DESC' },
            strikeouts: { meta: '_strikeouts', order: 'DESC' },
            errors: { meta: '_errors', order: 'DESC' }
        },
        pitcheo: {
            era: { meta: '_era', order: 'ASC' },
            wins: { meta: '_pitching_wins', order: 'DESC' },
            so: { meta: '_pitching_strikeouts', order: 'DESC' },
            ip: { meta: '_innings_pitched', order: 'DESC' }
        }
    };

    $('.main-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.main-tab').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tab + '-content').addClass('active');

        const defaultStat = $('#' + tab + '-content .filter-tab.active').data('stat');
        loadLeaders(tab, defaultStat);
    });

    $('.filter-tab').on('click', function() {
        const stat = $(this).data('stat');
        const tab = $(this).closest('.tab-content').attr('id').replace('-content', '');

        $(this).siblings().removeClass('active');
        $(this).addClass('active');

        loadLeaders(tab, stat);
    });

    function loadLeaders(category, stat) {
        const container = $('#' + category + '-stats-container');
        const statConfig = leaderStats[category][stat];

        container.html('<div class="loading">Cargando...</div>');

        $.ajax({
            url: '<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
            type: 'POST',
            data: {
                action: 'get_leaders',
                category: category,
                stat: stat,
                meta_key: statConfig.meta,
                order: statConfig.order
            },
            success: function(response) {
                if (response.success) {
                    container.html(response.data.html);
                } else {
                    container.html('<p>No hay datos disponibles</p>');
                }
            },
            error: function() {
                container.html('<p>Error al cargar los datos</p>');
            }
        });
    }

    loadLeaders('bateo', 'avg');
});
</script>
