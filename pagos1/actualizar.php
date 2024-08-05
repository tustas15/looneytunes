<?php
require_once('../Admin/configuracion/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idPago = $_POST['idPago'];
    $fecha = $_POST['fecha'];
    $tipo_pago = $_POST['tipo_pago'];
    $monto = $_POST['monto'];
    $motivo = $_POST['motivo'];
    $banco = ($tipo_pago == 'Transferencia') ? $_POST['banco'] : null;

    if ($tipo_pago == 'Transferencia') {
        $sql = "UPDATE tab_pagos
                SET FECHA = :FECHA,
                    TIPO_PAGO = :TIPO_PAGO,
                    MONTO = :MONTO,
                    MOTIVO = :MOTIVO,
                    BANCO = :BANCO
                WHERE ID_PAGO = :ID_PAGO";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':BANCO', $banco, PDO::PARAM_STR);
    } else {
        $sql = "UPDATE tab_pagos
                SET FECHA = :FECHA,
                    TIPO_PAGO = :TIPO_PAGO,
                    MONTO = :MONTO,
                    MOTIVO = :MOTIVO,
                    BANCO = NULL
                WHERE ID_PAGO = :ID_PAGO";
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindParam(':ID_PAGO', $idPago, PDO::PARAM_INT);
    $stmt->bindParam(':FECHA', $fecha, PDO::PARAM_STR);
    $stmt->bindParam(':TIPO_PAGO', $tipo_pago, PDO::PARAM_STR);
    $stmt->bindParam(':MONTO', $monto, PDO::PARAM_STR);
    $stmt->bindParam(':MOTIVO', $motivo, PDO::PARAM_STR);

    if ($stmt->execute()) {
        header("Location: pagos.php"); // Redirigir a la página principal o a donde desees después de actualizar el pago
        exit();
    } else {
        echo "Error al actualizar el pago.";
    }
}
?>