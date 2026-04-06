-- ============================================
-- Script SQL para Configurar Página de Noticias
-- Ejecutar en PRODUCCIÓN después de subir los archivos del tema
-- ============================================

-- PASO 1: Crear página "Noticias" (si no existe)
-- Verifica primero si ya existe con: SELECT * FROM wp_posts WHERE post_name = 'noticias' AND post_type = 'page';

INSERT INTO wp_posts (
    post_author, 
    post_date, 
    post_date_gmt, 
    post_content, 
    post_title, 
    post_excerpt, 
    post_status, 
    post_name, 
    post_type, 
    post_modified, 
    post_modified_gmt,
    to_ping,
    pinged,
    post_content_filtered
) 
SELECT 
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '',
    'Noticias',
    '',
    'publish',
    'noticias',
    'page',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    '',
    ''
WHERE NOT EXISTS (
    SELECT 1 FROM wp_posts WHERE post_name = 'noticias' AND post_type = 'page'
);

-- Obtener el ID de la página Noticias
SET @noticias_page_id = (SELECT ID FROM wp_posts WHERE post_name = 'noticias' AND post_type = 'page' LIMIT 1);

-- PASO 2: Asignar el template personalizado a la página Noticias
INSERT INTO wp_postmeta (post_id, meta_key, meta_value)
VALUES (@noticias_page_id, '_wp_page_template', 'page-noticias.php')
ON DUPLICATE KEY UPDATE meta_value = 'page-noticias.php';

-- PASO 3: Crear página "Inicio" (si no existe)
INSERT INTO wp_posts (
    post_author, 
    post_date, 
    post_date_gmt, 
    post_content, 
    post_title, 
    post_excerpt, 
    post_status, 
    post_name, 
    post_type, 
    post_modified, 
    post_modified_gmt,
    to_ping,
    pinged,
    post_content_filtered
) 
SELECT 
    1,
    NOW(),
    UTC_TIMESTAMP(),
    '',
    'Inicio',
    '',
    'publish',
    'inicio',
    'page',
    NOW(),
    UTC_TIMESTAMP(),
    '',
    '',
    ''
WHERE NOT EXISTS (
    SELECT 1 FROM wp_posts WHERE post_name = 'inicio' AND post_type = 'page'
);

-- Obtener el ID de la página Inicio
SET @inicio_page_id = (SELECT ID FROM wp_posts WHERE post_name = 'inicio' AND post_type = 'page' LIMIT 1);

-- PASO 4: Configurar WordPress para usar página estática como inicio
UPDATE wp_options SET option_value = @inicio_page_id WHERE option_name = 'page_on_front';
UPDATE wp_options SET option_value = '0' WHERE option_name = 'page_for_posts';
UPDATE wp_options SET option_value = 'page' WHERE option_name = 'show_on_front';

-- PASO 5: Verificar la configuración (OPCIONAL - para debug)
-- Descomenta estas líneas si quieres ver el resultado:
-- SELECT 'Configuración de WordPress:' as info;
-- SELECT option_name, option_value FROM wp_options WHERE option_name IN ('page_on_front', 'page_for_posts', 'show_on_front');
-- SELECT 'Páginas creadas:' as info;
-- SELECT ID, post_title, post_name, post_type FROM wp_posts WHERE post_name IN ('inicio', 'noticias') AND post_type = 'page';

-- ============================================
-- NOTAS IMPORTANTES:
-- ============================================
-- 1. Este script es seguro de ejecutar múltiples veces (usa INSERT ... WHERE NOT EXISTS)
-- 2. Después de ejecutar, ve a WordPress Admin → Ajustes → Enlaces permanentes y haz clic en "Guardar cambios"
-- 3. Verifica que las URLs funcionen:
--    - Página principal: http://tudominio.com/
--    - Todas las noticias: http://tudominio.com/noticias/
-- ============================================
