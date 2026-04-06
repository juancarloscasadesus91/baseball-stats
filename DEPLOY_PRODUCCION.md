# Guía de Despliegue a Producción

## Resumen de Cambios

Se han implementado las siguientes mejoras:

1. ✅ Añadido ranking de **BB (Bases por Bolas)** en líderes de bateo
2. ✅ Limitadas las noticias en página principal a **3 items**
3. ✅ Añadido botón **"Ver más noticias"** que lleva a página completa
4. ✅ Creada página de **todas las noticias** con sidebar de estadísticas
5. ✅ Habilitado **sistema de comentarios** en posts individuales
6. ✅ Añadido **sidebar con estadísticas** en posts individuales

---

## PASO 1: Archivos a Subir

### Archivos NUEVOS (crear en producción):
```
wp-content/themes/baseball-stats/
├── page-noticias.php          (Template para página de todas las noticias)
├── archive.php                 (Template para archivos de categorías)
└── update-noticias-produccion.sql  (Script SQL para ejecutar)
```

### Archivos MODIFICADOS (reemplazar en producción):
```
wp-content/themes/baseball-stats/
├── front-page.php             (Actualizado: 3 noticias + botón "Ver más" + BB en líderes)
├── single.php                 (Actualizado: Añadidos comentarios + sidebar)
├── style.css                  (Actualizado: Estilos para comentarios y single post)
└── functions.php              (Sin cambios - solo verificar que esté actualizado)
```

---

## PASO 2: Subir Archivos por FTP/SSH

### Opción A: FTP
1. Conecta a tu servidor FTP
2. Navega a: `/wp-content/themes/baseball-stats/`
3. Sube los archivos nuevos
4. Reemplaza los archivos modificados

### Opción B: SSH/Git
```bash
# Si usas Git
cd /ruta/a/wordpress/wp-content/themes/baseball-stats/
git pull origin main

# O copia los archivos manualmente
scp -r local/baseball-stats/* usuario@servidor:/ruta/wordpress/wp-content/themes/baseball-stats/
```

---

## PASO 3: Ejecutar Script SQL en Producción

### Opción A: phpMyAdmin
1. Accede a phpMyAdmin en tu hosting
2. Selecciona la base de datos de WordPress
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido de `update-noticias-produccion.sql`
5. Haz clic en **Continuar**

### Opción B: Línea de comandos
```bash
mysql -u USUARIO_DB -p NOMBRE_DB < update-noticias-produccion.sql
```

### Opción C: WP-CLI (si está disponible)
```bash
wp db query < update-noticias-produccion.sql
```

---

## PASO 4: Refrescar Permalinks en WordPress

**MUY IMPORTANTE:** Después de ejecutar el SQL, debes refrescar los permalinks:

1. Accede al panel de administración de WordPress
2. Ve a **Ajustes → Enlaces permanentes**
3. **NO cambies nada**, solo haz clic en **Guardar cambios**
4. Esto regenerará las reglas de reescritura

---

## PASO 5: Verificación

### URLs a verificar:

1. **Página Principal:**
   - URL: `http://tudominio.com/`
   - Debe mostrar: Banner destacado + 3 noticias + Botón "Ver más noticias"
   - Sidebar: Líderes (AVG, HR, H, 2B, 3B, **BB**, E) + Tabla de posiciones

2. **Página de Todas las Noticias:**
   - URL: `http://tudominio.com/noticias/`
   - Debe mostrar: Título "Todas las Noticias" + Grid de noticias + Sidebar
   - Sidebar: Líderes (AVG, HR, H, **BB**) + Tabla de posiciones

3. **Post Individual:**
   - URL: `http://tudominio.com/nombre-de-noticia/`
   - Debe mostrar: Contenido completo + Formulario de comentarios + Sidebar
   - Sidebar: Líderes + Tabla de posiciones

### Checklist de Verificación:

- [ ] Página principal muestra solo 3 noticias
- [ ] Botón "Ver más noticias" funciona
- [ ] Página /noticias/ muestra todas las noticias
- [ ] Sidebar con líderes aparece correctamente
- [ ] Tab de **BB** aparece en líderes de bateo
- [ ] Comentarios funcionan en posts individuales
- [ ] Paginación funciona en página de noticias
- [ ] Tabla de posiciones se muestra correctamente

---

## PASO 6: Habilitar Comentarios (si no están habilitados)

1. Ve a **Ajustes → Comentarios**
2. Marca: **"Permitir comentarios en nuevos artículos"**
3. Configura las opciones de moderación según prefieras
4. Guarda cambios

Para posts existentes:
1. Ve a **Entradas → Todas las entradas**
2. Selecciona los posts donde quieres habilitar comentarios
3. En **Acciones en lote** → **Editar** → **Aplicar**
4. En **Comentarios** selecciona **"Permitir"**
5. Haz clic en **Actualizar**

---

## Cambios en Base de Datos (Resumen)

El script SQL hace lo siguiente:

1. **Crea página "Noticias"** (si no existe)
   - Slug: `/noticias/`
   - Template: `page-noticias.php`

2. **Crea página "Inicio"** (si no existe)
   - Slug: `/inicio/`
   - Usará automáticamente `front-page.php`

3. **Configura WordPress:**
   - `page_on_front` → ID de página "Inicio"
   - `page_for_posts` → 0 (deshabilitado)
   - `show_on_front` → 'page' (usar página estática)

---

## Rollback (en caso de problemas)

Si algo sale mal, puedes revertir los cambios:

### Revertir configuración de WordPress:
```sql
UPDATE wp_options SET option_value = '0' WHERE option_name = 'page_on_front';
UPDATE wp_options SET option_value = 'posts' WHERE option_name = 'show_on_front';
```

### Eliminar páginas creadas:
```sql
DELETE FROM wp_posts WHERE post_name IN ('inicio', 'noticias') AND post_type = 'page';
DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_name IN ('inicio', 'noticias'));
```

---

## Soporte

Si encuentras algún problema:

1. Verifica que todos los archivos se hayan subido correctamente
2. Asegúrate de haber ejecutado el script SQL
3. Refresca los permalinks (Ajustes → Enlaces permanentes → Guardar)
4. Limpia la caché del navegador y del servidor (si usas caché)
5. Verifica los logs de errores de PHP

---

## Archivos Opcionales (pueden eliminarse después del deploy)

Estos archivos son solo para desarrollo/configuración y pueden eliminarse en producción:

- `setup-blog-page.php`
- `flush-permalinks.php`
- `INSTRUCCIONES_NOTICIAS.md`
- `DEPLOY_PRODUCCION.md` (este archivo)
- `update-noticias-produccion.sql` (después de ejecutarlo)
