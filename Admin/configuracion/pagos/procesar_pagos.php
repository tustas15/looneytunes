<?php
require_once 'conexion.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_representante = $_POST['id_representante'];
    $id_deportista = $_POST['id_deportista'];
    $tipo_pago = $_POST['tipo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];
    $nombre_archivo = ''; // Aquí puedes manejar la subida de archivos si es necesario

    // Insertar en la base de datos
    $sql = "INSERT INTO tab_pagos (id_representante, id_deportista, tipo_pago, monto, fecha, motivo, nombre_archivo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_representante, $id_deportista, $tipo_pago, $monto, $fecha, $motivo, $nombre_archivo]);

    // Retornar respuesta JSON
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
