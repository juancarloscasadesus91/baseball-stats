# Shortcodes - Sistema de Estadísticas de Baseball

## Lista Completa de Shortcodes

### 1. Mostrar Jugadores
**Shortcode:** `[baseball_players]`

**Atributos:**
- `id` - ID específico de un jugador (opcional)
- `team` - ID del equipo para filtrar (opcional)
- `limit` - Número de jugadores a mostrar (default: -1 = todos)

**Ejemplos:**
```
[baseball_players limit="10"]
[baseball_players team="5" limit="15"]
[baseball_players id="42"]
```

**Muestra:**
- Grid de tarjetas de jugadores
- Foto del jugador
- Número y posición
- Estadísticas principales (AVG, HR, RBI)
- Enlace al perfil completo

---

### 2. Tabla de Posiciones General
**Shortcode:** `[baseball_standings]`

**Atributos:**
- `limit` - Número de equipos a mostrar (default: -1 = todos)

**Ejemplos:**
```
[baseball_standings]
[baseball_standings limit="8"]
```

**Muestra:**
- Tabla con equipos
- Ciudad
- Victorias y derrotas
- Porcentaje de victorias

---

### 3. Tabla de Posiciones de Torneo
**Shortcode:** `[baseball_tournament_standings]`

**Atributos:**
- `tournament_id` - ID del torneo (REQUERIDO)

**Ejemplos:**
```
[baseball_tournament_standings tournament_id="5"]
```

**Muestra:**
- Posición
- Equipo
- Partidos jugados (PJ)
- Ganados (G)
- Perdidos (P)
- Porcentaje (%)
- Carreras a favor (CF)
- Carreras en contra (CC)

**Nota:** Los equipos se ordenan automáticamente por victorias y carreras a favor.

---

### 4. Líderes en Estadísticas
**Shortcode:** `[baseball_leaders]`

**Atributos:**
- `stat` - Tipo de estadística (default: batting_avg)
- `limit` - Número de jugadores (default: 10)

**Estadísticas disponibles:**
- `batting_avg` - Promedio de bateo
- `home_runs` - Home runs
- `rbis` - Carreras impulsadas
- `hits` - Hits
- `stolen_bases` - Bases robadas

**Ejemplos:**
```
[baseball_leaders stat="batting_avg" limit="10"]
[baseball_leaders stat="home_runs" limit="5"]
[baseball_leaders stat="rbis" limit="15"]
```

**Muestra:**
- Ranking numerado
- Nombre del jugador
- Equipo
- Valor de la estadística

---

### 5. Partidos Recientes
**Shortcode:** `[baseball_recent_games]`

**Atributos:**
- `limit` - Número de partidos (default: 5)
- `tournament_id` - Filtrar por torneo (opcional)

**Ejemplos:**
```
[baseball_recent_games limit="5"]
[baseball_recent_games limit="10" tournament_id="3"]
```

**Muestra:**
- Fecha del partido
- Equipos enfrentados
- Marcador
- Enlace a detalles del partido

---

### 6. Lista de Temporadas
**Shortcode:** `[baseball_seasons]`

**Atributos:**
- `limit` - Número de temporadas (default: -1 = todas)

**Ejemplos:**
```
[baseball_seasons]
[baseball_seasons limit="3"]
```

**Muestra:**
- Grid de tarjetas de temporadas
- Nombre de la temporada
- Año
- Fechas de inicio y fin
- Enlace a la temporada

---

## Uso en Páginas y Entradas

### Cómo Insertar Shortcodes

1. **En el Editor de Bloques (Gutenberg):**
   - Agrega un bloque "Shortcode"
   - Pega el shortcode
   - Publica/Actualiza

2. **En el Editor Clásico:**
   - Pega el shortcode directamente en el contenido
   - Publica/Actualiza

3. **En Widgets:**
   - Usa el widget "Texto" o "HTML personalizado"
   - Pega el shortcode
   - Guarda

4. **En Archivos de Tema (PHP):**
```php
<?php echo do_shortcode('[baseball_players limit="5"]'); ?>
```

---

## Ejemplos de Páginas Completas

### Página de Líderes
```
<h2>Líderes de Bateo</h2>
[baseball_leaders stat="batting_avg" limit="10"]

<h2>Líderes en Home Runs</h2>
[baseball_leaders stat="home_runs" limit="10"]

<h2>Líderes en Carreras Impulsadas</h2>
[baseball_leaders stat="rbis" limit="10"]
```

