<?php
/**
 * Recalculate batting cumulative stats after adding OBP fields.
 *
 * CLI:
 * php recalculate-obp-stats.php
 *
 * Browser:
 * /wp-content/themes/baseball-stats/recalculate-obp-stats.php
 *
 * @package Baseball_Stats
 */

$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

if (!file_exists($wp_load_path)) {
    die('Error: wp-load.php not found at ' . $wp_load_path);
}

require_once $wp_load_path;

function baseball_run_obp_stats_recalculation() {
    if (!function_exists('baseball_recalculate_single_player_stats')) {
        return array(
            'success' => false,
            'message' => 'Error: baseball_recalculate_single_player_stats() is not available. Activate the Baseball Stats theme first.',
        );
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

    return array(
        'success' => true,
        'message' => "Estadisticas de bateo y OBP recalculadas para {$count} jugadores.",
    );
}

if (php_sapi_name() === 'cli') {
    $result = baseball_run_obp_stats_recalculation();
    echo $result['message'] . "\n";
    exit($result['success'] ? 0 : 1);
}

if (!current_user_can('manage_options')) {
    wp_die('Error: necesitas iniciar sesion como administrador de WordPress.');
}

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'SI') {
    check_admin_referer('baseball_recalculate_obp_stats');
    $result = baseball_run_obp_stats_recalculation();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recalcular OBP</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 760px; margin: 40px auto; padding: 0 20px; line-height: 1.5; }
        .box { border: 1px solid #ccd0d4; padding: 20px; background: #fff; }
        .success { border-left: 4px solid #46b450; padding: 12px; background: #f0fff4; white-space: pre-line; }
        .error { border-left: 4px solid #dc3232; padding: 12px; background: #fff5f5; white-space: pre-line; }
        button { background: #2271b1; color: #fff; border: 0; padding: 10px 16px; cursor: pointer; }
        code { background: #f0f0f1; padding: 2px 4px; }
    </style>
</head>
<body>
    <h1>Recalcular OBP</h1>
    <div class="box">
        <?php if ($result): ?>
            <div class="<?php echo $result['success'] ? 'success' : 'error'; ?>"><?php echo esc_html($result['message']); ?></div>
        <?php else: ?>
            <p>Este proceso recalcula los acumulados de bateo de todos los jugadores, incluyendo <code>OBP</code>, desde la tabla historica de estadisticas por partido.</p>
            <form method="post">
                <?php wp_nonce_field('baseball_recalculate_obp_stats'); ?>
                <input type="hidden" name="confirm" value="SI">
                <button type="submit">Recalcular estadisticas acumuladas</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
