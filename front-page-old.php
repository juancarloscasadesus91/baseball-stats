<?php
/**
 * Front Page Template - MLB Style Blog Layout
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <!-- MLB Style Layout: Main Content + Sidebar -->
        <div class="mlb-layout">
            <!-- Main Content Area -->
            <div class="main-content-area">
                <!-- Featured Banner -->
                <?php
                $featured_posts = get_posts(array(
                    'post_type' => 'post',
                    'posts_per_page' => 1,
                    'meta_key' => '_featured_post',
                    'meta_value' => '1',
                ));
                
                if (empty($featured_posts)) {
                    $featured_posts = get_posts(array(
                        'post_type' => 'post',
                        'posts_per_page' => 1,
                    ));
                }
                
                if ($featured_posts) :
                    $featured = $featured_posts[0];
                ?>
                <div class="featured-banner">
                    <?php if (has_post_thumbnail($featured->ID)) : ?>
                        <div class="featured-image">
                            <?php echo get_the_post_thumbnail($featured->ID, 'large'); ?>
                            <div class="featured-overlay"></div>
                        </div>
                    <?php endif; ?>
                    <div class="featured-content">
                        <span class="featured-label">DESTACADO</span>
                        <h1 class="featured-title">
                            <a href="<?php echo get_permalink($featured->ID); ?>">
                                <?php echo esc_html($featured->post_title); ?>
                            </a>
                        </h1>
                        <p class="featured-excerpt"><?php echo wp_trim_words($featured->post_excerpt ?: $featured->post_content, 30); ?></p>
                        <div class="featured-meta">
                            <span><?php echo get_the_date('', $featured->ID); ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

        <!-- Stats Overview -->
        <div class="stat-boxes" style="margin: 40px 0;">
            <?php
            $teams_count = wp_count_posts('team')->publish;
            $players_count = wp_count_posts('player')->publish;
            $seasons_count = wp_count_posts('season')->publish;
            $tournaments_count = wp_count_posts('tournament')->publish;
            ?>
            <div class="stat-box">
                <div class="stat-label">Equipos</div>
                <div class="stat-value"><?php echo $teams_count; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Jugadores</div>
                <div class="stat-value"><?php echo $players_count; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Temporadas</div>
                <div class="stat-value"><?php echo $seasons_count; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Torneos</div>
                <div class="stat-value"><?php echo $tournaments_count; ?></div>
            </div>
        </div>

        <!-- Batting Leaders -->
        <div class="stats-card">
            <h2>Líderes en Bateo</h2>
            <?php
            $top_batters = get_posts(array(
                'post_type' => 'player',
                'posts_per_page' => 5,
                'meta_key' => '_batting_avg',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));

            if ($top_batters) : ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador</th>
                            <th>Equipo</th>
                            <th>AVG</th>
                            <th>HR</th>
                            <th>RBI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($top_batters as $player) : 
                            $batting_avg = get_post_meta($player->ID, '_batting_avg', true);
                            $home_runs = get_post_meta($player->ID, '_home_runs', true);
                            $rbis = get_post_meta($player->ID, '_rbis', true);
                            $team_id = get_post_meta($player->ID, '_player_team', true);
                            $team_name = $team_id ? get_the_title($team_id) : 'N/A';
                        ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><strong><a href="<?php echo get_permalink($player->ID); ?>"><?php echo esc_html($player->post_title); ?></a></strong></td>
                            <td><?php echo esc_html($team_name); ?></td>
                            <td><?php echo esc_html($batting_avg ?: '.000'); ?></td>
                            <td><?php echo esc_html($home_runs ?: '0'); ?></td>
                            <td><?php echo esc_html($rbis ?: '0'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No hay estadísticas de bateo disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Home Run Leaders -->
        <div class="stats-card">
            <h2>Líderes en Home Runs</h2>
            <?php
            $hr_leaders = get_posts(array(
                'post_type' => 'player',
                'posts_per_page' => 5,
                'meta_key' => '_home_runs',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
            ));

            if ($hr_leaders) : ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador</th>
                            <th>Equipo</th>
                            <th>Home Runs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        foreach ($hr_leaders as $player) : 
                            $home_runs = get_post_meta($player->ID, '_home_runs', true);
                            $team_id = get_post_meta($player->ID, '_player_team', true);
                            $team_name = $team_id ? get_the_title($team_id) : 'N/A';
                        ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><strong><a href="<?php echo get_permalink($player->ID); ?>"><?php echo esc_html($player->post_title); ?></a></strong></td>
                            <td><?php echo esc_html($team_name); ?></td>
                            <td><?php echo esc_html($home_runs ?: '0'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No hay estadísticas de home runs disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Team Standings -->
        <div class="stats-card">
            <h2>Tabla de Posiciones</h2>
            <?php
            $teams = get_posts(array(
                'post_type' => 'team',
                'posts_per_page' => -1,
            ));

            if ($teams) : ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Ciudad</th>
                            <th>V</th>
                            <th>D</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $team) : 
                            $wins = get_post_meta($team->ID, '_team_wins', true) ?: 0;
                            $losses = get_post_meta($team->ID, '_team_losses', true) ?: 0;
                            $total = $wins + $losses;
                            $pct = $total > 0 ? number_format($wins / $total, 3) : '.000';
                            $city = get_post_meta($team->ID, '_city', true);
                        ?>
                        <tr>
                            <td><strong><a href="<?php echo get_permalink($team->ID); ?>"><?php echo esc_html($team->post_title); ?></a></strong></td>
                            <td><?php echo esc_html($city ?: 'N/A'); ?></td>
                            <td><?php echo esc_html($wins); ?></td>
                            <td><?php echo esc_html($losses); ?></td>
                            <td><?php echo esc_html($pct); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No hay equipos registrados.</p>
            <?php endif; ?>
        </div>

        <!-- Recent Players -->
        <?php
        $recent_players = get_posts(array(
            'post_type' => 'player',
            'posts_per_page' => 6,
            'orderby' => 'date',
            'order' => 'DESC',
        ));

        if ($recent_players) : ?>
        <div class="stats-card">
            <h2>Jugadores Recientes</h2>
            <div class="player-grid">
                <?php foreach ($recent_players as $player) : 
                    $player_number = get_post_meta($player->ID, '_player_number', true);
                    $batting_avg = get_post_meta($player->ID, '_batting_avg', true);
                    $home_runs = get_post_meta($player->ID, '_home_runs', true);
                    $positions = wp_get_post_terms($player->ID, 'position');
                    $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
                ?>
                <div class="player-card">
                    <?php echo get_the_post_thumbnail($player->ID, 'player-thumbnail', array('class' => 'player-photo')); ?>
                    <div class="player-number">#<?php echo esc_html($player_number); ?></div>
                    <h3 class="player-name"><?php echo esc_html($player->post_title); ?></h3>
                    <div class="player-position"><?php echo esc_html($position_name); ?></div>
                    <div class="stat-boxes">
                        <div class="stat-box">
                            <div class="stat-label">AVG</div>
                            <div class="stat-value"><?php echo esc_html($batting_avg ?: '.000'); ?></div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-label">HR</div>
                            <div class="stat-value"><?php echo esc_html($home_runs ?: '0'); ?></div>
                        </div>
                    </div>
                    <a href="<?php echo get_permalink($player->ID); ?>" class="btn" style="margin-top: 15px;">Ver Perfil</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
