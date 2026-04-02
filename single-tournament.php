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
        
        <?php if ($games): ?>
            <div class="stats-card">
                <section class="tournament-games">
                    <h2>Partidos del Torneo (<?php echo count($games); ?>)</h2>
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
                                        Ver Detalles →
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        <?php else: ?>
            <div class="stats-card">
                <div class="no-content">
                    <p>No hay partidos registrados en este torneo.</p>
                </div>
            </div>
        <?php endif; ?>
    </article>

    <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
