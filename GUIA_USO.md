# Guía de Uso - Sistema de Estadísticas de Baseball

## Descripción General

Este tema de WordPress proporciona un sistema completo para gestionar estadísticas de baseball con una jerarquía de:
- **Temporadas** → **Torneos** → **Equipos** → **Jugadores** → **Partidos** → **Estadísticas por Partido**

## Estructura Jerárquica

```
Temporada (Season)
  └── Torneo (Tournament)
      ├── Equipo 1 (Team)
      │   ├── Jugador 1 (Player)
      │   ├── Jugador 2
      │   └── ...
      ├── Equipo 2
      └── ...
          └── Partidos (Games)
              └── Estadísticas por Jugador
```

## Configuración Inicial

### 1. Crear Temporadas
1. Ve a **Temporadas → Añadir Nueva**
2. Completa:
   - Título (ej: "Temporada 2024")
   - Año
   - Fecha de inicio y fin
3. Publica

### 2. Crear Torneos
1. Ve a **Torneos → Añadir Nuevo**
2. Completa:
   - Título (ej: "Liga Regular 2024")
   - Selecciona la temporada
   - Fechas de inicio y fin
   - **Opcional:** Sube una imagen destacada (logo del torneo)
3. Publica

### 3. Crear Equipos
1. Ve a **Equipos → Añadir Nuevo**
2. Completa:
   - Nombre del equipo
   - Ciudad, estadio, año de fundación
   - **Importante:** En el panel lateral "Torneos del Equipo", selecciona los torneos en los que participa
   - **Opcional:** Sube una imagen destacada (logo del equipo)
3. Publica

### 4. Crear Jugadores
1. Ve a **Jugadores → Añadir Nuevo**
2. Completa:
   - Nombre del jugador
   - Número de jugador
   - Selecciona el equipo
   - Posición (usa la taxonomía "Posiciones")
   - **Opcional:** Sube una imagen destacada (foto del jugador)
3. Publica

**Nota:** Las estadísticas acumuladas del jugador se calculan automáticamente desde los partidos.

### 5. Registrar Partidos
1. Ve a **Partidos → Añadir Nuevo**
2. Completa la información del partido:
   - Título (ej: "Equipo A vs Equipo B")
   - Torneo
   - Equipo Local y Visitante
   - Fecha, hora y ubicación
   - Puntuación final
3. **Guarda como borrador** primero
4. Luego verás las tablas de estadísticas para ambos equipos
5. Ingresa las estadísticas de cada jugador:
   - AB (Turnos al Bate)
   - H (Hits)
   - HR (Home Runs)
   - RBI (Carreras Impulsadas)
   - BB (Bases por Bolas)
   - SO (Ponches)
   - SB (Bases Robadas)
6. Publica el partido

**Las estadísticas acumuladas de los jugadores se actualizan automáticamente.**

## Gestión de Fotos y Logos

### Logos de Equipos
- Tamaño recomendado: 200x200 px (PNG con fondo transparente)
- Se usa en: páginas de equipos, tablas de posiciones, partidos

### Logos de Torneos
- Tamaño recomendado: 300x300 px
- Se usa en: páginas de torneos, listados de temporadas

### Fotos de Jugadores
- Tamaño recomendado: 300x300 px
- Se usa en: tarjetas de jugadores, perfiles

## Shortcodes Disponibles

### Mostrar Jugadores
```
[baseball_players limit="10" team="123"]
```
- `limit`: Número de jugadores a mostrar (-1 para todos)
- `team`: ID del equipo (opcional)

### Tabla de Posiciones General
```
[baseball_standings limit="10"]
```

### Tabla de Posiciones de Torneo
```
[baseball_tournament_standings tournament_id="123"]
```
- `tournament_id`: ID del torneo (requerido)

### Líderes en Estadísticas
```
[baseball_leaders stat="batting_avg" limit="10"]
```
Estadísticas disponibles:
- `batting_avg`: Promedio de bateo
- `home_runs`: Home runs
- `rbis`: Carreras impulsadas
- `hits`: Hits
- `stolen_bases`: Bases robadas

### Partidos Recientes
```
[baseball_recent_games limit="5" tournament_id="123"]
```
- `limit`: Número de partidos
- `tournament_id`: Filtrar por torneo (opcional)

### Lista de Temporadas
```
[baseball_seasons limit="-1"]
```

## Páginas Públicas

### Página de Temporada
URL: `/temporadas/nombre-temporada/`
- Muestra información de la temporada
- Lista todos los torneos de la temporada
- Cada torneo es clickeable

