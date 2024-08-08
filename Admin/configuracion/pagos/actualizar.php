<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Asegúrate de que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recupera los datos del formulario
    $id_pago = $_POST['id_pago'];
    $deportista = $_POST['deportista'];
    $representante = $_POST['representante'];
    $tipo_pago = $_POST['tipo_pago'];
    $fecha = $_POST['fecha'];
    $motivo = $_POST['motivo'];
    $monto = $_POST['monto'];

    try {
        // Prepara la consulta SQL para actualizar el pago
        $sql = "UPDATE tab_pagos SET 
                DEPORTISTA = :deportista,
                REPRESENTANTE = :representante,
                TIPO_PAGO = :tipo_pago,
                FECHA = :fecha,
                MOTIVO = :motivo,
                MONTO = :monto
                WHERE ID_PAGO = :id_pago";

        $stmt = $conn->prepare($sql);

        // Vincula los parámetros
        $stmt->bindParam(':deportista', $deportista);
        $stmt->bindParam(':representante', $representante);
        $stmt->bindParam(':tipo_pago', $tipo_pago);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':id_pago', $id_pago);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            // Si la actualización fue exitosa
            echo json_encode(['success' => true, 'message' => 'Pago actualizado con éxito']);
        } else {
            // Si hubo un error en la actualización
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el pago']);
        }
    } catch (PDOException $e) {
        // Si ocurre una excepción, devuelve un mensaje de error
        echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    // Si la solicitud no es POST, devuelve un error
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido']);
}
?>