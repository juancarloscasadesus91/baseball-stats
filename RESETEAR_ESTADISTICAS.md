# 🔄 Guía para Resetear Estadísticas

## 📍 ¿Dónde se Almacenan las Estadísticas?

Tu sistema de baseball guarda las estadísticas en **3 ubicaciones diferentes**:

### 1. **Tabla de Base de Datos: `wp_baseball_game_stats`**
```
Ubicación: Base de datos MySQL
Tabla: wp_baseball_game_stats
```

**Qué almacena:**
- Estadísticas individuales por jugador por partido
- Campos: `game_id`, `player_id`, `team_id`, `at_bats`, `hits`, `home_runs`, `rbis`, `walks`, `strikeouts`, `stolen_bases`

**Cuándo se llena:**
- Cuando guardas/publicas un partido con estadísticas de jugadores

### 2. **Post Meta de Jugadores (Estadísticas Acumuladas)**
```
Ubicación: Tabla wp_postmeta
Meta Keys: _at_bats, _hits, _home_runs, _rbis, etc.
```

**Qué almacena:**
- Estadísticas **totales/acumuladas** de cada jugador
- Suma de todas las estadísticas de todos los juegos

**Cuándo se actualiza:**
- Automáticamente cuando guardas un partido
- La función `baseball_update_player_cumulative_stats()` suma todos los registros de la tabla `wp_baseball_game_stats`

**Campos guardados:**
- Bateo: `_at_bats`, `_hits`, `_home_runs`, `_rbis`, `_walks`, `_strikeouts`, `_stolen_bases`, `_batting_avg`
- Pitcheo: `_innings_pitched`, `_pitching_hits`, `_pitching_runs`, `_pitching_earned_runs`, `_pitching_walks`, `_pitching_strikeouts`, `_pitching_wins`, `_pitching_losses`, `_pitching_saves`, `_era`

### 3. **Post Meta de Partidos**
```
Ubicación: Tabla wp_postmeta
Meta Keys: _game_home_pitchers, _game_away_pitchers, etc.
```

**Qué almacena:**
- Datos de pitcheo por partido
- Información del juego (equipos, scores, fecha, etc.)

---

## ⚠️ El Problema: Borraste Juegos pero las Estadísticas Quedaron

### ¿Qué pasó cuando borraste los 2 juegos?

✅ **Se borró:**
- Los posts de tipo `game`
- Los post meta de esos juegos
- Los registros en `wp_baseball_game_stats` (si vaciaste la papelera)

❌ **NO se borró:**
- Las estadísticas acumuladas en los jugadores (`_at_bats`, `_hits`, etc.)
- Estas quedaron con los valores antiguos

### ¿Por qué sigues viendo estadísticas?

Las estadísticas que ves son las **acumuladas** que quedaron guardadas en cada jugador. El sistema no las actualiza automáticamente cuando borras un juego.

---

## 🛠️ Soluciones

### Opción 1: Usar el Script de Reseteo (RECOMENDADO)

He creado un script con interfaz visual para resetear las estadísticas:

**Acceso:**
```
http://tu-sitio.com/wp-content/themes/baseball-stats/reset-stats.php
```

**Opciones disponibles:**

1. **🔄 Resetear Estadísticas Acumuladas de Jugadores**
   - Elimina solo las estadísticas totales de los jugadores
   - NO toca la tabla `wp_baseball_game_stats`
   - Útil cuando borraste juegos y quieres limpiar los totales

2. **🗑️ Limpiar Tabla de Estadísticas de Juegos**
   - Elimina TODOS los registros de `wp_baseball_game_stats`
   - NO toca las estadísticas acumuladas de jugadores
   - Útil para empezar de cero con los datos de juegos

3. **💣 Resetear TODO**
   - Combina las opciones 1 y 2
   - Limpia completamente el sistema
   - Útil para empezar desde cero

4. **♻️ Recalcular Estadísticas** (LA MÁS ÚTIL PARA TI)
   - Resetea las estadísticas acumuladas
   - Recalcula basándose SOLO en los juegos que existen actualmente
   - **Esta es la que necesitas si borraste juegos**

---

### Opción 2: Comandos SQL Manuales

Si prefieres usar phpMyAdmin o línea de comandos:

#### Limpiar estadísticas acumuladas de jugadores:
```sql
-- Eliminar todas las estadísticas de bateo
DELETE FROM wp_postmeta 
WHERE meta_key IN (
    '_at_bats', '_hits', '_home_runs', '_rbis', 
    '_walks', '_strikeouts', '_stolen_bases', '_batting_avg'
);

-- Eliminar todas las estadísticas de pitcheo
DELETE FROM wp_postmeta 
WHERE meta_key IN (
    '_innings_pitched', '_pitching_hits', '_pitching_runs',
    '_pitching_earned_runs', '_pitching_walks', '_pitching_strikeouts',
    '_pitching_wins', '_pitching_losses', '_pitching_saves', '_era'
);
```

