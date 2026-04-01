# Ejemplos de Uso - Sistema de Estadísticas de Baseball

## 📋 Casos de Uso Prácticos

### Caso 1: Configurar una Liga Completa

#### Paso 1: Crear la Estructura Base
```
1. Crear Temporada "Liga 2024"
   - Año: 2024
   - Inicio: 01/03/2024
   - Fin: 30/11/2024

2. Crear Torneos:
   - "Temporada Regular 2024" (01/03 - 31/08)
   - "Playoffs 2024" (01/09 - 30/09)
   - "Serie Final 2024" (01/10 - 31/10)
```

#### Paso 2: Crear Equipos
```
Equipo 1: Águilas del Norte
- Ciudad: Ciudad del Norte
- Estadio: Estadio Nacional
- Fundado: 1985
- Torneos: ✓ Temporada Regular, ✓ Playoffs
- Logo: aguilas-logo.png

Equipo 2: Leones del Sur
- Ciudad: Ciudad del Sur
- Estadio: Estadio Municipal
- Fundado: 1990
- Torneos: ✓ Temporada Regular, ✓ Playoffs
- Logo: leones-logo.png
```

#### Paso 3: Crear Jugadores
```
Jugador: Juan Pérez
- Número: 23
- Equipo: Águilas del Norte
- Posición: Pitcher
- Foto: juan-perez.jpg

Jugador: Carlos Rodríguez
- Número: 10
- Equipo: Águilas del Norte
- Posición: Catcher
- Foto: carlos-rodriguez.jpg
```

---

### Caso 2: Registrar un Partido Completo

#### Información del Partido
```
Título: Águilas vs Leones - Jornada 1
Torneo: Temporada Regular 2024
Equipo Local: Águilas del Norte
Equipo Visitante: Leones del Sur
Fecha: 15/03/2024
Hora: 19:00
Ubicación: Estadio Nacional
Puntuación Local: 5
Puntuación Visitante: 3
```

#### Estadísticas de Jugadores - Águilas (Local)

| Jugador | AB | H | HR | RBI | BB | SO | SB |
|---------|----|----|----|----|----|----|-----|
| Juan Pérez | 4 | 2 | 1 | 2 | 0 | 1 | 0 |
| Carlos Rodríguez | 4 | 1 | 0 | 1 | 1 | 0 | 0 |
| Miguel Torres | 3 | 2 | 0 | 1 | 1 | 0 | 1 |
| Luis García | 4 | 1 | 1 | 1 | 0 | 2 | 0 |

#### Estadísticas de Jugadores - Leones (Visitante)

| Jugador | AB | H | HR | RBI | BB | SO | SB |
|---------|----|----|----|----|----|----|-----|
| Pedro Martínez | 4 | 1 | 0 | 1 | 0 | 1 | 0 |
| Roberto Sánchez | 3 | 2 | 1 | 2 | 1 | 0 | 0 |
| Diego López | 4 | 0 | 0 | 0 | 0 | 2 | 0 |

**Resultado:** Las estadísticas se calculan automáticamente y se suman a los totales de cada jugador.

---

### Caso 3: Crear Páginas del Sitio Web

#### Página de Inicio (Front Page)
```html
<h2>Bienvenidos a la Liga de Baseball</h2>
<p>La mejor liga de baseball de la región</p>

<h3>Partidos Recientes</h3>
[baseball_recent_games limit="5"]

<h3>Líderes de Bateo</h3>
[baseball_leaders stat="batting_avg" limit="5"]

<h3>Tabla de Posiciones</h3>
[baseball_standings limit="10"]
```

#### Página "Estadísticas"
```html
<h2>Estadísticas de la Liga</h2>

<div class="stats-section">
    <h3>Líderes en Promedio de Bateo</h3>
    [baseball_leaders stat="batting_avg" limit="10"]
</div>

<div class="stats-section">
    <h3>Líderes en Home Runs</h3>
    [baseball_leaders stat="home_runs" limit="10"]
</div>

<div class="stats-section">
    <h3>Líderes en Carreras Impulsadas</h3>
    [baseball_leaders stat="rbis" limit="10"]
</div>

<div class="stats-section">
    <h3>Líderes en Bases Robadas</h3>
    [baseball_leaders stat="stolen_bases" limit="10"]
</div>
```

