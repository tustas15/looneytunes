<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('../admin/configuracion/conexion.php');

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
$id_detalle = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : 0;

if ($id_detalle <= 0) {
    echo "error";
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM tab_detalles WHERE ID_DETALLE = :id");
    $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
    $stmt->execute();

    echo "success";
    exit;
} catch (PDOException $e) {
    echo "error";
    exit;
}
?>