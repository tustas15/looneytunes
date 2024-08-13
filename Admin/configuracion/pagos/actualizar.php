<?php
// actualizar_pago.php

session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pago = $_POST['id_pago'];
    $representante = $_POST['representante'];
    $deportista = $_POST['deportista'];
    $metodo_pago = $_POST['metodo_pago'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $mes = $_POST['mes'];
    $anio = $_POST['anio'];
    $motivo = $_POST['motivo'];

    $sql = "UPDATE pagos SET 
            ID_REPRESENTANTE = ?, 
            ID_DEPORTISTA = ?, 
            METODO_PAGO = ?, 
            MONTO = ?, 
            FECHA_PAGO = ?, 
            MES = ?, 
            ANIO = ?, 
            MOTIVO = ? 
            WHERE ID_PAGO = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdssssi", $representante, $deportista, $metodo_pago, $monto, $fecha, $mes, $anio, $motivo, $id_pago);

    if ($stmt->execute()) {
        $response = array(
            "success" => true,
            "message" => "Pago actualizado correctamente"
        );
    } else {
        $response = array(
            "success" => false,
            "message" => "Error al actualizar el pago: " . $stmt->error
        );
    }

    $stmt->close();
    $conn->close();

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
?>