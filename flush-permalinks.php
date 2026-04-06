<?php
/**
 * Flush Permalinks
 * Run this to refresh WordPress rewrite rules
 */

require_once(__DIR__ . '/../../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this script.');
}

// Flush rewrite rules
flush_rewrite_rules(true);

echo "✓ Permalinks flushed successfully!<br>";
echo "✓ Rewrite rules refreshed!<br><br>";

echo "Now try accessing: <a href='" . home_url('/noticias/') . "'>" . home_url('/noticias/') . "</a>";
?>
