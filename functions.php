<?php
/**
 * Baseball Stats Theme Functions
 *
 * @package Baseball_Stats
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create Database Tables
 */
function baseball_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        game_id bigint(20) NOT NULL,
        player_id bigint(20) NOT NULL,
        team_id bigint(20) NOT NULL,
        at_bats int(11) DEFAULT 0,
        hits int(11) DEFAULT 0,
        home_runs int(11) DEFAULT 0,
        rbis int(11) DEFAULT 0,
        walks int(11) DEFAULT 0,
        strikeouts int(11) DEFAULT 0,
        stolen_bases int(11) DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY game_id (game_id),
        KEY player_id (player_id),
        KEY team_id (team_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'baseball_create_tables');

/**
 * Flush Rewrite Rules on Theme Activation
 */
function baseball_flush_rewrite_rules() {
    // Register custom post types
    baseball_register_seasons();
    baseball_register_tournaments();
    baseball_register_teams();
    baseball_register_players();
    baseball_register_games();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'baseball_flush_rewrite_rules');

/**
 * Theme Setup
 */
function baseball_stats_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    // Add custom logo support with specific dimensions
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    ));
    
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'baseball-stats'),
    ));
    
    // Add image sizes
    add_image_size('player-thumbnail', 300, 300, true);
    add_image_size('team-logo', 200, 200, false);
}
add_action('after_setup_theme', 'baseball_stats_setup');

/**
 * Enqueue scripts and styles
 */
function baseball_stats_scripts() {
    wp_enqueue_style('baseball-stats-style', get_stylesheet_uri(), array(), '1.0.0');
    wp_enqueue_script('baseball-stats-script', get_template_directory_uri() . '/js/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'baseball_stats_scripts');

/**
 * Register Custom Post Types
 */

// Register Teams Post Type
function baseball_register_teams() {
    $labels = array(
        'name'                  => 'Equipos',
        'singular_name'         => 'Equipo',
        'menu_name'             => 'Equipos',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Equipo',
        'edit_item'             => 'Editar Equipo',
        'new_item'              => 'Nuevo Equipo',
        'view_item'             => 'Ver Equipo',
        'search_items'          => 'Buscar Equipos',
        'not_found'             => 'No se encontraron equipos',
        'not_found_in_trash'    => 'No se encontraron equipos en la papelera',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'equipos'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-groups',
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'          => true,
    );

    register_post_type('team', $args);
}
add_action('init', 'baseball_register_teams');

// Register Players Post Type
function baseball_register_players() {
    $labels = array(
        'name'                  => 'Jugadores',
        'singular_name'         => 'Jugador',
        'menu_name'             => 'Jugadores',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Jugador',
        'edit_item'             => 'Editar Jugador',
        'new_item'              => 'Nuevo Jugador',
        'view_item'             => 'Ver Jugador',
        'search_items'          => 'Buscar Jugadores',
        'not_found'             => 'No se encontraron jugadores',
        'not_found_in_trash'    => 'No se encontraron jugadores en la papelera',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'jugadores'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-admin-users',
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'          => true,
    );

    register_post_type('player', $args);
}
add_action('init', 'baseball_register_players');

// Register Seasons Post Type
function baseball_register_seasons() {
    $labels = array(
        'name'                  => 'Temporadas',
        'singular_name'         => 'Temporada',
        'menu_name'             => 'Temporadas',
        'add_new'               => 'Añadir Nueva',
        'add_new_item'          => 'Añadir Nueva Temporada',
        'edit_item'             => 'Editar Temporada',
        'new_item'              => 'Nueva Temporada',
        'view_item'             => 'Ver Temporada',
        'search_items'          => 'Buscar Temporadas',
        'not_found'             => 'No se encontraron temporadas',
        'not_found_in_trash'    => 'No se encontraron temporadas en la papelera',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'temporadas'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-calendar-alt',
        'supports'              => array('title', 'editor', 'custom-fields'),
        'show_in_rest'          => true,
    );

    register_post_type('season', $args);
}
add_action('init', 'baseball_register_seasons');

// Register Tournaments Post Type
function baseball_register_tournaments() {
    $labels = array(
        'name'                  => 'Torneos',
        'singular_name'         => 'Torneo',
        'menu_name'             => 'Torneos',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Torneo',
        'edit_item'             => 'Editar Torneo',
        'new_item'              => 'Nuevo Torneo',
        'view_item'             => 'Ver Torneo',
        'search_items'          => 'Buscar Torneos',
        'not_found'             => 'No se encontraron torneos',
        'not_found_in_trash'    => 'No se encontraron torneos en la papelera',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'torneos'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 8,
        'menu_icon'             => 'dashicons-awards',
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'show_in_rest'          => true,
    );

    register_post_type('tournament', $args);
}
add_action('init', 'baseball_register_tournaments');

// Register Games Post Type
function baseball_register_games() {
    $labels = array(
        'name'                  => 'Partidos',
        'singular_name'         => 'Partido',
        'menu_name'             => 'Partidos',
        'add_new'               => 'Añadir Nuevo',
        'add_new_item'          => 'Añadir Nuevo Partido',
        'edit_item'             => 'Editar Partido',
        'new_item'              => 'Nuevo Partido',
        'view_item'             => 'Ver Partido',
        'search_items'          => 'Buscar Partidos',
        'not_found'             => 'No se encontraron partidos',
        'not_found_in_trash'    => 'No se encontraron partidos en la papelera',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => 'partidos'),
        'capability_type'       => 'post',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 9,
        'menu_icon'             => 'dashicons-tickets-alt',
        'supports'              => array('title', 'editor', 'custom-fields'),
        'show_in_rest'          => true,
    );

    register_post_type('game', $args);
}
add_action('init', 'baseball_register_games');

/**
 * Register Taxonomies
 */

// Register Position Taxonomy for Players
function baseball_register_position_taxonomy() {
    $labels = array(
        'name'              => 'Posiciones',
        'singular_name'     => 'Posición',
        'search_items'      => 'Buscar Posiciones',
        'all_items'         => 'Todas las Posiciones',
        'edit_item'         => 'Editar Posición',
        'update_item'       => 'Actualizar Posición',
        'add_new_item'      => 'Añadir Nueva Posición',
        'new_item_name'     => 'Nuevo Nombre de Posición',
        'menu_name'         => 'Posiciones',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'posicion'),
        'show_in_rest'      => true,
    );

    register_taxonomy('position', array('player'), $args);
}
add_action('init', 'baseball_register_position_taxonomy');

/**
 * Add Meta Boxes for Custom Fields
 */

