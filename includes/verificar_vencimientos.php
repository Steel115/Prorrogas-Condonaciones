<?php
date_default_timezone_set('America/Mexico_City'); // Zona horaria correcta
if (!isset($pdo)) return;

// Marcar como vencida (estatus = 2) las asignaciones activas cuya fecha_limite ya pasó
$pdo->prepare("
    UPDATE asignaciones 
    SET estatus = 2 
    WHERE estatus = 0 AND fecha_limite < NOW()
")->execute();