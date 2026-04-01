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

                <!-- Recent News/Posts -->
                <div class="news-section">
                    <h2 class="section-title">Últimas Noticias</h2>
                    <?php
                    $recent_posts = get_posts(array(
                        'post_type' => 'post',
                        'posts_per_page' => 5,
                        'offset' => 1,
                    ));

                    if ($recent_posts) : ?>
                        <div class="news-grid">
                            <?php foreach ($recent_posts as $post) : setup_postdata($post); ?>
                            <article class="news-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="news-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="news-content">
                                    <h3 class="news-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <p class="news-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                    <div class="news-meta">
                                        <span><?php echo get_the_date(); ?></span>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; wp_reset_postdata(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar-area">
                <!-- Batting Leaders -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Líderes en Bateo</h3>
                    <?php
                    $top_batters = get_posts(array(
                        'post_type' => 'player',
                        'posts_per_page' => 10,
                        'meta_key' => '_batting_avg',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));

                    if ($top_batters) : ?>
                        <div class="stats-list">
                            <?php 
                            $rank = 1;
                            foreach ($top_batters as $player) : 
                                $batting_avg = get_post_meta($player->ID, '_batting_avg', true);
                                $team_id = get_post_meta($player->ID, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : '';
                            ?>
                            <div class="stats-list-item">
                                <span class="rank"><?php echo $rank++; ?></span>
                                <div class="player-photo">
                                    <?php if (has_post_thumbnail($player->ID)) : ?>
                                        <?php echo get_the_post_thumbnail($player->ID, 'thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="no-photo">
                                            <span class="dashicons dashicons-admin-users"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="player-info">
                                    <a href="<?php echo get_permalink($player->ID); ?>" class="player-name-link">
                                        <?php echo esc_html($player->post_title); ?>
                                    </a>
                                    <?php if ($team_name) : ?>
                                        <span class="team-abbr"><?php echo esc_html(strtoupper(substr($team_name, 0, 3))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="stat-value"><?php echo esc_html($batting_avg ?: '.000'); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fielding Leaders (Stolen Bases) -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Líderes en Fildeo (SB)</h3>
                    <?php
                    $fielding_leaders = get_posts(array(
                        'post_type' => 'player',
                        'posts_per_page' => 10,
                        'meta_key' => '_stolen_bases',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC',
                    ));

                    if ($fielding_leaders) : ?>
                        <div class="stats-list">
                            <?php 
                            $rank = 1;
                            foreach ($fielding_leaders as $player) : 
                                $stolen_bases = get_post_meta($player->ID, '_stolen_bases', true);
                                $team_id = get_post_meta($player->ID, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : '';
                            ?>
                            <div class="stats-list-item">
                                <span class="rank"><?php echo $rank++; ?></span>
                                <div class="player-photo">
                                    <?php if (has_post_thumbnail($player->ID)) : ?>
                                        <?php echo get_the_post_thumbnail($player->ID, 'thumbnail'); ?>
                                    <?php else : ?>
                                        <div class="no-photo">
                                            <span class="dashicons dashicons-admin-users"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="player-info">
                                    <a href="<?php echo get_permalink($player->ID); ?>" class="player-name-link">
                                        <?php echo esc_html($player->post_title); ?>
                                    </a>
                                    <?php if ($team_name) : ?>
                                        <span class="team-abbr"><?php echo esc_html(strtoupper(substr($team_name, 0, 3))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="stat-value"><?php echo esc_html($stolen_bases ?: '0'); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pitching Leaders -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Líderes en Pitcheo (ERA)</h3>
                    <?php
                    $pitching_leaders = get_posts(array(
                        'post_type' => 'player',
                        'posts_per_page' => 10,
                        'meta_key' => '_era',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array(
                                'key' => '_era',
                                'value' => '0',
                                'compare' => '>',
                                'type' => 'DECIMAL'
                            )
                        )
                    ));

                    if ($pitching_leaders) : ?>
                        <div class="stats-list">
                            <?php 
                            $rank = 1;
                            foreach ($pitching_leaders as $player) : 
                                $era = get_post_meta($player->ID, '_era', true);
                                $team_id = get_post_meta($player->ID, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : '';
                            ?>
                            <div class="stats-list-item">
                                <span class="rank"><?php echo $rank++; ?></span>
                                <div class="player-info">
                                    <a href="<?php echo get_permalink($player->ID); ?>" class="player-name-link">
                                        <?php echo esc_html($player->post_title); ?>
                                    </a>
                                    <?php if ($team_name) : ?>
                                        <span class="team-abbr"><?php echo esc_html(substr($team_name, 0, 3)); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="stat-value"><?php echo esc_html($era ?: '0.00'); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Team Standings -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Tabla de Posiciones</h3>
                    <?php
                    $teams = get_posts(array(
                        'post_type' => 'team',
                        'posts_per_page' => -1,
                    ));

                    if ($teams) : 
                        // Sort teams by win percentage
                        usort($teams, function($a, $b) {
                            $wins_a = get_post_meta($a->ID, '_team_wins', true) ?: 0;
                            $losses_a = get_post_meta($a->ID, '_team_losses', true) ?: 0;
                            $total_a = $wins_a + $losses_a;
                            $pct_a = $total_a > 0 ? $wins_a / $total_a : 0;
                            
                            $wins_b = get_post_meta($b->ID, '_team_wins', true) ?: 0;
                            $losses_b = get_post_meta($b->ID, '_team_losses', true) ?: 0;
                            $total_b = $wins_b + $losses_b;
                            $pct_b = $total_b > 0 ? $wins_b / $total_b : 0;
                            
                            return $pct_b <=> $pct_a;
                        });
                    ?>
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
                                    <?php foreach ($teams as $team) : 
                                        $wins = get_post_meta($team->ID, '_team_wins', true) ?: 0;
                                        $losses = get_post_meta($team->ID, '_team_losses', true) ?: 0;
                                        $total = $wins + $losses;
                                        $pct = $total > 0 ? number_format($wins / $total, 3) : '.000';
                                    ?>
                                    <tr>
                                        <td class="team-name">
                                            <a href="<?php echo get_permalink($team->ID); ?>">
                                                <?php echo esc_html($team->post_title); ?>
                                            </a>
                                        </td>
                                        <td><?php echo esc_html($wins); ?></td>
                                        <td><?php echo esc_html($losses); ?></td>
                                        <td><strong><?php echo esc_html($pct); ?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php get_footer(); ?>
