<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}
require_once('../admin/configuracion/conexion.php');
header('Content-Type: application/json');

// Asume que el ID del representante está en la sesión
$id_representante = $_SESSION['id_representante'];

// Verifica si el ID del representante está definido
if (!isset($id_representante)) {
    echo json_encode(['data' => [], 'error' => 'ID del representante no encontrado en la sesión']);
    exit;
}
try {
    // Conecta a la base de datos usando PDO
    $conn = new PDO("mysql:host=localhost;dbname=looneytunes", 'root', ''); // Cambia los parámetros de conexión

    // Configura el manejo de errores de PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los pagos del representante
    $sql = "
    SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
           p.FECHA_PAGO, p.METODO_PAGO, p.MONTO, p.MOTIVO
    FROM tab_pagos p
    INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
    INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
    WHERE p.ID_REPRESENTANTE = :id_representante
    ORDER BY p.FECHA_PAGO DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_representante' => $id_representante]);
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($pagos as $pago) {
        $data[] = [
            'deportista' => $pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO'],
            'representante' => $pago['NOMBRE_REPRE'] . ' ' . $pago['APELLIDO_REPRE'],
            'metodo_pago' => $pago['METODO_PAGO'],
            'fecha_pago' => date('d/m/Y', strtotime($pago['FECHA_PAGO'])),
            'motivo' => $pago['MOTIVO'],
            'monto' => '$' . number_format($pago['MONTO'], 2),
            'acciones' => '<button class="btn btn-primary btn-sm edit-btn" data-id="'.$pago['ID_PAGO'].'">Editar</button>'
        ];
    }

    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['data' => [], 'error' => 'Error al obtener los pagos: ' . $e->getMessage()]);
}
?>
