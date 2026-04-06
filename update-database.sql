-- Script de actualización de base de datos para Baseball Stats Theme
-- Fecha: 2026-04-06
-- Descripción: Agrega columnas para 2B, 3B y E (Dobles, Triples, Errores)

-- IMPORTANTE: Reemplaza 'wp_' con tu prefijo de WordPress si es diferente

-- Agregar columnas a la tabla de estadísticas de juegos
ALTER TABLE wp_baseball_game_stats 
ADD COLUMN IF NOT EXISTS doubles int(11) DEFAULT 0 AFTER hits,
ADD COLUMN IF NOT EXISTS triples int(11) DEFAULT 0 AFTER doubles,
ADD COLUMN IF NOT EXISTS errors int(11) DEFAULT 0 AFTER strikeouts;

-- Verificar que las columnas se agregaron correctamente
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'wp_baseball_game_stats'
AND COLUMN_NAME IN ('doubles', 'triples', 'errors');

-- Opcional: Si quieres eliminar la columna stolen_bases (no recomendado si tienes datos históricos)
-- ALTER TABLE wp_baseball_game_stats DROP COLUMN IF EXISTS stolen_bases;

-- Mensaje de confirmación
SELECT 'Base de datos actualizada correctamente. Las columnas doubles, triples y errors han sido agregadas.' AS mensaje;
