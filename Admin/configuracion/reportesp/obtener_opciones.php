<?php
require_once('../conexion.php');

// Verificar los datos recibidos
if (!isset($_POST['tipos']) || !is_string($_POST['tipos'])) {
    echo json_encode(['error' => 'No se especificó el tipo de reporte']);
    exit;
}

$tipo = $_POST['tipos']; // Ahora es una sola cadena, no un array
$response = [];

try {
    switch ($tipo) {
        case 'categoria':
            $stmt = $conn->query("SELECT ID_CATEGORIA as id, CATEGORIA as nombre FROM tab_categorias");
            $response['categoria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'deportista':
            $stmt = $conn->query("SELECT ID_DEPORTISTA as id, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) as nombre FROM tab_deportistas");
            $response['deportista'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'representante':
            $stmt = $conn->query("SELECT u.ID_USUARIO as id, CONCAT(r.NOMBRE_REPRE, ' ', r.APELLIDO_REPRE) as nombre 
                                  FROM tab_representantes r 
                                  JOIN tab_usuarios u ON r.ID_USUARIO = u.ID_USUARIO");
            $response['representante'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            echo json_encode(['error' => 'Tipo de reporte no válido']);
            exit;
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
