<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id_pago = $_POST['id'];

    try {
        $sql = "DELETE FROM tab_pagos WHERE ID_PAGO = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_pago]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró el pago o ya fue eliminado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
?>