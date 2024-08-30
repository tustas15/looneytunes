<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

header('Content-Type: application/json');

// Asegúrate de que el ID del representante esté almacenado en la sesión
$id_deportista = $_SESSION['tipo_usuario'] ?? null;

if (!$id_deportista) {
    echo json_encode(['error' => 'No se ha identificado al deportista', 'session' => $_SESSION]);
    exit;
}

try {
    $sql = "SELECT d.ID_REPRESENTANTE, d.NOMBRE_REPRE, d.APELLIDO_REPRE
            FROM tab_representantes d
            INNER JOIN tab_representantes_deportistas rd ON d.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
            WHERE rd.ID_DEPORTISTA = :id_deportista";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    
    $id_deportista = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($representante)) {
        echo json_encode([
            'message' => 'No se encontraron deportistas asociados a este representante',
            'id_deportista' => $id_deportista,
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
        'id_deportista' => $id_deportista,
        'sql' => $sql
    ]);
}