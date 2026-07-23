<?php
/**
 * Script Simple para Resetear Estadísticas (Sin dependencia de WordPress UI)
 * 
 * @package Baseball_Stats
 */

// Solo cargar WordPress cuando se ejecuta una acción
if (isset($_POST['action'])) {
    // Cargar WordPress
    $wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
    if (!file_exists($wp_load_path)) {
        die('Error: No se encontró wp-load.php en: ' . $wp_load_path);
    }
    require_once($wp_load_path);
    
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        die('Error: No tienes permisos de administrador.');
    }
    
    // Procesar acción
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'SI') {
        $action = $_POST['action'];
        
        switch ($action) {
            case 'reset_player_stats':
                $result = reset_player_cumulative_stats();
                $message = $result['message'];
                break;
                
            case 'reset_game_stats_table':
                $result = reset_game_stats_table();
                $message = $result['message'];
                break;
                
            case 'reset_all':
                $result1 = reset_player_cumulative_stats();
                $result2 = reset_game_stats_table();
                $message = $result1['message'] . '<br>' . $result2['message'];
                break;
                
            case 'recalculate_stats':
                $result = recalculate_all_player_stats();
                $message = $result['message'];
                break;
        }
    }
}

/**
 * Resetear estadísticas acumuladas de todos los jugadores
 */
function reset_player_cumulative_stats() {
    $players = get_posts(array(
        'post_type' => 'player',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));
    
    $count = 0;
    $meta_keys = array(
        '_at_bats', '_hits', '_home_runs', '_rbis', '_walks',
        '_hit_by_pitch', '_grounded_into_dp', '_sacrifice_flies',
        '_reached_on_error', '_fielders_choice', '_strikeouts',
        '_doubles', '_triples', '_errors', '_batting_avg', '_on_base_percentage',
        '_innings_pitched', '_pitching_hits', '_pitching_runs',
        '_pitching_earned_runs', '_pitching_walks', '_pitching_strikeouts',
        '_pitching_wins', '_pitching_losses', '_pitching_saves', '_era'
    );
    
    foreach ($players as $player) {
        foreach ($meta_keys as $key) {
            delete_post_meta($player->ID, $key);
        }
        $count++;
    }
    
    return array(
        'success' => true,
        'message' => "✅ Estadísticas acumuladas de {$count} jugadores han sido reseteadas."
    );
}

/**
 * Limpiar tabla de estadísticas de juegos
 */
function reset_game_stats_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    $count = $wpdb->query("SELECT COUNT(*) FROM $table_name");
    $wpdb->query("TRUNCATE TABLE $table_name");
    
    return array(
        'success' => true,
        'message' => "✅ Se eliminaron {$count} registros de la tabla de estadísticas de juegos."
    );
}

/**
 * Recalcular estadísticas de todos los jugadores basándose en juegos existentes
 */
function recalculate_all_player_stats() {
    global $wpdb;
    
    // Primero resetear
    reset_player_cumulative_stats();
    
    // Obtener todos los juegos publicados
    $games = get_posts(array(
        'post_type' => 'game',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $count = 0;
    foreach ($games as $game) {
        // Recalcular estadísticas de bateo
        baseball_update_player_cumulative_stats($game->ID);
        
        // Recalcular estadísticas de pitcheo
        $home_pitchers = get_post_meta($game->ID, '_game_home_pitchers', true) ?: array();
        $away_pitchers = get_post_meta($game->ID, '_game_away_pitchers', true) ?: array();
        $all_pitchers = array_merge($home_pitchers, $away_pitchers);
        
        if (!empty($all_pitchers)) {
            baseball_update_pitcher_cumulative_stats($game->ID, $all_pitchers);
        }
        
        $count++;
    }
    
    return array(
        'success' => true,
        'message' => "✅ Estadísticas recalculadas basándose en {$count} juegos existentes."
    );
}

// Obtener estadísticas actuales (sin cargar WordPress completo)
$stats_info = '';
if (isset($_POST['action'])) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    $stats_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $players_count = wp_count_posts('player')->publish;
    $games_count = wp_count_posts('game')->publish;
} else {
    $stats_count = '?';
    $players_count = '?';
    $games_count = '?';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetear Estadísticas - Baseball Stats</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        
        .alert-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        
        .action-card {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .action-card h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .action-card p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-primary {
            background: #667eea;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .confirm-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 16px;
            margin: 10px 0;
        }
        
        .stats-info {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .stats-info strong {
            color: #667eea;
        }
        
        form {
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ Resetear Estadísticas</h1>
            <p>Herramienta de administración para limpiar y recalcular estadísticas</p>
        </div>
        
        <div class="content">
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
                <div class="alert alert-info">
                    <strong>✅ Acción completada.</strong> Puedes cerrar esta ventana o realizar otra acción.
                </div>
            <?php endif; ?>
            
            <div class="alert alert-warning">
                <strong>⚠️ ADVERTENCIA:</strong> Estas acciones son permanentes y no se pueden deshacer.
            </div>
            
            <div class="stats-info">
                <p><strong>Estadísticas actuales:</strong></p>
                <p>📊 Registros en tabla: <strong><?php echo $stats_count; ?></strong></p>
                <p>👤 Jugadores: <strong><?php echo $players_count; ?></strong></p>
                <p>⚾ Partidos: <strong><?php echo $games_count; ?></strong></p>
            </div>
            
            <!-- Acción 1 -->
            <div class="action-card">
                <h3>🔄 Resetear Estadísticas de Jugadores</h3>
                <p>Elimina todas las estadísticas acumuladas de los jugadores.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="reset_player_stats">
                    <div class="form-group">
                        <label>Escribe "SI" para confirmar:</label>
                        <input type="text" name="confirm" class="confirm-input" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Ejecutar</button>
                </form>
            </div>
            
            <!-- Acción 2 -->
            <div class="action-card">
                <h3>🗑️ Limpiar Tabla de Estadísticas</h3>
                <p>Elimina TODOS los registros de wp_baseball_game_stats.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="reset_game_stats_table">
                    <div class="form-group">
                        <label>Escribe "SI" para confirmar:</label>
                        <input type="text" name="confirm" class="confirm-input" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Ejecutar</button>
                </form>
            </div>
            
            <!-- Acción 3 -->
            <div class="action-card">
                <h3>💣 Resetear TODO</h3>
                <p>Elimina tanto las estadísticas de jugadores como la tabla de estadísticas.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="reset_all">
                    <div class="form-group">
                        <label>Escribe "SI" para confirmar:</label>
                        <input type="text" name="confirm" class="confirm-input" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Ejecutar</button>
                </form>
            </div>
            
            <!-- Acción 4 -->
            <div class="action-card">
                <h3>♻️ Recalcular Estadísticas ⭐ RECOMENDADO</h3>
                <p><strong>Esta es la que necesitas.</strong> Recalcula las estadísticas basándose en los juegos existentes.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="recalculate_stats">
                    <div class="form-group">
                        <label>Escribe "SI" para confirmar:</label>
                        <input type="text" name="confirm" class="confirm-input" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ejecutar</button>
                </form>
            </div>
            
            <p style="margin-top: 20px;">
                <a href="<?php echo isset($_POST['action']) ? '../../../..' : '../../../../wp-admin'; ?>" style="color: #667eea; text-decoration: none; font-weight: 600;">
                    ← Volver al Dashboard
                </a>
            </p>
        </div>
    </div>
</body>
</html>
