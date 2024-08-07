<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $representante = $_POST['apellido_representante'];
    $deportista = $_POST['deportista'];
    $tipo_pago = $_POST['tipo_pago'];
    $motivo = $_POST['motivo_' . $tipo_pago];
    $monto = $_POST['monto_' . $tipo_pago];
    $fecha = $_POST['fecha_' . $tipo_pago];
    
    $query = "INSERT INTO tab_pagos (id_representante, id_deportista, tipo_pago, fecha, motivo, monto) 
              VALUES (:representante, :deportista, :tipo_pago, :fecha, :motivo, :monto)";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':representante', $representante, PDO::PARAM_INT);
        $stmt->bindParam(':deportista', $deportista, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_pago', $tipo_pago, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            if ($tipo_pago == 'transferencia') {
                // Procesar el comprobante
                if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
                    $uploadDir = '/ruta/a/tu/directorio/de/comprobantes/';
                    $fileName = basename($_FILES['comprobante']['name']);
                    $uploadFile = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $uploadFile)) {
                        // Actualizar la ruta del comprobante en la base de datos
                        $updateQuery = "UPDATE tab_pagos SET comprobante = :comprobante WHERE id = :id";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bindParam(':comprobante', $uploadFile, PDO::PARAM_STR);
                        $updateStmt->bindParam(':id', $conn->lastInsertId(), PDO::PARAM_INT);
                        $updateStmt->execute();
                    }
                }
            }
            $response = array('success' => true);
        } else {
            $response = array('success' => false, 'message' => 'Error al ejecutar la consulta.');
        }
    } catch (PDOException $e) {
        $response = array('success' => false, 'message' => 'Error: ' . $e->getMessage());
    }
    
    echo json_encode($response);
}
?>