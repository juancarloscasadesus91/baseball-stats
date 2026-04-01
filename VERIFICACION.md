# ✅ Lista de Verificación del Sistema

## Verificación Post-Instalación

Usa esta lista para verificar que todo el sistema está funcionando correctamente.

---

## 1. ✅ Verificación de Instalación Básica

### Tema Activado
- [ ] El tema "Baseball Stats" está activado
- [ ] No hay errores en el panel de WordPress
- [ ] El sitio carga correctamente

### Base de Datos
- [ ] Accede a phpMyAdmin
- [ ] Busca la tabla `wp_baseball_game_stats`
- [ ] Verifica que la tabla existe con las columnas correctas

**Columnas esperadas:**
- id, game_id, player_id, at_bats, hits, home_runs, rbis, walks, strikeouts, stolen_bases, created_at

---

## 2. ✅ Verificación de Custom Post Types

### Panel de Administración
Verifica que aparecen en el menú lateral:
- [ ] Temporadas (icono: calendario)
- [ ] Torneos (icono: trofeo)
- [ ] Equipos (icono: grupos)
- [ ] Jugadores (icono: usuarios)
- [ ] Partidos (icono: tickets)

### Crear Contenido de Prueba

#### Crear Posición
1. [ ] Ve a Jugadores → Posiciones
2. [ ] Crea "Pitcher"
3. [ ] Verifica que se guardó correctamente

#### Crear Temporada
1. [ ] Ve a Temporadas → Añadir Nueva
2. [ ] Título: "Temporada Test 2024"
3. [ ] Año: 2024
4. [ ] Fecha inicio: 01/01/2024
5. [ ] Fecha fin: 31/12/2024
6. [ ] Publica
7. [ ] Verifica que aparece en el listado

#### Crear Torneo
1. [ ] Ve a Torneos → Añadir Nuevo
2. [ ] Título: "Torneo Test"
3. [ ] Selecciona la temporada creada
4. [ ] Ingresa fechas
5. [ ] Publica
6. [ ] Verifica que aparece en el listado

#### Crear Equipo
1. [ ] Ve a Equipos → Añadir Nuevo
2. [ ] Título: "Equipo Test A"
3. [ ] Ciudad: "Ciudad Test"
4. [ ] Estadio: "Estadio Test"
5. [ ] Año fundación: 2020
6. [ ] En el panel lateral, selecciona el torneo creado
7. [ ] Publica
8. [ ] Verifica que se guardó
9. [ ] Repite para "Equipo Test B"

#### Crear Jugador
1. [ ] Ve a Jugadores → Añadir Nuevo
2. [ ] Título: "Jugador Test 1"
3. [ ] Número: 10
4. [ ] Selecciona "Equipo Test A"
5. [ ] Selecciona posición "Pitcher"
6. [ ] Publica
7. [ ] Verifica que se guardó
8. [ ] Crea al menos 3 jugadores más para cada equipo

---

## 3. ✅ Verificación de Partidos y Estadísticas

### Crear Partido
1. [ ] Ve a Partidos → Añadir Nuevo
2. [ ] Título: "Partido Test - Equipo A vs Equipo B"
3. [ ] Selecciona el torneo
4. [ ] Equipo Local: Equipo Test A
5. [ ] Equipo Visitante: Equipo Test B
6. [ ] Fecha: Hoy
7. [ ] Hora: 19:00
8. [ ] Ubicación: "Estadio Test"
9. [ ] Puntuación Local: 5
10. [ ] Puntuación Visitante: 3
11. [ ] **Guarda como borrador**
12. [ ] Verifica que aparecen las tablas de estadísticas

### Ingresar Estadísticas
1. [ ] Verifica que aparecen los jugadores de ambos equipos
2. [ ] Ingresa estadísticas para al menos 2 jugadores de cada equipo:
   - AB: 4
   - H: 2
   - HR: 1
   - RBI: 2
   - BB: 1
   - SO: 1
   - SB: 0
3. [ ] Publica el partido
4. [ ] Verifica que no hay errores

### Verificar Cálculo Automático
1. [ ] Ve a Jugadores
2. [ ] Edita uno de los jugadores que tiene estadísticas
3. [ ] Verifica que los campos se llenaron automáticamente:
   - [ ] Turnos al Bate (AB)
   - [ ] Hits (H)
   - [ ] Home Runs (HR)
   - [ ] Carreras Impulsadas (RBI)
   - [ ] Promedio de Bateo (AVG) - debe ser calculado

**Ejemplo:** Si ingresaste AB=4, H=2, el AVG debe ser 0.500

---

## 4. ✅ Verificación de Vistas Públicas

### Página de Temporada
1. [ ] Ve a Temporadas en el admin
2. [ ] Haz clic en "Ver" en la temporada test
3. [ ] Verifica que se muestra:
   - [ ] Título de la temporada
   - [ ] Año
   - [ ] Fechas
   - [ ] Lista de torneos

