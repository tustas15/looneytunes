<?php
// Conexión a la base de datos
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Public/login.php");
    exit();
}

if (!isset($_SESSION['tipo_usuario'])) {
    echo "Tipo de usuario no definido.";
    exit();
}

if (!isset($_GET['ID_ADMINISTRADOR'])) {
    echo "ID de administrador no proporcionado.";
    exit();
}

$idAdministrador = $_GET['ID_ADMINISTRADOR'];

try {
    $sql = "DELETE FROM tab_administradores WHERE ID_ADMINISTRADOR = :idAdministrador";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idAdministrador', $idAdministrador, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: ../busqueda/busqueda_administradores.php?mensaje=eliminado");
    } else {
        echo "Error al eliminar el administrador.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Cierre de la conexión
$conn = null;
?>
