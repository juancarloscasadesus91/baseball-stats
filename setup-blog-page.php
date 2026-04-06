<?php
/**
 * Setup Blog Page Configuration
 * Run this file once to configure WordPress to show blog posts correctly
 * 
 * Instructions:
 * 1. Access this file via browser: http://localhost/stats/wordpress/wp-content/themes/baseball-stats/setup-blog-page.php
 * 2. Or run via command line: php setup-blog-page.php
 */

// Load WordPress
require_once(__DIR__ . '/../../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this script.');
}

echo "<h1>Configuración de Página de Blog</h1>";

// Check current settings
$page_on_front = get_option('page_on_front');
$page_for_posts = get_option('page_for_posts');
$show_on_front = get_option('show_on_front');

echo "<h2>Configuración Actual:</h2>";
echo "<ul>";
echo "<li><strong>show_on_front:</strong> " . $show_on_front . "</li>";
echo "<li><strong>page_on_front:</strong> " . $page_on_front . "</li>";
echo "<li><strong>page_for_posts:</strong> " . $page_for_posts . "</li>";
echo "</ul>";

// Create or find "Noticias" page
$noticias_page = get_page_by_path('noticias');

if (!$noticias_page) {
    echo "<h2>Creando página 'Noticias'...</h2>";
    
    $page_id = wp_insert_post(array(
        'post_title' => 'Noticias',
        'post_name' => 'noticias',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '<!-- Esta página mostrará todas las noticias del blog -->'
    ));
    
    if ($page_id) {
        echo "<p style='color: green;'>✓ Página 'Noticias' creada con ID: $page_id</p>";
        $noticias_page_id = $page_id;
    } else {
        echo "<p style='color: red;'>✗ Error al crear la página 'Noticias'</p>";
        exit;
    }
} else {
    $noticias_page_id = $noticias_page->ID;
    echo "<p style='color: green;'>✓ Página 'Noticias' ya existe con ID: $noticias_page_id</p>";
}

// Configure WordPress to use static front page
echo "<h2>Configurando WordPress...</h2>";

// Set the blog page
update_option('page_for_posts', $noticias_page_id);
echo "<p style='color: green;'>✓ Página de entradas configurada a 'Noticias' (ID: $noticias_page_id)</p>";

// Verify configuration
echo "<h2>Configuración Final:</h2>";
echo "<ul>";
echo "<li><strong>show_on_front:</strong> " . get_option('show_on_front') . "</li>";
echo "<li><strong>page_on_front:</strong> " . get_option('page_on_front') . "</li>";
echo "<li><strong>page_for_posts:</strong> " . get_option('page_for_posts') . "</li>";
echo "</ul>";

echo "<h2>URLs:</h2>";
echo "<ul>";
echo "<li><strong>Página Principal:</strong> <a href='" . home_url('/') . "'>" . home_url('/') . "</a></li>";
echo "<li><strong>Página de Noticias:</strong> <a href='" . get_permalink($noticias_page_id) . "'>" . get_permalink($noticias_page_id) . "</a></li>";
echo "</ul>";

echo "<h2 style='color: green;'>✓ Configuración completada!</h2>";
echo "<p>Ahora puedes acceder a todas las noticias en: <a href='" . get_permalink($noticias_page_id) . "'>" . get_permalink($noticias_page_id) . "</a></p>";
echo "<p><strong>Nota:</strong> Puedes eliminar este archivo (setup-blog-page.php) después de ejecutarlo.</p>";
?>
