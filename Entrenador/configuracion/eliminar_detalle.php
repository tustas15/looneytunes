<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_detalle'])) {
    $id_detalle = $_POST['id_detalle'];
    
    try {
        $sql = "DELETE FROM tab_detalles WHERE ID_DETALLE = :id_detalle";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_detalle', $id_detalle, PDO::PARAM_INT);
        $stmt->execute();

        echo 'success';
    } catch (Exception $e) {
        echo 'error';
    }
} else {
    echo 'invalid request';
}
?>