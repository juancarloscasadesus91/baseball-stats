<?php
/**
 * Add OBP-related batting stat columns to the production database.
 *
 * CLI:
 * php update-obp-stats-production.php
 *
 * Browser:
 * /wp-content/themes/baseball-stats/update-obp-stats-production.php
 *
 * @package Baseball_Stats
 */

$wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

if (!file_exists($wp_load_path)) {
    die('Error: wp-load.php not found at ' . $wp_load_path);
}

require_once $wp_load_path;

function baseball_run_obp_schema_migration() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'baseball_game_stats';
    $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name));

    if ($table_exists !== $table_name) {
        return array(
            'success' => false,
            'message' => "Error: table {$table_name} does not exist.",
        );
    }

    $columns = array(
        'hit_by_pitch' => array('definition' => 'int(11) DEFAULT 0', 'after' => 'walks'),
        'grounded_into_dp' => array('definition' => 'int(11) DEFAULT 0', 'after' => 'strikeouts'),
        'sacrifice_flies' => array('definition' => 'int(11) DEFAULT 0', 'after' => 'grounded_into_dp'),
        'reached_on_error' => array('definition' => 'int(11) DEFAULT 0', 'after' => 'sacrifice_flies'),
        'fielders_choice' => array('definition' => 'int(11) DEFAULT 0', 'after' => 'reached_on_error'),
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
            return array(
                'success' => false,
                'message' => "Error adding {$column}: {$wpdb->last_error}",
            );
        }

        $added[] = $column;
    }

    $set_clauses = array();
    foreach (array_keys($columns) as $column) {
        $set_clauses[] = "{$column} = COALESCE({$column}, 0)";
    }

    $normalized = $wpdb->query("UPDATE {$table_name} SET " . implode(', ', $set_clauses));

    if ($normalized === false) {
        return array(
            'success' => false,
            'message' => "Error normalizing null values: {$wpdb->last_error}",
        );
    }

    return array(
        'success' => true,
        'message' => "Migracion OBP completada.\nColumnas agregadas: " . (empty($added) ? 'ninguna' : implode(', ', $added)) . "\nYa existian: " . (empty($skipped) ? 'ninguna' : implode(', ', $skipped)) . "\nFilas normalizadas: {$normalized}",
    );
}

if (php_sapi_name() === 'cli') {
    $result = baseball_run_obp_schema_migration();
    echo $result['message'] . "\n";
    exit($result['success'] ? 0 : 1);
}

if (!current_user_can('manage_options')) {
    wp_die('Error: necesitas iniciar sesion como administrador de WordPress.');
}

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'SI') {
    check_admin_referer('baseball_update_obp_stats');
    $result = baseball_run_obp_schema_migration();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Estadisticas OBP</title>
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
    <h1>Actualizar Estadisticas OBP</h1>
    <div class="box">
        <?php if ($result): ?>
            <div class="<?php echo $result['success'] ? 'success' : 'error'; ?>"><?php echo esc_html($result['message']); ?></div>
        <?php else: ?>
            <p>Este proceso agrega las columnas <code>HBP</code>, <code>GIDP</code>, <code>SF</code>, <code>ROE</code> y <code>FC</code> a la tabla de estadisticas por partido.</p>
            <form method="post">
                <?php wp_nonce_field('baseball_update_obp_stats'); ?>
                <input type="hidden" name="confirm" value="SI">
                <button type="submit">Ejecutar actualizacion de base de datos</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
