<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

$id_representante = $_GET['id_representante'];

try {
    $stmt = $conn->prepare("SELECT d.ID_DEPORTISTA, d.NOMBRE_DEPO, d.APELLIDO_DEPO 
                            FROM tab_deportistas d
                            JOIN tab_representantes_deportistas rd ON d.ID_DEPORTISTA = rd.ID_DEPORTISTA
                            WHERE rd.ID_REPRESENTANTE = :id
                            ORDER BY d.APELLIDO_DEPO, d.NOMBRE_DEPO");
    $stmt->bindParam(':id', $id_representante, PDO::PARAM_INT);
    $stmt->execute();
    $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($deportistas);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
