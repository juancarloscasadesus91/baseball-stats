<?php
/**
 * Archive Games Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main games-archive-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">Partidos</h1>
        </header>

        <?php if (have_posts()): ?>
            <div class="games-grid">
                <?php while (have_posts()): the_post(); 
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
                    <a href="<?php the_permalink(); ?>" class="game-box">
                        <div class="game-status">FINAL</div>
                        
                        <div class="game-teams">
                            <div class="team-row">
                                <div class="team-info">
                                    <?php if (has_post_thumbnail($away_team_id)): ?>
                                        <div class="team-logo-mini">
                                            <?php echo get_the_post_thumbnail($away_team_id, 'thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="team-name-short"><?php echo get_the_title($away_team_id); ?></span>
                                </div>
                                <div class="team-scores">
                                    <span class="score-r"><?php echo $away_score !== '' ? $away_score : '0'; ?></span>
                                    <span class="score-h"><?php echo $away_hits !== '' ? $away_hits : '-'; ?></span>
                                    <span class="score-e"><?php echo $away_errors !== '' ? $away_errors : '-'; ?></span>
                                </div>
                            </div>
                            
                            <div class="team-row">
                                <div class="team-info">
                                    <?php if (has_post_thumbnail($home_team_id)): ?>
                                        <div class="team-logo-mini">
                                            <?php echo get_the_post_thumbnail($home_team_id, 'thumbnail'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="team-name-short"><?php echo get_the_title($home_team_id); ?></span>
                                </div>
                                <div class="team-scores">
                                    <span class="score-r"><?php echo $home_score !== '' ? $home_score : '0'; ?></span>
                                    <span class="score-h"><?php echo $home_hits !== '' ? $home_hits : '-'; ?></span>
                                    <span class="score-e"><?php echo $home_errors !== '' ? $home_errors : '-'; ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Siguiente →',
                ));
                ?>
            </div>
        <?php else: ?>
            <div class="no-content">
                <p>No se encontraron partidos.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
