# 🔄 Instrucciones de Actualización - Baseball Stats Theme

## ⚾ Cambios Realizados

### Estadísticas Eliminadas:
- ❌ **SB** (Bases Robadas)

### Estadísticas Añadidas:
- ✅ **2B** (Dobles)
- ✅ **3B** (Triples)  
- ✅ **E** (Errores)

---

## 📋 Pasos para Actualizar

### 1️⃣ Actualizar Base de Datos (OBLIGATORIO)

Ejecuta el archivo SQL incluido en tu base de datos:

**Opción A - Usando phpMyAdmin:**
1. Accede a phpMyAdmin
2. Selecciona tu base de datos de WordPress
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `update-database.sql`
5. Haz click en "Continuar"

**Opción B - Usando línea de comandos:**
```bash
mysql -u tu_usuario -p tu_base_datos < update-database.sql
```

**Opción C - SQL Manual:**
```sql
ALTER TABLE wp_baseball_game_stats 
ADD COLUMN doubles int(11) DEFAULT 0 AFTER hits,
ADD COLUMN triples int(11) DEFAULT 0 AFTER doubles,
ADD COLUMN errors int(11) DEFAULT 0 AFTER strikeouts;
```

⚠️ **Importante**: Si tu prefijo de WordPress no es `wp_`, cámbialo en el SQL.

---

### 2️⃣ Configurar Segundo Servidor (OPCIONAL)

Si quieres hacer deployment automático a un segundo servidor:

1. Ve a tu repositorio en GitHub
2. Settings → Secrets and variables → Actions
3. Agrega estos 4 secrets:

| Secret | Descripción | Ejemplo |
|--------|-------------|---------|
| `FTP_SERVER_2` | Dirección del servidor FTP | `ftp.miservidor.com` |
| `FTP_USER_2` | Usuario FTP | `usuario@miservidor.com` |
| `FTP_PASSWORD_2` | Contraseña FTP | `tu_contraseña` |
| `FTP_SERVER_DIR_2` | Directorio destino | `/htdocs/wp-content/themes/baseball-stats/` |

---

### 3️⃣ Verificar Cambios

Después de actualizar, verifica:

✅ **En el Admin de WordPress:**
- Edita un partido
- Verifica que aparezcan los campos: 2B, 3B, E
- Verifica que NO aparezca el campo SB

✅ **En el Frontend:**
- Ve a la página de jugadores
- Verifica que las columnas 2B, 3B, E aparezcan
- Verifica que la columna SB NO aparezca

✅ **En la Página Principal:**
- Verifica el widget de "Líderes"
- Debe mostrar opciones para 2B, 3B, E
- NO debe mostrar opción para SB

---

## 🔧 Solución de Problemas

### Problema: Las nuevas columnas no aparecen en el admin
**Solución**: Limpia el caché de WordPress y recarga la página.

### Problema: Error al guardar estadísticas
**Solución**: Verifica que ejecutaste el SQL de actualización correctamente.

### Problema: El deployment al segundo servidor no funciona
**Solución**: 
1. Verifica que los 4 secrets estén configurados en GitHub
2. Revisa los logs en Actions → Deploy to Multiple Servers
3. Verifica las credenciales FTP del segundo servidor

### Problema: Datos antiguos con SB
**Solución**: Los datos antiguos permanecen en la base de datos pero no se muestran. Puedes:
- Dejarlos así (recomendado)
- Editarlos manualmente para agregar 2B, 3B, E

---

## 📊 Recalcular Estadísticas

Si necesitas recalcular las estadísticas acumuladas:

**Opción 1 - Por partido:**
1. Ve al admin de WordPress
2. Edita cualquier partido
3. Haz click en "Actualizar" (sin cambiar nada)
4. Las estadísticas se recalcularán automáticamente

**Opción 2 - Reset completo:**
1. Accede a: `tu-sitio.com/wp-content/themes/baseball-stats/reset-stats-simple.php`
2. Sigue las instrucciones en pantalla
3. ⚠️ Esto borrará todas las estadísticas acumuladas

---

## 📝 Archivos Modificados

### Core:
- ✏️ `functions.php` - Lógica principal actualizada
- ✏️ `.github/workflows/deploy.yml` - Deployment a 2 servidores

### Templates:
- ✏️ `front-page.php`
- ✏️ `archive-player.php`
- ✏️ `single-player.php`
- ✏️ `single-team.php`
- ✏️ `single-game.php`
- ✏️ `page-leaders.php`

### Scripts:
- ✏️ `reset-stats-simple.php`
- ✏️ `reset-stats.php`

### Nuevos Archivos:
- 📄 `CAMBIOS_ESTADISTICAS.md` - Documentación detallada
- 📄 `update-database.sql` - Script SQL de actualización
- 📄 `INSTRUCCIONES_ACTUALIZACION.md` - Este archivo

---

## ✅ Checklist de Actualización

Marca cada paso al completarlo:

- [ ] Ejecuté el SQL de actualización de base de datos
- [ ] Verifiqué que las nuevas columnas existen en la BD
- [ ] Probé crear/editar un partido con las nuevas estadísticas
- [ ] Verifiqué el frontend (jugadores, equipos, partidos)
- [ ] Verifiqué el widget de líderes en la página principal
- [ ] (Opcional) Configuré los secrets para el segundo servidor
- [ ] (Opcional) Verifiqué que el deployment funciona en ambos servidores
- [ ] Limpié el caché de WordPress

---

## 🆘 Soporte

Si tienes problemas:

1. Lee el archivo `CAMBIOS_ESTADISTICAS.md` para más detalles
2. Verifica los logs de error de WordPress
3. Revisa los logs de GitHub Actions si el deployment falla
4. Asegúrate de que tu versión de PHP es compatible (7.4+)

---

## 🎯 Próximos Pasos

Después de actualizar:

1. Ingresa algunos partidos de prueba con las nuevas estadísticas
2. Verifica que todo se calcule correctamente
3. Si todo funciona bien, puedes empezar a usar el sistema normalmente
4. Los datos antiguos con SB permanecerán ocultos pero intactos

---

**Fecha de actualización**: 2026-04-06  
**Versión**: 2.0 - Nuevas Estadísticas
