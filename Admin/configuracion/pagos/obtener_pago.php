<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if (isset($_GET['id'])) {
    $id_pago = $_GET['id'];

    try {
        $sql = "SELECT * FROM tab_pagos WHERE ID_PAGO = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id_pago]);
        $pago = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pago) {
            echo json_encode($pago);
        } else {
            echo json_encode(['error' => 'Pago no encontrado']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID de pago no proporcionado']);
}
?>