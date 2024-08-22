<?php
// procesar_datos.php
require_once('conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

try {
    $sql = "SELECT c.categoria_nombre, COUNT(p.id_producto) AS num_productos
            FROM tab_producto_categoria c
            LEFT JOIN tab_productos p ON c.id_categoria_producto = p.id_categoria_producto
            GROUP BY c.categoria_nombre";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categorias = [];
    $cantidades = [];
    foreach ($datos as $row) {
        $categorias[] = $row['categoria_nombre'];
        $cantidades[] = (int)$row['num_productos'];
    }

    // Devuelve los datos como JSON
    echo json_encode([
        'categorias' => $categorias,
        'cantidades' => $cantidades
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => "Error al ejecutar la consulta: " . $e->getMessage()]);
    exit();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

$conn = null;
?>
