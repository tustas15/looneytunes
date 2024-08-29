<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

header('Content-Type: application/json');

// Asegúrate de que el ID del representante esté almacenado en la sesión
$id_representante = $_SESSION['tipo_usuario'] ?? null;

if (!$id_representante) {
    echo json_encode(['error' => 'No se ha identificado al representante', 'session' => $_SESSION]);
    exit;
}

try {
    $sql = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO
            FROM tab_deportistas d
            INNER JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
            WHERE rd.ID_REPRESENTANTE = :id_representante";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($deportistas)) {
        echo json_encode([
            'message' => 'No se encontraron deportistas asociados a este representante',
            'id_representante' => $id_representante,
            'sql' => $sql
        ]);
    } else {
        echo json_encode($deportistas);
    }
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error al obtener los deportistas',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'id_representante' => $id_representante,
        'sql' => $sql
    ]);
}