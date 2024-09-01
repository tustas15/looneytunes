<?php
session_start();
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 3) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit;
}

// Obtener el ID del usuario actual
$id_usuario = isset($_SESSION['user_id']) ? $_SESSION['uder_id'] : null;

if (!$id_usuario) {
    header('Content-Type: application/json');

    echo json_encode(['error' => 'ID de usuario no encontrado en la sesión']);
    exit;
}

try {
    // Paso 1: Obtener el ID del representante asociado al usuario
    $sqlRepresentante = "SELECT ID_REPRESENTANTE FROM tab_representantes WHERE ID_USUARIO = :id_usuario";
    $stmtRepresentante = $conn->prepare($sqlRepresentante);
    $stmtRepresentante->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtRepresentante->execute();
    
    $representante = $stmtRepresentante->fetch(PDO::FETCH_ASSOC);

    if (!$representante) {
        header('Content-Type: application/json');

        echo json_encode(['error' => 'No se encontró el representante asociado a este usuario']);
        exit;
    }

    $id_representante = $representante['ID_REPRESENTANTE'];
    echo "ID Representante: $id_representante<br>";

    // Paso 2: Obtener los deportistas asociados al representante
    $sqlDeportistas = "SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO, d.CEDULA_DEPO
                       FROM tab_deportistas d
                       INNER JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
                       WHERE rd.ID_REPRESENTANTE = :id_representante";

    $stmtDeportistas = $conn->prepare($sqlDeportistas);
    $stmtDeportistas->bindParam(':id_representante', $id_representante, PDO::PARAM_INT);
    $stmtDeportistas->execute();
    
    $deportistas = $stmtDeportistas->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');

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
    header('Content-Type: application/json');

    echo json_encode([
        'error' => 'Error al obtener los deportistas',
        'message' => 'Ocurrió un error al procesar su solicitud. Por favor, inténtelo de nuevo más tarde.'
    ]);
}
?>
