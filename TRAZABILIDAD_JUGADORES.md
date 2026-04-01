# 🔄 Sistema de Trazabilidad de Jugadores

## Cambio Importante: Liga Irregular con Cambios de Equipo

El sistema ha sido modificado para soportar **ligas irregulares** donde los jugadores pueden cambiar de equipo entre partidos y mantener la trazabilidad completa de sus estadísticas.

---

## 🎯 Cómo Funciona

### Concepto Clave
**Las estadísticas se vinculan al equipo con el que jugó en ese partido específico, NO al equipo actual del jugador.**

### Ejemplo Práctico
```
Jugador: Juan Pérez
- Partido 1 (01/03/2024): Jugó con "Águilas" → Estadísticas vinculadas a Águilas
- Partido 2 (15/03/2024): Jugó con "Leones" → Estadísticas vinculadas a Leones  
- Partido 3 (30/03/2024): Jugó con "Águilas" → Estadísticas vinculadas a Águilas

Resultado:
✅ Todas las estadísticas se mantienen
✅ Se puede ver con qué equipo jugó cada partido
✅ Las estadísticas acumuladas son correctas
✅ El historial completo está preservado
```

---

## 📊 Base de Datos Actualizada

### Tabla: `wp_baseball_game_stats`

**Nuevo campo agregado:**
- `team_id` - ID del equipo con el que jugó en ese partido

**Estructura completa:**
```sql
CREATE TABLE wp_baseball_game_stats (
    id bigint(20) AUTO_INCREMENT PRIMARY KEY,
    game_id bigint(20) NOT NULL,
    player_id bigint(20) NOT NULL,
    team_id bigint(20) NOT NULL,  ← NUEVO
    at_bats int(11) DEFAULT 0,
    hits int(11) DEFAULT 0,
    home_runs int(11) DEFAULT 0,
    rbis int(11) DEFAULT 0,
    walks int(11) DEFAULT 0,
    strikeouts int(11) DEFAULT 0,
    stolen_bases int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    KEY game_id (game_id),
    KEY player_id (player_id),
    KEY team_id (team_id)  ← NUEVO ÍNDICE
);
```

---

## 🔧 Cómo Usar el Sistema

### 1. Crear Jugadores

Los jugadores ya **NO necesitan estar asignados a un equipo fijo**. El campo "Equipo" en el jugador es opcional y solo sirve como referencia del equipo actual.

```
Crear Jugador: "Carlos Rodríguez"
├── Número: 10
├── Equipo: [Opcional] - Puede dejarse vacío o poner el equipo actual
├── Posición: Catcher
└── Publicar
```

### 2. Registrar un Partido

Al crear un partido, el sistema automáticamente:
1. Muestra los jugadores del equipo local
2. Muestra los jugadores del equipo visitante
3. **Guarda automáticamente con qué equipo jugó cada jugador**

```
Partido: "Águilas vs Leones"
├── Equipo Local: Águilas
├── Equipo Visitante: Leones
├── Fecha: 15/03/2024
└── Estadísticas:
    ├── Jugadores de Águilas → team_id = ID de Águilas
    └── Jugadores de Leones → team_id = ID de Leones
```

### 3. Jugador Cambia de Equipo

**Opción A: Cambiar el equipo del jugador**
```
1. Editar jugador
2. Cambiar el campo "Equipo" al nuevo equipo
3. Guardar
```

**Opción B: Dejar el equipo vacío**
```
1. Editar jugador
2. Dejar el campo "Equipo" vacío
3. El jugador aparecerá en todos los equipos para selección manual
```

### 4. Jugador Juega con Diferentes Equipos

**Escenario:** Juan Pérez juega a veces con Águilas y a veces con Leones

**Solución 1: Campo equipo vacío**
```
1. Editar "Juan Pérez"
2. Dejar campo "Equipo" vacío
3. Al crear partidos, agregarlo manualmente a la lista de jugadores
```

**Solución 2: Cambiar equipo según partido**
```
Antes del Partido 1:
- Asignar Juan Pérez a "Águilas"
- Crear partido con Águilas
- Ingresar estadísticas

Antes del Partido 2:
- Cambiar Juan Pérez a "Leones"
- Crear partido con Leones
- Ingresar estadísticas

Las estadísticas anteriores se mantienen vinculadas al equipo correcto.
```

