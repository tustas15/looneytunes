<?php
// get_cedula_deportista.php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$id_deportista = $_GET['id_deportista'];

try {
    $stmt = $conn->prepare("SELECT CEDULA_DEPO FROM tab_deportistas WHERE ID_DEPORTISTA = :id");
    $stmt->bindParam(':id', $id_deportista, PDO::PARAM_INT);
    $stmt->execute();
    $cedula_deportista = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($cedula_deportista);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
