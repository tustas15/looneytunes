<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

header('Content-Type: application/json');

// Obtener el ID del deportista desde la sesión
$id_deportista = $_SESSION['user_id'] ?? null;

if (!$id_deportista) {
    echo json_encode(['error' => 'No se ha identificado al deportista']);
    exit;
}

try {
    // Consulta para obtener el representante del deportista
    $sql = "SELECT r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE
    FROM tab_representantes r
    INNER JOIN tab_representantes_deportistas rd ON r.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
    WHERE rd.ID_DEPORTISTA = :id_deportista";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
$stmt->execute();

    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_deportista', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();

    $representante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$representante) {
        echo json_encode([
            'message' => 'No se encontró un representante asociado a este deportista',
            'id_deportista' => $id_deportista
        ]);
    } else {
        echo json_encode($representante);
    }
} catch (PDOException $e) {
    echo json_encode([
        'error' => 'Error al obtener el representante',
        'message' => $e->getMessage()
    ]);
}
?>
