<?php
require_once('../conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['types'])) {
    $types = $_POST['types'];
    $response = [];

    try {
        foreach ($types as $type) {
            switch ($type) {
                case 'categoria':
                    $stmt = $pdo->query("SELECT id, nombre_categoria as nombre FROM categorias");
                    $response['Categorías'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'deportista':
                    $stmt = $pdo->query("SELECT id, nombre FROM deportistas");
                    $response['Deportistas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'representante':
                    $stmt = $pdo->query("SELECT id, nombre FROM representantes");
                    $response['Representantes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (PDOException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Solicitud inválida']);
}