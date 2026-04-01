# Instrucciones de Uso - Baseball Stats Theme

## 🏠 Configuración de la Página Principal

### Crear Artículos Destacados

Para que un artículo aparezca en el banner principal:

1. Ve a **Entradas → Añadir nueva**
2. Crea tu artículo con título, contenido e imagen destacada
3. En el editor, añade un **Campo Personalizado**:
   - Nombre: `_featured_post`
   - Valor: `1`
4. Publica el artículo

**Nota**: Solo el artículo más reciente marcado como destacado aparecerá en el banner.

### Estructura de la Página Principal

La página principal tiene dos columnas:

#### Columna Principal (Izquierda)
- **Banner Destacado**: Artículo principal con imagen grande
- **Últimas Noticias**: 5 artículos recientes en formato lista

#### Sidebar (Derecha - Sticky)
- **Líderes en Bateo**: Top 10 jugadores por promedio (AVG)
- **Líderes en Fildeo**: Top 10 jugadores por bases robadas (SB)
- **Líderes en Pitcheo**: Top 10 jugadores por efectividad (ERA)
- **Tabla de Posiciones**: Todos los equipos ordenados por porcentaje de victorias

## 📊 Gestión de Estadísticas

### Añadir un Jugador con Estadísticas

1. Ve a **Jugadores → Añadir Nuevo**
2. Completa la información básica:
   - Nombre del jugador
   - Biografía (opcional)
   - Foto (imagen destacada)
3. En el metabox "Estadísticas del Jugador":
   - **Número de Jugador**: Ej: 23
   - **Equipo**: Selecciona de la lista
   - **Posición**: Asigna desde la taxonomía
4. Completa las estadísticas de bateo:
   - Turnos al Bate (AB)
   - Hits (H)
   - Promedio de Bateo (AVG) - se calcula automáticamente
   - Home Runs (HR)
   - Carreras Impulsadas (RBI)
   - Bases Robadas (SB)
5. Si es pitcher, completa estadísticas de pitcheo:
   - Victorias (W)
   - Derrotas (L)
   - Efectividad (ERA)
   - Ponches (K)

### Añadir un Equipo

1. Ve a **Equipos → Añadir Nuevo**
2. Completa:
   - Nombre del equipo
   - Logo (imagen destacada)
   - Ciudad
   - Estadio
   - Año de fundación
   - Victorias
   - Derrotas
3. El porcentaje de victorias se calcula automáticamente

## 🎨 Personalización de Colores

Para cambiar los colores del tema, edita el archivo `style.css` en las líneas 21-41:

```css
:root {
    --mlb-navy: #002D72;      /* Color principal */
    --mlb-red: #D50032;       /* Color de acento */
    --mlb-blue: #041E42;      /* Color secundario */
    --mlb-light-blue: #27AAE1; /* Highlights */
}
```

## 📱 Responsive Design

El tema es completamente responsive:

- **Desktop (>1024px)**: Layout de 2 columnas con sidebar sticky
- **Tablet (768px-1024px)**: Layout de 1 columna, sidebar en grid 2x2
- **Mobile (<768px)**: Layout de 1 columna, todo apilado verticalmente

## 🔧 Funciones Avanzadas

### Cálculo Automático de Promedio de Bateo

El JavaScript del tema calcula automáticamente el AVG cuando cambias:
- Hits (H)
- Turnos al Bate (AB)

Fórmula: AVG = H / AB

### Ordenamiento de Tablas

Las tablas de estadísticas son ordenables haciendo clic en los encabezados de columna.

### Sidebar Sticky

El sidebar permanece visible mientras haces scroll en la página principal, mejorando la experiencia de usuario.

## 📝 Crear Contenido de Ejemplo

### Posts de Noticias

Crea al menos 6 posts para ver el tema en acción:

1. **Post Destacado**: Con imagen grande para el banner
2. **5 Posts Recientes**: Con imágenes medianas para la lista

### Jugadores

Crea al menos 10 jugadores con estadísticas variadas para ver las tablas de líderes.

### Equipos

Crea al menos 4-6 equipos con récords diferentes para la tabla de posiciones.

## 🎯 Tips para Mejores Resultados

1. **Imágenes**: Usa imágenes de alta calidad (mínimo 1200x800px para el banner)
2. **Estadísticas Reales**: Ingresa estadísticas realistas para mejor visualización
3. **Nombres Cortos**: Los nombres de equipos cortos se ven mejor en el sidebar
4. **Actualiza Regular**: Mantén las estadísticas actualizadas para engagement

## 🚀 Optimización

- Las imágenes se redimensionan automáticamente
- El sidebar usa `position: sticky` para mejor rendimiento
- Las tablas usan hover effects suaves
- Transiciones CSS optimizadas para 60fps

## 📞 Soporte

Para problemas o preguntas, revisa:
- `README.md` - Documentación general
- `style.css` - Todos los estilos y variables
- `functions.php` - Funcionalidad del tema
