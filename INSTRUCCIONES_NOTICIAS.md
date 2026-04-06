# Configurar Página de Noticias

## Problema
El botón "Ver más noticias" muestra "No se encontraron resultados" porque WordPress no tiene configurada una página para mostrar todas las entradas del blog.

## Solución - Opción 1: Desde el Panel de WordPress (RECOMENDADO)

1. **Accede al panel de administración de WordPress:**
   - Ve a: http://localhost/stats/wordpress/wp-admin

2. **Crea una nueva página para las noticias:**
   - Ve a **Páginas → Añadir nueva**
   - Título: `Noticias`
   - No necesitas agregar contenido (WordPress lo llenará automáticamente)
   - Haz clic en **Publicar**

3. **Configura WordPress para usar esta página:**
   - Ve a **Ajustes → Lectura**
   - En "Tu página principal muestra":
     - Selecciona **"Una página estática"**
     - **Página de inicio:** Selecciona la página que quieres como inicio (si no tienes una, déjala en blanco)
     - **Página de entradas:** Selecciona **"Noticias"**
   - Haz clic en **Guardar cambios**

4. **Verifica que funcione:**
   - Ve a la página principal de tu sitio
   - Haz clic en el botón "Ver más noticias"
   - Deberías ver todas las noticias con el sidebar de estadísticas

## Solución - Opción 2: Usando WP-CLI (Línea de comandos)

```bash
# Crear la página de noticias
wp post create --post_type=page --post_title='Noticias' --post_status=publish --post_name=noticias

# Obtener el ID de la página creada
PAGE_ID=$(wp post list --post_type=page --name=noticias --field=ID)

# Configurar WordPress para usar esta página como página de entradas
wp option update page_for_posts $PAGE_ID
wp option update show_on_front 'page'
```

## Solución - Opción 3: Usando SQL directo

```sql
-- 1. Crear la página de noticias
INSERT INTO wp_posts (post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, post_name, post_type, post_modified, post_modified_gmt)
VALUES (1, NOW(), UTC_TIMESTAMP(), '', 'Noticias', '', 'publish', 'noticias', 'page', NOW(), UTC_TIMESTAMP());

-- 2. Obtener el ID de la página recién creada
SET @page_id = LAST_INSERT_ID();

-- 3. Configurar WordPress
UPDATE wp_options SET option_value = @page_id WHERE option_name = 'page_for_posts';
UPDATE wp_options SET option_value = 'page' WHERE option_name = 'show_on_front';
```

## Verificación

Después de completar cualquiera de las opciones anteriores:

1. Ve a la página principal: http://localhost/stats/wordpress/
2. Haz clic en "Ver más noticias"
3. Deberías ver:
   - Título: "Todas las Noticias"
   - Lista de todas las noticias en formato de tarjetas
   - Sidebar derecho con:
     - Líderes de Bateo (AVG, HR, H, BB)
     - Líderes de Pitcheo (ERA, W, K, IP)
     - Tabla de Posiciones

## Archivos Creados/Modificados

- ✅ `home.php` - Template para mostrar todas las noticias (se usa cuando hay una página estática como front page)
- ✅ `archive.php` - Template alternativo para archivos de categorías
- ✅ `front-page.php` - Actualizado para enlazar correctamente a la página de noticias
- ✅ `style.css` - Estilos para la página de archivo de noticias

## Notas Importantes

- El template `home.php` se usa automáticamente cuando WordPress está configurado con una página estática como front page
- Si no configuras una "Página de entradas", WordPress usará la URL raíz para el blog
- El sidebar con estadísticas se carga dinámicamente vía AJAX
