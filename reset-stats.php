<?php
/**
 * Script para Resetear Estadísticas
 * 
 * INSTRUCCIONES:
 * 1. Accede a este archivo desde el navegador: http://tu-sitio.com/wp-content/themes/baseball-stats/reset-stats.php
 * 2. Elige qué quieres resetear
 * 3. Confirma la acción
 * 
 * IMPORTANTE: Este script borrará datos permanentemente. Úsalo con cuidado.
 * 
 * @package Baseball_Stats
 */

// Cargar WordPress
// Intentar diferentes rutas para encontrar wp-load.php
$wp_load_paths = array(
    '../../../../wp-load.php',
    '../../../wp-load.php',
    dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
    $_SERVER['DOCUMENT_ROOT'] . '/stats/wordpress/wp-load.php',
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('Error: No se pudo cargar WordPress. Por favor, edita la ruta en reset-stats.php línea 16.');
}

// Verificar que el usuario es administrador
if (!current_user_can('manage_options')) {
    wp_die('No tienes permisos para acceder a esta página.');
}

// Procesar acciones
$message = '';
$error = '';

if (isset($_POST['action']) && isset($_POST['confirm']) && $_POST['confirm'] === 'SI') {
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
        '_strikeouts', '_stolen_bases', '_batting_avg',
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
        
        .alert-danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .info-box h3 {
            color: #1976D2;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .info-box ul {
            margin-left: 20px;
            color: #333;
        }
        
        .info-box li {
            margin-bottom: 8px;
        }
        
        .action-card {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .action-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
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
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-content h2 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        
        .modal-content p {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .confirm-input {
            width: 100%;
            padding: 10px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .confirm-input:focus {
            outline: none;
            border-color: #667eea;
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
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
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
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-warning">
                <strong>⚠️ ADVERTENCIA:</strong> Estas acciones son permanentes y no se pueden deshacer. Asegúrate de saber lo que estás haciendo.
            </div>
            
            <div class="info-box">
                <h3>📊 ¿Dónde se almacenan las estadísticas?</h3>
                <ul>
                    <li><strong>Tabla wp_baseball_game_stats:</strong> Estadísticas por jugador por partido</li>
                    <li><strong>Post Meta de Jugadores:</strong> Estadísticas acumuladas (totales)</li>
                    <li><strong>Post Meta de Partidos:</strong> Datos de pitcheo y detalles del juego</li>
                </ul>
            </div>
            
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'baseball_game_stats';
            $stats_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            $players_count = wp_count_posts('player')->publish;
            $games_count = wp_count_posts('game')->publish;
            ?>
            
            <div class="stats-info">
                <p><strong>Estadísticas actuales:</strong></p>
                <p>📊 Registros en tabla de estadísticas: <strong><?php echo $stats_count; ?></strong></p>
                <p>👤 Jugadores: <strong><?php echo $players_count; ?></strong></p>
                <p>⚾ Partidos publicados: <strong><?php echo $games_count; ?></strong></p>
            </div>
            
            <!-- Acción 1: Resetear estadísticas de jugadores -->
            <div class="action-card">
                <h3>🔄 Resetear Estadísticas Acumuladas de Jugadores</h3>
                <p>Elimina todas las estadísticas acumuladas (totales) guardadas en los jugadores. Los datos en la tabla de estadísticas por juego NO se borran.</p>
                <p><strong>Útil cuando:</strong> Borraste juegos y las estadísticas de los jugadores no se actualizaron.</p>
                <button class="btn btn-warning" onclick="showModal('reset_player_stats', 'Resetear Estadísticas de Jugadores', 'Se eliminarán todas las estadísticas acumuladas de todos los jugadores.')">
                    Resetear Estadísticas de Jugadores
                </button>
            </div>
            
            <!-- Acción 2: Limpiar tabla de estadísticas -->
            <div class="action-card">
                <h3>🗑️ Limpiar Tabla de Estadísticas de Juegos</h3>
                <p>Elimina TODOS los registros de la tabla wp_baseball_game_stats. Esta es la tabla que almacena las estadísticas por jugador por partido.</p>
                <p><strong>Útil cuando:</strong> Quieres empezar de cero con las estadísticas de juegos.</p>
                <button class="btn btn-danger" onclick="showModal('reset_game_stats_table', 'Limpiar Tabla de Estadísticas', 'Se eliminarán TODOS los registros de estadísticas de juegos.')">
                    Limpiar Tabla de Estadísticas
                </button>
            </div>
            
            <!-- Acción 3: Resetear todo -->
            <div class="action-card">
                <h3>💣 Resetear TODO</h3>
                <p>Elimina tanto las estadísticas acumuladas de jugadores como todos los registros de la tabla de estadísticas de juegos.</p>
                <p><strong>Útil cuando:</strong> Quieres empezar completamente de cero.</p>
                <button class="btn btn-danger" onclick="showModal('reset_all', 'RESETEAR TODO', 'Se eliminarán TODAS las estadísticas del sistema.')">
                    ⚠️ Resetear TODO
                </button>
            </div>
            
            <!-- Acción 4: Recalcular estadísticas -->
            <div class="action-card">
                <h3>♻️ Recalcular Estadísticas</h3>
                <p>Recalcula las estadísticas acumuladas de todos los jugadores basándose en los juegos existentes. Primero resetea las estadísticas y luego las recalcula.</p>
                <p><strong>Útil cuando:</strong> Borraste juegos y quieres que las estadísticas reflejen solo los juegos actuales.</p>
                <button class="btn btn-primary" onclick="showModal('recalculate_stats', 'Recalcular Estadísticas', 'Se resetearán y recalcularán las estadísticas basándose en los juegos existentes.')">
                    Recalcular Estadísticas
                </button>
            </div>
            
            <a href="<?php echo admin_url(); ?>" class="back-link">← Volver al Dashboard</a>
        </div>
    </div>
    
    <!-- Modal de confirmación -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Confirmar Acción</h2>
            <p id="modalMessage">¿Estás seguro?</p>
            <p><strong>Esta acción no se puede deshacer.</strong></p>
            <p>Escribe <strong>SI</strong> para confirmar:</p>
            <form method="POST" id="confirmForm">
                <input type="hidden" name="action" id="modalAction" value="">
                <input type="text" name="confirm" class="confirm-input" placeholder="Escribe SI para confirmar" required>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showModal(action, title, message) {
            document.getElementById('modalAction').value = action;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('confirmModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('confirmModal').classList.remove('active');
            document.getElementById('confirmForm').reset();
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
