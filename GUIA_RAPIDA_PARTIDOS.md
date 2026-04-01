# 🎮 Guía Rápida - Ver y Gestionar Partidos

## 📋 Cómo Ver un Partido

### Opción 1: Desde el Panel de Administración

1. **Ve a Partidos → Todos los Partidos**
2. **Haz clic en "Ver"** en el partido que quieres visualizar
3. Se abrirá la página pública del partido

### Opción 2: Desde el Sitio Público

**URL directa:**
```
https://tu-sitio.com/partidos/nombre-del-partido/
```

**Desde otras páginas:**
- Desde la página de un torneo → Lista de partidos → Clic en partido
- Desde la página de un equipo → Partidos recientes → Clic en partido
- Desde la página de un jugador → Estadísticas por partido → Clic en partido
- Desde el archivo de partidos → `/partidos/` → Clic en partido

---

## 🎯 Qué Muestra la Página de un Partido

### 1. **Información del Partido**
```
✅ Título del partido
✅ Torneo (con enlace)
✅ Fecha y hora
✅ Ubicación/estadio
```

### 2. **Marcador Visual Grande**
```
┌─────────────────────────────────────┐
│  EQUIPO VISITANTE    VS   EQUIPO LOCAL  │
│        [Logo]              [Logo]       │
│         3                    5          │
└─────────────────────────────────────┘
```

### 3. **Estadísticas Completas**

**Equipo Visitante:**
| Jugador | AB | H | AVG | HR | RBI | BB | SO | SB |
|---------|----|----|-----|----|----|----|----|-----|
| Juan Pérez | 4 | 2 | .500 | 1 | 2 | 0 | 1 | 0 |
| Carlos R. | 3 | 1 | .333 | 0 | 1 | 1 | 0 | 0 |

**Equipo Local:**
| Jugador | AB | H | AVG | HR | RBI | BB | SO | SB |
|---------|----|----|-----|----|----|----|----|-----|
| Miguel T. | 4 | 3 | .750 | 2 | 3 | 0 | 0 | 1 |
| Luis G. | 4 | 1 | .250 | 1 | 1 | 0 | 2 | 0 |

---

## ➕ Cómo Crear un Partido

### Paso 1: Crear el Partido (Borrador)

1. **Ve a Partidos → Añadir Nuevo**

2. **Completa la información básica:**
   ```
   Título: "Águilas vs Leones - Jornada 1"
   
   Torneo: [Seleccionar torneo]
   Equipo Local: [Seleccionar equipo]
   Equipo Visitante: [Seleccionar equipo]
   
   Fecha: 15/03/2024
   Hora: 19:00
   Ubicación: Estadio Nacional
   
   Puntuación Local: 5
   Puntuación Visitante: 3
   ```

3. **IMPORTANTE: Guarda como BORRADOR**
   - Clic en "Guardar borrador"
   - NO publiques todavía

### Paso 2: Ingresar Estadísticas

Después de guardar como borrador, verás dos tablas:

**Tabla 1: Equipo Local**
```
Jugador         | AB | H | HR | RBI | BB | SO | SB
----------------|----|----|----|----|----|----|----
Juan Pérez      | 4  | 2  | 1  | 2  | 0  | 1  | 0
Carlos Rodríguez| 4  | 1  | 0  | 1  | 1  | 0  | 0
Miguel Torres   | 3  | 2  | 0  | 1  | 1  | 0  | 1
```

**Tabla 2: Equipo Visitante**
```
Jugador         | AB | H | HR | RBI | BB | SO | SB
----------------|----|----|----|----|----|----|----
Pedro Martínez  | 4  | 1  | 0  | 1  | 0  | 1  | 0
Roberto Sánchez | 3  | 2  | 1  | 2  | 1  | 0  | 0
```

**Ingresa los números en cada campo**

### Paso 3: Publicar

1. **Revisa que todo esté correcto**
2. **Clic en "Publicar"**
3. **El sistema automáticamente:**
   - Guarda las estadísticas en la base de datos
   - Vincula cada jugador con el equipo correcto
   - Actualiza las estadísticas acumuladas de cada jugador
   - Calcula los promedios

---

## 🔍 Verificar que Funciona

### 1. Ver el Partido Publicado

**Clic en "Ver Partido"** o visita:
```
https://tu-sitio.com/partidos/nombre-partido/
```

**Deberías ver:**
✅ Marcador grande con logos
✅ Información del partido
✅ Tablas de estadísticas de ambos equipos
✅ Enlaces clickeables a jugadores

### 2. Verificar Estadísticas del Jugador

1. **Clic en el nombre de un jugador** en la tabla
2. **En la página del jugador, verifica:**
   - ✅ Estadísticas acumuladas actualizadas
   - ✅ Nueva fila en "Estadísticas por Partido"
   - ✅ Columna "Equipo" muestra el equipo correcto

### 3. Verificar en el Equipo

1. **Ve a la página del equipo**
2. **Verifica:**
   - ✅ El partido aparece en "Partidos Recientes"
   - ✅ El resultado es correcto
   - ✅ Las estadísticas del equipo se actualizaron

---

## 🎨 Personalización de la Vista

### Modificar el Diseño del Marcador

Edita `style.css` en la sección `.scoreboard`:

