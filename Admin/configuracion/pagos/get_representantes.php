<?php
require_once('/xampp/htdocs/looneytunes/admin/configuracion/conexion.php');

try {
    $stmt = $conn->prepare("SELECT DISTINCT r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CEDULA_REPRE
                            FROM tab_representantes r
                            JOIN tab_representantes_deportistas rd ON r.ID_REPRESENTANTE = rd.ID_REPRESENTANTE
                            GROUP BY r.ID_REPRESENTANTE, r.NOMBRE_REPRE, r.APELLIDO_REPRE, r.CEDULA_REPRE
                            ORDER BY r.APELLIDO_REPRE, r.NOMBRE_REPRE");
    $stmt->execute();
    $representantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($representantes);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>