#### Página "Temporadas"
```html
<h2>Temporadas de la Liga</h2>
<p>Explora las diferentes temporadas y sus torneos</p>

[baseball_seasons]
```

#### Página de Torneo Específico
```html
<h2>Temporada Regular 2024</h2>

<h3>Tabla de Posiciones</h3>
[baseball_tournament_standings tournament_id="5"]

<h3>Partidos del Torneo</h3>
[baseball_recent_games tournament_id="5" limit="20"]
```

---

### Caso 4: Widgets en Sidebar

#### Widget 1: Líderes de Bateo
```
Tipo: HTML Personalizado
Título: Líderes de Bateo
Contenido:
[baseball_leaders stat="batting_avg" limit="5"]
```

#### Widget 2: Próximos Partidos
```
Tipo: HTML Personalizado
Título: Partidos Recientes
Contenido:
[baseball_recent_games limit="3"]
```

#### Widget 3: Tabla de Posiciones
```
Tipo: HTML Personalizado
Título: Posiciones
Contenido:
[baseball_standings limit="8"]
```

---

### Caso 5: Menú de Navegación

#### Estructura del Menú Principal
```
🏠 Inicio
📊 Estadísticas (página personalizada)
👥 Equipos (archivo de equipos)
⚾ Jugadores (archivo de jugadores)
🏆 Temporadas (archivo de temporadas)
📅 Partidos (archivo de partidos)
📰 Noticias (blog)
```

#### Crear el Menú
```
1. Ve a Apariencia → Menús
2. Crea un nuevo menú "Menú Principal"
3. Agrega los enlaces:
   - Página de Inicio
   - Página de Estadísticas (personalizada)
   - Enlace personalizado a /equipos/
   - Enlace personalizado a /jugadores/
   - Enlace personalizado a /temporadas/
   - Enlace personalizado a /partidos/
   - Categoría de Noticias
4. Asigna a "Primary Menu"
```

---

### Caso 6: Flujo de Trabajo Semanal

#### Lunes - Planificación
```
1. Revisar calendario de partidos
2. Crear partidos de la semana (como borradores)
3. Verificar que equipos y jugadores estén actualizados
```

#### Durante la Semana - Partidos
```
Para cada partido:
1. Abrir el partido en borrador
2. Actualizar fecha/hora si cambió
3. Después del partido:
   - Ingresar puntuación final
   - Ingresar estadísticas de cada jugador
   - Publicar el partido
```

#### Viernes - Revisión
```
1. Verificar que todos los partidos estén publicados
2. Revisar tabla de posiciones
3. Verificar líderes de estadísticas
4. Crear artículo de resumen semanal
```

---

### Caso 7: Crear Artículo de Noticias

#### Artículo: Resumen de la Jornada
```
Título: "Águilas Vencen a Leones en Emocionante Partido"

Contenido:
Las Águilas del Norte se impusieron 5-3 sobre los Leones del Sur en un 
emocionante encuentro disputado en el Estadio Nacional.

<h3>Destacados del Partido</h3>
- Juan Pérez conectó un home run decisivo en la 7ma entrada
- Carlos Rodríguez tuvo una actuación sólida detrás del plato
- Miguel Torres robó una base importante

<h3>Estadísticas del Partido</h3>
[Enlace al partido completo]

<h3>Tabla de Posiciones Actualizada</h3>
[baseball_tournament_standings tournament_id="5"]

<h3>Próximos Partidos</h3>
[baseball_recent_games tournament_id="5" limit="3"]
```

---

### Caso 8: Página de Perfil de Equipo

Cuando un usuario visita `/equipos/aguilas-del-norte/`, verá automáticamente:

```
✅ Logo del equipo
✅ Información (ciudad, estadio, fundación)
✅ Récord de victorias/derrotas
✅ Roster completo con fotos
✅ Estadísticas por torneo
✅ Últimos 10 partidos con resultados
```