### Página de Torneo
1. [ ] Desde la página de temporada, haz clic en un torneo
2. [ ] Verifica que se muestra:
   - [ ] Título del torneo
   - [ ] Temporada (con enlace)
   - [ ] Tabla de posiciones con equipos
   - [ ] Lista de partidos

### Página de Equipo
1. [ ] Ve a Equipos → Ver equipo
2. [ ] Verifica que se muestra:
   - [ ] Nombre del equipo
   - [ ] Ciudad, estadio, fundación
   - [ ] Roster de jugadores con fotos
   - [ ] Torneos en los que participa
   - [ ] Partidos recientes

### Página de Jugador
1. [ ] Ve a Jugadores → Ver jugador
2. [ ] Verifica que se muestra:
   - [ ] Nombre del jugador
   - [ ] Número y posición
   - [ ] Equipo
   - [ ] Estadísticas acumuladas
   - [ ] Tabla de estadísticas por partido

### Página de Partido
1. [ ] Ve a Partidos → Ver partido
2. [ ] Verifica que se muestra:
   - [ ] Título del partido
   - [ ] Torneo (con enlace)
   - [ ] Fecha, hora, ubicación
   - [ ] Marcador visual grande
   - [ ] Estadísticas de ambos equipos
   - [ ] Estadísticas individuales de jugadores

---

## 5. ✅ Verificación de Shortcodes

### Crear Página de Prueba
1. [ ] Ve a Páginas → Añadir Nueva
2. [ ] Título: "Prueba Shortcodes"

### Probar Cada Shortcode

#### Jugadores
```
[baseball_players limit="10"]
```
- [ ] Pega el shortcode
- [ ] Actualiza la página
- [ ] Ve a la página pública
- [ ] Verifica que muestra grid de jugadores

#### Tabla de Posiciones
```
[baseball_standings]
```
- [ ] Verifica que muestra tabla con equipos
- [ ] Verifica que muestra victorias/derrotas

#### Tabla de Posiciones por Torneo
```
[baseball_tournament_standings tournament_id="X"]
```
(Reemplaza X con el ID del torneo test)
- [ ] Verifica que muestra tabla de posiciones
- [ ] Verifica que calcula porcentajes

#### Líderes
```
[baseball_leaders stat="batting_avg" limit="5"]
```
- [ ] Verifica que muestra líderes
- [ ] Verifica que muestra estadísticas

#### Partidos Recientes
```
[baseball_recent_games limit="5"]
```
- [ ] Verifica que muestra partidos
- [ ] Verifica que muestra marcadores

#### Temporadas
```
[baseball_seasons]
```
- [ ] Verifica que muestra lista de temporadas

---

## 6. ✅ Verificación de Imágenes

### Logo del Sitio
1. [ ] Ve a Apariencia → Personalizar → Logo y Título
2. [ ] Sube un logo de prueba
3. [ ] Verifica que aparece en el header

### Logo de Equipo
1. [ ] Edita un equipo
2. [ ] Sube una imagen destacada
3. [ ] Verifica que aparece en:
   - [ ] Página del equipo
   - [ ] Tabla de posiciones
   - [ ] Página de partido

### Foto de Jugador
1. [ ] Edita un jugador
2. [ ] Sube una imagen destacada
3. [ ] Verifica que aparece en:
   - [ ] Tarjeta del jugador
   - [ ] Página del jugador
   - [ ] Roster del equipo

### Logo de Torneo
1. [ ] Edita un torneo
2. [ ] Sube una imagen destacada
3. [ ] Verifica que aparece en la página del torneo

---

## 7. ✅ Verificación de Responsive

### Dispositivos Móviles
1. [ ] Abre el sitio en un móvil o usa DevTools
2. [ ] Verifica que se ve bien en:
   - [ ] 320px (móvil pequeño)
   - [ ] 768px (tablet)
   - [ ] 1024px (desktop)

### Elementos a Verificar
- [ ] Header se adapta
- [ ] Menú funciona en móvil
- [ ] Tablas son scrolleables
- [ ] Marcador de partido se adapta
- [ ] Grid de jugadores se ajusta
- [ ] Imágenes se redimensionan

---

## 8. ✅ Verificación de Navegación

### Enlaces Internos
1. [ ] Desde página de temporada → torneo
2. [ ] Desde página de torneo → equipo
3. [ ] Desde página de equipo → jugador
4. [ ] Desde página de jugador → partido
5. [ ] Desde página de partido → equipos/jugadores

### Menú de Navegación
1. [ ] Ve a Apariencia → Menús
2. [ ] Crea un menú con enlaces a:
   - [ ] Inicio
   - [ ] Temporadas
   - [ ] Torneos
   - [ ] Equipos
   - [ ] Jugadores
   - [ ] Partidos
3. [ ] Asigna a "Primary Menu"
4. [ ] Verifica que funciona en el sitio

---

## 9. ✅ Verificación de Cálculos

### Estadísticas de Jugador
1. [ ] Crea un segundo partido con el mismo jugador
2. [ ] Ingresa estadísticas diferentes
3. [ ] Publica el partido
4. [ ] Edita el jugador
5. [ ] Verifica que las estadísticas son la suma de ambos partidos

