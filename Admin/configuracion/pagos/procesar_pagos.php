<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_representante = $_POST['id_representante'];
    $id_deportista = $_POST['id_deportista'];
    $tipo_pago = $_POST['tipo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha_pago'];
    $motivo = $_POST['motivo'];

    
    // Insertar en tab_pago
    $stmt = $conn->prepare("INSERT INTO tab_pagos (id_representante, id_deportista, tipo_pago, monto, fecha, motivo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdss", $id_representante, $id_deportista, $tipo_pago, $monto, $fecha, $motivo);
    
    if ($stmt->execute()) {
        $id_pago = $conn->insert_id;
        
        // Insertar detalles adicionales en tab_pago_detalle
        $mes = $_POST['mes'];
        $anio = $_POST['anio'];
        
        if ($tipo_pago == 'transferencia') {
            $banco_destino = $_POST['banco_destino'];
            $entidad_financiera = $_POST['entidad_financiera'];
            
            $stmt_detalle = $conn->prepare("INSERT INTO tab_pago_detalle (id_pago, banco_destino, entidad_financiera, mes, anio) VALUES (?, ?, ?, ?, ?)");
            $stmt_detalle->bind_param("iissi", $id_pago, $banco_destino, $entidad_financiera, $mes, $anio);
        } else {
            $stmt_detalle = $conn->prepare("INSERT INTO tab_pago_detalle (id_pago, mes, anio) VALUES (?, ?, ?)");
            $stmt_detalle->bind_param("isi", $id_pago, $mes, $anio);
        }
        
        if ($stmt_detalle->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar los detalles del pago']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el pago']);
    }
}
?>