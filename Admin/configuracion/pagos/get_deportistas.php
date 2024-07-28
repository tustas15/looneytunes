<?php
// get_deportistas.php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$id_representante = $_GET['id_representante'];

try {
    $stmt = $conn->prepare("SELECT ID_DEPORTISTA, NOMBRE_DEPO, APELLIDO_DEPO FROM tab_deportistas WHERE ID_REPRESENTANTE = :id ORDER BY APELLIDO_DEPO ASC");
    $stmt->bindParam(':id', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($deportistas);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
