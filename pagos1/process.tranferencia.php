<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_representante = $_POST['id_representante'];
    $id_deportista = $_POST['id_deportista'];
    $banco = $_POST['banco'];
    $numero_factura = $_POST['numero_factura'];
    $cuenta_origen = $_POST['cuenta_origen'];
    $cuenta_destino = $_POST['cuenta_destino'];
    $fecha_pago = $_POST['fecha_pago'];
    $motivo = $_POST['motivo'];
    $monto = $_POST['monto'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];
    $nombre_archivo = $_FILES['comprobante_transferencia']['name'];

    // Movemos el archivo a la carpeta de destino
    move_uploaded_file($_FILES['comprobante_transferencia']['tmp_name'], 'uploads/' . $nombre_archivo);

    $sql = "INSERT INTO tab_pagos (ID_REPRESENTANTE, ID_DEPORTISTA, TIPO_PAGO, MONTO, FECHA, BANCO, MOTIVO, NOMBRE_ARCHIVO) 
            VALUES (:id_representante, :id_deportista, 'transferencia', :monto, :fecha_pago, :banco, :motivo, :nombre_archivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_representante' => $id_representante,
        ':id_deportista' => $id_deportista,
        ':monto' => $monto,
        ':fecha_pago' => $fecha_pago,
        ':banco' => $banco,
        ':motivo' => $motivo,
        ':nombre_archivo' => $nombre_archivo
    ]);

    echo "Pago por transferencia registrado exitosamente.";
}
?>
