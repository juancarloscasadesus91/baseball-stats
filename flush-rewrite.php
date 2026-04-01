<?php
/**
 * Flush Rewrite Rules Script
 * 
 * Ejecuta este archivo UNA VEZ visitando:
 * https://tu-sitio.com/wp-content/themes/baseball-stats/flush-rewrite.php
 * 
 * Luego ELIMINA este archivo por seguridad.
 */

// Cargar WordPress
require_once(dirname(__FILE__) . '/../../../../wp-load.php');

// Verificar que el usuario esté logueado y sea administrador
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('No tienes permisos para ejecutar este script.');
}

// Forzar el registro de los custom post types
do_action('init');

// Flush rewrite rules
flush_rewrite_rules(true);

echo '<h1>✅ Reglas de Reescritura Actualizadas</h1>';
echo '<p>Las URLs de WordPress han sido regeneradas correctamente.</p>';
echo '<p><strong>Ahora puedes:</strong></p>';
echo '<ul>';
echo '<li>Visitar <a href="' . home_url('/partidos/') . '">' . home_url('/partidos/') . '</a></li>';
echo '<li>Ver cualquier partido individual</li>';
echo '</ul>';
echo '<hr>';
echo '<p style="color: red;"><strong>IMPORTANTE: Elimina este archivo (flush-rewrite.php) por seguridad.</strong></p>';
echo '<p><a href="' . home_url() . '">← Volver al sitio</a></p>';
?>
