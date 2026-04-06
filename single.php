<?php
/**
 * Single Post Template
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
                <?php while (have_posts()) : the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
                    <header class="single-post-header">
                        <h1 class="single-post-title"><?php the_title(); ?></h1>
                        
                        <div class="single-post-meta">
                            <span class="post-date">
                                <i class="dashicons dashicons-calendar"></i>
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php if (get_the_author()) : ?>
                                <span class="post-author">
                                    <i class="dashicons dashicons-admin-users"></i>
                                    <?php the_author(); ?>
                                </span>
                            <?php endif; ?>
                            <?php
                            $categories = get_the_category();
                            if ($categories) :
                                foreach ($categories as $category) :
                            ?>
                                <span class="post-category"><?php echo esc_html($category->name); ?></span>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </header>
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="single-post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="single-post-content">
                        <?php the_content(); ?>
                    </div>
                    
                    <?php if (get_the_tags()) : ?>
                        <div class="post-tags">
                            <strong>Etiquetas:</strong>
                            <?php the_tags('', ', '); ?>
                        </div>
                    <?php endif; ?>
                </article>
                
                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
                
                <?php endwhile; ?>
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
                            <button class="filter-tab" data-stat="bb">BB</button>
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
            bb: { meta: '_walks', order: 'DESC', label: 'BB', default: '0' }
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