### Página de Torneo
URL: `/torneos/nombre-torneo/`
- Información del torneo
- Tabla de posiciones automática
- Lista de partidos del torneo
- Equipos participantes

### Página de Equipo
URL: `/equipos/nombre-equipo/`
- Información del equipo
- Roster completo con fotos
- Estadísticas por torneo
- Partidos recientes con resultados

### Página de Jugador
URL: `/jugadores/nombre-jugador/`
- Foto y datos del jugador
- Estadísticas acumuladas de bateo
- Estadísticas de pitcheo (si aplica)
- Tabla de estadísticas por partido

### Página de Partido
URL: `/partidos/nombre-partido/`
- Marcador visual
- Información del partido
- Estadísticas completas de ambos equipos
- Estadísticas individuales de cada jugador

## Cálculo Automático de Estadísticas

### Estadísticas de Jugadores
Cuando se guarda un partido, el sistema automáticamente:
1. Suma todas las estadísticas del jugador de todos los partidos
2. Calcula el promedio de bateo (Hits / Turnos al Bate)
3. Actualiza los meta campos del jugador

### Estadísticas de Equipos
Las estadísticas de equipos se calculan dinámicamente:
- Victorias y derrotas
- Porcentaje de victorias
- Carreras a favor y en contra
- Se pueden filtrar por torneo

## Flujo de Trabajo Recomendado

1. **Inicio de Temporada:**
   - Crear la temporada
   - Crear los torneos de la temporada
   - Asignar equipos a los torneos

2. **Durante la Temporada:**
   - Registrar partidos después de cada juego
   - Ingresar estadísticas de jugadores
   - Las tablas de posiciones se actualizan automáticamente

3. **Gestión de Jugadores:**
   - Agregar nuevos jugadores cuando sea necesario
   - Asignarlos a sus equipos
   - Las estadísticas se acumulan automáticamente

## Base de Datos

El sistema crea una tabla personalizada:
- `wp_baseball_game_stats`: Almacena estadísticas por jugador por partido

Esta tabla se crea automáticamente al activar el tema.

## Tipos de Contenido Personalizados

1. **Temporadas (season)**: Agrupa torneos
2. **Torneos (tournament)**: Agrupa equipos y partidos
3. **Equipos (team)**: Agrupa jugadores
4. **Jugadores (player)**: Individuos con estadísticas
5. **Partidos (game)**: Eventos con estadísticas detalladas

## Taxonomías

- **Posiciones (position)**: Para clasificar jugadores (Pitcher, Catcher, etc.)

## Soporte de Imágenes

Todos los custom post types soportan imágenes destacadas:
- **Equipos**: Logo del equipo
- **Jugadores**: Foto del jugador
- **Torneos**: Logo del torneo
- **Temporadas**: Imagen representativa (opcional)

## Consejos y Mejores Prácticas

1. **Siempre guarda los partidos como borrador primero** para poder ingresar las estadísticas
2. **Usa nombres descriptivos** para los partidos (ej: "Águilas vs Leones - Final")
3. **Mantén actualizadas las relaciones** equipo-torneo
4. **Sube logos en formato PNG** con fondo transparente para mejor visualización
5. **Verifica las estadísticas** antes de publicar un partido
6. **Las estadísticas acumuladas son automáticas**, no las edites manualmente en el jugador

## Solución de Problemas

### Las estadísticas no se actualizan
- Verifica que el partido esté publicado (no borrador)
- Asegúrate de que los jugadores estén asignados al equipo correcto
- Re-guarda el partido para forzar el recálculo

### Los equipos no aparecen en el torneo
- Verifica que hayas seleccionado el torneo en la página de edición del equipo
- Guarda los cambios en el equipo

### Las tablas de posiciones están vacías
- Asegúrate de que haya partidos publicados en el torneo
- Verifica que los equipos estén asignados al torneo
- Verifica que los partidos tengan puntuaciones

## Personalización

### Modificar Colores
Edita las variables CSS en `style.css`:
```css
:root {
    --primary-color: #041E42;
    --accent-color: #D50032;
    /* ... más colores */
}
```

### Agregar Campos Personalizados
Modifica `functions.php` y agrega campos en las funciones de meta boxes correspondientes.

## Soporte

Para más información sobre WordPress y temas personalizados, visita:
- [WordPress Codex](https://codex.wordpress.org/)
- [WordPress Developer Resources](https://developer.wordpress.org/)
