UPDATE reportes
SET `diurnas` = 8,
`almuerzo` = 1,
`horas_trabajadas` = 9,
`nocturnas` = 0,
`extra_diurnas` = 1, `extra_nocturnas` = 0,
`dominical_diurno` = 0, `dominical_nocturno` = 0, `dominical_extra_diurno` = 0, `dominical_extra_nocturno` = 0
WHERE DAY (fecha_ini) > 15 AND DAY (fecha_ini) < 23 AND `deleted_at` IS NULL AND MONTH (fecha_ini) = 4
AND TIME (fecha_ini) = '07:00:00'
AND TIME (fecha_fin) = '17:00:00';
