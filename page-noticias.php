<?php
/**
 * Template Name: Página de Noticias
 * Template for displaying all blog posts
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="archive-header">
            <h1 class="archive-title">Todas las Noticias</h1>
            <p class="archive-description">Mantente al día con todas las últimas noticias del béisbol</p>
        </div>

        <!-- MLB Style Layout: Main Content + Sidebar -->
        <div class="mlb-layout">
            <!-- Main Content Area -->
            <div class="main-content-area">
                <?php
                // Query all posts
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 10,
                    'paged' => $paged,
                    'post_status' => 'publish'
                );
                $news_query = new WP_Query($args);
                
                if ($news_query->have_posts()) : ?>
                    <div class="news-archive-grid">
                        <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
                            <article class="news-archive-item">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="news-archive-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="news-archive-content">
                                    <h2 class="news-archive-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="news-archive-meta">
                                        <span class="news-date"><?php echo get_the_date(); ?></span>
                                        <?php
                                        $categories = get_the_category();
                                        if ($categories) :
                                            foreach ($categories as $category) :
                                        ?>
                                            <span class="news-category"><?php echo esc_html($category->name); ?></span>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </div>
                                    <p class="news-archive-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="read-more-link">Leer más →</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="archive-pagination">
                        <?php
                        echo paginate_links(array(
                            'total' => $news_query->max_num_pages,
                            'current' => $paged,
                            'mid_size' => 2,
                            'prev_text' => '← Anterior',
                            'next_text' => 'Siguiente →',
                        ));
                        ?>
                    </div>

                <?php else : ?>
                    <div class="no-posts">
                        <p>No hay noticias disponibles en este momento.</p>
                    </div>
                <?php endif; 
                wp_reset_postdata();
                ?>
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
