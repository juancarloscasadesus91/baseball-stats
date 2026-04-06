# 🔍 Debugging: Problema al Guardar Bateadores

## ⚡ ACCIÓN INMEDIATA REQUERIDA

He agregado logging extensivo al código. Ahora necesitas:

### 1️⃣ Activar el Debug de WordPress

Edita el archivo `wp-config.php` y agrega/modifica estas líneas:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

### 2️⃣ Intentar Guardar un Partido

1. Ve al admin de WordPress
2. Edita o crea un partido
3. Agrega al menos 1 bateador con estadísticas
4. Haz click en "Actualizar" o "Publicar"

### 3️⃣ Revisar el Log

El archivo de log estará en: `/wp-content/debug.log`

Busca líneas que contengan:
- `baseball_save_game_info called`
- `Nonce verification failed`
- `Processing stats for game`
- `Player ID:`
- `Successfully saved stats`
- `ERROR saving stats`

### 4️⃣ Qué Buscar en el Log

#### ✅ Si TODO funciona bien, verás:
```
baseball_save_game_info called for post_id: 123
All checks passed, proceeding to save
Checking for stats in POST: YES
Processing stats for game 123. Total entries: 2
Deleted 0 existing stats for game 123
Processing stat entry key: 456
Player ID: 10
Valid entry - will save for player 10
Successfully saved stats for player 10. Insert ID: 789
Final results - Saved: 2, Skipped: 0
Cumulative stats updated for game 123
```

#### ❌ Si el nonce falla, verás:
```
baseball_save_game_info called for post_id: 123
Nonce verification failed
```
**Solución**: Hay un problema con el formulario

#### ❌ Si no encuentra stats, verás:
```
baseball_save_game_info called for post_id: 123
All checks passed, proceeding to save
Checking for stats in POST: NO
No stats data found in POST
```
**Solución**: El formulario no está enviando los datos correctamente

#### ❌ Si el player_id está vacío, verás:
```
Processing stat entry key: new_123456
Player ID: 0
Skipping - no player selected
```
**Solución**: No seleccionaste un jugador del dropdown

#### ❌ Si hay error de base de datos, verás:
```
ERROR saving stats for player 10 in game 123: [mensaje de error]
Data attempted: Array(...)
```
**Solución**: Problema con la base de datos

## 🎯 Casos Comunes

### Caso 1: "Nonce verification failed"
**Problema**: El formulario no tiene el campo nonce correcto
**Solución**: Verifica que el meta box se esté cargando correctamente

### Caso 2: "No stats data found in POST"
**Problema**: El formulario no está enviando el array `stats[]`
**Solución**: Verifica el HTML del formulario con "Inspeccionar elemento"

### Caso 3: "Player ID: 0"
**Problema**: No seleccionaste un jugador o el select está vacío
**Solución**: Asegúrate de seleccionar un jugador del dropdown

### Caso 4: "Table doesn't exist"
**Problema**: No ejecutaste el SQL de actualización
**Solución**: Ejecuta `update-database.sql`

### Caso 5: "Unknown column 'doubles'"
**Problema**: Las nuevas columnas no existen en la tabla
**Solución**: Ejecuta este SQL:
```sql
ALTER TABLE wp_baseball_game_stats 
ADD COLUMN doubles int(11) DEFAULT 0 AFTER hits,
ADD COLUMN triples int(11) DEFAULT 0 AFTER doubles,
ADD COLUMN errors int(11) DEFAULT 0 AFTER strikeouts;
```

## 📋 Checklist de Verificación

Antes de intentar guardar, verifica:

- [ ] Activé WP_DEBUG en wp-config.php
- [ ] Ejecuté el SQL de actualización de base de datos
- [ ] Las columnas doubles, triples, errors existen en la tabla
- [ ] Seleccioné un jugador del dropdown (no dejé "Seleccionar jugador...")
- [ ] Ingresé al menos el campo AB (puede ser 0)
- [ ] Hice click en "Actualizar" o "Publicar"

## 🔧 Comandos SQL Útiles

### Verificar que la tabla existe:
```sql
SHOW TABLES LIKE '%baseball_game_stats%';
```

### Verificar las columnas:
```sql
SHOW COLUMNS FROM wp_baseball_game_stats;
```

### Ver las estadísticas guardadas:
```sql
SELECT * FROM wp_baseball_game_stats ORDER BY id DESC LIMIT 10;
```

### Contar estadísticas por partido:
```sql
SELECT game_id, COUNT(*) as total_players 
FROM wp_baseball_game_stats 
GROUP BY game_id 
ORDER BY game_id DESC;
```

## 📞 Próximos Pasos

1. **Activa el debug** en wp-config.php
2. **Intenta guardar** un partido con bateadores
3. **Revisa el log** en /wp-content/debug.log
4. **Copia las líneas relevantes** del log
5. **Compártelas** para que pueda ayudarte mejor

## 💡 Información Adicional

El log mostrará EXACTAMENTE dónde está fallando:
- Si no llega a la función → Problema con el hook
- Si falla el nonce → Problema con el formulario
- Si no encuentra stats → Problema con el HTML
- Si player_id es 0 → No seleccionaste jugador
- Si hay error SQL → Problema con la base de datos

---

**Fecha**: 2026-04-06  
**Versión**: 2.2 - Debug extensivo agregado