---

## 📈 Visualización de Estadísticas

### Página del Jugador

La tabla de estadísticas por partido ahora muestra:

| Fecha | **Equipo** | Partido | AB | H | AVG | HR | RBI | BB | SO | SB |
|-------|------------|---------|----|----|-----|----|----|----|----|-----|
| 01/03/2024 | **Águilas** | vs Leones | 4 | 2 | .500 | 1 | 2 | 0 | 1 | 0 |
| 15/03/2024 | **Leones** | @ Águilas | 3 | 1 | .333 | 0 | 1 | 1 | 0 | 0 |
| 30/03/2024 | **Águilas** | vs Tigres | 4 | 3 | .750 | 2 | 3 | 0 | 0 | 1 |

**Columna "Equipo"** muestra con qué equipo jugó en ese partido específico.

### Estadísticas Acumuladas

Las estadísticas acumuladas del jugador suman **todos los partidos**, independientemente del equipo:

```
Juan Pérez - Estadísticas Totales:
- AB: 11 (4 + 3 + 4)
- H: 6 (2 + 1 + 3)
- AVG: .545 (6 / 11)
- HR: 3 (1 + 0 + 2)
- RBI: 6 (2 + 1 + 3)
```

---

## 🔍 Consultas Avanzadas

### Ver Estadísticas de un Jugador con un Equipo Específico

Puedes filtrar las estadísticas por equipo en las consultas:

```php
global $wpdb;
$table_name = $wpdb->prefix . 'baseball_game_stats';

// Estadísticas de Juan Pérez solo con Águilas
$stats = $wpdb->get_results($wpdb->prepare(
    "SELECT SUM(at_bats) as ab, SUM(hits) as h, SUM(home_runs) as hr
     FROM $table_name 
     WHERE player_id = %d AND team_id = %d",
    $player_id,
    $team_id
));
```

### Ver Historial Completo de Equipos de un Jugador

```php
global $wpdb;
$table_name = $wpdb->prefix . 'baseball_game_stats';

// Equipos en los que ha jugado
$teams = $wpdb->get_results($wpdb->prepare(
    "SELECT DISTINCT t.ID, t.post_title as team_name, COUNT(*) as games
     FROM $table_name gs
     LEFT JOIN {$wpdb->posts} t ON gs.team_id = t.ID
     WHERE gs.player_id = %d
     GROUP BY t.ID
     ORDER BY games DESC",
    $player_id
));
```

---

## ✅ Ventajas del Sistema

### 1. Flexibilidad Total
- ✅ Los jugadores pueden cambiar de equipo libremente
- ✅ Un jugador puede jugar con múltiples equipos en la misma temporada
- ✅ No hay restricciones de afiliación

### 2. Trazabilidad Completa
- ✅ Se sabe exactamente con qué equipo jugó en cada partido
- ✅ El historial completo está preservado
- ✅ Las estadísticas son precisas y verificables

### 3. Estadísticas Precisas
- ✅ Las estadísticas acumuladas son correctas
- ✅ Se pueden filtrar por equipo si es necesario
- ✅ Los cálculos automáticos funcionan perfectamente

### 4. Ideal para Ligas Irregulares
- ✅ Ligas recreativas donde los equipos cambian
- ✅ Torneos con equipos mixtos
- ✅ Ligas de pickup donde los jugadores rotan
- ✅ Competiciones con equipos temporales

---

## 🔄 Migración de Datos Existentes

### Si ya tienes datos en el sistema:

**Opción 1: Actualizar la base de datos manualmente**
```sql
-- Agregar la columna team_id a registros existentes
-- basándose en el equipo del jugador en ese momento

UPDATE wp_baseball_game_stats gs
INNER JOIN wp_postmeta pm ON gs.player_id = pm.post_id AND pm.meta_key = '_player_team'
SET gs.team_id = pm.meta_value
WHERE gs.team_id = 0 OR gs.team_id IS NULL;
```

**Opción 2: Re-ingresar estadísticas**
```
1. Exportar datos existentes
2. Desactivar y reactivar el tema (recrea la tabla)
3. Re-ingresar partidos con las estadísticas correctas
```

