<?php
/**
 * Recalculate batting cumulative stats after adding OBP fields.
 *
 * Run from the theme directory:
 * php recalculate-obp-stats.php
 *
 * @package Baseball_Stats
 */

$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

if (!file_exists($wp_load_path)) {
    die("Error: wp-load.php not found at {$wp_load_path}\n");
}

require_once $wp_load_path;

if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) {
    die("Error: administrator permissions required.\n");
}

if (!function_exists('baseball_recalculate_single_player_stats')) {
    die("Error: baseball_recalculate_single_player_stats() is not available. Activate the Baseball Stats theme first.\n");
}

$players = get_posts(array(
    'post_type' => 'player',
    'posts_per_page' => -1,
    'post_status' => 'any',
    'fields' => 'ids',
));

$count = 0;

foreach ($players as $player_id) {
    baseball_recalculate_single_player_stats(intval($player_id));
    $count++;
}

echo "Recalculated batting stats and OBP for {$count} players.\n";