**No necesitas crear nada adicional**, la plantilla `single-team.php` lo hace automáticamente.

---

### Caso 9: Página de Perfil de Jugador

Cuando un usuario visita `/jugadores/juan-perez/`, verá automáticamente:

```
✅ Foto del jugador
✅ Número y posición
✅ Equipo actual
✅ Estadísticas acumuladas de bateo
✅ Estadísticas de pitcheo (si aplica)
✅ Tabla completa de estadísticas por partido
```

**Completamente automático** con la plantilla `single-player.php`.

---

### Caso 10: Integración con Redes Sociales

#### Compartir Resultados
```
Después de publicar un partido, comparte en redes sociales:

Twitter/X:
"⚾ ¡Final del partido! Águilas 5 - 3 Leones
Juan Pérez conectó HR decisivo 💪
Ver estadísticas completas: [enlace]
#Baseball #Liga2024"

Facebook:
"¡Emocionante victoria de las Águilas! 🦅
Revive el partido y consulta todas las estadísticas en nuestro sitio.
[enlace al partido]"
```

---

## 🎯 Tips y Mejores Prácticas

### 1. Nombres Consistentes
```
✅ Bueno: "Temporada Regular 2024"
❌ Malo: "temp reg 24"

✅ Bueno: "Águilas vs Leones - Jornada 1"
❌ Malo: "partido 1"
```

### 2. Imágenes Optimizadas
```
Logos de Equipos:
- Formato: PNG con fondo transparente
- Tamaño: 200x200px
- Peso: < 50KB

Fotos de Jugadores:
- Formato: JPG o PNG
- Tamaño: 300x300px
- Peso: < 100KB
```

### 3. Orden de Creación
```
1. Temporadas
2. Torneos
3. Equipos
4. Jugadores
5. Partidos
```

### 4. Backup Regular
```
Semanal:
- Exportar base de datos
- Backup de imágenes
- Backup de archivos del tema
```

### 5. Verificación de Datos
```
Antes de publicar un partido:
✓ Equipos correctos
✓ Fecha y hora correctas
✓ Puntuación ingresada
✓ Estadísticas de todos los jugadores
✓ Suma de estadísticas coherente
```

---

## 🚀 Casos de Uso Avanzados

### Múltiples Temporadas
```
Temporada 2023 (Finalizada)
  └── Torneos 2023
      └── Partidos históricos

Temporada 2024 (Actual)
  └── Torneos 2024
      └── Partidos actuales

Temporada 2025 (Futura)
  └── Torneos planificados
```

### Jugadores que Cambian de Equipo
```
1. Editar el jugador
2. Cambiar el equipo en el campo "Equipo"
3. Las estadísticas anteriores se mantienen
4. Las nuevas estadísticas se acumulan
```

### Torneos Simultáneos
```
Un equipo puede participar en:
- Liga Regular
- Copa Nacional
- Torneo Internacional

Selecciona múltiples torneos en la página del equipo.
```

---

## 📊 Ejemplos de Reportes

### Reporte Mensual
```
Crear una página "Reporte Marzo 2024":

<h2>Reporte Mensual - Marzo 2024</h2>

<h3>Partidos Jugados</h3>
Total: 45 partidos

<h3>Líderes del Mes</h3>
[baseball_leaders stat="batting_avg" limit="5"]

<h3>Tabla de Posiciones</h3>
[baseball_tournament_standings tournament_id="5"]
```

### Reporte de Fin de Temporada
```
<h2>Resumen Temporada 2024</h2>

<h3>Campeón</h3>
[Nombre del equipo campeón]

<h3>Líderes Finales</h3>
[baseball_leaders stat="batting_avg" limit="10"]
[baseball_leaders stat="home_runs" limit="10"]

<h3>Tabla Final</h3>
[baseball_tournament_standings tournament_id="5"]

<h3>Estadísticas Destacadas</h3>
- Total de partidos: XXX
- Total de home runs: XXX
- Promedio de bateo más alto: .XXX
```

---

**¡Estos ejemplos te ayudarán a aprovechar al máximo el sistema!** ⚾
