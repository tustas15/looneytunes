<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_representante = $_POST['id_representante'];
    $id_deportista = $_POST['id_deportista'];
    $fecha_pago = $_POST['fecha_pago'];
    $motivo = $_POST['motivo'];
    $monto = $_POST['monto'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];

    $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, TIPO_PAGO, MONTO, FECHA, MOTIVO) 
            VALUES (:id_representante, :id_deportista, 'efectivo', :monto, :fecha_pago, :motivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_representante' => $id_representante,
        ':id_deportista' => $id_deportista,
        ':monto' => $monto,
        ':fecha_pago' => $fecha_pago,
        ':motivo' => $motivo
    ]);

    echo "Pago en efectivo registrado exitosamente.";
}
?>
