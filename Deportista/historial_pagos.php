<?php
session_start(); // Asegúrate de iniciar la sesión
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verifica si el ID del usuario está disponible en la sesión
if (!isset($_SESSION['user_id'])) {
    die("ID de usuario no disponible en la sesión.");
}

// Obtén el ID del usuario desde la sesión
$id_usuario = $_SESSION['user_id'];

try {
    // Consulta para obtener el ID del representante usando el ID del usuario
    $sql_representante = "SELECT ID_DEPORTISTA FROM tab_deportistas WHERE ID_USUARIO = :id_usuario";
    $stmt = $conn->prepare($sql_deportista);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $deportista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deportista) {
        echo json_encode(['error' => 'No se encontró el representante para el usuario actual.']);
        exit;
    }

    $id_deportista = $deportista['ID_DEPORTISTA'];

    // Consulta para obtener los pagos registrados por el representante actual
    $sql = "
        SELECT p.ID_PAGO, r.NOMBRE_REPRE, r.APELLIDO_REPRE, d.NOMBRE_DEPO, d.APELLIDO_DEPO, 
               p.FECHA_PAGO, p.METODO_PAGO, p.MONTO, p.MOTIVO
        FROM tab_pagos p
        INNER JOIN tab_representantes r ON p.ID_REPRESENTANTE = r.ID_REPRESENTANTE
        INNER JOIN tab_deportistas d ON p.ID_DEPORTISTA = d.ID_DEPORTISTA
        WHERE p.ID_REPRESENTANTE = :id_representante
        AND p.REGISTRADO_POR NOT IN ('ADMIN', 'REPRE')
        ORDER BY p.FECHA_PAGO DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id_deportista' => $id_deportista
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
    // Manejo de errores en la consulta
    echo json_encode(['error' => 'Error al obtener los pagos: ' . $e->getMessage()]);
}
?>
