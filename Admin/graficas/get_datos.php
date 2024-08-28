<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Establecer el tipo de contenido a JSON
header('Content-Type: application/json');

// Consulta SQL para obtener los datos de pagos
$query = "SELECT FECHA_PAGO, MONTO FROM tab_pagos ORDER BY FECHA_PAGO";
$stmt = $conn->prepare($query);
$stmt->execute();

// Obtener todos los resultados
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
echo json_encode($data);

// Cerrar la conexión (opcional, el recolector de basura lo hará automáticamente)
$conn = null;
?>
