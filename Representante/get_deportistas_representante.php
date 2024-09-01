<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

header('Content-Type: application/json');

// Verificar si el usuario está autenticado y es un representante
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'Representante') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// Obtener el ID del representante de la sesión
$id_representante = $_SESSION['id_usuario'] ?? null;

if (!$id_representante) {
    echo json_encode(['error' => 'ID de representante no encontrado en la sesión']);
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
            'deportistas' => []
        ]);
    } else {
        echo json_encode([
            'message' => 'Deportistas encontrados',
            'deportistas' => $deportistas
        ]);
    }
} catch (PDOException $e) {
    error_log('Error en get_deportistas_representante.php: ' . $e->getMessage());
    echo json_encode([
        'error' => 'Error al obtener los deportistas',
        'message' => 'Ocurrió un error al procesar su solicitud. Por favor, inténtelo de nuevo más tarde.'
    ]);
}
?>