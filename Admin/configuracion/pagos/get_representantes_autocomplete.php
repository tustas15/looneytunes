<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$term = $_GET['term'];

try {
    $stmt = $conn->prepare("SELECT r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE, rd.ID_DEPORTISTA 
                            FROM tab_representantes r
                            JOIN tab_representantes_deportistas rd ON r.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
                            WHERE r.APELLIDO_REPRE LIKE :term
                            ORDER BY r.APELLIDO_REPRE, r.NOMBRE_REPRE");
    $term = "%$term%";
    $stmt->bindParam(':term', $term, PDO::PARAM_STR);
    $stmt->execute();
    $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($representantes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
