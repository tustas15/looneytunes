<?php
session_start();
require_once('../conexion.php');

$tipo = $_GET['tipo'] ?? '';

try {
    $query = "SELECT d.nombre, c.nombre_categoria, p.estado
              FROM tab_deportistas d
              JOIN tab_categorias c ON d.id_categoria = c.id_categoria
              LEFT JOIN (
                  SELECT id_deportista, estado
                  FROM tab_pagos
                  WHERE fecha_pago = (
                      SELECT MAX(fecha_pago)
                      FROM pagos p2
                      WHERE p2.id_deportista = pagos.id_deportista
                  )
              ) p ON d.id_deportista = p.id_deportista
              WHERE p.estado = :estado
              ORDER BY d.nombre";

    $estado = ($tipo === 'al_dia') ? 'Al día' : 'Atrasado';

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
    $stmt->execute();

    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($resultado);

} catch (PDOException $e) {
    // Manejar errores
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}