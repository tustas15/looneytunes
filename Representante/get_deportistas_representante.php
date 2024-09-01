<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

header('Content-Type: application/json');

// Obtener el ID del usuario de la sesión
$idUsuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : null;

if (!$idUsuario) {
    echo json_encode(['error' => 'Usuario no identificado']);
    exit;
}

try {
    $sql = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.CEDULA_DEPO
            FROM tab_deportistas d
            INNER JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
            INNER JOIN tab_representantes r ON rd.ID_REPRESENTANTE = r.ID_REPRESENTANTE
            WHERE r.ID_USUARIO = :id_usuario";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
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