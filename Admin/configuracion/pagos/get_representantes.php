<?php
// get_representantes.php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    $stmt = $conn->prepare("SELECT ID_REPRESENTANTE, APELLIDO_REPRE FROM tab_representantes");
    $stmt->execute();
    $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($representantes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
