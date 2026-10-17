-- MathApp: migración 01
-- Ejecutar en phpMyAdmin: base de datos math_app > pestaña SQL > pegar y ejecutar.
-- Se puede ejecutar más de una vez sin problema.

USE `math_app`;

-- 1) Pista para "Aprende a Estudiar" (da la idea, pero NO el resultado)
ALTER TABLE `preguntas`
  ADD COLUMN IF NOT EXISTS `pista` text DEFAULT NULL AFTER `explicacion`;

UPDATE `preguntas` SET `pista` = 'Identifica los datos del problema. Si juntas o agrupas cantidades, usas una Suma (+).' WHERE `id` = 1;
UPDATE `preguntas` SET `pista` = 'Newton junta lo que encontró en la mañana con lo de la tarde. Cuando se agrupan colecciones, se suma.' WHERE `id` = 2;
UPDATE `preguntas` SET `pista` = 'Si algo se pierde o alguien se lo lleva, la cantidad baja. Usa una Resta (-).' WHERE `id` = 3;
UPDATE `preguntas` SET `pista` = 'Compara lo que necesitas con lo que ya tienes. La diferencia entre las dos cantidades se calcula con una Resta (-).' WHERE `id` = 4;

-- 2) Historial de intentos: cada respuesta del alumno, bien o mal
CREATE TABLE IF NOT EXISTS `historial_intentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `reino` int(11) NOT NULL DEFAULT 1,
  `enunciado` varchar(255) NOT NULL,
  `opcion_elegida` char(1) NOT NULL,
  `opcion_correcta` char(1) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL DEFAULT 0,
  `xp_ganado` int(11) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_usuario_fecha` (`usuario_id`, `creado_en`),
  CONSTRAINT `fk_intentos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