**Opción 3: Script de migración**
```php
// Ejecutar una sola vez
function migrate_game_stats_team_id() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'baseball_game_stats';
    
    // Obtener todos los registros sin team_id
    $stats = $wpdb->get_results("SELECT * FROM $table_name WHERE team_id = 0 OR team_id IS NULL");
    
    foreach ($stats as $stat) {
        // Obtener equipos del partido
        $home_team = get_post_meta($stat->game_id, '_game_home_team', true);
        $away_team = get_post_meta($stat->game_id, '_game_away_team', true);
        
        // Obtener equipo del jugador en ese momento
        $player_team = get_post_meta($stat->player_id, '_player_team', true);
        
        // Determinar con qué equipo jugó
        $team_id = ($player_team == $home_team) ? $home_team : $away_team;
        
        // Actualizar
        $wpdb->update(
            $table_name,
            array('team_id' => $team_id),
            array('id' => $stat->id),
            array('%d'),
            array('%d')
        );
    }
}
```

---

## 📝 Casos de Uso

### Caso 1: Liga Recreativa Semanal
```
Escenario: Cada semana se forman equipos diferentes con los jugadores disponibles

Solución:
1. Crear jugadores sin equipo asignado
2. Cada semana, crear los equipos del día
3. Registrar el partido con los jugadores que participaron
4. El sistema guarda automáticamente con qué equipo jugó cada uno
```

### Caso 2: Jugador Transferido
```
Escenario: Juan Pérez jugó 10 partidos con Águilas, luego fue transferido a Leones

Solución:
1. Los 10 partidos anteriores mantienen team_id = Águilas
2. Cambiar el equipo del jugador a Leones
3. Los nuevos partidos tendrán team_id = Leones
4. En la página del jugador se ve el historial completo con ambos equipos
```

### Caso 3: Jugador Invitado
```
Escenario: Carlos es jugador de Leones pero juega un partido como invitado de Águilas

Solución:
1. Antes del partido, cambiar temporalmente a Carlos a Águilas
2. Registrar el partido
3. Después del partido, volver a cambiar a Carlos a Leones
4. Las estadísticas quedan correctamente vinculadas a Águilas para ese partido
```

---

## 🎯 Mejores Prácticas

### 1. Mantén el Campo "Equipo" Actualizado
Aunque no es obligatorio, ayuda a organizar los jugadores:
```
✅ Bueno: Actualizar el equipo del jugador al equipo actual
✅ Aceptable: Dejar vacío si el jugador rota mucho
❌ Evitar: Nunca actualizar el campo
```

### 2. Verifica Antes de Publicar
Antes de publicar un partido:
```
✅ Verifica que los jugadores estén en el equipo correcto
✅ Revisa las estadísticas ingresadas
✅ Confirma que el team_id se guardará correctamente
```

### 3. Documenta los Cambios
Para ligas organizadas:
```
✅ Lleva un registro de transferencias
✅ Documenta jugadores invitados
✅ Anota cambios temporales de equipo
```

---

## 🔧 Solución de Problemas

### Problema: Las estadísticas no muestran el equipo correcto

**Solución:**
1. Verifica que la tabla tenga la columna `team_id`
2. Desactiva y reactiva el tema para recrear la tabla
3. Re-ingresa las estadísticas del partido

### Problema: Un jugador no aparece en la lista del equipo

**Solución:**
1. Asigna temporalmente el jugador a ese equipo
2. Registra el partido
3. Cambia el jugador de vuelta si es necesario

### Problema: Necesito ver estadísticas solo con un equipo

**Solución:**
Usa una consulta personalizada (ver sección "Consultas Avanzadas" arriba)

---

## 📊 Resumen

✅ **Sistema actualizado** para soportar cambios de equipo
✅ **Trazabilidad completa** - se sabe con qué equipo jugó en cada partido
✅ **Estadísticas precisas** - los cálculos automáticos funcionan correctamente
✅ **Flexibilidad total** - ideal para ligas irregulares
✅ **Historial preservado** - nada se pierde al cambiar de equipo

**El sistema ahora soporta completamente ligas irregulares con cambios de equipo manteniendo la trazabilidad completa de las estadísticas.** ⚾🔄
