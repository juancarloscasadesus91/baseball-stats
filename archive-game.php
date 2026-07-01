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

if ($selected_tournament) {
    $meta_query[] = array(
        'key' => '_game_tournament',
        'value' => $selected_tournament,
        'compare' => '=',
    );
} elseif ($selected_season) {
    $season_tournament_ids = wp_list_pluck($tournaments, 'ID');
    $meta_query[] = !empty($season_tournament_ids)
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

if ($selected_team) {
    $meta_query[] = array(
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

            <div class="game-filter-actions">
                <button type="submit" class="btn">Filtrar</button>
                <a href="<?php echo esc_url(get_post_type_archive_link('game')); ?>" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

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

<?php
get_footer();