#### Limpiar tabla de estadísticas de juegos:
```sql
TRUNCATE TABLE wp_baseball_game_stats;
```

#### Ver cuántos registros tienes:
```sql
-- Contar registros en tabla de estadísticas
SELECT COUNT(*) FROM wp_baseball_game_stats;

-- Ver estadísticas de un jugador específico
SELECT * FROM wp_postmeta 
WHERE post_id = 123 
AND meta_key LIKE '_at_bats%' OR meta_key LIKE '_hits%';
```

---

### Opción 3: Código PHP en functions.php (Temporal)

Agrega esto **temporalmente** al final de `functions.php` y visita cualquier página del sitio:

```php
// TEMPORAL - Eliminar después de usar
add_action('init', function() {
    if (isset($_GET['reset_player_stats']) && current_user_can('manage_options')) {
        $players = get_posts(array('post_type' => 'player', 'posts_per_page' => -1));
        
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
        }
        
        echo '<h1>Estadísticas reseteadas!</h1>';
        exit;
    }
});
```

Luego visita: `http://tu-sitio.com/?reset_player_stats=1`

**IMPORTANTE:** Elimina este código después de usarlo.

---

## 🎯 Recomendación para tu Caso

Como borraste los 2 juegos que habías creado, te recomiendo:

### Paso 1: Usar "Recalcular Estadísticas"
1. Ve a: `http://tu-sitio.com/wp-content/themes/baseball-stats/reset-stats.php`
2. Haz clic en **"♻️ Recalcular Estadísticas"**
3. Escribe `SI` para confirmar
4. El sistema:
   - Borrará las estadísticas acumuladas de todos los jugadores
   - Recalculará basándose SOLO en los juegos que existen actualmente
   - Como no hay juegos, todos quedarán en 0

### Paso 2: Verificar
- Ve a la página de un jugador
- Verifica que las estadísticas estén en 0 o vacías

### Paso 3: Crear nuevos juegos
- Ahora puedes crear juegos nuevos
- Las estadísticas se calcularán correctamente

---

## 🔍 Cómo Prevenir este Problema en el Futuro

### Opción A: Mejorar el sistema (Recomendado)

Agregar un hook que actualice las estadísticas cuando se borra un juego:

```php
// Agregar a functions.php
add_action('before_delete_post', function($post_id) {
    if (get_post_type($post_id) === 'game') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'baseball_game_stats';
        
        // Eliminar estadísticas de este juego
        $wpdb->delete($table_name, array('game_id' => $post_id));
        
        // Recalcular estadísticas de todos los jugadores
        $players = get_posts(array('post_type' => 'player', 'posts_per_page' => -1));
        foreach ($players as $player) {
            baseball_update_player_cumulative_stats_for_player($player->ID);
        }
    }
});
```

### Opción B: Usar "Recalcular Estadísticas" periódicamente

Después de borrar juegos, siempre ejecuta la opción "Recalcular Estadísticas" del script.

---

## 📊 Entender el Flujo de Datos

```
CREAR JUEGO
    ↓
Guardar estadísticas en wp_baseball_game_stats
    ↓
Ejecutar baseball_update_player_cumulative_stats()
    ↓
Sumar todas las estadísticas de la tabla
    ↓
Guardar totales en wp_postmeta (jugador)
```

```
BORRAR JUEGO
    ↓
Se borra el post 'game'
    ↓
Se borran los post_meta del juego
    ↓
Se borran registros de wp_baseball_game_stats (si vacías papelera)
    ↓
❌ NO se actualizan las estadísticas acumuladas de jugadores
    ↓
⚠️ Los jugadores siguen mostrando estadísticas antiguas
```

**Solución:** Usar "Recalcular Estadísticas" después de borrar juegos.

---

## 🆘 Soporte

Si tienes problemas:

1. **Verifica permisos:** Debes ser administrador de WordPress
2. **Revisa errores:** Activa WP_DEBUG en wp-config.php
3. **Backup:** Siempre haz backup antes de resetear datos
4. **Prueba en desarrollo:** Si es posible, prueba primero en un ambiente de desarrollo

---

## 📝 Resumen Rápido

| Acción | Comando/URL |
|--------|-------------|
| Resetear todo con interfaz | `http://tu-sitio.com/wp-content/themes/baseball-stats/reset-stats.php` |
| Limpiar tabla SQL | `TRUNCATE TABLE wp_baseball_game_stats;` |
| Limpiar estadísticas jugadores | Ver comandos SQL arriba |
| Recalcular después de borrar juegos | Usar opción "Recalcular Estadísticas" del script |

---

**Creado:** 2026-04-04  
**Versión:** 1.0  
**Tema:** Baseball Stats WordPress Theme
