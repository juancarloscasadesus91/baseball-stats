<?php
/**
 * Archive Teams Template
 *
 * @package Baseball_Stats
 */

get_header(); ?>

<main class="site-content">
    <div class="container">
        <div class="stats-card">
            <h1>Todos los Equipos</h1>
            <p>Tabla de posiciones y estadísticas de equipos</p>
        </div>

        <?php if (have_posts()) : ?>
            <div class="stats-card">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Ciudad</th>
                            <th>Estadio</th>
                            <th>Victorias</th>
                            <th>Derrotas</th>
                            <th>% Victorias</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while (have_posts()) : the_post(); 
                            $team_id = get_the_ID();
                            $city = get_post_meta($team_id, '_city', true);
                            $stadium = get_post_meta($team_id, '_stadium', true);
                            $wins = get_post_meta($team_id, '_team_wins', true) ?: 0;
                            $losses = get_post_meta($team_id, '_team_losses', true) ?: 0;
                            $total = $wins + $losses;
                            $pct = $total > 0 ? number_format(($wins / $total), 3) : '.000';
                        ?>
                        <tr>
                            <td>
                                <strong><?php the_title(); ?></strong>
                            </td>
                            <td><?php echo esc_html($city ?: 'N/A'); ?></td>
                            <td><?php echo esc_html($stadium ?: 'N/A'); ?></td>
                            <td><?php echo esc_html($wins); ?></td>
                            <td><?php echo esc_html($losses); ?></td>
                            <td><?php echo esc_html($pct); ?></td>
                            <td>
                                <a href="<?php the_permalink(); ?>" class="btn">Ver Equipo</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php the_posts_pagination(); ?>
            
        <?php else : ?>
            <div class="stats-card">
                <h2>No hay equipos registrados</h2>
                <p>Aún no se han añadido equipos al sistema.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
