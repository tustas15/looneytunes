<?php
session_start(); // Asegúrate de iniciar la sesión
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$tipo_usuario = 'REPRESENTANTE'; // Valor fijo si estás en la vista de representante
$id_representante = $_SESSION['user_id']; // ID del representante, obtenido desde la sesión

try {
    // Consulta SQL para obtener pagos registrados por el representante actual
    $sql = "
        SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
               p.FECHA_PAGO, p.METODO_PAGO, p.MONTO, p.MOTIVO
        FROM tab_pagos p
        INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.REGISTRADO_POR = :tipo_usuario AND p.ID_REPRESENTANTE = :id_representante
        ORDER BY p.FECHA_PAGO DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':tipo_usuario' => $tipo_usuario,
        ':id_representante' => $id_usuario
    ]);
    
    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar los datos para DataTables
    $data = [];
    foreach ($pagos as $pago) {
        $data[] = [
            'deportista' => $pago['NOMBRE_DEPO'] . ' ' . $pago['APELLIDO_DEPO'],
            'representante' => $pago['NOMBRE_REPRE'] . ' ' . $pago['APELLIDO_REPRE'],
            'metodo_pago' => $pago['METODO_PAGO'],
            'fecha_pago' => date('d/m/Y', strtotime($pago['FECHA_PAGO'])),
            'motivo' => $pago['MOTIVO'],
            'monto' => '$' . number_format($pago['MONTO'], 2),
            'acciones' => '<button class="btn btn-primary btn-sm edit-btn" data-id="' . $pago['ID_PAGO'] . '"><i class="fas fa-pencil-alt"></i></button> ' .
                          '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $pago['ID_PAGO'] . '"><i class="fas fa-trash"></i></button>'
        ];
    }

    // Devolver los datos en formato JSON
    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener los pagos: ' . $e->getMessage()]);
}
?>