// Player Stats Meta Box
function baseball_add_player_meta_boxes() {
    add_meta_box(
        'player_stats',
        'Estadísticas del Jugador',
        'baseball_player_stats_callback',
        'player',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'baseball_add_player_meta_boxes');

function baseball_player_stats_callback($post) {
    wp_nonce_field('baseball_save_player_stats', 'baseball_player_stats_nonce');
    
    // Get existing values
    $player_number = get_post_meta($post->ID, '_player_number', true);
    $team_id = get_post_meta($post->ID, '_player_team', true);
    $batting_avg = get_post_meta($post->ID, '_batting_avg', true);
    $home_runs = get_post_meta($post->ID, '_home_runs', true);
    $rbis = get_post_meta($post->ID, '_rbis', true);
    $hits = get_post_meta($post->ID, '_hits', true);
    $at_bats = get_post_meta($post->ID, '_at_bats', true);
    $stolen_bases = get_post_meta($post->ID, '_stolen_bases', true);
    $era = get_post_meta($post->ID, '_era', true);
    $wins = get_post_meta($post->ID, '_wins', true);
    $losses = get_post_meta($post->ID, '_losses', true);
    $strikeouts = get_post_meta($post->ID, '_strikeouts', true);
    
    // Get all teams for dropdown
    $teams = get_posts(array('post_type' => 'team', 'posts_per_page' => -1));
    
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h3>Información General</h3>
            <p>
                <label for="player_number"><strong>Número de Jugador:</strong></label><br>
                <input type="number" id="player_number" name="player_number" value="<?php echo esc_attr($player_number); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="player_team"><strong>Equipo:</strong></label><br>
                <select id="player_team" name="player_team" style="width: 100%;">
                    <option value="">Seleccionar Equipo</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team->ID; ?>" <?php selected($team_id, $team->ID); ?>>
                            <?php echo esc_html($team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        </div>
        
        <div>
            <h3>Estadísticas de Bateo</h3>
            <p>
                <label for="at_bats"><strong>Turnos al Bate (AB):</strong></label><br>
                <input type="number" id="at_bats" name="at_bats" value="<?php echo esc_attr($at_bats); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="hits"><strong>Hits (H):</strong></label><br>
                <input type="number" id="hits" name="hits" value="<?php echo esc_attr($hits); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="batting_avg"><strong>Promedio de Bateo (AVG):</strong></label><br>
                <input type="text" id="batting_avg" name="batting_avg" value="<?php echo esc_attr($batting_avg); ?>" placeholder="0.000" style="width: 100%;">
            </p>
            <p>
                <label for="home_runs"><strong>Home Runs (HR):</strong></label><br>
                <input type="number" id="home_runs" name="home_runs" value="<?php echo esc_attr($home_runs); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="rbis"><strong>Carreras Impulsadas (RBI):</strong></label><br>
                <input type="number" id="rbis" name="rbis" value="<?php echo esc_attr($rbis); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="stolen_bases"><strong>Bases Robadas (SB):</strong></label><br>
                <input type="number" id="stolen_bases" name="stolen_bases" value="<?php echo esc_attr($stolen_bases); ?>" style="width: 100%;">
            </p>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h3>Estadísticas de Pitcheo</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
            <p>
                <label for="wins"><strong>Victorias (W):</strong></label><br>
                <input type="number" id="wins" name="wins" value="<?php echo esc_attr($wins); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="losses"><strong>Derrotas (L):</strong></label><br>
                <input type="number" id="losses" name="losses" value="<?php echo esc_attr($losses); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="era"><strong>Efectividad (ERA):</strong></label><br>
                <input type="text" id="era" name="era" value="<?php echo esc_attr($era); ?>" placeholder="0.00" style="width: 100%;">
            </p>
            <p>
                <label for="strikeouts"><strong>Ponches (K):</strong></label><br>
                <input type="number" id="strikeouts" name="strikeouts" value="<?php echo esc_attr($strikeouts); ?>" style="width: 100%;">
            </p>
        </div>
    </div>
    <?php
}

function baseball_save_player_stats($post_id) {
    // Check nonce
    if (!isset($_POST['baseball_player_stats_nonce']) || !wp_verify_nonce($_POST['baseball_player_stats_nonce'], 'baseball_save_player_stats')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save fields
    $fields = array(
        'player_number', 'player_team', 'batting_avg', 'home_runs', 'rbis',
        'hits', 'at_bats', 'stolen_bases', 'era', 'wins', 'losses', 'strikeouts'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_player', 'baseball_save_player_stats');

// Team Stats Meta Box
function baseball_add_team_meta_boxes() {
    add_meta_box(
        'team_stats',
        'Información del Equipo',
        'baseball_team_stats_callback',
        'team',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'baseball_add_team_meta_boxes');

function baseball_team_stats_callback($post) {
    wp_nonce_field('baseball_save_team_stats', 'baseball_team_stats_nonce');
    
    $founded_year = get_post_meta($post->ID, '_founded_year', true);
    $stadium = get_post_meta($post->ID, '_stadium', true);
    $city = get_post_meta($post->ID, '_city', true);
    $wins = get_post_meta($post->ID, '_team_wins', true);
    $losses = get_post_meta($post->ID, '_team_losses', true);
    
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <p>
                <label for="city"><strong>Ciudad:</strong></label><br>
                <input type="text" id="city" name="city" value="<?php echo esc_attr($city); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="stadium"><strong>Estadio:</strong></label><br>
                <input type="text" id="stadium" name="stadium" value="<?php echo esc_attr($stadium); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="founded_year"><strong>Año de Fundación:</strong></label><br>
                <input type="number" id="founded_year" name="founded_year" value="<?php echo esc_attr($founded_year); ?>" style="width: 100%;">
            </p>
        </div>
        <div>
            <p>
                <label for="team_wins"><strong>Victorias:</strong></label><br>
                <input type="number" id="team_wins" name="team_wins" value="<?php echo esc_attr($wins); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="team_losses"><strong>Derrotas:</strong></label><br>
                <input type="number" id="team_losses" name="team_losses" value="<?php echo esc_attr($losses); ?>" style="width: 100%;">
            </p>
        </div>
    </div>
    <?php
}

function baseball_save_team_stats($post_id) {
    if (!isset($_POST['baseball_team_stats_nonce']) || !wp_verify_nonce($_POST['baseball_team_stats_nonce'], 'baseball_save_team_stats')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('founded_year', 'stadium', 'city', 'team_wins', 'team_losses');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_team', 'baseball_save_team_stats');

// Season Meta Box
function baseball_add_season_meta_boxes() {
    add_meta_box(
        'season_info',
        'Información de la Temporada',
        'baseball_season_info_callback',
        'season',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'baseball_add_season_meta_boxes');

function baseball_season_info_callback($post) {
    wp_nonce_field('baseball_save_season_info', 'baseball_season_info_nonce');
    
    $start_date = get_post_meta($post->ID, '_season_start_date', true);
    $end_date = get_post_meta($post->ID, '_season_end_date', true);
    $year = get_post_meta($post->ID, '_season_year', true);
    
    ?>
    <p>
        <label for="season_year"><strong>Año:</strong></label><br>
        <input type="number" id="season_year" name="season_year" value="<?php echo esc_attr($year); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="season_start_date"><strong>Fecha de Inicio:</strong></label><br>
        <input type="date" id="season_start_date" name="season_start_date" value="<?php echo esc_attr($start_date); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="season_end_date"><strong>Fecha de Fin:</strong></label><br>
        <input type="date" id="season_end_date" name="season_end_date" value="<?php echo esc_attr($end_date); ?>" style="width: 100%;">
    </p>
    <?php
}

function baseball_save_season_info($post_id) {
    if (!isset($_POST['baseball_season_info_nonce']) || !wp_verify_nonce($_POST['baseball_season_info_nonce'], 'baseball_save_season_info')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('season_start_date', 'season_end_date', 'season_year');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_season', 'baseball_save_season_info');

// Tournament Meta Box
function baseball_add_tournament_meta_boxes() {
    add_meta_box(
        'tournament_info',
        'Información del Torneo',
        'baseball_tournament_info_callback',
        'tournament',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'baseball_add_tournament_meta_boxes');

function baseball_tournament_info_callback($post) {
    wp_nonce_field('baseball_save_tournament_info', 'baseball_tournament_info_nonce');
    
    $season_id = get_post_meta($post->ID, '_tournament_season', true);
    $start_date = get_post_meta($post->ID, '_tournament_start_date', true);
    $end_date = get_post_meta($post->ID, '_tournament_end_date', true);
    
    // Get all seasons for dropdown
    $seasons = get_posts(array('post_type' => 'season', 'posts_per_page' => -1, 'orderby' => 'meta_value_num', 'meta_key' => '_season_year', 'order' => 'DESC'));
    
    ?>
    <p>
        <label for="tournament_season"><strong>Temporada:</strong></label><br>
        <select id="tournament_season" name="tournament_season" style="width: 100%;">
            <option value="">Seleccionar Temporada</option>
            <?php foreach ($seasons as $season): 
                $year = get_post_meta($season->ID, '_season_year', true);
            ?>
                <option value="<?php echo $season->ID; ?>" <?php selected($season_id, $season->ID); ?>>
                    <?php echo esc_html($season->post_title . ' (' . $year . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="tournament_start_date"><strong>Fecha de Inicio:</strong></label><br>
        <input type="date" id="tournament_start_date" name="tournament_start_date" value="<?php echo esc_attr($start_date); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="tournament_end_date"><strong>Fecha de Fin:</strong></label><br>
        <input type="date" id="tournament_end_date" name="tournament_end_date" value="<?php echo esc_attr($end_date); ?>" style="width: 100%;">
    </p>
    <?php
}

function baseball_save_tournament_info($post_id) {
    if (!isset($_POST['baseball_tournament_info_nonce']) || !wp_verify_nonce($_POST['baseball_tournament_info_nonce'], 'baseball_save_tournament_info')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('tournament_season', 'tournament_start_date', 'tournament_end_date');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_tournament', 'baseball_save_tournament_info');

// Team Tournament Relationship Meta Box
function baseball_add_team_tournament_meta_box() {
    add_meta_box(
        'team_tournament',
        'Torneos del Equipo',
        'baseball_team_tournament_callback',
        'team',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'baseball_add_team_tournament_meta_box');

function baseball_team_tournament_callback($post) {
    wp_nonce_field('baseball_save_team_tournament', 'baseball_team_tournament_nonce');
    
    $selected_tournaments = get_post_meta($post->ID, '_team_tournaments', true);
    if (!is_array($selected_tournaments)) {
        $selected_tournaments = array();
    }
    
    $tournaments = get_posts(array('post_type' => 'tournament', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
    
    ?>
    <p><strong>Selecciona los torneos en los que participa este equipo:</strong></p>
    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <?php foreach ($tournaments as $tournament): 
            $season_id = get_post_meta($tournament->ID, '_tournament_season', true);
            $season_name = $season_id ? get_the_title($season_id) : '';
        ?>
            <label style="display: block; margin-bottom: 8px;">
                <input type="checkbox" name="team_tournaments[]" value="<?php echo $tournament->ID; ?>" 
                    <?php checked(in_array($tournament->ID, $selected_tournaments)); ?>>
                <?php echo esc_html($tournament->post_title); ?>
                <?php if ($season_name): ?>
                    <em>(<?php echo esc_html($season_name); ?>)</em>
                <?php endif; ?>
            </label>
        <?php endforeach; ?>
    </div>
    <?php
}

function baseball_save_team_tournament($post_id) {
    if (!isset($_POST['baseball_team_tournament_nonce']) || !wp_verify_nonce($_POST['baseball_team_tournament_nonce'], 'baseball_save_team_tournament')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $tournaments = isset($_POST['team_tournaments']) ? array_map('intval', $_POST['team_tournaments']) : array();
    update_post_meta($post_id, '_team_tournaments', $tournaments);
}
add_action('save_post_team', 'baseball_save_team_tournament');

// Game Meta Box
function baseball_add_game_meta_boxes() {
    add_meta_box(
        'game_info',
        'Información del Partido',
        'baseball_game_info_callback',
        'game',
        'normal',
        'high'
    );
    
    add_meta_box(
        'game_pitchers',
        'Pitchers del Partido',
        'baseball_game_pitchers_callback',
        'game',
        'normal',
        'high'
    );
    
    add_meta_box(
        'game_stats',
        'Estadísticas del Partido',
        'baseball_game_stats_callback',
        'game',
        'normal',
        'default'
    );
    
    add_meta_box(
        'game_highlights',
        'Highlights del Partido',
        'baseball_game_highlights_callback',
        'game',
        'normal',
        'default'
    );
    
    add_meta_box(
        'game_scorecard',
        'Anotación Oficial',
        'baseball_game_scorecard_callback',
        'game',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'baseball_add_game_meta_boxes');

function baseball_game_info_callback($post) {
    wp_nonce_field('baseball_save_game_info', 'baseball_game_info_nonce');
    
    $tournament_id = get_post_meta($post->ID, '_game_tournament', true);
    $home_team_id = get_post_meta($post->ID, '_game_home_team', true);
    $away_team_id = get_post_meta($post->ID, '_game_away_team', true);
    $home_score = get_post_meta($post->ID, '_game_home_score', true);
    $away_score = get_post_meta($post->ID, '_game_away_score', true);
    $game_date = get_post_meta($post->ID, '_game_date', true);
    $game_time = get_post_meta($post->ID, '_game_time', true);
    $location = get_post_meta($post->ID, '_game_location', true);
    
    $tournaments = get_posts(array('post_type' => 'tournament', 'posts_per_page' => -1));
    $teams = get_posts(array('post_type' => 'team', 'posts_per_page' => -1));
    
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <p>
                <label for="game_tournament"><strong>Torneo:</strong></label><br>
                <select id="game_tournament" name="game_tournament" style="width: 100%;">
                    <option value="">Seleccionar Torneo</option>
                    <?php foreach ($tournaments as $tournament): ?>
                        <option value="<?php echo $tournament->ID; ?>" <?php selected($tournament_id, $tournament->ID); ?>>
                            <?php echo esc_html($tournament->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="game_home_team"><strong>Equipo Local:</strong></label><br>
                <select id="game_home_team" name="game_home_team" style="width: 100%;">
                    <option value="">Seleccionar Equipo</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team->ID; ?>" <?php selected($home_team_id, $team->ID); ?>>
                            <?php echo esc_html($team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label for="game_away_team"><strong>Equipo Visitante:</strong></label><br>
                <select id="game_away_team" name="game_away_team" style="width: 100%;">
                    <option value="">Seleccionar Equipo</option>
                    <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team->ID; ?>" <?php selected($away_team_id, $team->ID); ?>>
                            <?php echo esc_html($team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
        </div>
        <div>
            <p>
                <label for="game_date"><strong>Fecha:</strong></label><br>
                <input type="date" id="game_date" name="game_date" value="<?php echo esc_attr($game_date); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="game_time"><strong>Hora:</strong></label><br>
                <input type="time" id="game_time" name="game_time" value="<?php echo esc_attr($game_time); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="game_location"><strong>Ubicación:</strong></label><br>
                <input type="text" id="game_location" name="game_location" value="<?php echo esc_attr($location); ?>" style="width: 100%;">
            </p>
            <p>
                <label for="game_home_score"><strong>Puntuación Local:</strong></label><br>
                <input type="number" id="game_home_score" name="game_home_score" value="<?php echo esc_attr($home_score); ?>" style="width: 100%;" min="0">
            </p>
            <p>
                <label for="game_away_score"><strong>Puntuación Visitante:</strong></label><br>
                <input type="number" id="game_away_score" name="game_away_score" value="<?php echo esc_attr($away_score); ?>" style="width: 100%;" min="0">
            </p>
        </div>
    </div>
    
    <hr style="margin: 20px 0;">
    
    <h3>Line Score por Innings</h3>
    <p><em>Ingresa las carreras anotadas en cada inning. Deja en blanco si no aplica.</em></p>
    
    <?php
    // Get innings data
    $away_innings = get_post_meta($post->ID, '_game_away_innings', true);
    $home_innings = get_post_meta($post->ID, '_game_home_innings', true);
    $away_hits = get_post_meta($post->ID, '_game_away_hits', true);
    $home_hits = get_post_meta($post->ID, '_game_home_hits', true);
    $away_errors = get_post_meta($post->ID, '_game_away_errors', true);
    $home_errors = get_post_meta($post->ID, '_game_home_errors', true);
    
    // Parse innings (stored as comma-separated)
    $away_innings_array = $away_innings ? explode(',', $away_innings) : array_fill(0, 9, '');
    $home_innings_array = $home_innings ? explode(',', $home_innings) : array_fill(0, 9, '');
    ?>
    
    <table class="widefat" style="margin-top: 15px;">
        <thead>
            <tr>
                <th style="width: 150px;">Equipo</th>
                <th style="width: 50px;">1</th>
                <th style="width: 50px;">2</th>
                <th style="width: 50px;">3</th>
                <th style="width: 50px;">4</th>
                <th style="width: 50px;">5</th>
                <th style="width: 50px;">6</th>
                <th style="width: 50px;">7</th>
                <th style="width: 50px;">8</th>
                <th style="width: 50px;">9</th>
                <th style="width: 60px; background: #f0f0f0;"><strong>R</strong></th>
                <th style="width: 60px; background: #f0f0f0;"><strong>H</strong></th>
                <th style="width: 60px; background: #f0f0f0;"><strong>E</strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong><?php echo $away_team_id ? get_the_title($away_team_id) : 'Visitante'; ?></strong></td>
                <?php for ($i = 0; $i < 9; $i++): ?>
                    <td>
                        <input type="number" 
                               name="away_inning_<?php echo $i + 1; ?>" 
                               value="<?php echo isset($away_innings_array[$i]) ? esc_attr($away_innings_array[$i]) : ''; ?>" 
                               style="width: 100%;" 
                               min="0" 
                               placeholder="0">
                    </td>
                <?php endfor; ?>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="away_runs" 
                           value="<?php echo esc_attr($away_score); ?>" 
                           style="width: 100%; font-weight: bold;" 
                           min="0" 
                           readonly>
                </td>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="away_hits" 
                           value="<?php echo esc_attr($away_hits); ?>" 
                           style="width: 100%;" 
                           min="0" 
                           placeholder="0">
                </td>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="away_errors" 
                           value="<?php echo esc_attr($away_errors); ?>" 
                           style="width: 100%;" 
                           min="0" 
                           placeholder="0">
                </td>
            </tr>
            <tr>
                <td><strong><?php echo $home_team_id ? get_the_title($home_team_id) : 'Local'; ?></strong></td>
                <?php for ($i = 0; $i < 9; $i++): ?>
                    <td>
                        <input type="number" 
                               name="home_inning_<?php echo $i + 1; ?>" 
                               value="<?php echo isset($home_innings_array[$i]) ? esc_attr($home_innings_array[$i]) : ''; ?>" 
                               style="width: 100%;" 
                               min="0" 
                               placeholder="0">
                    </td>
                <?php endfor; ?>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="home_runs" 
                           value="<?php echo esc_attr($home_score); ?>" 
                           style="width: 100%; font-weight: bold;" 
                           min="0" 
                           readonly>
                </td>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="home_hits" 
                           value="<?php echo esc_attr($home_hits); ?>" 
                           style="width: 100%;" 
                           min="0" 
                           placeholder="0">
                </td>
                <td style="background: #f9f9f9;">
                    <input type="number" 
                           name="home_errors" 
                           value="<?php echo esc_attr($home_errors); ?>" 
                           style="width: 100%;" 
                           min="0" 
                           placeholder="0">
                </td>
            </tr>
        </tbody>
    </table>
    
    <p style="margin-top: 10px;"><em><strong>Nota:</strong> Las carreras totales (R) se calculan automáticamente de la puntuación final. Los campos H (Hits) y E (Errores) son opcionales.</em></p>
    <?php
}

function baseball_game_stats_callback($post) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    $home_team_id = get_post_meta($post->ID, '_game_home_team', true);
    $away_team_id = get_post_meta($post->ID, '_game_away_team', true);
    
    if (!$home_team_id || !$away_team_id) {
        echo '<p><strong>Por favor, selecciona primero los equipos del partido y guarda el borrador.</strong></p>';
        return;
    }
    
    // Get ALL players (no team filter)
    $all_players = get_posts(array(
        'post_type' => 'player',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    // Get existing stats
    $existing_stats = array();
    $home_player_ids = array();
    $away_player_ids = array();
    
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE game_id = %d",
            $post->ID
        ));
        foreach ($stats as $stat) {
            $existing_stats[$stat->player_id] = $stat;
            if ($stat->team_id == $home_team_id) {
                $home_player_ids[] = $stat->player_id;
            } else {
                $away_player_ids[] = $stat->player_id;
            }
        }
    }
    
    ?>
    <div id="game-stats-container">
        <h3>Equipo Local: <?php echo esc_html(get_the_title($home_team_id)); ?></h3>
        <p><button type="button" class="button" onclick="addPlayerRow('home', <?php echo $home_team_id; ?>)">+ Agregar Jugador</button></p>
        <table class="widefat" id="home-team-stats" style="margin-bottom: 30px;">
            <thead>
                <tr>
                    <th style="width: 200px;">Jugador</th>
                    <th>AB</th>
                    <th>H</th>
                    <th>HR</th>
                    <th>RBI</th>
                    <th>BB</th>
                    <th>SO</th>
                    <th>SB</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($home_player_ids as $player_id): 
                    $stat = isset($existing_stats[$player_id]) ? $existing_stats[$player_id] : null;
                    $player = get_post($player_id);
                    if (!$player) continue;
                ?>
                <tr>
                    <td>
                        <select name="stats[<?php echo $player->ID; ?>][player_id]" style="width: 100%;" required>
                            <option value="<?php echo $player->ID; ?>"><?php echo esc_html($player->post_title); ?></option>
                        </select>
                    </td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][at_bats]" value="<?php echo $stat ? esc_attr($stat->at_bats) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][hits]" value="<?php echo $stat ? esc_attr($stat->hits) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][home_runs]" value="<?php echo $stat ? esc_attr($stat->home_runs) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][rbis]" value="<?php echo $stat ? esc_attr($stat->rbis) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][walks]" value="<?php echo $stat ? esc_attr($stat->walks) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][strikeouts]" value="<?php echo $stat ? esc_attr($stat->strikeouts) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][stolen_bases]" value="<?php echo $stat ? esc_attr($stat->stolen_bases) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <input type="hidden" name="stats[<?php echo $player->ID; ?>][team_id]" value="<?php echo esc_attr($home_team_id); ?>">
                    <td><button type="button" class="button" onclick="this.closest('tr').remove()">Eliminar</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3>Equipo Visitante: <?php echo esc_html(get_the_title($away_team_id)); ?></h3>
        <p><button type="button" class="button" onclick="addPlayerRow('away', <?php echo $away_team_id; ?>)">+ Agregar Jugador</button></p>
        <table class="widefat" id="away-team-stats">
            <thead>
                <tr>
                    <th style="width: 200px;">Jugador</th>
                    <th>AB</th>
                    <th>H</th>
                    <th>HR</th>
                    <th>RBI</th>
                    <th>BB</th>
                    <th>SO</th>
                    <th>SB</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($away_player_ids as $player_id): 
                    $stat = isset($existing_stats[$player_id]) ? $existing_stats[$player_id] : null;
                    $player = get_post($player_id);
                    if (!$player) continue;
                ?>
                <tr>
                    <td>
                        <select name="stats[<?php echo $player->ID; ?>][player_id]" style="width: 100%;" required>
                            <option value="<?php echo $player->ID; ?>"><?php echo esc_html($player->post_title); ?></option>
                        </select>
                    </td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][at_bats]" value="<?php echo $stat ? esc_attr($stat->at_bats) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][hits]" value="<?php echo $stat ? esc_attr($stat->hits) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][home_runs]" value="<?php echo $stat ? esc_attr($stat->home_runs) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][rbis]" value="<?php echo $stat ? esc_attr($stat->rbis) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][walks]" value="<?php echo $stat ? esc_attr($stat->walks) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][strikeouts]" value="<?php echo $stat ? esc_attr($stat->strikeouts) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <td><input type="number" name="stats[<?php echo $player->ID; ?>][stolen_bases]" value="<?php echo $stat ? esc_attr($stat->stolen_bases) : '0'; ?>" style="width: 50px;" min="0"></td>
                    <input type="hidden" name="stats[<?php echo $player->ID; ?>][team_id]" value="<?php echo esc_attr($away_team_id); ?>">
                    <td><button type="button" class="button" onclick="this.closest('tr').remove()">Eliminar</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><em>AB = Turnos al Bate, H = Hits, HR = Home Runs, RBI = Carreras Impulsadas, BB = Bases por Bolas, SO = Ponches, SB = Bases Robadas</em></p>
    </div>
    
    <script>
    var allPlayers = <?php echo json_encode(array_map(function($p) {
        return array('id' => $p->ID, 'title' => $p->post_title);
    }, $all_players)); ?>;
    
    // Get selected players from both teams
    function getSelectedPlayers() {
        var selected = {
            home: [],
            away: []
        };
        
        // Get home team selections
        document.querySelectorAll('#home-team-stats select[name*="player_id"]').forEach(function(select) {
            if (select.value) {
                selected.home.push(select.value);
            }
        });
        
        // Get away team selections
        document.querySelectorAll('#away-team-stats select[name*="player_id"]').forEach(function(select) {
            if (select.value) {
                selected.away.push(select.value);
            }
        });
        
        return selected;
    }
    
    // Update available players in selects
    function updateAvailablePlayers() {
        var selected = getSelectedPlayers();
        
        // Update home team selects (exclude away team players)
        document.querySelectorAll('#home-team-stats select[name*="player_id"]').forEach(function(select) {
            var currentValue = select.value;
            updateSelectOptions(select, selected.away, currentValue);
        });
        
        // Update away team selects (exclude home team players)
        document.querySelectorAll('#away-team-stats select[name*="player_id"]').forEach(function(select) {
            var currentValue = select.value;
            updateSelectOptions(select, selected.home, currentValue);
        });
    }
    
    // Update options in a select element
    function updateSelectOptions(select, excludeIds, currentValue) {
        var options = '<option value="">Seleccionar jugador...</option>';
        allPlayers.forEach(function(player) {
            // Include if it's the current value OR if it's not in the exclude list
            if (player.id == currentValue || excludeIds.indexOf(player.id.toString()) === -1) {
                var selected = player.id == currentValue ? 'selected' : '';
                options += `<option value="${player.id}" ${selected}>${player.title}</option>`;
            }
        });
        select.innerHTML = options;
    }
    
    function addPlayerRow(team, teamId) {
        var tableId = team === 'home' ? 'home-team-stats' : 'away-team-stats';
        var tbody = document.getElementById(tableId).querySelector('tbody');
        var playerId = 'new_' + Date.now();
        
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="stats[${playerId}][player_id]" style="width: 100%;" required onchange="updateAvailablePlayers()">
                    <option value="">Seleccionar jugador...</option>
                    ${allPlayers.map(p => `<option value="${p.id}">${p.title}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="stats[${playerId}][at_bats]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][hits]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][home_runs]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][rbis]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][walks]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][strikeouts]" value="0" style="width: 50px;" min="0"></td>
            <td><input type="number" name="stats[${playerId}][stolen_bases]" value="0" style="width: 50px;" min="0"></td>
            <input type="hidden" name="stats[${playerId}][team_id]" value="${teamId}">
            <td><button type="button" class="button" onclick="this.closest('tr').remove(); updateAvailablePlayers();">Eliminar</button></td>
        `;
        tbody.appendChild(row);
        updateAvailablePlayers();
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add change listeners to existing selects
        document.querySelectorAll('select[name*="player_id"]').forEach(function(select) {
            select.addEventListener('change', updateAvailablePlayers);
        });
        
        // Initial update
        updateAvailablePlayers();
    });
    </script>
    <?php
}

/**
 * Game Highlights Meta Box Callback
 */
function baseball_game_highlights_callback($post) {
    wp_nonce_field('baseball_save_game_highlights', 'baseball_game_highlights_nonce');
    $highlights = get_post_meta($post->ID, '_game_highlights', true) ?: array();
    ?>
    <div id="highlights-container">
        <?php if (!empty($highlights) && is_array($highlights)) : ?>
            <?php foreach ($highlights as $index => $highlight) : ?>
                <div class="highlight-row" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
                    <h4>Highlight #<?php echo $index + 1; ?></h4>
                    <p>
                        <label><strong>Título:</strong></label><br>
                        <input type="text" name="highlight_title[]" value="<?php echo esc_attr($highlight['title'] ?? ''); ?>" style="width: 100%;">
                    </p>
                    <p>
                        <label><strong>URL del Video/Imagen:</strong></label><br>
                        <input type="url" name="highlight_url[]" value="<?php echo esc_url($highlight['url'] ?? ''); ?>" class="highlight-url-input" style="width: calc(100% - 150px);" placeholder="YouTube, Instagram, Facebook, Twitter o archivo">
                        <button type="button" class="button upload-media-button" style="margin-left: 10px;">Subir Archivo</button>
                        <small style="display: block; margin-top: 5px; color: #666;">
                            Soporta: YouTube, Instagram, Facebook, Twitter/X, videos (.mp4, .webm) e imágenes (.jpg, .png, .gif)
                        </small>
                    </p>
                    <p>
                        <label><strong>Descripción:</strong></label><br>
                        <textarea name="highlight_description[]" rows="3" style="width: 100%;"><?php echo esc_textarea($highlight['description'] ?? ''); ?></textarea>
                    </p>
                    <button type="button" class="button remove-highlight">Eliminar Highlight</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="button button-primary" id="add-highlight">Agregar Highlight</button>
    
    <script>
    jQuery(document).ready(function($) {
        var highlightIndex = <?php echo count($highlights); ?>;
        
        // Add new highlight
        $('#add-highlight').on('click', function() {
            highlightIndex++;
            var html = '<div class="highlight-row" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">' +
                '<h4>Highlight #' + highlightIndex + '</h4>' +
                '<p><label><strong>Título:</strong></label><br>' +
                '<input type="text" name="highlight_title[]" style="width: 100%;"></p>' +
                '<p><label><strong>URL del Video/Imagen:</strong></label><br>' +
                '<input type="url" name="highlight_url[]" class="highlight-url-input" style="width: calc(100% - 150px);" placeholder="YouTube, Instagram, Facebook, Twitter o archivo">' +
                '<button type="button" class="button upload-media-button" style="margin-left: 10px;">Subir Archivo</button>' +
                '<small style="display: block; margin-top: 5px; color: #666;">Soporta: YouTube, Instagram, Facebook, Twitter/X, videos (.mp4, .webm) e imágenes (.jpg, .png, .gif)</small></p>' +
                '<p><label><strong>Descripción:</strong></label><br>' +
                '<textarea name="highlight_description[]" rows="3" style="width: 100%;"></textarea></p>' +
                '<button type="button" class="button remove-highlight">Eliminar Highlight</button>' +
                '</div>';
            $('#highlights-container').append(html);
        });
        
        // Remove highlight
        $(document).on('click', '.remove-highlight', function() {
            $(this).closest('.highlight-row').remove();
        });
        
        // Upload media button
        var mediaUploader;
        $(document).on('click', '.upload-media-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var inputField = button.siblings('.highlight-url-input');
            
            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Extend the wp.media object
            mediaUploader = wp.media({
                title: 'Seleccionar Video o Imagen',
                button: {
                    text: 'Usar este archivo'
                },
                library: {
                    type: ['video', 'image']
                },
                multiple: false
            });
            
            // When a file is selected, run a callback
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url);
            });
            
            // Open the uploader dialog
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

/**
 * Game Scorecard Meta Box Callback
 */
function baseball_game_scorecard_callback($post) {
    wp_nonce_field('baseball_save_game_scorecard', 'baseball_game_scorecard_nonce');
    $scorecard_image = get_post_meta($post->ID, '_game_scorecard_image', true);
    ?>
    <div style="margin-bottom: 20px;">
        <p>
            <label><strong>Imagen de la Anotación Oficial:</strong></label><br>
            <input type="hidden" id="scorecard-image-url" name="scorecard_image" value="<?php echo esc_url($scorecard_image); ?>">
            <button type="button" class="button button-primary" id="upload-scorecard-button">
                <?php echo $scorecard_image ? 'Cambiar Imagen' : 'Subir Imagen'; ?>
            </button>
            <button type="button" class="button" id="remove-scorecard-button" style="<?php echo !$scorecard_image ? 'display:none;' : ''; ?>">
                Eliminar Imagen
            </button>
        </p>
        <div id="scorecard-preview" style="margin-top: 15px; <?php echo !$scorecard_image ? 'display:none;' : ''; ?>">
            <img src="<?php echo esc_url($scorecard_image); ?>" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <p style="margin-top: 10px; color: #666;">
            <small>Sube una imagen de la anotación oficial del partido (scorecard). Formatos soportados: JPG, PNG, PDF.</small>
        </p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var scorecardUploader;
        
        $('#upload-scorecard-button').on('click', function(e) {
            e.preventDefault();
            
            if (scorecardUploader) {
                scorecardUploader.open();
                return;
            }
            
            scorecardUploader = wp.media({
                title: 'Seleccionar Anotación Oficial',
                button: {
                    text: 'Usar esta imagen'
                },
                library: {
                    type: ['image']
                },
                multiple: false
            });
            
            scorecardUploader.on('select', function() {
                var attachment = scorecardUploader.state().get('selection').first().toJSON();
                $('#scorecard-image-url').val(attachment.url);
                $('#scorecard-preview img').attr('src', attachment.url);
                $('#scorecard-preview').show();
                $('#remove-scorecard-button').show();
                $('#upload-scorecard-button').text('Cambiar Imagen');
            });
            
            scorecardUploader.open();
        });
        
        $('#remove-scorecard-button').on('click', function(e) {
            e.preventDefault();
            $('#scorecard-image-url').val('');
            $('#scorecard-preview').hide();
            $(this).hide();
            $('#upload-scorecard-button').text('Subir Imagen');
        });
    });
    </script>
    <?php
}

function baseball_save_game_info($post_id) {
    if (!isset($_POST['baseball_game_info_nonce']) || !wp_verify_nonce($_POST['baseball_game_info_nonce'], 'baseball_save_game_info')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array('game_tournament', 'game_home_team', 'game_away_team', 'game_home_score', 'game_away_score', 'game_date', 'game_time', 'game_location');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Save innings data
    $away_innings = array();
    $home_innings = array();
    
    for ($i = 1; $i <= 9; $i++) {
        $away_innings[] = isset($_POST['away_inning_' . $i]) ? intval($_POST['away_inning_' . $i]) : 0;
        $home_innings[] = isset($_POST['home_inning_' . $i]) ? intval($_POST['home_inning_' . $i]) : 0;
    }
    
    update_post_meta($post_id, '_game_away_innings', implode(',', $away_innings));
    update_post_meta($post_id, '_game_home_innings', implode(',', $home_innings));
    
    // Save hits and errors
    if (isset($_POST['away_hits'])) {
        update_post_meta($post_id, '_game_away_hits', intval($_POST['away_hits']));
    }
    if (isset($_POST['home_hits'])) {
        update_post_meta($post_id, '_game_home_hits', intval($_POST['home_hits']));
    }
    if (isset($_POST['away_errors'])) {
        update_post_meta($post_id, '_game_away_errors', intval($_POST['away_errors']));
    }
    if (isset($_POST['home_errors'])) {
        update_post_meta($post_id, '_game_home_errors', intval($_POST['home_errors']));
    }
    
    // Save game statistics
    if (isset($_POST['stats']) && is_array($_POST['stats'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'baseball_game_stats';
        
        // First, delete all existing stats for this game
        $wpdb->delete($table_name, array('game_id' => $post_id), array('%d'));
        
        foreach ($_POST['stats'] as $key => $stats) {
            // Get the actual player ID from the form
            $player_id = isset($stats['player_id']) ? intval($stats['player_id']) : intval($key);
            
            // Skip if no player selected or no at-bats
            if (!$player_id || !isset($stats['at_bats'])) {
                continue;
            }
            
            $data = array(
                'game_id' => $post_id,
                'player_id' => $player_id,
                'team_id' => intval($stats['team_id']),
                'at_bats' => intval($stats['at_bats']),
                'hits' => intval($stats['hits']),
                'home_runs' => intval($stats['home_runs']),
                'rbis' => intval($stats['rbis']),
                'walks' => intval($stats['walks']),
                'strikeouts' => intval($stats['strikeouts']),
                'stolen_bases' => intval($stats['stolen_bases'])
            );
            
            $wpdb->insert(
                $table_name,
                $data,
                array('%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d')
            );
        }
        
        // Update player cumulative stats
        baseball_update_player_cumulative_stats($post_id);
    }
    
    // Save pitchers data
    if (isset($_POST['baseball_game_pitchers_nonce']) && wp_verify_nonce($_POST['baseball_game_pitchers_nonce'], 'baseball_save_game_pitchers')) {
        // Save away pitchers
        $away_pitchers = array();
        if (isset($_POST['away_pitcher_id']) && is_array($_POST['away_pitcher_id'])) {
            foreach ($_POST['away_pitcher_id'] as $index => $player_id) {
                if (!empty($player_id)) {
                    $away_pitchers[] = array(
                        'player_id' => intval($player_id),
                        'ip' => floatval($_POST['away_pitcher_ip'][$index] ?? 0),
                        'h' => intval($_POST['away_pitcher_h'][$index] ?? 0),
                        'r' => intval($_POST['away_pitcher_r'][$index] ?? 0),
                        'er' => intval($_POST['away_pitcher_er'][$index] ?? 0),
                        'bb' => intval($_POST['away_pitcher_bb'][$index] ?? 0),
                        'so' => intval($_POST['away_pitcher_so'][$index] ?? 0),
                        'decision' => sanitize_text_field($_POST['away_pitcher_decision'][$index] ?? '')
                    );
                }
            }
        }
        update_post_meta($post_id, '_game_away_pitchers', $away_pitchers);
        
        // Save home pitchers
        $home_pitchers = array();
        if (isset($_POST['home_pitcher_id']) && is_array($_POST['home_pitcher_id'])) {
            foreach ($_POST['home_pitcher_id'] as $index => $player_id) {
                if (!empty($player_id)) {
                    $home_pitchers[] = array(
                        'player_id' => intval($player_id),
                        'ip' => floatval($_POST['home_pitcher_ip'][$index] ?? 0),
                        'h' => intval($_POST['home_pitcher_h'][$index] ?? 0),
                        'r' => intval($_POST['home_pitcher_r'][$index] ?? 0),
                        'er' => intval($_POST['home_pitcher_er'][$index] ?? 0),
                        'bb' => intval($_POST['home_pitcher_bb'][$index] ?? 0),
                        'so' => intval($_POST['home_pitcher_so'][$index] ?? 0),
                        'decision' => sanitize_text_field($_POST['home_pitcher_decision'][$index] ?? '')
                    );
                }
            }
        }
        update_post_meta($post_id, '_game_home_pitchers', $home_pitchers);
        
        // Update pitcher cumulative stats
        $all_pitchers_data = array_merge($away_pitchers, $home_pitchers);
        if (!empty($all_pitchers_data)) {
            baseball_update_pitcher_cumulative_stats($post_id, $all_pitchers_data);
        }
    }
}
add_action('save_post_game', 'baseball_save_game_info');

/**
 * Save Game Highlights
 */
function baseball_save_game_highlights($post_id) {
    if (!isset($_POST['baseball_game_highlights_nonce']) || !wp_verify_nonce($_POST['baseball_game_highlights_nonce'], 'baseball_save_game_highlights')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $highlights = array();
    
    if (isset($_POST['highlight_title']) && is_array($_POST['highlight_title'])) {
        foreach ($_POST['highlight_title'] as $index => $title) {
            $url = isset($_POST['highlight_url'][$index]) ? esc_url_raw($_POST['highlight_url'][$index]) : '';
            $description = isset($_POST['highlight_description'][$index]) ? sanitize_textarea_field($_POST['highlight_description'][$index]) : '';
            
            // Only save if there's at least a URL
            if (!empty($url)) {
                $highlights[] = array(
                    'title' => sanitize_text_field($title),
                    'url' => $url,
                    'description' => $description
                );
            }
        }
    }
    
    update_post_meta($post_id, '_game_highlights', $highlights);
}
add_action('save_post_game', 'baseball_save_game_highlights');

/**
 * Save Game Scorecard
 */
function baseball_save_game_scorecard($post_id) {
    if (!isset($_POST['baseball_game_scorecard_nonce']) || !wp_verify_nonce($_POST['baseball_game_scorecard_nonce'], 'baseball_save_game_scorecard')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['scorecard_image'])) {
        update_post_meta($post_id, '_game_scorecard_image', esc_url_raw($_POST['scorecard_image']));
    } else {
        delete_post_meta($post_id, '_game_scorecard_image');
    }
}
add_action('save_post_game', 'baseball_save_game_scorecard');

/**
 * Game Pitchers Meta Box Callback
 */
function baseball_game_pitchers_callback($post) {
    wp_nonce_field('baseball_save_game_pitchers', 'baseball_game_pitchers_nonce');
    
    $home_team_id = get_post_meta($post->ID, '_game_home_team', true);
    $away_team_id = get_post_meta($post->ID, '_game_away_team', true);
    
    // Get saved pitchers data
    $home_pitchers = get_post_meta($post->ID, '_game_home_pitchers', true) ?: array();
    $away_pitchers = get_post_meta($post->ID, '_game_away_pitchers', true) ?: array();
    
    // Get all players
    $all_players = get_posts(array(
        'post_type' => 'player',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    
    ?>
    <style>
        .pitchers-section { margin-bottom: 30px; }
        .pitchers-section h3 { margin-bottom: 15px; color: #0073aa; }
        .pitcher-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr 50px; gap: 10px; margin-bottom: 10px; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 4px; }
        .pitcher-row label { font-weight: 600; font-size: 11px; text-transform: uppercase; color: #666; }
        .pitcher-row input, .pitcher-row select { width: 100%; padding: 5px; }
        .pitcher-row.header { background: #0073aa; color: white; font-weight: bold; }
        .pitcher-row.header label { color: white; }
        .add-pitcher-btn { margin-top: 10px; }
        .remove-pitcher { background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; }
    </style>
    
    <div class="pitchers-section">
        <h3>Pitchers Visitante (<?php echo $away_team_id ? get_the_title($away_team_id) : 'Seleccionar equipo'; ?>)</h3>
        <div class="pitcher-row header">
            <label>Pitcher</label>
            <label>IP</label>
            <label>H</label>
            <label>R</label>
            <label>ER</label>
            <label>BB</label>
            <label>K</label>
            <label>W/L/S</label>
            <label></label>
        </div>
        <div id="away-pitchers-container">
            <?php if (!empty($away_pitchers)): ?>
                <?php foreach ($away_pitchers as $index => $pitcher): ?>
                    <div class="pitcher-row">
                        <select name="away_pitcher_id[]" required>
                            <option value="">Seleccionar Pitcher</option>
                            <?php foreach ($all_players as $player): ?>
                                <option value="<?php echo $player->ID; ?>" <?php selected($pitcher['player_id'], $player->ID); ?>>
                                    <?php echo esc_html($player->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="away_pitcher_ip[]" value="<?php echo esc_attr($pitcher['ip']); ?>" step="0.1" min="0" placeholder="0.0">
                        <input type="number" name="away_pitcher_h[]" value="<?php echo esc_attr($pitcher['h']); ?>" min="0" placeholder="0">
                        <input type="number" name="away_pitcher_r[]" value="<?php echo esc_attr($pitcher['r']); ?>" min="0" placeholder="0">
                        <input type="number" name="away_pitcher_er[]" value="<?php echo esc_attr($pitcher['er']); ?>" min="0" placeholder="0">
                        <input type="number" name="away_pitcher_bb[]" value="<?php echo esc_attr($pitcher['bb']); ?>" min="0" placeholder="0">
                        <input type="number" name="away_pitcher_so[]" value="<?php echo esc_attr($pitcher['so']); ?>" min="0" placeholder="0">
                        <select name="away_pitcher_decision[]">
                            <option value="">-</option>
                            <option value="W" <?php selected($pitcher['decision'], 'W'); ?>>W</option>
                            <option value="L" <?php selected($pitcher['decision'], 'L'); ?>>L</option>
                            <option value="S" <?php selected($pitcher['decision'], 'S'); ?>>S</option>
                        </select>
                        <button type="button" class="remove-pitcher" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="button add-pitcher-btn" onclick="addAwayPitcher()">+ Agregar Pitcher Visitante</button>
    </div>
    
    <hr style="margin: 30px 0;">
    
    <div class="pitchers-section">
        <h3>Pitchers Local (<?php echo $home_team_id ? get_the_title($home_team_id) : 'Seleccionar equipo'; ?>)</h3>
        <div class="pitcher-row header">
            <label>Pitcher</label>
            <label>IP</label>
            <label>H</label>
            <label>R</label>
            <label>ER</label>
            <label>BB</label>
            <label>K</label>
            <label>W/L/S</label>
            <label></label>
        </div>
        <div id="home-pitchers-container">
            <?php if (!empty($home_pitchers)): ?>
                <?php foreach ($home_pitchers as $index => $pitcher): ?>
                    <div class="pitcher-row">
                        <select name="home_pitcher_id[]" required>
                            <option value="">Seleccionar Pitcher</option>
                            <?php foreach ($all_players as $player): ?>
                                <option value="<?php echo $player->ID; ?>" <?php selected($pitcher['player_id'], $player->ID); ?>>
                                    <?php echo esc_html($player->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="home_pitcher_ip[]" value="<?php echo esc_attr($pitcher['ip']); ?>" step="0.1" min="0" placeholder="0.0">
                        <input type="number" name="home_pitcher_h[]" value="<?php echo esc_attr($pitcher['h']); ?>" min="0" placeholder="0">
                        <input type="number" name="home_pitcher_r[]" value="<?php echo esc_attr($pitcher['r']); ?>" min="0" placeholder="0">
                        <input type="number" name="home_pitcher_er[]" value="<?php echo esc_attr($pitcher['er']); ?>" min="0" placeholder="0">
                        <input type="number" name="home_pitcher_bb[]" value="<?php echo esc_attr($pitcher['bb']); ?>" min="0" placeholder="0">
                        <input type="number" name="home_pitcher_so[]" value="<?php echo esc_attr($pitcher['so']); ?>" min="0" placeholder="0">
                        <select name="home_pitcher_decision[]">
                            <option value="">-</option>
                            <option value="W" <?php selected($pitcher['decision'], 'W'); ?>>W</option>
                            <option value="L" <?php selected($pitcher['decision'], 'L'); ?>>L</option>
                            <option value="S" <?php selected($pitcher['decision'], 'S'); ?>>S</option>
                        </select>
                        <button type="button" class="remove-pitcher" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="button add-pitcher-btn" onclick="addHomePitcher()">+ Agregar Pitcher Local</button>
    </div>
    
    <script type="text/javascript">
    (function() {
        const allPlayers = <?php echo json_encode(array_map(function($p) { return ['id' => $p->ID, 'title' => $p->post_title]; }, $all_players)); ?>;
        
        function createPitcherRow(players) {
            let options = '<option value="">Seleccionar Pitcher</option>';
            players.forEach(p => {
                options += '<option value="' + p.id + '">' + p.title + '</option>';
            });
            
            return '<div class="pitcher-row">' +
                '<select name="TEAM_pitcher_id[]" required>' + options + '</select>' +
                '<input type="number" name="TEAM_pitcher_ip[]" step="0.1" min="0" placeholder="0.0">' +
                '<input type="number" name="TEAM_pitcher_h[]" min="0" placeholder="0">' +
                '<input type="number" name="TEAM_pitcher_r[]" min="0" placeholder="0">' +
                '<input type="number" name="TEAM_pitcher_er[]" min="0" placeholder="0">' +
                '<input type="number" name="TEAM_pitcher_bb[]" min="0" placeholder="0">' +
                '<input type="number" name="TEAM_pitcher_so[]" min="0" placeholder="0">' +
                '<select name="TEAM_pitcher_decision[]">' +
                    '<option value="">-</option>' +
                    '<option value="W">W</option>' +
                    '<option value="L">L</option>' +
                    '<option value="S">S</option>' +
                '</select>' +
                '<button type="button" class="remove-pitcher" onclick="this.parentElement.remove()">×</button>' +
            '</div>';
        }
        
        // Make functions global
        window.addAwayPitcher = function() {
            var container = document.getElementById('away-pitchers-container');
            if (container) {
                container.insertAdjacentHTML('beforeend', createPitcherRow(allPlayers).replace(/TEAM/g, 'away'));
            }
        };
        
        window.addHomePitcher = function() {
            var container = document.getElementById('home-pitchers-container');
            if (container) {
                container.insertAdjacentHTML('beforeend', createPitcherRow(allPlayers).replace(/TEAM/g, 'home'));
            }
        };
    })();
    </script>
    <?php
}

/**
 * Update Player Cumulative Statistics
 */
function baseball_update_player_cumulative_stats($game_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    // Get all players in this game
    $players = $wpdb->get_results($wpdb->prepare(
        "SELECT DISTINCT player_id FROM $table_name WHERE game_id = %d",
        $game_id
    ));
    
    foreach ($players as $player) {
        $player_id = $player->player_id;
        
        // Calculate cumulative stats for this player
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                SUM(at_bats) as total_ab,
                SUM(hits) as total_hits,
                SUM(home_runs) as total_hr,
                SUM(rbis) as total_rbi,
                SUM(walks) as total_bb,
                SUM(strikeouts) as total_so,
                SUM(stolen_bases) as total_sb
            FROM $table_name 
            WHERE player_id = %d",
            $player_id
        ));
        
        // Update player meta
        update_post_meta($player_id, '_at_bats', $stats->total_ab);
        update_post_meta($player_id, '_hits', $stats->total_hits);
        update_post_meta($player_id, '_home_runs', $stats->total_hr);
        update_post_meta($player_id, '_rbis', $stats->total_rbi);
        update_post_meta($player_id, '_walks', $stats->total_bb);
        update_post_meta($player_id, '_strikeouts', $stats->total_so);
        update_post_meta($player_id, '_stolen_bases', $stats->total_sb);
        
        // Calculate batting average
        if ($stats->total_ab > 0) {
            $avg = number_format($stats->total_hits / $stats->total_ab, 3);
            update_post_meta($player_id, '_batting_avg', $avg);
        }
    }
}

/**
 * Update Pitcher Cumulative Statistics
 */
function baseball_update_pitcher_cumulative_stats($game_id, $pitchers_data) {
    // Get all unique pitcher IDs from this game
    $pitcher_ids = array_unique(array_column($pitchers_data, 'player_id'));
    
    foreach ($pitcher_ids as $pitcher_id) {
        // Get all games where this pitcher has pitched
        $all_games = get_posts(array(
            'post_type' => 'game',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $total_ip = 0;
        $total_h = 0;
        $total_r = 0;
        $total_er = 0;
        $total_bb = 0;
        $total_so = 0;
        $total_wins = 0;
        $total_losses = 0;
        $total_saves = 0;
        
        foreach ($all_games as $game) {
            $home_pitchers = get_post_meta($game->ID, '_game_home_pitchers', true) ?: array();
            $away_pitchers = get_post_meta($game->ID, '_game_away_pitchers', true) ?: array();
            $all_pitchers = array_merge($home_pitchers, $away_pitchers);
            
            foreach ($all_pitchers as $pitcher) {
                if ($pitcher['player_id'] == $pitcher_id) {
                    $total_ip += floatval($pitcher['ip']);
                    $total_h += intval($pitcher['h']);
                    $total_r += intval($pitcher['r']);
                    $total_er += intval($pitcher['er']);
                    $total_bb += intval($pitcher['bb']);
                    $total_so += intval($pitcher['so']);
                    
                    if ($pitcher['decision'] === 'W') $total_wins++;
                    if ($pitcher['decision'] === 'L') $total_losses++;
                    if ($pitcher['decision'] === 'S') $total_saves++;
                }
            }
        }
        
        // Update pitcher meta - ensure all values are properly typed
        update_post_meta($pitcher_id, '_innings_pitched', floatval($total_ip));
        update_post_meta($pitcher_id, '_pitching_hits', intval($total_h));
        update_post_meta($pitcher_id, '_pitching_runs', intval($total_r));
        update_post_meta($pitcher_id, '_pitching_earned_runs', intval($total_er));
        update_post_meta($pitcher_id, '_pitching_walks', intval($total_bb));
        update_post_meta($pitcher_id, '_pitching_strikeouts', intval($total_so));
        update_post_meta($pitcher_id, '_pitching_wins', intval($total_wins));
        update_post_meta($pitcher_id, '_pitching_losses', intval($total_losses));
        update_post_meta($pitcher_id, '_pitching_saves', intval($total_saves));
        
        // Calculate ERA (Earned Run Average)
        // Formula: (Earned Runs × 9) / Innings Pitched
        if ($total_ip > 0) {
            $era = floatval(($total_er * 9) / $total_ip);
            update_post_meta($pitcher_id, '_era', $era);
        } else {
            update_post_meta($pitcher_id, '_era', 0);
        }
    }
}

/**
 * Get Team Record (W-L)
 */
function baseball_get_team_record($team_id) {
    if (!$team_id) return '0-0';
    
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_game_home_team',
                'value' => $team_id,
            ),
            array(
                'key' => '_game_away_team',
                'value' => $team_id,
            )
        )
    ));
    
    $wins = 0;
    $losses = 0;
    
    foreach ($games as $game) {
        $home_team = get_post_meta($game->ID, '_game_home_team', true);
        $away_team = get_post_meta($game->ID, '_game_away_team', true);
        $home_score = get_post_meta($game->ID, '_game_home_score', true);
        $away_score = get_post_meta($game->ID, '_game_away_score', true);
        
        if ($home_score === '' || $away_score === '') continue;
        
        if ($home_team == $team_id) {
            if ($home_score > $away_score) $wins++;
            else if ($home_score < $away_score) $losses++;
        } else if ($away_team == $team_id) {
            if ($away_score > $home_score) $wins++;
            else if ($away_score < $home_score) $losses++;
        }
    }
    
    return $wins . '-' . $losses;
}

/**
 * Get Player Game Stats
 */
function baseball_get_player_game_stats($player_id, $tournament_id = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    $query = "SELECT gs.*, p.post_title as game_title, 
              pm1.meta_value as home_team_id,
              pm2.meta_value as away_team_id,
              pm3.meta_value as game_date,
              t.post_title as team_name
              FROM $table_name gs
              LEFT JOIN {$wpdb->posts} p ON gs.game_id = p.ID
              LEFT JOIN {$wpdb->posts} t ON gs.team_id = t.ID
              LEFT JOIN {$wpdb->postmeta} pm1 ON (gs.game_id = pm1.post_id AND pm1.meta_key = '_game_home_team')
              LEFT JOIN {$wpdb->postmeta} pm2 ON (gs.game_id = pm2.post_id AND pm2.meta_key = '_game_away_team')
              LEFT JOIN {$wpdb->postmeta} pm3 ON (gs.game_id = pm3.post_id AND pm3.meta_key = '_game_date')
              WHERE gs.player_id = %d";
    
    if ($tournament_id) {
        $query .= $wpdb->prepare(" AND EXISTS (
            SELECT 1 FROM {$wpdb->postmeta} pm4 
            WHERE pm4.post_id = gs.game_id 
            AND pm4.meta_key = '_game_tournament' 
            AND pm4.meta_value = %d
        )", $tournament_id);
    }
    
    $query .= " ORDER BY pm3.meta_value DESC";
    
    return $wpdb->get_results($wpdb->prepare($query, $player_id));
}

/**
 * Get Team Statistics
 */
function baseball_get_team_stats($team_id, $tournament_id = null) {
    global $wpdb;
    
    $where = array();
    $where[] = $wpdb->prepare("(pm1.meta_value = %d OR pm2.meta_value = %d)", $team_id, $team_id);
    
    if ($tournament_id) {
        $where[] = $wpdb->prepare("pm3.meta_value = %d", $tournament_id);
    }
    
    $where_clause = implode(' AND ', $where);
    
    $games = $wpdb->get_results("
        SELECT p.ID, p.post_title,
               pm1.meta_value as home_team_id,
               pm2.meta_value as away_team_id,
               pm4.meta_value as home_score,
               pm5.meta_value as away_score,
               pm6.meta_value as game_date
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = '_game_home_team')
        LEFT JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = '_game_away_team')
        LEFT JOIN {$wpdb->postmeta} pm3 ON (p.ID = pm3.post_id AND pm3.meta_key = '_game_tournament')
        LEFT JOIN {$wpdb->postmeta} pm4 ON (p.ID = pm4.post_id AND pm4.meta_key = '_game_home_score')
        LEFT JOIN {$wpdb->postmeta} pm5 ON (p.ID = pm5.post_id AND pm5.meta_key = '_game_away_score')
        LEFT JOIN {$wpdb->postmeta} pm6 ON (p.ID = pm6.post_id AND pm6.meta_key = '_game_date')
        WHERE p.post_type = 'game' 
        AND p.post_status = 'publish'
        AND $where_clause
        ORDER BY pm6.meta_value DESC
    ");
    
    $wins = 0;
    $losses = 0;
    $runs_scored = 0;
    $runs_allowed = 0;
    
    foreach ($games as $game) {
        $is_home = ($game->home_team_id == $team_id);
        $team_score = $is_home ? $game->home_score : $game->away_score;
        $opponent_score = $is_home ? $game->away_score : $game->home_score;
        
        if ($team_score > $opponent_score) {
            $wins++;
        } else if ($team_score < $opponent_score) {
            $losses++;
        }
        
        $runs_scored += $team_score;
        $runs_allowed += $opponent_score;
    }
    
    return array(
        'wins' => $wins,
        'losses' => $losses,
        'games' => count($games),
        'runs_scored' => $runs_scored,
        'runs_allowed' => $runs_allowed,
        'win_pct' => ($wins + $losses) > 0 ? number_format($wins / ($wins + $losses), 3) : '.000'
    );
}

/**
 * Shortcodes
 */

// Shortcode to display player stats
function baseball_player_stats_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'team' => 0,
        'limit' => -1,
    ), $atts);

    $args = array(
        'post_type' => 'player',
        'posts_per_page' => $atts['limit'],
    );

    if ($atts['id']) {
        $args['p'] = $atts['id'];
    }

    if ($atts['team']) {
        $args['meta_query'] = array(
            array(
                'key' => '_player_team',
                'value' => $atts['team'],
            ),
        );
    }

    $players = new WP_Query($args);

    ob_start();
    
    if ($players->have_posts()) {
        echo '<div class="player-grid">';
        while ($players->have_posts()) {
            $players->the_post();
            $player_id = get_the_ID();
            $player_number = get_post_meta($player_id, '_player_number', true);
            $batting_avg = get_post_meta($player_id, '_batting_avg', true);
            $home_runs = get_post_meta($player_id, '_home_runs', true);
            $rbis = get_post_meta($player_id, '_rbis', true);
            $positions = wp_get_post_terms($player_id, 'position');
            $position_name = !empty($positions) ? $positions[0]->name : 'N/A';
            
            ?>
            <div class="player-card">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('player-thumbnail', array('class' => 'player-photo')); ?>
                <?php endif; ?>
                <div class="player-number">#<?php echo esc_html($player_number); ?></div>
                <h3 class="player-name"><?php the_title(); ?></h3>
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
                    <div class="stat-box">
                        <div class="stat-label">RBI</div>
                        <div class="stat-value"><?php echo esc_html($rbis ?: '0'); ?></div>
                    </div>
                </div>
                <a href="<?php the_permalink(); ?>" class="btn" style="margin-top: 15px;">Ver Detalles</a>
            </div>
            <?php
        }
        echo '</div>';
    }
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('baseball_players', 'baseball_player_stats_shortcode');

// Shortcode to display team standings
function baseball_team_standings_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => -1,
    ), $atts);

    $teams = get_posts(array(
        'post_type' => 'team',
        'posts_per_page' => $atts['limit'],
    ));

    ob_start();
    
    if ($teams) {
        ?>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Ciudad</th>
                    <th>Victorias</th>
                    <th>Derrotas</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): 
                    $wins = get_post_meta($team->ID, '_team_wins', true) ?: 0;
                    $losses = get_post_meta($team->ID, '_team_losses', true) ?: 0;
                    $total = $wins + $losses;
                    $pct = $total > 0 ? number_format($wins / $total, 3) : '.000';
                    $city = get_post_meta($team->ID, '_city', true);
                ?>
                <tr>
                    <td><strong><a href="<?php echo get_permalink($team->ID); ?>"><?php echo esc_html($team->post_title); ?></a></strong></td>
                    <td><?php echo esc_html($city); ?></td>
                    <td><?php echo esc_html($wins); ?></td>
                    <td><?php echo esc_html($losses); ?></td>
                    <td><?php echo esc_html($pct); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    return ob_get_clean();
}
add_shortcode('baseball_standings', 'baseball_team_standings_shortcode');

// Shortcode to display batting leaders
function baseball_batting_leaders_shortcode($atts) {
    $atts = shortcode_atts(array(
        'stat' => 'batting_avg',
        'limit' => 6,
        'show_more' => 'true',
    ), $atts);

    $args = array(
        'post_type' => 'player',
        'posts_per_page' => $atts['limit'],
        'meta_key' => '_' . $atts['stat'],
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );

    $players = new WP_Query($args);

    $stat_labels = array(
        'batting_avg' => 'Promedio de Bateo',
        'home_runs' => 'Home Runs',
        'rbis' => 'Carreras Impulsadas',
        'hits' => 'Hits',
        'stolen_bases' => 'Bases Robadas',
    );

    $stat_label = isset($stat_labels[$atts['stat']]) ? $stat_labels[$atts['stat']] : $atts['stat'];

    ob_start();
    
    if ($players->have_posts()) {
        ?>
        <div class="leaders-compact">
            <h3 class="leaders-compact-title">Líderes en <?php echo esc_html($stat_label); ?></h3>
            <div class="leaders-compact-list">
                <?php 
                $rank = 1;
                while ($players->have_posts()): 
                    $players->the_post();
                    $player_id = get_the_ID();
                    $stat_value = get_post_meta($player_id, '_' . $atts['stat'], true);
                    $team_id = get_post_meta($player_id, '_player_team', true);
                    $team_name = $team_id ? get_the_title($team_id) : 'N/A';
                    $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'N/A';
                ?>
                <a href="<?php the_permalink(); ?>" class="leader-compact-item">
                    <div class="leader-compact-rank"><?php echo $rank++; ?></div>
                    <div class="leader-compact-photo">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('thumbnail'); ?>
                        <?php else: ?>
                            <div class="no-photo-compact">
                                <span class="dashicons dashicons-admin-users"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="leader-compact-info">
                        <div class="leader-compact-name"><?php the_title(); ?></div>
                        <div class="leader-compact-team"><?php echo esc_html($team_abbr); ?></div>
                    </div>
                    <div class="leader-compact-stat"><?php echo esc_html($stat_value ?: '0'); ?></div>
                </a>
                <?php endwhile; ?>
            </div>
            
            <?php if ($atts['show_more'] === 'true'): ?>
                <div class="leaders-more">
                    <a href="<?php echo home_url('/lideres/'); ?>" class="btn btn-primary">Ver Todos los Líderes →</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('baseball_leaders', 'baseball_batting_leaders_shortcode');

// Shortcode compacto para sidebar/widgets
function baseball_leaders_compact_shortcode($atts) {
    $atts = shortcode_atts(array(
        'stat' => 'batting_avg',
        'limit' => 6,
        'title' => '',
    ), $atts);

    $args = array(
        'post_type' => 'player',
        'posts_per_page' => $atts['limit'],
        'meta_key' => '_' . $atts['stat'],
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );

    $players = new WP_Query($args);

    $stat_labels = array(
        'batting_avg' => 'AVG',
        'home_runs' => 'HR',
        'rbis' => 'RBI',
        'hits' => 'H',
        'stolen_bases' => 'SB',
    );

    $stat_label = isset($stat_labels[$atts['stat']]) ? $stat_labels[$atts['stat']] : $atts['stat'];

    ob_start();
    
    if ($players->have_posts()) {
        ?>
        <div class="leaders-compact">
            <?php if ($atts['title']): ?>
                <h3 class="leaders-compact-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <div class="leaders-compact-list">
                <?php 
                $rank = 1;
                while ($players->have_posts()): 
                    $players->the_post();
                    $player_id = get_the_ID();
                    $stat_value = get_post_meta($player_id, '_' . $atts['stat'], true);
                    $team_id = get_post_meta($player_id, '_player_team', true);
                    $team_name = $team_id ? get_the_title($team_id) : 'N/A';
                    
                    // Get team abbreviation (first 3 letters)
                    $team_abbr = $team_id ? strtoupper(substr(get_the_title($team_id), 0, 3)) : 'N/A';
                ?>
                <a href="<?php the_permalink(); ?>" class="leader-compact-item">
                    <div class="leader-compact-rank"><?php echo $rank++; ?></div>
                    <div class="leader-compact-photo">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('thumbnail'); ?>
                        <?php else: ?>
                            <div class="no-photo-compact">
                                <span class="dashicons dashicons-admin-users"></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="leader-compact-info">
                        <div class="leader-compact-name"><?php the_title(); ?></div>
                        <div class="leader-compact-team"><?php echo esc_html($team_abbr); ?></div>
                    </div>
                    <div class="leader-compact-stat"><?php echo esc_html($stat_value ?: '0'); ?></div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
    }
    
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('baseball_leaders_compact', 'baseball_leaders_compact_shortcode');

// Shortcode to display tournament standings
function baseball_tournament_standings_shortcode($atts) {
    $atts = shortcode_atts(array(
        'tournament_id' => 0,
    ), $atts);

    if (!$atts['tournament_id']) {
        return '<p>Por favor especifica un ID de torneo.</p>';
    }

    $teams_args = array(
        'post_type' => 'team',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_team_tournaments',
                'value' => serialize(strval($atts['tournament_id'])),
                'compare' => 'LIKE'
            )
        )
    );
    $teams = get_posts($teams_args);

    ob_start();
    
    if ($teams) {
        $standings = array();
        foreach ($teams as $team) {
            $stats = baseball_get_team_stats($team->ID, $atts['tournament_id']);
            $standings[] = array(
                'team' => $team,
                'stats' => $stats
            );
        }
        
        usort($standings, function($a, $b) {
            if ($a['stats']['wins'] == $b['stats']['wins']) {
                return $b['stats']['runs_scored'] - $a['stats']['runs_scored'];
            }
            return $b['stats']['wins'] - $a['stats']['wins'];
        });
        
        ?>
        <div class="tournament-standings">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Pos</th>
                        <th>Equipo</th>
                        <th>PJ</th>
                        <th>G</th>
                        <th>P</th>
                        <th>%</th>
                        <th>CF</th>
                        <th>CC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $pos = 1;
                    foreach ($standings as $standing): 
                        $team = $standing['team'];
                        $stats = $standing['stats'];
                    ?>
                    <tr>
                        <td><?php echo $pos++; ?></td>
                        <td>
                            <strong>
                                <a href="<?php echo get_permalink($team->ID); ?>">
                                    <?php echo esc_html($team->post_title); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo $stats['games']; ?></td>
                        <td><?php echo $stats['wins']; ?></td>
                        <td><?php echo $stats['losses']; ?></td>
                        <td><?php echo $stats['win_pct']; ?></td>
                        <td><?php echo $stats['runs_scored']; ?></td>
                        <td><?php echo $stats['runs_allowed']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><em>PJ = Partidos Jugados, G = Ganados, P = Perdidos, % = Porcentaje, CF = Carreras a Favor, CC = Carreras en Contra</em></p>
        </div>
        <?php
    } else {
        echo '<p>No hay equipos en este torneo.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('baseball_tournament_standings', 'baseball_tournament_standings_shortcode');

// Shortcode to display recent games
function baseball_recent_games_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 5,
        'tournament_id' => 0,
    ), $atts);

    $args = array(
        'post_type' => 'game',
        'posts_per_page' => $atts['limit'],
        'meta_key' => '_game_date',
        'orderby' => 'meta_value',
        'order' => 'DESC'
    );

    if ($atts['tournament_id']) {
        $args['meta_query'] = array(
            array(
                'key' => '_game_tournament',
                'value' => $atts['tournament_id']
            )
        );
    }

    $games = get_posts($args);

    ob_start();
    
    if ($games) {
        ?>
        <div class="recent-games">
            <?php foreach ($games as $game): 
                $home_team_id = get_post_meta($game->ID, '_game_home_team', true);
                $away_team_id = get_post_meta($game->ID, '_game_away_team', true);
                $home_score = get_post_meta($game->ID, '_game_home_score', true);
                $away_score = get_post_meta($game->ID, '_game_away_score', true);
                $game_date = get_post_meta($game->ID, '_game_date', true);
            ?>
                <div class="game-card">
                    <div class="game-date">
                        <?php echo $game_date ? date('d/m/Y', strtotime($game_date)) : '-'; ?>
                    </div>
                    <div class="game-matchup">
                        <div class="team away-team">
                            <span class="team-name"><?php echo get_the_title($away_team_id); ?></span>
                            <span class="team-score"><?php echo $away_score !== '' ? $away_score : '-'; ?></span>
                        </div>
                        <div class="vs">vs</div>
                        <div class="team home-team">
                            <span class="team-name"><?php echo get_the_title($home_team_id); ?></span>
                            <span class="team-score"><?php echo $home_score !== '' ? $home_score : '-'; ?></span>
                        </div>
                    </div>
                    <a href="<?php echo get_permalink($game->ID); ?>" class="btn btn-small">Ver Detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        echo '<p>No hay partidos disponibles.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('baseball_recent_games', 'baseball_recent_games_shortcode');

// Shortcode to display seasons list
function baseball_seasons_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => -1,
    ), $atts);

    $seasons = get_posts(array(
        'post_type' => 'season',
        'posts_per_page' => $atts['limit'],
        'orderby' => 'meta_value_num',
        'meta_key' => '_season_year',
        'order' => 'DESC'
    ));

    ob_start();
    
    if ($seasons) {
        ?>
        <div class="seasons-grid">
            <?php foreach ($seasons as $season): 
                $year = get_post_meta($season->ID, '_season_year', true);
                $start_date = get_post_meta($season->ID, '_season_start_date', true);
                $end_date = get_post_meta($season->ID, '_season_end_date', true);
            ?>
                <div class="season-card">
                    <h3>
                        <a href="<?php echo get_permalink($season->ID); ?>">
                            <?php echo esc_html($season->post_title); ?>
                        </a>
                    </h3>
                    <?php if ($year): ?>
                        <p class="season-year"><?php echo esc_html($year); ?></p>
                    <?php endif; ?>
                    <?php if ($start_date && $end_date): ?>
                        <p class="season-dates">
                            <?php echo date('d/m/Y', strtotime($start_date)); ?> - 
                            <?php echo date('d/m/Y', strtotime($end_date)); ?>
                        </p>
                    <?php endif; ?>
                    <a href="<?php echo get_permalink($season->ID); ?>" class="btn">Ver Temporada</a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        echo '<p>No hay temporadas disponibles.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('baseball_seasons', 'baseball_seasons_list_shortcode');

/**
 * Widget Areas
 */
function baseball_widgets_init() {
    register_sidebar(array(
        'name'          => 'Sidebar Principal',
        'id'            => 'sidebar-1',
        'description'   => 'Área de widgets para la barra lateral',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'baseball_widgets_init');

/**
 * Customize the WordPress Customizer
 */
function baseball_customize_register($wp_customize) {
    // Modify the logo section
    $wp_customize->get_section('title_tagline')->title = __('Logo y Título del Sitio', 'baseball-stats');
    $wp_customize->get_section('title_tagline')->priority = 20;
    $wp_customize->get_section('title_tagline')->description = __('Configura el logo de tu sitio de baseball. El logo aparecerá en la esquina superior izquierda del header.', 'baseball-stats');
    
    // Modify the logo control
    $wp_customize->get_control('custom_logo')->label = __('Logo del Sitio', 'baseball-stats');
    $wp_customize->get_control('custom_logo')->description = __('Sube tu logo personalizado. Recomendado: 300x100 píxeles (PNG o SVG para mejor calidad).', 'baseball-stats');
    $wp_customize->get_control('custom_logo')->priority = 5;
}
add_action('customize_register', 'baseball_customize_register');

/**
 * AJAX Handler for Leaders Widget
 */
function baseball_get_leaders_ajax() {
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'bateo';
    $stat = isset($_POST['stat']) ? sanitize_text_field($_POST['stat']) : 'avg';
    $meta_key = isset($_POST['meta_key']) ? sanitize_text_field($_POST['meta_key']) : '_batting_avg';
    $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
    
    // Get players
    $args = array(
        'post_type' => 'player',
        'posts_per_page' => 6,
        'meta_key' => $meta_key,
        'orderby' => 'meta_value_num',
        'order' => $order,
    );
    
    // Special handling for pitching stats - only show players with pitching data
    if ($category === 'pitcheo') {
        if ($stat === 'era') {
            // For ERA, show players with innings pitched > 0
            $args['meta_query'] = array(
                array(
                    'key' => '_innings_pitched',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'DECIMAL'
                )
            );
        } else {
            // For other pitching stats, show players with values > 0
            $args['meta_query'] = array(
                array(
                    'key' => $meta_key,
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
        }
    }
    
    $players = get_posts($args);
    
    if (!$players) {
        ob_start();
        ?>
        <div class="no-leaders-data">
            <p><em>No hay jugadores con estadísticas de <?php echo $category === 'pitcheo' ? 'pitcheo' : 'bateo'; ?> disponibles.</em></p>
            <p><small>Asegúrate de haber registrado partidos con estadísticas de <?php echo $category === 'pitcheo' ? 'pitchers' : 'bateadores'; ?>.</small></p>
        </div>
        <?php
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
        return;
    }
    
    // Build HTML
    ob_start();
    ?>
    <div class="stats-list">
        <?php 
        $rank = 1;
        foreach ($players as $player) : 
            $stat_value = get_post_meta($player->ID, $meta_key, true);
            $team_id = get_post_meta($player->ID, '_player_team', true);
            $team_name = $team_id ? get_the_title($team_id) : '';
            $team_abbr = $team_name ? strtoupper(substr($team_name, 0, 3)) : '';
            
            // Format stat value
            if ($stat === 'avg') {
                $display_value = $stat_value ? number_format(floatval($stat_value), 3) : '.000';
            } elseif ($stat === 'era') {
                // Always calculate ERA from IP and ER to ensure accuracy
                $ip = floatval(get_post_meta($player->ID, '_innings_pitched', true));
                $er = floatval(get_post_meta($player->ID, '_pitching_earned_runs', true));
                
                if ($ip > 0) {
                    $calculated_era = ($er * 9) / $ip;
                    $display_value = number_format($calculated_era, 2);
                } else {
                    $display_value = '0.00';
                }
            } elseif ($stat === 'ip') {
                $display_value = number_format(floatval($stat_value), 1);
            } else {
                $display_value = intval($stat_value);
            }
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
                <?php if ($team_abbr) : ?>
                    <span class="team-abbr"><?php echo esc_html($team_abbr); ?></span>
                <?php endif; ?>
            </div>
            <span class="stat-value"><?php echo esc_html($display_value); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="leaders-view-more">
        <a href="<?php echo get_post_type_archive_link('player'); ?>" class="btn-view-more">
            Ver más →
        </a>
    </div>
    <?php
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_get_leaders', 'baseball_get_leaders_ajax');
add_action('wp_ajax_nopriv_get_leaders', 'baseball_get_leaders_ajax');
