<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

// Iniciar la sesión
session_start();

// Verificar que la conexión se estableció correctamente
if ($conn === null) {
    die("Error de conexión a la base de datos.");
}

// Verificar que el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener y sanitizar el ID del detalle a eliminar
$id_detalle = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if ($id_detalle <= 0) {
    die("No se especificó un ID de detalle válido.");
}

try {
    $stmt = $conn->prepare("DELETE FROM tab_detalles WHERE ID_DETALLE = :id");
    $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: detalles_deportista.php?id=" . $_GET['deportista_id']);
    exit();
} catch (PDOException $e) {
    die("Error al eliminar el detalle: " . $e->getMessage());
}
?>
