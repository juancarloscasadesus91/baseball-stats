<?php
/**
 * Add OBP-related batting stat columns to the production database.
 *
 * Run from the theme directory:
 * php update-obp-stats-production.php
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

global $wpdb;

$table_name = $wpdb->prefix . 'baseball_game_stats';
$table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name));

if ($table_exists !== $table_name) {
    die("Error: table {$table_name} does not exist.\n");
}

$columns = array(
    'hit_by_pitch' => array(
        'definition' => 'int(11) DEFAULT 0',
        'after' => 'walks',
    ),
    'grounded_into_dp' => array(
        'definition' => 'int(11) DEFAULT 0',
        'after' => 'strikeouts',
    ),
    'sacrifice_flies' => array(
        'definition' => 'int(11) DEFAULT 0',
        'after' => 'grounded_into_dp',
    ),
    'reached_on_error' => array(
        'definition' => 'int(11) DEFAULT 0',
        'after' => 'sacrifice_flies',
    ),
    'fielders_choice' => array(
        'definition' => 'int(11) DEFAULT 0',
        'after' => 'reached_on_error',
    ),
);

$added = array();
$skipped = array();

foreach ($columns as $column => $config) {
    $exists = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM {$table_name} LIKE %s", $column));

    if ($exists === $column) {
        $skipped[] = $column;
        continue;
    }

    $sql = "ALTER TABLE {$table_name} ADD COLUMN {$column} {$config['definition']} AFTER {$config['after']}";
    $result = $wpdb->query($sql);

    if ($result === false) {
        die("Error adding {$column}: {$wpdb->last_error}\n");
    }

    $added[] = $column;
}

$set_clauses = array();
foreach (array_keys($columns) as $column) {
    $set_clauses[] = "{$column} = COALESCE({$column}, 0)";
}

$result = $wpdb->query("UPDATE {$table_name} SET " . implode(', ', $set_clauses));

if ($result === false) {
    die("Error normalizing null values: {$wpdb->last_error}\n");
}

echo "OBP stat migration complete.\n";
echo 'Added columns: ' . (empty($added) ? 'none' : implode(', ', $added)) . "\n";
echo 'Already existed: ' . (empty($skipped) ? 'none' : implode(', ', $skipped)) . "\n";
echo "Normalized rows: {$result}\n";
