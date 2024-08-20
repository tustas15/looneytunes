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

// Comprobar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

// Obtener y sanitizar el ID del informe
$id_informe = isset($_POST['id_informe']) ? intval($_POST['id_informe']) : 0;

if ($id_informe === 0) {
    die("No se especificó un informe válido.");
}

try {
    // Preparar la consulta de eliminación
    $stmt = $conn->prepare("DELETE FROM tab_informes WHERE id_informe = :id_informe");
    $stmt->bindParam(':id_informe', $id_informe, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigir de vuelta a la página anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();

} catch (PDOException $e) {
    die("Error al eliminar el informe: " . $e->getMessage());
}
?>
