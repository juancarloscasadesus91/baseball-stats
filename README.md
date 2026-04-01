# Baseball Stats Theme

Un tema completo de WordPress para gestionar estadísticas de baseball con una jerarquía completa de Temporadas → Torneos → Equipos → Jugadores → Partidos con estadísticas por partido.

## 🎯 Características Principales

### Sistema Jerárquico Completo
- ✅ **Temporadas**: Organiza múltiples torneos por año
- ✅ **Torneos**: Agrupa equipos y partidos
- ✅ **Equipos**: Gestiona equipos con logos y roster
- ✅ **Jugadores**: Perfiles completos con fotos y estadísticas
- ✅ **Partidos**: Registro de partidos con estadísticas detalladas

### Gestión de Estadísticas
- ✅ **Estadísticas por Partido**: Ingresa estadísticas individuales de cada jugador por partido
- ✅ **Cálculo Automático**: Las estadísticas acumuladas se calculan automáticamente
- ✅ **Estadísticas de Bateo**: AB, H, HR, RBI, BB, SO, SB, AVG
- ✅ **Estadísticas de Pitcheo**: W, L, ERA, K
- ✅ **Estadísticas de Equipos**: Victorias, derrotas, porcentaje, carreras
- ✅ **Trazabilidad de Jugadores**: Sistema de cambio de equipos con historial completo

### Visualización Profesional
- ✅ **Diseño Inspirado en MLB**: Colores y estilos profesionales
- ✅ **Tablas de Posiciones**: Generadas automáticamente por torneo
- ✅ **Marcadores Visuales**: Presentación atractiva de resultados
- ✅ **Responsive**: Optimizado para móviles y tablets
- ✅ **Soporte de Imágenes**: Logos de equipos/torneos y fotos de jugadores

### Funcionalidades Avanzadas
- ✅ **6 Shortcodes**: Para mostrar datos en cualquier página
- ✅ **Base de Datos Personalizada**: Para estadísticas por partido
- ✅ **Relaciones Complejas**: Muchos a muchos entre equipos y torneos
- ✅ **Filtrado por Torneo**: Estadísticas específicas por competición
- ✅ **Navegación Intuitiva**: Enlaces entre todas las entidades relacionadas

## 📦 Instalación

### Requisitos
- WordPress 5.0 o superior
- PHP 7.4 o superior
- MySQL 5.6 o superior

### Pasos de Instalación

1. **Activar el tema**
   - Ve a **Apariencia → Temas** en el panel de WordPress
   - Activa "Baseball Stats"

2. **Verificar la creación de la base de datos**
   - La tabla `wp_baseball_game_stats` se crea automáticamente
   - Verifica en phpMyAdmin si es necesario

3. **Configurar el logo** (Opcional)
   - Ve a **Apariencia → Personalizar → Logo y Título del Sitio**
   - Sube tu logo (recomendado: 300x100px PNG)

## 🚀 Inicio Rápido

### 1. Crear Posiciones de Jugadores
```
Jugadores → Posiciones → Añadir Nueva
```
Crea: Pitcher, Catcher, Primera Base, Segunda Base, Tercera Base, Short Stop, Left Field, Center Field, Right Field

### 2. Crear una Temporada
```
Temporadas → Añadir Nueva
```
- Título: "Temporada 2024"
- Año: 2024
- Fechas de inicio y fin

### 3. Crear un Torneo
```
Torneos → Añadir Nuevo
```
- Título: "Liga Regular 2024"
- Selecciona la temporada
- Fechas del torneo
- Sube logo (opcional)

### 4. Crear Equipos
```
Equipos → Añadir Nuevo
```
- Nombre del equipo
- Ciudad, estadio, año de fundación
- **Importante**: Selecciona los torneos en el panel lateral
- Sube logo del equipo (200x200px PNG recomendado)

### 5. Crear Jugadores
```
Jugadores → Añadir Nuevo
```
- Nombre del jugador
- Número, equipo, posición
- Sube foto (300x300px recomendado)

### 6. Registrar un Partido
```
Partidos → Añadir Nuevo
```
1. Completa información básica
2. **Guarda como borrador**
3. Ingresa estadísticas de jugadores
4. Publica

## 📊 Shortcodes Disponibles

### Jugadores
```
[baseball_players limit="10" team="5"]
```

### Tabla de Posiciones
```
[baseball_standings limit="10"]
[baseball_tournament_standings tournament_id="5"]
```

### Líderes
```
[baseball_leaders stat="batting_avg" limit="10"]
[baseball_leaders stat="home_runs" limit="5"]
```

### Partidos Recientes
```
[baseball_recent_games limit="5" tournament_id="3"]
```

### Temporadas
```
[baseball_seasons limit="-1"]
```

Ver **SHORTCODES.md** para documentación completa.

## 📁 Estructura de Archivos

