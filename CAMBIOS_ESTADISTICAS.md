# Cambios en Estadísticas - Baseball Stats Theme

## Fecha: 2026-04-06

## Cambios Realizados

### 1. Estadísticas Modificadas

Se han realizado los siguientes cambios en las estadísticas de bateo:

#### Eliminado:
- **SB (Stolen Bases / Bases Robadas)**: Esta estadística ha sido completamente removida del sistema.

#### Añadido:
- **2B (Doubles / Dobles)**: Número de dobles bateados por el jugador
- **3B (Triples / Triples)**: Número de triples bateados por el jugador
- **E (Errors / Errores)**: Número de errores cometidos por el jugador

### 2. Archivos Modificados

Los siguientes archivos han sido actualizados para reflejar estos cambios:

#### Base de Datos:
- `functions.php` - Función `baseball_create_tables()`: Actualizada la estructura de la tabla `wp_baseball_game_stats`

#### Formularios de Administración:
- `functions.php` - Meta boxes de jugadores y partidos actualizados
- Formularios de entrada de estadísticas ahora incluyen 2B, 3B y E

#### Templates Frontend:
- `front-page.php` - Widget de líderes actualizado
- `archive-player.php` - Tabla de jugadores actualizada
- `single-player.php` - Perfil de jugador actualizado
- `single-team.php` - Roster del equipo actualizado
- `single-game.php` - Estadísticas del partido actualizadas
- `page-leaders.php` - Página de líderes actualizada

#### Scripts de Mantenimiento:
- `reset-stats-simple.php` - Actualizado para incluir nuevas estadísticas
- `reset-stats.php` - Actualizado para incluir nuevas estadísticas

### 3. Actualización de Base de Datos

**IMPORTANTE**: Después de actualizar el tema, es necesario ejecutar el siguiente SQL en tu base de datos para agregar las nuevas columnas:

```sql
ALTER TABLE wp_baseball_game_stats 
ADD COLUMN doubles int(11) DEFAULT 0 AFTER hits,
ADD COLUMN triples int(11) DEFAULT 0 AFTER doubles,
ADD COLUMN errors int(11) DEFAULT 0 AFTER strikeouts;
```

**Nota**: Si tu prefijo de WordPress no es `wp_`, reemplaza `wp_` con tu prefijo correspondiente.

### 4. Deployment a Segundo Servidor

Se ha configurado GitHub Actions para hacer deployment automático a dos servidores:

#### Servidor 1 (InfinityFree):
- Usa las credenciales existentes: `FTP_USER`, `FTP_PASSWORD`
- Servidor: `ftpupload.net`

#### Servidor 2 (Nuevo):
Necesitas configurar los siguientes secrets en GitHub:
- `FTP_SERVER_2`: Dirección del servidor FTP (ej: ftp.tuservidor.com)
- `FTP_USER_2`: Usuario FTP del segundo servidor
- `FTP_PASSWORD_2`: Contraseña FTP del segundo servidor
- `FTP_SERVER_DIR_2`: Directorio en el servidor (ej: /htdocs/wp-content/themes/baseball-stats/)

#### Cómo Configurar los Secrets en GitHub:

1. Ve a tu repositorio en GitHub
2. Click en **Settings** (Configuración)
3. En el menú lateral, click en **Secrets and variables** → **Actions**
4. Click en **New repository secret**
5. Agrega cada uno de los 4 secrets mencionados arriba

### 5. Migración de Datos Existentes

Si tienes datos existentes con la estadística SB, estos datos permanecerán en la base de datos pero no se mostrarán en el frontend. Las nuevas columnas (2B, 3B, E) comenzarán con valores en 0 para todos los registros existentes.

### 6. Recalcular Estadísticas

Después de actualizar la base de datos, puedes recalcular las estadísticas acumuladas de los jugadores:

1. Ve a cualquier partido existente en el admin de WordPress
2. Haz click en "Actualizar" sin cambiar nada
3. Esto recalculará las estadísticas del jugador basándose en los datos del partido

O usa el script de reset si necesitas empezar desde cero:
- Accede a: `tu-sitio.com/wp-content/themes/baseball-stats/reset-stats-simple.php`

### 7. Verificación

Para verificar que todo funciona correctamente:

1. ✅ Verifica que las nuevas columnas aparezcan en los formularios de admin
2. ✅ Ingresa un partido nuevo con las nuevas estadísticas
3. ✅ Verifica que las estadísticas se muestren correctamente en:
   - Perfil del jugador
   - Tabla de jugadores
   - Detalles del partido
   - Widget de líderes en la página principal
4. ✅ Verifica que el deployment automático funcione en ambos servidores

## Soporte

Si encuentras algún problema con estos cambios, verifica:

1. Que hayas ejecutado el SQL de actualización de base de datos
2. Que hayas configurado los secrets de GitHub correctamente
3. Que hayas limpiado el caché de WordPress si usas algún plugin de caché

## Notas Adicionales

- Los partidos antiguos con SB no se verán afectados, pero esa estadística ya no se mostrará
- Puedes editar partidos antiguos para agregar las nuevas estadísticas (2B, 3B, E)
- El sistema calculará automáticamente las estadísticas acumuladas cuando guardes un partido
