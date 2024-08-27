<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir el archivo de conexión a la base de datos
require_once('../admin/configuracion/conexion.php');

// Iniciar la sesión


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
$deportistaId = isset($_POST['deportista_id']) ? intval($_POST['deportista_id']) : 0;

if ($id_detalle <= 0) {
    echo "error";
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM tab_detalles WHERE ID_DETALLE = :id");
    $stmt->bindParam(':id', $id_detalle, PDO::PARAM_INT);
    $stmt->execute();

    // Seleccionar el nombre del deportista
    $stmt = $conn->prepare("SELECT NOMBRE_DEPO FROM tab_deportistas WHERE ID_DEPORTISTA = :deportistaId");
    $stmt->bindParam(':deportistaId', $deportistaId, PDO::PARAM_INT);
    $stmt->execute();
    $nom_depo = $stmt->fetch(PDO::FETCH_ASSOC);

    $ip = $_SERVER['REMOTE_ADDR'];
    $evento = "Dato Eliminado de " . $nom_depo['NOMBRE_DEPO'];
    $tipo_evento = "dato_elimnado";
    $query = "INSERT INTO tab_logs (ID_USUARIO, EVENTO, HORA_LOG, DIA_LOG, IP, TIPO_EVENTO) VALUES (?, ?, CURRENT_TIME(), CURRENT_DATE(), ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $evento, $ip, $tipo_evento]);

    echo "success";
    exit();
} catch (PDOException $e) {
    echo "error";
    exit();
}
?>