**Ejemplo:**
- Partido 1: AB=4, H=2
- Partido 2: AB=3, H=1
- Total esperado: AB=7, H=3, AVG=0.429

### Estadísticas de Equipo
1. [ ] Ve a la página del equipo
2. [ ] Verifica que muestra:
   - [ ] Victorias correctas
   - [ ] Derrotas correctas
   - [ ] Porcentaje calculado

### Tabla de Posiciones
1. [ ] Ve a la página del torneo
2. [ ] Verifica que los equipos están ordenados por:
   - [ ] Victorias (descendente)
   - [ ] Carreras a favor (si empate en victorias)

---

## 10. ✅ Verificación de Rendimiento

### Velocidad de Carga
- [ ] Las páginas cargan en menos de 3 segundos
- [ ] Las imágenes están optimizadas
- [ ] No hay errores en la consola del navegador

### Consultas a Base de Datos
- [ ] Instala Query Monitor (plugin)
- [ ] Verifica que no hay consultas duplicadas
- [ ] Verifica que las consultas son eficientes

---

## 11. ✅ Verificación de Errores

### Consola del Navegador
1. [ ] Abre DevTools (F12)
2. [ ] Ve a la pestaña Console
3. [ ] Navega por el sitio
4. [ ] Verifica que no hay errores JavaScript

### Errores PHP
1. [ ] Activa WP_DEBUG en wp-config.php
2. [ ] Navega por el sitio
3. [ ] Verifica que no hay warnings o errores
4. [ ] Desactiva WP_DEBUG después

### Logs de WordPress
1. [ ] Revisa el archivo debug.log (si existe)
2. [ ] Verifica que no hay errores relacionados con el tema

---

## 12. ✅ Verificación de Seguridad

### Permisos
- [ ] Solo administradores pueden crear/editar contenido
- [ ] Los visitantes solo pueden ver contenido público

### Validación de Datos
- [ ] Intenta ingresar valores negativos en estadísticas
- [ ] Verifica que se validan correctamente
- [ ] Intenta dejar campos vacíos
- [ ] Verifica que se manejan correctamente

---

## 13. ✅ Verificación de Documentación

### Archivos de Documentación
- [ ] README.md existe y es legible
- [ ] GUIA_USO.md existe y es completo
- [ ] SHORTCODES.md existe y tiene ejemplos
- [ ] RESUMEN_SISTEMA.md existe
- [ ] EJEMPLOS_USO.md existe
- [ ] IMPLEMENTACION_COMPLETA.md existe

### Contenido de Documentación
- [ ] Las instrucciones son claras
- [ ] Los ejemplos funcionan
- [ ] Los IDs de ejemplo son correctos

---

## 14. ✅ Pruebas de Integración

### Flujo Completo
1. [ ] Crear temporada
2. [ ] Crear torneo en esa temporada
3. [ ] Crear 2 equipos
4. [ ] Asignar equipos al torneo
5. [ ] Crear 4 jugadores (2 por equipo)
6. [ ] Crear partido entre los equipos
7. [ ] Ingresar estadísticas
8. [ ] Verificar que todo se calcula correctamente
9. [ ] Verificar que todo se muestra correctamente

### Múltiples Partidos
1. [ ] Crear 3 partidos diferentes
2. [ ] Ingresar estadísticas en todos
3. [ ] Verificar que las estadísticas acumuladas son correctas
4. [ ] Verificar que la tabla de posiciones es correcta

---

## 15. ✅ Checklist Final

### Funcionalidad
- [ ] Todos los custom post types funcionan
- [ ] Todos los meta boxes guardan datos
- [ ] Todos los shortcodes funcionan
- [ ] Todas las plantillas se muestran correctamente
- [ ] Los cálculos automáticos funcionan
- [ ] Las imágenes se muestran correctamente

### Diseño
- [ ] El sitio se ve profesional
- [ ] Los colores son consistentes
- [ ] El diseño es responsive
- [ ] Las tablas son legibles
- [ ] Los marcadores son atractivos

### Usabilidad
- [ ] La navegación es intuitiva
- [ ] Los formularios son claros
- [ ] Los mensajes de ayuda son útiles
- [ ] El flujo de trabajo es lógico

---

## 🎉 Sistema Verificado

Si todos los checkboxes están marcados, el sistema está **100% funcional** y listo para usar en producción.

### Próximos Pasos
1. Eliminar el contenido de prueba
2. Crear contenido real
3. Configurar permalinks
4. Optimizar imágenes
5. Configurar backup automático
6. ¡Empezar a usar el sistema!

---

## 🐛 Solución de Problemas

Si algo no funciona:

1. **Verifica la versión de PHP** (debe ser 7.4+)
2. **Verifica la versión de WordPress** (debe ser 5.0+)
3. **Desactiva otros plugins** para verificar conflictos
4. **Revisa los logs de errores** en debug.log
5. **Consulta la documentación** en los archivos .md
6. **Verifica los permisos** de archivos y carpetas

---

**¡Buena suerte con tu sistema de estadísticas de baseball!** ⚾
