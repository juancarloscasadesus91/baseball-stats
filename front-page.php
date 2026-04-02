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
                <!-- Leaders Widget with Tabs -->
                <div class="sidebar-widget leaders-widget">
                    <h3 class="widget-title">Líderes</h3>
                    
                    <!-- Main Tabs: Bateo / Pitcheo -->
                    <div class="leaders-main-tabs">
                        <button class="main-tab active" data-tab="bateo">BATEO</button>
                        <button class="main-tab" data-tab="pitcheo">PITCHEO</button>
                    </div>
                    
                    <!-- Bateo Content -->
                    <div class="tab-content active" id="bateo-content">
                        <!-- Bateo Filter Tabs -->
                        <div class="leaders-filter-tabs">
                            <button class="filter-tab active" data-stat="avg">AVG</button>
                            <button class="filter-tab" data-stat="hr">HR</button>
                            <button class="filter-tab" data-stat="hits">H</button>
                            <button class="filter-tab" data-stat="so">K</button>
                            <button class="filter-tab" data-stat="bb">BB</button>
                            <button class="filter-tab" data-stat="sb">SB</button>
                        </div>
                        
                        <div id="bateo-stats-container" class="stats-list-container">
                            <!-- Stats will be loaded here via JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Pitcheo Content -->
                    <div class="tab-content" id="pitcheo-content">
                        <!-- Pitcheo Filter Tabs -->
                        <div class="leaders-filter-tabs">
                            <button class="filter-tab active" data-stat="era">ERA</button>
                            <button class="filter-tab" data-stat="wins">W</button>
                            <button class="filter-tab" data-stat="so">K</button>
                            <button class="filter-tab" data-stat="ip">IP</button>
                        </div>
                        
                        <div id="pitcheo-stats-container" class="stats-list-container">
                            <!-- Stats will be loaded here via JavaScript -->
                        </div>
                    </div>
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

<script>
jQuery(document).ready(function($) {
    // Leaders data mapping
    const leaderStats = {
        bateo: {
            avg: { meta: '_batting_avg', order: 'DESC', label: 'AVG', default: '.000' },
            hr: { meta: '_home_runs', order: 'DESC', label: 'HR', default: '0' },
            hits: { meta: '_hits', order: 'DESC', label: 'H', default: '0' },
            so: { meta: '_strikeouts', order: 'DESC', label: 'K', default: '0' },
            bb: { meta: '_walks', order: 'DESC', label: 'BB', default: '0' },
            sb: { meta: '_stolen_bases', order: 'DESC', label: 'SB', default: '0' }
        },
        pitcheo: {
            era: { meta: '_era', order: 'ASC', label: 'ERA', default: '0.00' },
            wins: { meta: '_pitching_wins', order: 'DESC', label: 'W', default: '0' },
            so: { meta: '_pitching_strikeouts', order: 'DESC', label: 'K', default: '0' },
            ip: { meta: '_innings_pitched', order: 'DESC', label: 'IP', default: '0.0' }
        }
    };
    
    // Main tab switching
    $('.main-tab').on('click', function() {
        const tab = $(this).data('tab');
        $('.main-tab').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tab + '-content').addClass('active');
        
        // Load default stat for this tab
        const defaultStat = $('#' + tab + '-content .filter-tab.active').data('stat');
        loadLeaders(tab, defaultStat);
    });
    
    // Filter tab switching
    $('.filter-tab').on('click', function() {
        const stat = $(this).data('stat');
        const tab = $(this).closest('.tab-content').attr('id').replace('-content', '');
        
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
        
        loadLeaders(tab, stat);
    });
    
    // Load leaders function
    function loadLeaders(category, stat) {
        const container = $('#' + category + '-stats-container');
        const statConfig = leaderStats[category][stat];
        
        container.html('<div class="loading">Cargando...</div>');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
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
    
    // Load initial data (AVG for Bateo)
    loadLeaders('bateo', 'avg');
});
</script>

<?php get_footer(); ?>