```css
.scoreboard {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    padding: 32px;
    border-radius: 12px;
    /* Personaliza aquí */
}
```

### Cambiar Colores del Marcador

```css
.team-score .score {
    font-size: 4rem;  /* Tamaño del marcador */
    color: var(--white);  /* Color */
}
```

### Modificar Tablas de Estadísticas

```css
.stats-table {
    font-size: 14px;  /* Tamaño de texto */
    /* Personaliza aquí */
}
```

---

## 📱 Vista Responsive

La página del partido es completamente responsive:

### Desktop (1024px+)
```
┌────────────────────────────────────┐
│  Visitante  [VS]  Local            │
│    Logo            Logo            │
│     3               5              │
└────────────────────────────────────┘
```

### Tablet (768px)
```
┌──────────────┐
│  Visitante   │
│    Logo      │
│      3       │
│     VS       │
│    Local     │
│    Logo      │
│      5       │
└──────────────┘
```

### Móvil (320px+)
- Tablas con scroll horizontal
- Marcador apilado verticalmente
- Información compacta

---

## 🔧 Solución de Problemas

### Problema: No veo las estadísticas

**Causas posibles:**
1. El partido está en borrador (no publicado)
2. No se ingresaron estadísticas
3. La tabla de base de datos no existe

**Solución:**
```
1. Verifica que el partido esté publicado
2. Edita el partido y verifica que las estadísticas estén guardadas
3. Desactiva y reactiva el tema para recrear la tabla
```

### Problema: Los jugadores no aparecen en la tabla

**Causas posibles:**
1. Los jugadores no están asignados a los equipos
2. No se guardaron las estadísticas

**Solución:**
```
1. Edita el partido
2. Verifica que los jugadores aparezcan en las tablas
3. Ingresa las estadísticas nuevamente
4. Guarda/Actualiza
```

### Problema: El marcador no se ve bien

**Causas posibles:**
1. Falta CSS
2. Conflicto con otro plugin

**Solución:**
```
1. Verifica que style.css esté cargando
2. Inspecciona con DevTools (F12)
3. Desactiva otros plugins temporalmente
```

### Problema: Las estadísticas no se actualizan

**Causas posibles:**
1. El partido no se publicó
2. Error en la función de guardado

**Solución:**
```
1. Re-publica el partido
2. Verifica errores en debug.log
3. Activa WP_DEBUG para ver errores
```

---

## 📊 Ejemplo Completo

### Crear Partido "Águilas vs Leones"

```
1. Partidos → Añadir Nuevo

2. Información:
   Título: Águilas vs Leones - Final
   Torneo: Liga Regular 2024
   Equipo Local: Águilas del Norte
   Equipo Visitante: Leones del Sur
   Fecha: 15/03/2024
   Hora: 19:00
   Ubicación: Estadio Nacional
   Puntuación Local: 5
   Puntuación Visitante: 3

3. Guardar como borrador

4. Estadísticas Águilas (Local):
   Juan Pérez:    AB=4, H=2, HR=1, RBI=2, BB=0, SO=1, SB=0
   Carlos R.:     AB=4, H=1, HR=0, RBI=1, BB=1, SO=0, SB=0
   Miguel T.:     AB=3, H=2, HR=0, RBI=1, BB=1, SO=0, SB=1
   Luis G.:       AB=4, H=1, HR=1, RBI=1, BB=0, SO=2, SB=0

5. Estadísticas Leones (Visitante):
   Pedro M.:      AB=4, H=1, HR=0, RBI=1, BB=0, SO=1, SB=0
   Roberto S.:    AB=3, H=2, HR=1, RBI=2, BB=1, SO=0, SB=0
   Diego L.:      AB=4, H=0, HR=0, RBI=0, BB=0, SO=2, SB=0

6. Publicar

7. Ver partido en:
   https://tu-sitio.com/partidos/aguilas-vs-leones-final/
```

---

## 🎯 Consejos

### ✅ Mejores Prácticas

1. **Siempre guarda como borrador primero**
   - Permite ingresar estadísticas
   - Evita publicar partidos incompletos

2. **Verifica antes de publicar**
   - Revisa puntuaciones
   - Verifica estadísticas
   - Confirma equipos correctos

3. **Usa nombres descriptivos**
   - "Águilas vs Leones - Final" ✅
   - "partido 1" ❌

4. **Completa toda la información**
   - Fecha, hora, ubicación
   - Torneo
   - Puntuaciones

### ⚡ Atajos

**Ver partido rápidamente:**
```
Admin → Partidos → Hover sobre partido → "Ver"
```

**Editar estadísticas:**
```
Admin → Partidos → Editar → Scroll a tablas → Modificar → Actualizar
```

**Compartir partido:**
```
Copiar URL del partido y compartir en redes sociales
```

---

## 📚 Archivos Relacionados

- **Template:** `/single-game.php`
- **Estilos:** `/style.css` (sección `.scoreboard`, `.game-statistics`)
- **Funciones:** `/functions.php` (funciones `baseball_save_game_info`, etc.)

---

**¡La vista de partidos está lista y funcionando!** ⚾

Para ver un partido, simplemente:
1. Crea el partido
2. Ingresa estadísticas
3. Publica
4. Visita la URL del partido

Todo está configurado y listo para usar. 🎉
