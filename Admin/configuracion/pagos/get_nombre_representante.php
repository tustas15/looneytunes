<?php
// get_nombre_representante.php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$id_representante = $_GET['id_representante'];

try {
    $stmt = $conn->prepare("SELECT NOMBRE_REPRE, CEDULA_REPRE FROM tab_representantes WHERE ID_REPRESENTANTE = :id");
    $stmt->bindParam(':id', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $representante = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($representante);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