```
baseball-stats/
├── functions.php           # Funcionalidad principal
├── style.css              # Estilos del tema
├── header.php             # Cabecera
├── footer.php             # Pie de página
├── index.php              # Plantilla principal
├── front-page.php         # Página de inicio
├── single-season.php      # Plantilla de temporada
├── single-tournament.php  # Plantilla de torneo
├── single-team.php        # Plantilla de equipo
├── single-player.php      # Plantilla de jugador
├── single-game.php        # Plantilla de partido
├── archive-*.php          # Plantillas de archivo
├── GUIA_USO.md           # Guía completa de uso
├── SHORTCODES.md         # Documentación de shortcodes
├── RESUMEN_SISTEMA.md    # Resumen del sistema
└── README.md             # Este archivo
```

## 🗄️ Base de Datos

### Tabla Personalizada
El tema crea automáticamente la tabla `wp_baseball_game_stats`:

```sql
CREATE TABLE wp_baseball_game_stats (
    id bigint(20) AUTO_INCREMENT PRIMARY KEY,
    game_id bigint(20) NOT NULL,
    player_id bigint(20) NOT NULL,
    at_bats int(11) DEFAULT 0,
    hits int(11) DEFAULT 0,
    home_runs int(11) DEFAULT 0,
    rbis int(11) DEFAULT 0,
    walks int(11) DEFAULT 0,
    strikeouts int(11) DEFAULT 0,
    stolen_bases int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP
);
```

## 🎨 Personalización

### Modificar Colores
Edita las variables CSS en `style.css`:

```css
:root {
    --primary-color: #041E42;
    --secondary-color: #002D72;
    --accent-color: #D50032;
    /* Personaliza más colores aquí */
}
```

### Agregar Campos Personalizados
Modifica las funciones de meta boxes en `functions.php`.

## 📖 Documentación Completa

- **GUIA_USO.md**: Guía detallada de uso del sistema
- **SHORTCODES.md**: Referencia completa de shortcodes
- **RESUMEN_SISTEMA.md**: Descripción técnica del sistema
- **TRAZABILIDAD_JUGADORES.md**: Sistema de cambio de equipos y trazabilidad
- **CONFIGURAR_LOGO.md**: Instrucciones para configurar el logo

## 🔧 Funciones Helper Principales

```php
// Obtener estadísticas de un jugador por partido
baseball_get_player_game_stats($player_id, $tournament_id);

// Obtener estadísticas de un equipo
baseball_get_team_stats($team_id, $tournament_id);

// Actualizar estadísticas acumuladas (automático)
baseball_update_player_cumulative_stats($game_id);
```

## 🌐 URLs Públicas

- Temporadas: `/temporadas/nombre-temporada/`
- Torneos: `/torneos/nombre-torneo/`
- Equipos: `/equipos/nombre-equipo/`
- Jugadores: `/jugadores/nombre-jugador/`
- Partidos: `/partidos/nombre-partido/`

## ⚙️ Configuración Recomendada

### Permalinks
Ve a **Ajustes → Enlaces Permanentes** y selecciona "Nombre de la entrada".

### Tamaños de Imagen
El tema registra automáticamente:
- `player-thumbnail`: 300x300px (crop)
- `team-logo`: 200x200px (no crop)

### Menú de Navegación
Crea un menú en **Apariencia → Menús** y asígnalo a "Primary Menu".

## 🐛 Solución de Problemas

### Las estadísticas no se actualizan
1. Verifica que el partido esté publicado
2. Re-guarda el partido
3. Verifica que los jugadores estén asignados correctamente

### Los equipos no aparecen en el torneo
1. Edita el equipo
2. Selecciona el torneo en el panel lateral "Torneos del Equipo"
3. Guarda los cambios

### La tabla de base de datos no existe
1. Desactiva y reactiva el tema
2. O ejecuta manualmente la función `baseball_create_tables()`

## 📝 Changelog

### Versión 1.1.0 (2026-04-01)
- ✅ Sistema completo de jerarquía Temporadas → Torneos → Equipos → Jugadores → Partidos
- ✅ Gestión de estadísticas por partido
- ✅ Cálculo automático de estadísticas acumuladas
- ✅ 6 shortcodes nuevos
- ✅ Plantillas para todas las entidades
- ✅ Base de datos personalizada
- ✅ Documentación completa

### Versión 1.0.0
- Versión inicial con equipos y jugadores básicos

## 🤝 Contribuir

Este es un tema personalizado. Para modificaciones:
1. Haz un backup antes de modificar
2. Usa un tema hijo para personalizaciones
3. Documenta tus cambios

## 📄 Licencia

GNU General Public License v2 or later
http://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Soporte

Para preguntas y soporte:
- Consulta la documentación en los archivos .md
- Revisa el código en `functions.php`
- Verifica la consola del navegador para errores JavaScript

## 🎉 Créditos

- Diseño inspirado en MLB (Major League Baseball)
- Desarrollado con WordPress y PHP
- Estilos con CSS3 moderno
- Compatible con Gutenberg

---

**¡Disfruta gestionando tus estadísticas de baseball!** ⚾
