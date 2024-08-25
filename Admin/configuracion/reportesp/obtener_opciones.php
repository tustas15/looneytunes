<?php
require_once('../conexion.php');

if (!isset($_POST['tipos'])) {
    echo json_encode(['error' => 'No se especificaron tipos de reporte']);
    exit;
}
$tipos = $_POST['tipos'];
$response = [];

try {
    foreach ($tipos as $tipo) {
        switch ($tipo) {
            case 'categoria':
                $stmt = $conn->query("SELECT ID_CATEGORIA as id, CATEGORIA as nombre FROM tab_categorias");
                $response['categorias'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'deportista':
                $stmt = $conn->query("SELECT ID_DEPORTISTA as id, CONCAT(NOMBRE_DEPO, ' ', APELLIDO_DEPO) as nombre FROM tab_deportistas");
                $response['deportistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'representante':
                // Ajuste para obtener opciones de representantes basadas en el ID del usuario
                $stmt = $conn->query("SELECT u.ID_USUARIO as id, CONCAT(r.NOMBRE_REPRE, ' ', r.APELLIDO_REPRE) as nombre 
                                      FROM tab_representantes r 
                                      JOIN tab_usuarios u ON r.ID_USUARIO = u.ID_USUARIO");
                $response['representantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
