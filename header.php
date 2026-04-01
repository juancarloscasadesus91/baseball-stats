<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="site-branding">
            <?php if (has_custom_logo()) : ?>
                <div class="branding-wrapper">
                    <?php the_custom_logo(); ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                        <?php bloginfo('name'); ?>
                    </a>
                </div>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-title">
                    <?php bloginfo('name'); ?>
                </a>
            <?php endif; ?>
        </div>

        <button class="mobile-menu-toggle" aria-label="Toggle menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => function() {
                    echo '<ul>';
                    echo '<li><a href="' . home_url('/') . '">Inicio</a></li>';
                    echo '<li><a href="' . get_post_type_archive_link('team') . '">Equipos</a></li>';
                    echo '<li><a href="' . get_post_type_archive_link('player') . '">Jugadores</a></li>';
                    echo '<li><a href="' . get_post_type_archive_link('season') . '">Temporadas</a></li>';
                    echo '<li><a href="' . get_post_type_archive_link('tournament') . '">Torneos</a></li>';
                    echo '<li><a href="' . get_post_type_archive_link('game') . '">Partidos</a></li>';
                    echo '</ul>';
                }
            ));
            ?>
        </nav>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }
});
</script>
