<?php
/**
 * Template Name: Líderes Completos
 * Template for displaying full leaders list
 *
 * @package Baseball_Stats
 */

get_header();
?>

<main class="site-main leaders-page">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">Líderes de la Liga</h1>
            <p class="page-description">Estadísticas de los mejores jugadores</p>
        </header>

        <div class="leaders-tabs">
            <button class="tab-button active" data-stat="batting_avg">Promedio de Bateo</button>
            <button class="tab-button" data-stat="home_runs">Home Runs</button>
            <button class="tab-button" data-stat="rbis">Carreras Impulsadas</button>
            <button class="tab-button" data-stat="hits">Hits</button>
            <button class="tab-button" data-stat="stolen_bases">Bases Robadas</button>
        </div>

        <div class="leaders-content">
            <?php
            $stats = array(
                'batting_avg' => 'Promedio de Bateo',
                'home_runs' => 'Home Runs',
                'rbis' => 'Carreras Impulsadas',
                'hits' => 'Hits',
                'stolen_bases' => 'Bases Robadas'
            );

            foreach ($stats as $stat_key => $stat_label):
                $args = array(
                    'post_type' => 'player',
                    'posts_per_page' => 50,
                    'meta_key' => '_' . $stat_key,
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                );

                $players = new WP_Query($args);
            ?>
                <div class="leaders-section" data-stat="<?php echo $stat_key; ?>" style="<?php echo $stat_key !== 'batting_avg' ? 'display: none;' : ''; ?>">
                    <h2><?php echo esc_html($stat_label); ?></h2>
                    
                    <?php if ($players->have_posts()): ?>
                        <div class="leaders-list">
                            <?php 
                            $rank = 1;
                            while ($players->have_posts()): 
                                $players->the_post();
                                $player_id = get_the_ID();
                                $stat_value = get_post_meta($player_id, '_' . $stat_key, true);
                                $team_id = get_post_meta($player_id, '_player_team', true);
                                $team_name = $team_id ? get_the_title($team_id) : 'N/A';
                            ?>
                            <a href="<?php the_permalink(); ?>" class="leader-item">
                                <div class="leader-rank"><?php echo $rank++; ?></div>
                                <div class="leader-photo">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    <?php else: ?>
                                        <div class="no-photo">
                                            <span class="dashicons dashicons-admin-users"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="leader-info">
                                    <div class="leader-name"><?php the_title(); ?></div>
                                    <div class="leader-team"><?php echo esc_html($team_name); ?></div>
                                </div>
                                <div class="leader-stat">
                                    <div class="stat-value"><?php echo esc_html($stat_value ?: '0'); ?></div>
                                    <div class="stat-label"><?php echo esc_html($stat_label); ?></div>
                                </div>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No hay jugadores con estadísticas disponibles.</p>
                    <?php endif; ?>
                    
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-button');
    const sections = document.querySelectorAll('.leaders-section');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const stat = this.getAttribute('data-stat');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding section
            sections.forEach(section => {
                if (section.getAttribute('data-stat') === stat) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
get_footer();
