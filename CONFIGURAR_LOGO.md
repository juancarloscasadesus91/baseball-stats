# Cómo Configurar el Logo del Sitio

## Vista Previa del Resultado

Cuando configures tu logo, aparecerá así en el header:

```
┌─────────────────────────────────────────────────────────┐
│  [TU LOGO] BASEBALL        Inicio Equipos Jugadores ... │
└─────────────────────────────────────────────────────────┘
```

El logo aparece a la **izquierda** del texto "BASEBALL" sin romper el diseño del banner.

## Pasos para agregar tu logo personalizado

### Opción 1: Desde el Customizer (Recomendado)

1. **Accede al Customizer de WordPress:**
   - Ve a tu panel de administración de WordPress
   - Haz clic en **Apariencia** → **Personalizar**

2. **Navega a la sección de Logo:**
   - En el menú lateral, busca y haz clic en **"Logo y Título del Sitio"**
   - Verás la opción **"Logo del Sitio"**

3. **Sube tu logo:**
   - Haz clic en **"Seleccionar logo"**
   - Puedes subir una imagen nueva o seleccionar una de la biblioteca de medios
   - **Formatos recomendados:** PNG (con fondo transparente) o SVG
   - **Dimensiones recomendadas:** 300x100 píxeles (ancho x alto)
   - El logo se ajustará automáticamente a una altura máxima de 60px en desktop y 50px en móvil

4. **Ajusta el tamaño (opcional):**
   - WordPress te permitirá recortar la imagen si es necesario
   - Puedes omitir el recorte si tu logo ya tiene las dimensiones correctas

5. **Publica los cambios:**
   - Haz clic en **"Publicar"** en la parte superior del Customizer

### Opción 2: Desde Apariencia → Personalizar

Es el mismo proceso que la Opción 1, solo que accedes directamente desde el menú de WordPress.

## Características del Logo

- **Responsive:** El logo se adapta automáticamente a diferentes tamaños de pantalla
- **Aparece junto al texto:** El logo se muestra al lado izquierdo del texto "Baseball" sin romper el diseño
- **Hover effect:** El logo tiene un efecto de opacidad al pasar el mouse sobre él
- **Optimizado:** Se carga rápidamente y se ve nítido en pantallas de alta resolución
- **Flexible:** Mantiene la proporción original de tu imagen

## Recomendaciones de Diseño

### Formato de Archivo
- **PNG:** Ideal para logos con fondo transparente
- **SVG:** Mejor opción para logos vectoriales (se ven perfectos en cualquier tamaño)
- **JPG:** Solo si tu logo tiene fondo sólido

### Dimensiones
- **Logos cuadrados (como el tuyo 1024x1024):** Funcionan perfectamente, se ajustarán automáticamente
- **Logos horizontales:** También funcionan bien
- **Tamaño del archivo:** Cualquier tamaño se ajustará automáticamente
- **Importante:** El logo se redimensionará automáticamente a:
  - Desktop: Máximo 80x60px
  - Tablet: Máximo 70x50px
  - Móvil: Máximo 50x45px
  - Pantallas pequeñas: Máximo 40x35px

### Colores
- Asegúrate de que tu logo tenga buen contraste con el fondo blanco del header
- Los colores del tema son:
  - Azul marino MLB: #041E42
  - Rojo MLB: #D50032
  - Azul claro: #27AAE1

## Solución de Problemas

### El logo se ve muy grande o rompe el diseño
- **Solución:** El sistema ahora ajusta automáticamente logos de cualquier tamaño
- **Logos cuadrados grandes (ej: 1024x1024):** Se redimensionan automáticamente sin romper el diseño
- **Si aún hay problemas:** Recarga la página con Ctrl+F5 (Windows) o Cmd+Shift+R (Mac) para limpiar la caché

### El logo se ve pixelado
- Sube una imagen de mayor resolución
- Considera usar formato SVG para logos vectoriales

### El logo no aparece
- Verifica que hayas hecho clic en "Publicar" en el Customizer
- Limpia la caché del navegador (Ctrl+F5 o Cmd+Shift+R)
- Verifica que la imagen se haya subido correctamente

### Quiero eliminar el logo y dejar solo el texto "Baseball"
- Ve a **Apariencia** → **Personalizar** → **Logo y Título del Sitio**
- Haz clic en **"Eliminar"** debajo del logo actual
- Haz clic en **"Publicar"**
- El texto "Baseball" seguirá apareciendo solo

## Soporte Técnico

El logo está configurado con las siguientes especificaciones técnicas:
- **Desktop:** Máximo 80px ancho × 60px alto
- **Tablet (≤1024px):** Máximo 70px ancho × 50px alto
- **Móvil (≤768px):** Máximo 50px ancho × 45px alto
- **Pantallas pequeñas (≤480px):** Máximo 40px ancho × 35px alto
- Separación entre logo y texto: 12px (adaptable según pantalla)
- Transición suave al hacer hover (opacidad)
- Compatible con retina displays
- El logo y el texto aparecen juntos horizontalmente
- `object-fit: contain` para mantener proporciones sin distorsión
- Funciona con logos cuadrados, horizontales y verticales

## Archivos Modificados

Esta funcionalidad se implementó modificando:
- `functions.php` - Configuración del soporte de logo personalizado
- `header.php` - Muestra el logo en el header
- `style.css` - Estilos CSS para el logo