### Página de Torneo
```
<h2>Tabla de Posiciones</h2>
[baseball_tournament_standings tournament_id="5"]

<h2>Partidos Recientes</h2>
[baseball_recent_games tournament_id="5" limit="10"]

<h2>Mejores Bateadores del Torneo</h2>
[baseball_leaders stat="batting_avg" limit="5"]
```

### Página de Equipo (Complemento)
```
<h2>Roster del Equipo</h2>
[baseball_players team="3"]

<h2>Partidos Recientes</h2>
[baseball_recent_games limit="5"]
```

### Página Principal
```
<h2>Temporadas</h2>
[baseball_seasons limit="3"]

<h2>Partidos de Hoy</h2>
[baseball_recent_games limit="5"]

<h2>Líderes de Bateo</h2>
[baseball_leaders stat="batting_avg" limit="5"]

<h2>Tabla de Posiciones</h2>
[baseball_standings limit="10"]
```

---

## Combinación con HTML/CSS

Puedes envolver los shortcodes en HTML para personalizar la presentación:

```html
<div class="stats-section">
    <div class="container">
        <h2 class="section-title">Líderes de la Liga</h2>
        [baseball_leaders stat="batting_avg" limit="10"]
    </div>
</div>

<div class="standings-section" style="background: #f4f4f4; padding: 40px 0;">
    <div class="container">
        <h2>Tabla de Posiciones</h2>
        [baseball_standings]
    </div>
</div>
```

---

## Obtener IDs

### Para obtener el ID de un Torneo:
1. Ve a **Torneos** en el admin
2. Pasa el mouse sobre el torneo
3. Mira la URL en la parte inferior del navegador
4. El número después de `post=` es el ID

### Para obtener el ID de un Equipo:
1. Ve a **Equipos** en el admin
2. Pasa el mouse sobre el equipo
3. El número después de `post=` es el ID

### Alternativa - Ver en la URL al editar:
- Al editar un torneo/equipo, la URL será: `post.php?post=123&action=edit`
- El `123` es el ID

---

## Notas Importantes

1. **Los shortcodes son sensibles a mayúsculas/minúsculas** - usa exactamente como se muestra
2. **Los atributos deben estar entre comillas** - `limit="10"` no `limit=10`
3. **No agregues espacios extras** dentro del shortcode
4. **Los IDs deben ser números** - no uses nombres
5. **Algunos shortcodes requieren datos** - asegúrate de tener contenido creado

---

## Shortcodes en Código PHP

Si eres desarrollador y quieres usar shortcodes en tus archivos de tema:

```php
// Básico
<?php echo do_shortcode('[baseball_players limit="5"]'); ?>

// Con variable
<?php 
$tournament_id = 5;
echo do_shortcode('[baseball_tournament_standings tournament_id="' . $tournament_id . '"]'); 
?>

// Condicional
<?php if (is_front_page()): ?>
    <?php echo do_shortcode('[baseball_recent_games limit="3"]'); ?>
<?php endif; ?>
```

---

## Solución de Problemas

### El shortcode se muestra como texto
- Verifica que no haya espacios al inicio/final
- Asegúrate de usar corchetes `[]` no paréntesis
- Verifica la ortografía del shortcode

### No se muestra nada
- Verifica que exista contenido (jugadores, equipos, etc.)
- Revisa que los IDs sean correctos
- Comprueba que el contenido esté publicado (no borrador)

### Formato incorrecto
- Verifica que los atributos estén entre comillas
- No uses comillas tipográficas ("") usa comillas rectas ("")
- Respeta mayúsculas/minúsculas en los nombres de atributos

---

## Personalización Avanzada

Los shortcodes usan las clases CSS del tema. Puedes personalizar con CSS adicional:

```css
/* Personalizar grid de jugadores */
.player-grid {
    grid-template-columns: repeat(4, 1fr);
}

/* Personalizar tabla de posiciones */
.stats-table {
    font-size: 14px;
}

/* Personalizar tarjetas de partidos */
.game-card {
    border-left-width: 6px;
}
```

Agrega este CSS en: **Apariencia → Personalizar → CSS Adicional**
