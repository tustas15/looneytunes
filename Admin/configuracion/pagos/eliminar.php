<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verificar si se ha proporcionado un ID_PAGO válido
if (isset($_GET['id'])) {
    $idPago = $_GET['id'];

    // Eliminar el pago por ID_PAGO
    $sql = "DELETE FROM tab_pagos WHERE ID_PAGO = :idPago";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idPago', $idPago, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: pagos.php"); // Redirigir a la página principal o a donde desees después de eliminar el pago
        exit();
    } else {
        echo "Error al eliminar el pago.";
    }
} else {
    echo "ID de pago no especificado.";
}
?>