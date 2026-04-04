<?php
/**
 * Single Game Template
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main game-single-page">
    <div class="container">
    <?php while (have_posts()) : the_post(); 
        $tournament_id = get_post_meta(get_the_ID(), '_game_tournament', true);
        $home_team_id = get_post_meta(get_the_ID(), '_game_home_team', true);
        $away_team_id = get_post_meta(get_the_ID(), '_game_away_team', true);
        $home_score = get_post_meta(get_the_ID(), '_game_home_score', true);
        $away_score = get_post_meta(get_the_ID(), '_game_away_score', true);
        $game_date = get_post_meta(get_the_ID(), '_game_date', true);
        $game_time = get_post_meta(get_the_ID(), '_game_time', true);
        $location = get_post_meta(get_the_ID(), '_game_location', true);
        
        // Get innings data
        $away_innings = get_post_meta(get_the_ID(), '_game_away_innings', true);
        $home_innings = get_post_meta(get_the_ID(), '_game_home_innings', true);
        $away_hits = get_post_meta(get_the_ID(), '_game_away_hits', true);
        $home_hits = get_post_meta(get_the_ID(), '_game_home_hits', true);
        $away_errors = get_post_meta(get_the_ID(), '_game_away_errors', true);
        $home_errors = get_post_meta(get_the_ID(), '_game_home_errors', true);
        
        // Parse innings
        $away_innings_array = $away_innings ? explode(',', $away_innings) : array_fill(0, 9, '-');
        $home_innings_array = $home_innings ? explode(',', $home_innings) : array_fill(0, 9, '-');
        
        // Get game stats
        global $wpdb;
        $table_name = $wpdb->prefix . 'baseball_game_stats';
        $game_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE game_id = %d",
            get_the_ID()
        ));
        
        // Organize stats by team using team_id from the stats table
        $home_stats = array();
        $away_stats = array();
        
        foreach ($game_stats as $stat) {
            // Use team_id from the game stats (the team they played with in this game)
            if ($stat->team_id == $home_team_id) {
                $home_stats[] = $stat;
            } else {
                $away_stats[] = $stat;
            }
        }
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            
            <div class="game-info">
                <?php if ($tournament_id): ?>
                    <div class="game-tournament">
                        <strong>Torneo:</strong> 
                        <a href="<?php echo get_permalink($tournament_id); ?>">
                            <?php echo get_the_title($tournament_id); ?>
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if ($game_date): ?>
                    <div class="game-datetime">
                        <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($game_date)); ?>
                        <?php if ($game_time): ?>
                            - <?php echo date('H:i', strtotime($game_time)); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($location): ?>
                    <div class="game-location">
                        <strong>Ubicación:</strong> <?php echo esc_html($location); ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="entry-content">
            <section class="game-scoreboard-mlb">
                <div class="scoreboard-header">
                    <div class="team-header away">
                        <?php if (has_post_thumbnail($away_team_id)): ?>
                            <div class="team-logo-large">
                                <?php echo get_the_post_thumbnail($away_team_id, 'medium'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="team-details">
                            <h2>
                                <span class="team-name-full"><?php echo get_the_title($away_team_id); ?></span>
                                <span class="team-name-abbr"><?php echo strtoupper(substr(get_the_title($away_team_id), 0, 3)); ?></span>
                            </h2>
                            <span class="team-record">Visitante</span>
                        </div>
                    </div>
                    
                    <div class="score-display">
                        <div class="final-score">
                            <span class="score-number away-score"><?php echo $away_score !== '' ? $away_score : '0'; ?></span>
                            <span class="status-label">FINAL</span>
                            <span class="score-number home-score"><?php echo $home_score !== '' ? $home_score : '0'; ?></span>
                        </div>
                    </div>
                    
                    <div class="team-header home">
                        <?php if (has_post_thumbnail($home_team_id)): ?>
                            <div class="team-logo-large">
                                <?php echo get_the_post_thumbnail($home_team_id, 'medium'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="team-details">
                            <h2>
                                <span class="team-name-full"><?php echo get_the_title($home_team_id); ?></span>
                                <span class="team-name-abbr"><?php echo strtoupper(substr(get_the_title($home_team_id), 0, 3)); ?></span>
                            </h2>
                            <span class="team-record">Local</span>
                        </div>
                    </div>
                </div>
                
                <div class="line-score">
                    <table class="line-score-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                                <th>6</th>
                                <th>7</th>
                                <th>8</th>
                                <th>9</th>
                                <th class="total">R</th>
                                <th class="total">H</th>
                                <th class="total">E</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="away-line">
                                <td class="team-name">
                                    <span class="team-full-name"><?php echo get_the_title($away_team_id); ?></span>
                                    <span class="team-abbr-name"><?php echo strtoupper(substr(get_the_title($away_team_id), 0, 3)); ?></span>
                                </td>
                                <?php foreach ($away_innings_array as $inning): ?>
                                    <td><?php echo $inning !== '' ? $inning : '-'; ?></td>
                                <?php endforeach; ?>
                                <td class="total"><strong><?php echo $away_score !== '' ? $away_score : '0'; ?></strong></td>
                                <td class="total"><?php echo $away_hits !== '' ? $away_hits : '-'; ?></td>
                                <td class="total"><?php echo $away_errors !== '' ? $away_errors : '-'; ?></td>
                            </tr>
                            <tr class="home-line">
                                <td class="team-name">
                                    <span class="team-full-name"><?php echo get_the_title($home_team_id); ?></span>
                                    <span class="team-abbr-name"><?php echo strtoupper(substr(get_the_title($home_team_id), 0, 3)); ?></span>
                                </td>
                                <?php foreach ($home_innings_array as $inning): ?>
                                    <td><?php echo $inning !== '' ? $inning : '-'; ?></td>
                                <?php endforeach; ?>
                                <td class="total"><strong><?php echo $home_score !== '' ? $home_score : '0'; ?></strong></td>
                                <td class="total"><?php echo $home_hits !== '' ? $home_hits : '-'; ?></td>
                                <td class="total"><?php echo $home_errors !== '' ? $home_errors : '-'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
            
            <?php the_content(); ?>
            
            <?php 
            // Get pitchers data
            $away_pitchers = get_post_meta(get_the_ID(), '_game_away_pitchers', true) ?: array();
            $home_pitchers = get_post_meta(get_the_ID(), '_game_home_pitchers', true) ?: array();
            ?>
            
            <?php if (!empty($away_stats) || !empty($home_stats) || !empty($away_pitchers) || !empty($home_pitchers)): ?>
                <section class="game-statistics">
                    <h2>Estadísticas del Partido</h2>
                    
                    <!-- Team Tabs -->
                    <div class="game-stats-tabs">
                        <button class="team-tab active" data-team="away">
                            <?php if (has_post_thumbnail($away_team_id)): ?>
                                <span class="team-tab-logo">
                                    <?php echo get_the_post_thumbnail($away_team_id, 'thumbnail'); ?>
                                </span>
                            <?php endif; ?>
                            <span class="team-tab-text">
                                <span class="team-tab-full"><?php echo get_the_title($away_team_id); ?> (Visitante)</span>
                                <span class="team-tab-abbr"><?php echo strtoupper(substr(get_the_title($away_team_id), 0, 3)); ?></span>
                            </span>
                        </button>
                        <button class="team-tab" data-team="home">
                            <?php if (has_post_thumbnail($home_team_id)): ?>
                                <span class="team-tab-logo">
                                    <?php echo get_the_post_thumbnail($home_team_id, 'thumbnail'); ?>
                                </span>
                            <?php endif; ?>
                            <span class="team-tab-text">
                                <span class="team-tab-full"><?php echo get_the_title($home_team_id); ?> (Local)</span>
                                <span class="team-tab-abbr"><?php echo strtoupper(substr(get_the_title($home_team_id), 0, 3)); ?></span>
                            </span>
                        </button>
                    </div>
                    
                    <!-- Away Team Content -->
                    <div class="team-stats-content active" id="away-stats">
                        <!-- Sub-tabs: Bateo / Pitcheo -->
                        <div class="stats-type-tabs">
                            <button class="stats-type-tab active" data-type="batting">Bateo</button>
                            <button class="stats-type-tab" data-type="pitching">Pitcheo</button>
                        </div>
                        
                        <!-- Batting Stats -->
                        <div class="stats-type-content active" id="away-batting">
                            <?php if (!empty($away_stats)): ?>
                                <div class="stats-table-wrapper">
                                    <table class="stats-table">
                                        <thead>
                                            <tr>
                                                <th>Jugador</th>
                                                <th>AB</th>
                                                <th>H</th>
                                                <th>AVG</th>
                                                <th>HR</th>
                                                <th>RBI</th>
                                                <th>BB</th>
                                                <th>SO</th>
                                                <th>SB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($away_stats as $stat): 
                                                $avg = $stat->at_bats > 0 ? number_format($stat->hits / $stat->at_bats, 3) : '.000';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="player-name-cell">
                                                        <div class="player-mini-photo">
                                                            <?php if (has_post_thumbnail($stat->player_id)) : ?>
                                                                <?php echo get_the_post_thumbnail($stat->player_id, 'thumbnail'); ?>
                                                            <?php else : ?>
                                                                <div class="player-placeholder">
                                                                    <span class="dashicons dashicons-admin-users"></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <strong>
                                                            <a href="<?php echo get_permalink($stat->player_id); ?>">
                                                                <?php echo get_the_title($stat->player_id); ?>
                                                            </a>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><?php echo $stat->at_bats; ?></td>
                                                <td><?php echo $stat->hits; ?></td>
                                                <td><?php echo $avg; ?></td>
                                                <td><?php echo $stat->home_runs; ?></td>
                                                <td><?php echo $stat->rbis; ?></td>
                                                <td><?php echo $stat->walks; ?></td>
                                                <td><?php echo $stat->strikeouts; ?></td>
                                                <td><?php echo $stat->stolen_bases; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p><em>No hay estadísticas de bateo disponibles.</em></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pitching Stats -->
                        <div class="stats-type-content" id="away-pitching">
                            <?php if (!empty($away_pitchers)): ?>
                                <div class="stats-table-wrapper">
                                    <table class="stats-table">
                                        <thead>
                                            <tr>
                                                <th>Pitcher</th>
                                                <th>IP</th>
                                                <th>H</th>
                                                <th>R</th>
                                                <th>ER</th>
                                                <th>BB</th>
                                                <th>SO</th>
                                                <th>Dec</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($away_pitchers as $pitcher): ?>
                                            <tr>
                                                <td>
                                                    <div class="player-name-cell">
                                                        <div class="player-mini-photo">
                                                            <?php if (has_post_thumbnail($pitcher['player_id'])) : ?>
                                                                <?php echo get_the_post_thumbnail($pitcher['player_id'], 'thumbnail'); ?>
                                                            <?php else : ?>
                                                                <div class="player-placeholder">
                                                                    <span class="dashicons dashicons-admin-users"></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <strong>
                                                            <a href="<?php echo get_permalink($pitcher['player_id']); ?>">
                                                                <?php echo get_the_title($pitcher['player_id']); ?>
                                                            </a>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><?php echo number_format($pitcher['ip'], 1); ?></td>
                                                <td><?php echo $pitcher['h']; ?></td>
                                                <td><?php echo $pitcher['r']; ?></td>
                                                <td><?php echo $pitcher['er']; ?></td>
                                                <td><?php echo $pitcher['bb']; ?></td>
                                                <td><?php echo $pitcher['so']; ?></td>
                                                <td><strong><?php echo $pitcher['decision'] ?: '-'; ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p><em>No hay estadísticas de pitcheo disponibles.</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Home Team Content -->
                    <div class="team-stats-content" id="home-stats">
                        <!-- Sub-tabs: Bateo / Pitcheo -->
                        <div class="stats-type-tabs">
                            <button class="stats-type-tab active" data-type="batting">Bateo</button>
                            <button class="stats-type-tab" data-type="pitching">Pitcheo</button>
                        </div>
                        
                        <!-- Batting Stats -->
                        <div class="stats-type-content active" id="home-batting">
                            <?php if (!empty($home_stats)): ?>
                                <div class="stats-table-wrapper">
                                    <table class="stats-table">
                                        <thead>
                                            <tr>
                                                <th>Jugador</th>
                                                <th>AB</th>
                                                <th>H</th>
                                                <th>AVG</th>
                                                <th>HR</th>
                                                <th>RBI</th>
                                                <th>BB</th>
                                                <th>SO</th>
                                                <th>SB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($home_stats as $stat): 
                                                $avg = $stat->at_bats > 0 ? number_format($stat->hits / $stat->at_bats, 3) : '.000';
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="player-name-cell">
                                                        <div class="player-mini-photo">
                                                            <?php if (has_post_thumbnail($stat->player_id)) : ?>
                                                                <?php echo get_the_post_thumbnail($stat->player_id, 'thumbnail'); ?>
                                                            <?php else : ?>
                                                                <div class="player-placeholder">
                                                                    <span class="dashicons dashicons-admin-users"></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <strong>
                                                            <a href="<?php echo get_permalink($stat->player_id); ?>">
                                                                <?php echo get_the_title($stat->player_id); ?>
                                                            </a>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><?php echo $stat->at_bats; ?></td>
                                                <td><?php echo $stat->hits; ?></td>
                                                <td><?php echo $avg; ?></td>
                                                <td><?php echo $stat->home_runs; ?></td>
                                                <td><?php echo $stat->rbis; ?></td>
                                                <td><?php echo $stat->walks; ?></td>
                                                <td><?php echo $stat->strikeouts; ?></td>
                                                <td><?php echo $stat->stolen_bases; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p><em>No hay estadísticas de bateo disponibles.</em></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pitching Stats -->
                        <div class="stats-type-content" id="home-pitching">
                            <?php if (!empty($home_pitchers)): ?>
                                <div class="stats-table-wrapper">
                                    <table class="stats-table">
                                        <thead>
                                            <tr>
                                                <th>Pitcher</th>
                                                <th>IP</th>
                                                <th>H</th>
                                                <th>R</th>
                                                <th>ER</th>
                                                <th>BB</th>
                                                <th>SO</th>
                                                <th>Dec</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($home_pitchers as $pitcher): ?>
                                            <tr>
                                                <td>
                                                    <div class="player-name-cell">
                                                        <div class="player-mini-photo">
                                                            <?php if (has_post_thumbnail($pitcher['player_id'])) : ?>
                                                                <?php echo get_the_post_thumbnail($pitcher['player_id'], 'thumbnail'); ?>
                                                            <?php else : ?>
                                                                <div class="player-placeholder">
                                                                    <span class="dashicons dashicons-admin-users"></span>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <strong>
                                                            <a href="<?php echo get_permalink($pitcher['player_id']); ?>">
                                                                <?php echo get_the_title($pitcher['player_id']); ?>
                                                            </a>
                                                        </strong>
                                                    </div>
                                                </td>
                                                <td><?php echo number_format($pitcher['ip'], 1); ?></td>
                                                <td><?php echo $pitcher['h']; ?></td>
                                                <td><?php echo $pitcher['r']; ?></td>
                                                <td><?php echo $pitcher['er']; ?></td>
                                                <td><?php echo $pitcher['bb']; ?></td>
                                                <td><?php echo $pitcher['so']; ?></td>
                                                <td><strong><?php echo $pitcher['decision'] ?: '-'; ?></strong></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p><em>No hay estadísticas de pitcheo disponibles.</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <script>
                    jQuery(document).ready(function($) {
                        // Team tabs
                        $('.team-tab').on('click', function() {
                            const team = $(this).data('team');
                            $('.team-tab').removeClass('active');
                            $(this).addClass('active');
                            $('.team-stats-content').removeClass('active');
                            $('#' + team + '-stats').addClass('active');
                        });
                        
                        // Stats type tabs
                        $('.stats-type-tab').on('click', function() {
                            const type = $(this).data('type');
                            const parent = $(this).closest('.team-stats-content');
                            const teamId = parent.attr('id').replace('-stats', '');
                            
                            parent.find('.stats-type-tab').removeClass('active');
                            $(this).addClass('active');
                            parent.find('.stats-type-content').removeClass('active');
                            parent.find('#' + teamId + '-' + type).addClass('active');
                        });
                    });
                    </script>
                </section>
            <?php endif; ?>
            
            <!-- Scorecard Section -->
            <?php
            $scorecard_image = get_post_meta(get_the_ID(), '_game_scorecard_image', true);
            if ($scorecard_image) : ?>
            <section class="game-scorecard">
                <h2>Anotación Oficial</h2>
                <div class="scorecard-image-container">
                    <img src="<?php echo esc_url($scorecard_image); ?>" alt="Anotación Oficial del Partido" class="scorecard-image">
                    <a href="<?php echo esc_url($scorecard_image); ?>" target="_blank" class="btn scorecard-download">
                        Ver en Tamaño Completo
                    </a>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Highlights Section -->
            <section class="game-highlights">
                <h2>Highlights del Partido</h2>
                <?php
                $highlights = get_post_meta(get_the_ID(), '_game_highlights', true);
                if ($highlights && is_array($highlights) && count($highlights) > 0) : ?>
                    <div class="highlights-grid">
                        <?php foreach ($highlights as $index => $highlight) : ?>
                            <div class="highlight-item">
                                <div class="highlight-video">
                                    <?php if (!empty($highlight['url'])) : ?>
                                        <?php if (strpos($highlight['url'], 'youtube.com') !== false || strpos($highlight['url'], 'youtu.be') !== false) : ?>
                                            <?php
                                            // Extract YouTube video ID
                                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $highlight['url'], $matches);
                                            $video_id = $matches[1] ?? '';
                                            ?>
                                            <?php if ($video_id) : ?>
                                                <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            <?php endif; ?>
                                        <?php elseif (strpos($highlight['url'], 'instagram.com') !== false) : ?>
                                            <?php
                                            // Instagram - use thumbnail and link
                                            $instagram_url = rtrim($highlight['url'], '/');
                                            // Try to get thumbnail from Instagram oEmbed API
                                            $oembed_url = 'https://graph.instagram.com/oembed?url=' . urlencode($instagram_url);
                                            $oembed_data = @file_get_contents($oembed_url);
                                            $thumbnail_url = '';
                                            
                                            if ($oembed_data) {
                                                $oembed_json = json_decode($oembed_data, true);
                                                $thumbnail_url = $oembed_json['thumbnail_url'] ?? '';
                                            }
                                            ?>
                                            <div class="instagram-embed-wrapper">
                                                <?php if ($thumbnail_url) : ?>
                                                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="Instagram post" style="width: 100%; height: auto;">
                                                <?php else : ?>
                                                    <div style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); padding: 60px 20px; text-align: center;">
                                                        <svg style="width: 80px; height: 80px; fill: white; margin-bottom: 20px;" viewBox="0 0 24 24">
                                                            <path d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"/>
                                                        </svg>
                                                        <p style="color: white; font-size: 18px; margin: 0;">Video de Instagram</p>
                                                    </div>
                                                <?php endif; ?>
                                                <a href="<?php echo esc_url($instagram_url); ?>" target="_blank" class="instagram-link-overlay">
                                                    <span>Ver en Instagram</span>
                                                </a>
                                            </div>
                                        <?php elseif (strpos($highlight['url'], 'facebook.com') !== false || strpos($highlight['url'], 'fb.watch') !== false) : ?>
                                            <?php
                                            // Facebook video embed
                                            $fb_url = urlencode($highlight['url']);
                                            ?>
                                            <iframe src="https://www.facebook.com/plugins/video.php?href=<?php echo $fb_url; ?>&show_text=false&width=560" width="100%" height="315" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                                        <?php elseif (strpos($highlight['url'], 'twitter.com') !== false || strpos($highlight['url'], 'x.com') !== false) : ?>
                                            <?php
                                            // Twitter/X video - use blockquote for now
                                            ?>
                                            <blockquote class="twitter-tweet" data-width="100%">
                                                <a href="<?php echo esc_url($highlight['url']); ?>">Ver en Twitter/X</a>
                                            </blockquote>
                                            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                                        <?php else : ?>
                                            <?php
                                            // Check if it's an image or video
                                            $file_extension = strtolower(pathinfo($highlight['url'], PATHINFO_EXTENSION));
                                            $image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg');
                                            $video_extensions = array('mp4', 'webm', 'ogg', 'mov');
                                            ?>
                                            <?php if (in_array($file_extension, $image_extensions)) : ?>
                                                <img src="<?php echo esc_url($highlight['url']); ?>" alt="<?php echo esc_attr($highlight['title'] ?? 'Highlight'); ?>" style="width: 100%; height: auto; display: block;">
                                            <?php elseif (in_array($file_extension, $video_extensions)) : ?>
                                                <video controls width="100%" style="display: block;">
                                                    <source src="<?php echo esc_url($highlight['url']); ?>" type="video/<?php echo $file_extension === 'mov' ? 'mp4' : $file_extension; ?>">
                                                    Tu navegador no soporta el elemento de video.
                                                </video>
                                            <?php else : ?>
                                                <div style="padding: 40px; text-align: center; background: #f5f5f5;">
                                                    <p>Formato de archivo no soportado</p>
                                                    <a href="<?php echo esc_url($highlight['url']); ?>" target="_blank" class="btn">Abrir archivo</a>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($highlight['title'])) : ?>
                                    <h4><?php echo esc_html($highlight['title']); ?></h4>
                                <?php endif; ?>
                                <?php if (!empty($highlight['description'])) : ?>
                                    <p><?php echo esc_html($highlight['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p>No hay highlights disponibles para este partido.</p>
                <?php endif; ?>
            </section>
        </div>
    </article>

    <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
