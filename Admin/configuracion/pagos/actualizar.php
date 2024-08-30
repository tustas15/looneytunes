<?php
// actualizar_pago.php

session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pago = $_POST['id_pago'];
    $representante = $_POST['representante'];
    $deportista = $_POST['deportista'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];

    try {
        $sql = "UPDATE tab_pagos 
                SET 
                    MONTO = :monto, 
                    FECHA_PAGO = :fecha, 
                    ID_REPRESENTANTE = :representante, 
                    ID_DEPORTISTA = :deportista 
                WHERE 
                    ID_PAGO = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':representante', $representante);
        $stmt->bindParam(':deportista', $deportista);
        $stmt->bindParam(':id', $id_pago);

        if ($stmt->execute()) {
            $response = array(
                "success" => true,
                "message" => "Pago actualizado correctamente"
            );
        } else {
            $response = array(
                "success" => false,
                "message" => "Error al actualizar el pago"
            );
        }
    } catch (PDOException $e) {
        $response = array(
            "success" => false,
            "message" => "Error al actualizar el pago: " . $e->getMessage()
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    $response = array(
        "success" => false,
        "message" => "No se recibieron datos para actualizar"
    );
    header('Content-Type: application/json');
    echo json_encode($response);
}
