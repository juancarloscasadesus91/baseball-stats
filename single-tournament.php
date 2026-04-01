<?php
/**
 * Single Tournament Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main">
    <?php while (have_posts()) : the_post(); 
        $season_id = get_post_meta(get_the_ID(), '_tournament_season', true);
        $start_date = get_post_meta(get_the_ID(), '_tournament_start_date', true);
        $end_date = get_post_meta(get_the_ID(), '_tournament_end_date', true);
        
        // Get teams in this tournament
        $teams_args = array(
            'post_type' => 'team',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_team_tournaments',
                    'value' => serialize(strval(get_the_ID())),
                    'compare' => 'LIKE'
                )
            )
        );
        $teams = get_posts($teams_args);
        
        // Get games in this tournament
        $games = get_posts(array(
            'post_type' => 'game',
            'posts_per_page' => -1,
            'meta_key' => '_game_tournament',
            'meta_value' => get_the_ID(),
            'orderby' => 'meta_value',
            'meta_key' => '_game_date',
            'order' => 'DESC'
        ));
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <?php if (has_post_thumbnail()): ?>
                <div class="tournament-logo-large">
                    <?php the_post_thumbnail('medium'); ?>
                </div>
            <?php endif; ?>
            
            <h1 class="entry-title"><?php the_title(); ?></h1>
            
            <div class="tournament-meta">
                <?php if ($season_id): ?>
                    <span class="tournament-season">
                        <strong>Temporada:</strong> 
                        <a href="<?php echo get_permalink($season_id); ?>">
                            <?php echo get_the_title($season_id); ?>
                        </a>
                    </span>
                <?php endif; ?>
                <?php if ($start_date && $end_date): ?>
                    <span class="tournament-dates">
                        <strong>Período:</strong> 
                        <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                        <?php echo date('d/m/Y', strtotime($end_date)); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>

        <div class="entry-content">
            <?php the_content(); ?>
            
            <?php if ($teams): ?>
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
                            ?>
                            <tr>
                                <td><?php echo $pos++; ?></td>
                                <td>
                                    <strong>
                                        <a href="<?php echo get_permalink($team->ID); ?>">
                                            <?php echo esc_html($team->post_title); ?>
                                        </a>
                                    </strong>
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
            <?php endif; ?>
            
            <?php if ($games): ?>
                <section class="tournament-games">
                    <h2>Partidos del Torneo</h2>
                    <div class="games-list">
                        <?php foreach ($games as $game): 
                            $home_team_id = get_post_meta($game->ID, '_game_home_team', true);
                            $away_team_id = get_post_meta($game->ID, '_game_away_team', true);
                            $home_score = get_post_meta($game->ID, '_game_home_score', true);
                            $away_score = get_post_meta($game->ID, '_game_away_score', true);
                            $game_date = get_post_meta($game->ID, '_game_date', true);
                            $game_time = get_post_meta($game->ID, '_game_time', true);
                        ?>
                            <div class="game-card">
                                <div class="game-date">
                                    <?php if ($game_date): ?>
                                        <?php echo date('d/m/Y', strtotime($game_date)); ?>
                                        <?php if ($game_time): ?>
                                            - <?php echo date('H:i', strtotime($game_time)); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="game-matchup">
                                    <div class="team away-team">
                                        <span class="team-name"><?php echo get_the_title($away_team_id); ?></span>
                                        <span class="team-score"><?php echo $away_score !== '' ? $away_score : '-'; ?></span>
                                    </div>
                                    <div class="vs">vs</div>
                                    <div class="team home-team">
                                        <span class="team-name"><?php echo get_the_title($home_team_id); ?></span>
                                        <span class="team-score"><?php echo $home_score !== '' ? $home_score : '-'; ?></span>
                                    </div>
                                </div>
                                <a href="<?php echo get_permalink($game->ID); ?>" class="btn btn-small">Ver Detalles</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </article>

    <?php endwhile; ?>
</main>

<?php
get_footer();
