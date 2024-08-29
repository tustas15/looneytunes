<?php
// historial_pagos.php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$id_representante = $_SESSION['tipo_usuario'] ?? null;

if (!$id_representante) {
    echo json_encode(['data' => [], 'error' => 'Sesión no válida']);
    exit;
}

try {
    $sql = "SELECT p.*, d.NOMBRE_DEPO, d.APELLIDO_DEPO, r.NOMBRE_REPRE, r.APELLIDO_REPRE
            FROM tab_pagos p
            JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
            JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
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
            'acciones' => '<button class="btn btn-primary btn-sm edit-btn" data-id="' . $pago['ID_PAGO'] . '"><i class="fas fa-pencil-alt"></i></button> ' .
                          '<button class="btn btn-danger btn-sm delete-btn" data-id="' . $pago['ID_PAGO'] . '"><i class="fas fa-trash"></i></button>'
        ];
    }

    echo json_encode(['data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['data' => [], 'error' => 'Error al obtener los pagos: ' . $e->getMessage()]);
}